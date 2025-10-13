<?php

namespace App\Http\Controllers\API\SEO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use SimpleXMLElement;

class SitemapValidatorController extends Controller
{
    public function validateSitemap(Request $request)
    {
        // Path to CA certificate bundle
        $caCertPath = storage_path('app/cacert.pem');

        // Check if CA bundle exists
        if (!file_exists($caCertPath)) {
            Log::error('CA certificate bundle not found', ['path' => $caCertPath]);
            return response()->json([
                'status' => 'error',
                'message' => 'Server configuration error: CA certificate bundle not found.',
                'is_valid' => false,
                'errors' => ['Please download cacert.pem from https://curl.se/ca/cacert.pem and place it at ' . $caCertPath],
                'sitemap_url' => null,
                'sitemaps' => [],
            ], 500);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'url' => ['required', 'url'],
            'sitemap_url' => ['nullable', 'url', 'regex:/\.xml($|\.gz$)/i'],
        ], [
            'url.required' => 'The URL is required.',
            'url.url' => 'The URL must be a valid URL.',
            'sitemap_url.url' => 'The sitemap URL must be a valid URL.',
            'sitemap_url.regex' => 'The sitemap URL must point to an XML file (optionally gzipped).',
        ]);

        if ($validator->fails()) {
            Log::error('Request validation failed', ['errors' => $validator->errors()->toArray()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Request validation failed.',
                'is_valid' => false,
                'errors' => $validator->errors()->toArray(),
                'sitemap_url' => null,
                'sitemaps' => [],
            ], 400);
        }

        // Resolve URL
        $baseUrl = preg_match('/^https?:\/\//i', $request->input('url')) ? $request->input('url') : 'https://' . ltrim($request->input('url'), '/');
        $parsedBaseUrl = parse_url($baseUrl);
        $baseHost = $parsedBaseUrl['host'] ?? '';
        $scheme = $parsedBaseUrl['scheme'] ?? 'https';

        // Initialize Guzzle client
        $client = new Client([
            'timeout' => 10,
            'allow_redirects' => true,
            'verify' => $caCertPath,
        ]);

        // Sitemap candidates
        $sitemapUrl = $request->input('sitemap_url');
        $sitemapCandidates = [
            "$scheme://$baseHost/sitemap.xml",
            "$scheme://$baseHost/sitemap_index.xml",
        ];
        if ($sitemapUrl) {
            array_unshift($sitemapCandidates, $sitemapUrl);
        }

        // Check robots.txt
        try {
            $response = $client->get("$scheme://$baseHost/robots.txt");
            if ($response->getStatusCode() === 200) {
                $robotsContent = $response->getBody()->getContents();
                if (preg_match_all('/^Sitemap:\s*(.+)$/im', $robotsContent, $matches)) {
                    $sitemapCandidates = array_merge($sitemapCandidates, $matches[1]);
                }
            }
        } catch (RequestException $e) {
            Log::warning('Failed to fetch robots.txt', ['url' => "$scheme://$baseHost/robots.txt", 'error' => $e->getMessage()]);
        }

        // Try to fetch sitemap
        $xmlContent = null;
        $errors = [];
        foreach (array_unique($sitemapCandidates) as $candidate) {
            try {
                $response = $client->get($candidate);
                if ($response->getStatusCode() === 200) {
                    $sitemapUrl = $candidate;
                    $xmlContent = $response->getBody()->getContents();
                    break;
                }
            } catch (RequestException $e) {
                $errors[] = "Failed to fetch sitemap at $candidate: " . $e->getMessage();
                Log::warning('Sitemap fetch failed', ['url' => $candidate, 'error' => $e->getMessage()]);
            }
        }

        if (!$sitemapUrl) {
            Log::error('No sitemap found', ['base_url' => $baseUrl, 'errors' => $errors]);
            return response()->json([
                'status' => 'error',
                'message' => 'No sitemap found.',
                'is_valid' => false,
                'errors' => $errors,
                'sitemap_url' => null,
                'sitemaps' => [],
            ], 400);
        }

