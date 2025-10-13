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
                'http_status' => null,
                'host_status' => 'unknown',
                'sitemap_url' => null,
                'urls' => [],
            ], 500);
        }

        // Custom validation
        $validator = Validator::make($request->all(), [
            'url' => [
                'required',
                'url',
                function ($attribute, $value, $fail) {
                    $resolvedUrl = preg_match('/^https?:\/\//i', $value) ? $value : 'https://' . ltrim($value, '/');
                    if (!filter_var($resolvedUrl, FILTER_VALIDATE_URL)) {
                        $fail('The URL format is invalid.');
                        return;
                    }
                    $parsedUrl = parse_url($resolvedUrl);
                    if (!$parsedUrl || !isset($parsedUrl['host']) || !filter_var($parsedUrl['host'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                        $fail('The URL must contain a valid domain.');
                        return;
                    }
                },
            ],
            'sitemap_url' => [
                'nullable',
                'url',
                function ($attribute, $value, $fail) {
                    if ($value && !preg_match('/\.xml($|\.gz$)/i', $value)) {
                        $fail('The sitemap URL must point to an XML file (optionally gzipped).');
                    }
                },
            ],
        ], [
            'url.required' => 'The URL is required.',
            'url.url' => 'The URL must be a valid URL.',
            'sitemap_url.url' => 'The sitemap URL must be a valid URL.',
        ]);

        // Collect valid domains from the request
        $resolvedUrl = preg_match('/^https?:\/\//i', $request->input('url')) ? $request->input('url') : 'https://' . ltrim($request->input('url'), '/');
        $parsedBaseUrl = parse_url($resolvedUrl);
        $baseHost = $parsedBaseUrl['host'] ?? '';
        $validDomains = [];
        if ($baseHost && (dns_get_record($baseHost, DNS_A) || dns_get_record($baseHost, DNS_CNAME))) {
            $validDomains[] = $baseHost;
        }

        // Return validation errors
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            if ($baseHost && !dns_get_record($baseHost, DNS_A) && !dns_get_record($baseHost, DNS_CNAME)) {
                $errors['url'] = array_merge($errors['url'] ?? [], ['The domain does not exist or is not reachable for ' . $resolvedUrl]);
                foreach ($validDomains as $validDomain) {
                    $distance = levenshtein(strtolower($baseHost), strtolower($validDomain));
                    if ($distance <= 5 && $distance > 0) {
                        $errors['url'][] = 'Did you mean https://' . $validDomain . '?';
                    }
                }
                Log::error('DNS resolution failed for URL', ['url' => $resolvedUrl, 'host' => $baseHost]);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'URL validation failed.',
                'is_valid' => false,
                'errors' => $errors,
                'http_status' => null,
                'host_status' => 'not live',
                'sitemap_url' => null,
                'urls' => [],
            ], 400);
        }

        // Resolve URL
        $baseUrl = $resolvedUrl;
        $scheme = $parsedBaseUrl['scheme'] ?? 'https';
        $host = $baseHost;

        // Initialize Guzzle client
        $clientOptions = [
            'timeout' => 5,
            'allow_redirects' => true,
            'verify' => $caCertPath,
        ];
        $client = new Client($clientOptions);

        // Try to find sitemap
        $sitemapUrl = $request->input('sitemap_url');
        $sitemapErrors = [];
        $sitemapCandidates = [
            "$scheme://$host/sitemap.xml",
            "$scheme://$host/sitemap_index.xml",
            "$scheme://$host/sitemap-main.xml",
            "$scheme://$host/sitemap.xml.gz",
            "$scheme://$host/sitemap/sitemap.xml",
        ];

        // If a custom sitemap URL is provided, prioritize it
        if ($sitemapUrl) {
            $sitemapCandidates = array_merge([$sitemapUrl], $sitemapCandidates);
        }

        // Check robots.txt for Sitemap directive
        try {
            $robotsResponse = $client->get("$scheme://$host/robots.txt");
            if ($robotsResponse->getStatusCode() === 200) {
                $robotsContent = $robotsResponse->getBody()->getContents();
                if (preg_match_all('/^Sitemap:\s*(.+)$/im', $robotsContent, $matches)) {
                    $sitemapCandidates = array_merge($sitemapCandidates, $matches[1]);
                }
            }
        } catch (RequestException $e) {
            Log::warning('Failed to fetch robots.txt', ['url' => "$scheme://$host/robots.txt", 'error' => $e->getMessage()]);
            $sitemapErrors[] = "Failed to fetch robots.txt: " . $e->getMessage();
        }

        // Try each sitemap candidate
        $xmlContent = null;
        foreach (array_unique($sitemapCandidates) as $candidate) {
            try {
                $response = $client->get($candidate);
                if ($response->getStatusCode() === 200) {
                    $sitemapUrl = $candidate;
                    $xmlContent = $response->getBody()->getContents();
                    break;
                }
            } catch (RequestException $e) {
                $sitemapErrors[] = "Failed to fetch sitemap at $candidate: " . $e->getMessage();
            }
        }

        // Optional: Crawl for sitemap if none found
        if (!$sitemapUrl && !$request->input('sitemap_url')) {
            try {
                $response = $client->get("$scheme://$host");
                if ($response->getStatusCode() === 200) {
                    $html = $response->getBody()->getContents();
                    // Look for links to .xml files in the HTML
                    if (preg_match_all('/href=["\'](.*?\.xml(?:\.gz)?)["\']/i', $html, $matches)) {
                        foreach ($matches[1] as $link) {
                            $candidate = (preg_match('/^https?:\/\//i', $link)) ? $link : "$scheme://$host/" . ltrim($link, '/');
                            try {
                                $response = $client->get($candidate);
                                if ($response->getStatusCode() === 200 && strpos($response->getBody()->getContents(), '<urlset') !== false) {
                                    $sitemapUrl = $candidate;
                                    $xmlContent = $response->getBody()->getContents();
                                    break;
                                }
                            } catch (RequestException $e) {
                                $sitemapErrors[] = "Failed to fetch potential sitemap at $candidate: " . $e->getMessage();
                            }
                        }
                    }
                }
            } catch (RequestException $e) {
                Log::warning('Failed to crawl website for sitemap', ['url' => "$scheme://$host", 'error' => $e->getMessage()]);
                $sitemapErrors[] = "Failed to crawl website for sitemap: " . $e->getMessage();
            }
        }

        if (!$sitemapUrl) {
            Log::error('No sitemap found', ['base_url' => $baseUrl, 'errors' => $sitemapErrors]);
            return response()->json([
                'status' => 'error',
                'message' => 'No sitemap found for the provided URL.',
                'is_valid' => false,
                'errors' => ['No accessible sitemap found at common locations, robots.txt, or via crawling.'],
                'http_status' => null,
                'host_status' => 'live',
                'sitemap_url' => null,
                'urls' => [],
            ], 400);
        }

        // Parse and validate sitemap
        try {
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($xmlContent);
            if ($xml === false) {
                $errors = [];
                foreach (libxml_get_errors() as $error) {
                    $errors[] = 'XML error: ' . trim($error->message) . ' at line ' . $error->line;
                }
                libxml_clear_errors();
                Log::error('Invalid sitemap XML', ['sitemap_url' => $sitemapUrl, 'errors' => $errors]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid sitemap XML.',
                    'is_valid' => false,
                    'errors' => $errors,
                    'http_status' => '200 OK',
                    'host_status' => 'live',
                    'sitemap_url' => $sitemapUrl,
                    'urls' => [],
                ], 400);
            }

            // Check sitemap type and structure
            if ($xml->getName() !== 'urlset' && $xml->getName() !== 'sitemapindex') {
                Log::error('Invalid sitemap structure', ['sitemap_url' => $sitemapUrl, 'root_element' => $xml->getName()]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid sitemap: Must be a <urlset> or <sitemapindex>.',
                    'is_valid' => false,
                    'errors' => ['Invalid root element: ' . $xml->getName()],
                    'http_status' => '200 OK',
                    'host_status' => 'live',
                    'sitemap_url' => $sitemapUrl,
                    'urls' => [],
                ], 400);
            }

            // Check size and URL count
            if (strlen($xmlContent) > 50 * 1024 * 1024) {
                Log::error('Sitemap exceeds size limit', ['sitemap_url' => $sitemapUrl, 'size' => strlen($xmlContent)]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sitemap exceeds 50MB size limit.',
                    'is_valid' => false,
                    'errors' => ['Sitemap size exceeds 50MB.'],
                    'http_status' => '200 OK',
                    'host_status' => 'live',
                    'sitemap_url' => $sitemapUrl,
                    'urls' => [],
                ], 400);
            }

            $isSitemapValid = true;
            $errors = [];
            $urls = [];

            // Collect valid domains from sitemap URLs
            if ($xml->getName() === 'urlset') {
                foreach ($xml->url as $urlEntry) {
                    $url = (string)$urlEntry->loc;
                    $parsedUrl = parse_url($url);
                    $urlHost = $parsedUrl['host'] ?? '';
                    if ($urlHost && (dns_get_record($urlHost, DNS_A) || dns_get_record($urlHost, DNS_CNAME))) {
                        $validDomains[] = $urlHost;
                    }
                }
            } elseif ($xml->getName() === 'sitemapindex') {
                foreach ($xml->sitemap as $sitemap) {
                    $url = (string)$sitemap->loc;
                    $parsedUrl = parse_url($url);
                    $urlHost = $parsedUrl['host'] ?? '';
                    if ($urlHost && (dns_get_record($urlHost, DNS_A) || dns_get_record($urlHost, DNS_CNAME))) {
                        $validDomains[] = $urlHost;
                    }
                }
            }

            // Handle sitemapindex
            if ($xml->getName() === 'sitemapindex') {
                $errors[] = 'Sitemap index detected. Only individual sitemaps (<urlset>) are fully validated in this version.';
                foreach ($xml->sitemap as $sitemap) {
                    $url = (string)$sitemap->loc;
                    $urlData = [
                        'url' => $url,
                        'is_valid' => false,
                        'http_status' => null,
                        'host_status' => 'unknown',
                        'errors' => ['Sitemap index URLs not validated individually.'],
                    ];
                    $urls[] = $urlData;
                }
                $isSitemapValid = false;
            } else {
                // Handle urlset
                if (count($xml->url) > 50000) {
                    $errors[] = 'Sitemap exceeds 50,000 URL limit.';
                    $isSitemapValid = false;
                }

                foreach ($xml->url as $urlEntry) {
                    $url = (string)$urlEntry->loc;
                    $urlData = [
                        'url' => $url,
                        'is_valid' => false,
                        'http_status' => null,
                        'host_status' => 'not live',
                        'errors' => [],
                    ];

                    // Validate URL format
                    if (!filter_var($url, FILTER_VALIDATE_URL)) {
                        $urlData['errors'][] = 'Invalid URL format.';
                        $urls[] = $urlData;
                        $isSitemapValid = false;
                        continue;
                    }

                    // Check DNS for URL
                    $parsedUrl = parse_url($url);
                    $urlHost = $parsedUrl['host'] ?? '';
                    if (!$urlHost || (!dns_get_record($urlHost, DNS_A) && !dns_get_record($urlHost, DNS_CNAME))) {
                        $urlData['errors'][] = 'Domain does not exist or is not reachable.';
                        foreach ($validDomains as $validDomain) {
                            $distance = levenshtein(strtolower($urlHost), strtolower($validDomain));
                            if ($distance <= 5 && $distance > 0) {
                                $urlData['errors'][] = 'Did you mean https://' . $validDomain . '?';
                            }
                        }
                        $urls[] = $urlData;
                        $isSitemapValid = false;
                        continue;
                    }

                    // Check URL accessibility
                    try {
                        $response = $client->head($url);
                        $urlData['http_status'] = $response->getStatusCode() . ' ' . $response->getReasonPhrase();
                        $urlData['host_status'] = $response->getStatusCode() === 200 ? 'live' : 'not live';
                        $urlData['is_valid'] = $response->getStatusCode() === 200;
                    } catch (RequestException $e) {
                        $urlData['errors'][] = $e->getMessage();
                        if (strpos($e->getMessage(), 'cURL error 60') !== false) {
                            $urlData['errors'][] = 'SSL verification failed. Ensure cacert.pem is valid at ' . $caCertPath;
                        }
                    }

                    $urls[] = $urlData;
                    if (!$urlData['is_valid']) {
                        $isSitemapValid = false;
                    }
                }
            }

            // Prepare response
            $responseData = [
                'status' => 'success',
                'url' => $baseUrl,
                'is_valid' => $isSitemapValid,
                'http_status' => '200 OK',
                'host_status' => 'live',
                'sitemap_url' => $sitemapUrl,
                'urls' => $urls,
                'errors' => $errors,
            ];

            Log::info('Sitemap validation completed', [
                'url' => $baseUrl,
                'sitemap_url' => $sitemapUrl,
                'is_valid' => $isSitemapValid,
                'url_count' => count($urls),
            ]);

            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            Log::error('Unexpected error during sitemap validation', ['url' => $baseUrl, 'sitemap_url' => $sitemapUrl, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while validating the sitemap.',
                'is_valid' => false,
                'errors' => [$e->getMessage()],
                'http_status' => null,
                'host_status' => 'live',
                'sitemap_url' => $sitemapUrl,
                'urls' => [],
            ], 500);
        }
    }
}