        // Parse and validate sitemap
        try {
            // Handle gzipped sitemaps
            if (preg_match('/\.gz$/i', $sitemapUrl)) {
                $xmlContent = gzdecode($xmlContent);
                if ($xmlContent === false) {
                    throw new \Exception('Failed to decompress sitemap.');
                }
            }

            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($xmlContent, 'SimpleXMLElement', 0, 'http://www.sitemaps.org/schemas/sitemap/0.9');
            if ($xml === false) {
                $xmlErrors = [];
                foreach (libxml_get_errors() as $error) {
                    $xmlErrors[] = 'XML error: ' . trim($error->message) . ' at line ' . $error->line;
                }
                libxml_clear_errors();
                Log::error('Invalid sitemap XML', ['sitemap_url' => $sitemapUrl, 'errors' => $xmlErrors]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid sitemap XML.',
                    'is_valid' => false,
                    'errors' => $xmlErrors,
                    'sitemap_url' => $sitemapUrl,
                    'sitemaps' => [],
                ], 400);
            }

            if ($xml->getName() !== 'urlset' && $xml->getName() !== 'sitemapindex') {
                Log::error('Invalid sitemap structure', ['sitemap_url' => $sitemapUrl, 'root_element' => $xml->getName()]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid sitemap: Must be a <urlset> or <sitemapindex>.',
                    'is_valid' => false,
                    'errors' => ['Invalid root element: ' . $xml->getName()],
                    'sitemap_url' => $sitemapUrl,
                    'sitemaps' => [],
                ], 400);
            }

            // Check size
            if (strlen($xmlContent) > 50 * 1024 * 1024) {
                Log::error('Sitemap exceeds size limit', ['sitemap_url' => $sitemapUrl, 'size' => strlen($xmlContent)]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sitemap exceeds 50MB size limit.',
                    'is_valid' => false,
                    'errors' => ['Sitemap size exceeds 50MB.'],
                    'sitemap_url' => $sitemapUrl,
                    'sitemaps' => [],
                ], 400);
            }

            // Initialize response data
            $isSitemapValid = true;
            $sitemaps = [];

            // Helper function to validate a single sitemap
            $validateSingleSitemap = function ($sitemapUrl, $xmlContent, $client, &$isSitemapValid) use ($baseHost, $caCertPath) {
                $result = [
                    'sitemap_url' => $sitemapUrl,
                    'is_valid' => false,
                    'http_status' => '200 OK',
                    'urls' => [],
                    'errors' => [],
                ];

                // Handle gzipped sitemaps
                if (preg_match('/\.gz$/i', $sitemapUrl)) {
                    $xmlContent = gzdecode($xmlContent);
                    if ($xmlContent === false) {
                        $result['errors'][] = 'Failed to decompress sitemap.';
                        Log::error('Sitemap decompression failed', ['sitemap_url' => $sitemapUrl]);
                        $isSitemapValid = false;
                        return $result;
                    }
                }

                libxml_use_internal_errors(true);
                $xml = simplexml_load_string($xmlContent, 'SimpleXMLElement', 0, 'http://www.sitemaps.org/schemas/sitemap/0.9');
                if ($xml === false) {
                    foreach (libxml_get_errors() as $error) {
                        $result['errors'][] = 'XML error: ' . trim($error->message) . ' at line ' . $error->line;
                    }
                    libxml_clear_errors();
                    Log::error('Invalid sitemap XML', ['sitemap_url' => $sitemapUrl, 'errors' => $result['errors']]);
                    $isSitemapValid = false;
                    return $result;
                }

                Log::info('Raw XML content length', ['sitemap_url' => $sitemapUrl, 'xml_length' => strlen($xmlContent)]);

                if ($xml->getName() === 'sitemapindex') {
                    $result['errors'][] = 'Nested sitemapindex detected; only processing direct sitemaps.';
                    Log::warning('Nested sitemapindex detected', ['sitemap_url' => $sitemapUrl]);
                    $isSitemapValid = false;
                    return $result;
                }

                if ($xml->getName() === 'urlset') {
                    $urlCount = count($xml->url);
                    Log::info('Processing sitemap with URL count', ['sitemap_url' => $sitemapUrl, 'url_count' => $urlCount]);

                    if ($urlCount > 50000) {
                        $result['errors'][] = 'Sitemap exceeds 50,000 URL limit.';
                        Log::error('Sitemap exceeds URL limit', ['sitemap_url' => $sitemapUrl, 'url_count' => $urlCount]);
                        $isSitemapValid = false;
                    }

                    // Process up to 100 URLs to prevent timeouts
                    $urlsToProcess = array_slice(iterator_to_array($xml->url), 0, 100);
                    Log::info('URLs to process', ['sitemap_url' => $sitemapUrl, 'urls_to_process_count' => count($urlsToProcess)]);

                    foreach ($urlsToProcess as $urlEntry) {
                        $url = (string)$urlEntry->loc;
                        $urlData = [
                            'url' => $url,
                            'is_valid' => false,
                            'http_status' => null,
                            'host_status' => 'not live',
                            'errors' => [],
                        ];

                        if (!filter_var($url, FILTER_VALIDATE_URL)) {
                            $urlData['errors'][] = 'Invalid URL format.';
                            $isSitemapValid = false;
                        } else {
                            $parsedUrl = parse_url($url);
                            $urlHost = $parsedUrl['host'] ?? '';
                            if ($urlHost !== $baseHost) {
                                $urlData['errors'][] = 'URL belongs to a different domain.';
                                $isSitemapValid = false;
                            } else {
                                try {
                                    $response = $client->head($url);
                                    $urlData['http_status'] = $response->getStatusCode() . ' ' . $response->getReasonPhrase();
                                    $urlData['host_status'] = $response->getStatusCode() === 200 ? 'live' : 'not live';
                                    $urlData['is_valid'] = $response->getStatusCode() === 200;
                                } catch (RequestException $e) {
                                    $urlData['errors'][] = $e->getMessage();
                                    $isSitemapValid = false;
                                }
                            }
                        }

                        $result['urls'][] = $urlData;
                    }

                    $result['is_valid'] = empty($result['errors']) && empty(array_filter($result['urls'], fn($urlData) => !$urlData['is_valid']));
                    Log::info('Processed URLs for sitemap', ['sitemap_url' => $sitemapUrl, 'processed_url_count' => count($result['urls'])]);
                }

                return $result;
            };

            // Process sitemap
            if ($xml->getName() === 'urlset') {
                $sitemaps[] = $validateSingleSitemap($sitemapUrl, $xmlContent, $client, $isSitemapValid);
            } else {
                // Handle sitemapindex, process all sitemaps
                foreach ($xml->sitemap as $sitemap) {
                    $nestedSitemapUrl = (string)$sitemap->loc;
                    Log::info('Processing nested sitemap', ['sitemap_url' => $nestedSitemapUrl]);
                    try {
                        $response = $client->get($nestedSitemapUrl);
                        if ($response->getStatusCode() === 200) {
                            $nestedXmlContent = $response->getBody()->getContents();
                            $sitemaps[] = $validateSingleSitemap($nestedSitemapUrl, $nestedXmlContent, $client, $isSitemapValid);
                        } else {
                            $sitemaps[] = [
                                'sitemap_url' => $nestedSitemapUrl,
                                'is_valid' => false,
                                'http_status' => $response->getStatusCode() . ' ' . $response->getReasonPhrase(),
                                'urls' => [],
                                'errors' => ['Failed to fetch sitemap: HTTP ' . $response->getStatusCode()],
                            ];
                            $isSitemapValid = false;
                            Log::error('Failed to fetch nested sitemap', ['sitemap_url' => $nestedSitemapUrl, 'http_status' => $response->getStatusCode()]);
                        }
                    } catch (RequestException $e) {
                        $sitemaps[] = [
                            'sitemap_url' => $nestedSitemapUrl,
                            'is_valid' => false,
                            'http_status' => null,
                            'urls' => [],
                            'errors' => ['Failed to fetch sitemap: ' . $e->getMessage()],
                        ];
                        $isSitemapValid = false;
                        Log::error('Exception fetching nested sitemap', ['sitemap_url' => $nestedSitemapUrl, 'error' => $e->getMessage()]);
                    }
                }
            }

            // Prepare response
            $responseData = [
                'status' => 'success',
                'url' => $baseUrl,
                'is_valid' => $isSitemapValid,
                'sitemap_url' => $sitemapUrl,
                'sitemaps' => $sitemaps,
                'errors' => $errors,
            ];

            Log::info('Sitemap validation completed', [
                'url' => $baseUrl,
                'sitemap_url' => $sitemapUrl,
                'is_valid' => $isSitemapValid,
                'sitemap_count' => count($sitemaps),
                'url_count' => array_sum(array_map(fn($sitemap) => count($sitemap['urls']), $sitemaps)),
            ]);

            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            Log::error('Unexpected error during sitemap validation', ['url' => $baseUrl, 'sitemap_url' => $sitemapUrl, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while validating the sitemap.',
                'is_valid' => false,
                'errors' => [$e->getMessage()],
                'sitemap_url' => $sitemapUrl,
                'sitemaps' => [],
            ], 500);
        }
    }
}
