<?php

namespace App\Http\Controllers\API\SEO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GoogleIndexCheckerController extends Controller
{
    public function checkIndex(Request $request)
    {
        // Path to CA certificate bundle
        $caCertPath = storage_path('app/cacert.pem'); // D:\xampp\htdocs\lpl_seoengine\storage\app\cacert.pem

        // Check if CA bundle exists
        if (!file_exists($caCertPath)) {
            Log::error('CA certificate bundle not found', ['path' => $caCertPath]);
            return response()->json([
                'status' => 'error',
                'message' => 'Server configuration error: CA certificate bundle not found.',
                'errors' => ['Please download cacert.pem from https://curl.se/ca/cacert.pem and place it at ' . $caCertPath],
                'url' => null,
                'is_indexed' => false,
            ], 500);
        }

        // Custom validation
        $validator = Validator::make($request->all(), [
            'url' => [
                'required',
                'url',
                function ($attribute, $value, $fail) {
                    // Normalize URL
                    $resolvedUrl = preg_match('/^https?:\/\//i', $value) ? $value : 'https://' . ltrim($value, '/');

                    // Validate URL format
                    if (!filter_var($resolvedUrl, FILTER_VALIDATE_URL)) {
                        $fail('The URL format is invalid for ' . $value);
                        return;
                    }

                    // Validate domain
                    $parsedUrl = parse_url($resolvedUrl);
                    if (!$parsedUrl || !isset($parsedUrl['host']) || !filter_var($parsedUrl['host'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                        $fail('The URL must contain a valid domain for ' . $value);
                        return;
                    }
                },
            ],
            'reference_domains' => 'nullable|array|max:100',
            'reference_domains.*' => 'string|regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        ], [
            'url.required' => 'The URL is required.',
            'url.url' => 'The URL must be a valid URL.',
            'reference_domains.array' => 'Reference domains must be an array.',
            'reference_domains.max' => 'You can provide up to 100 reference domains.',
            'reference_domains.*.string' => 'Each reference domain must be a valid domain string.',
            'reference_domains.*.regex' => 'Each reference domain must be a valid domain format (e.g., example.com).',
        ]);

        // Return validation errors
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $resolvedUrl = preg_match('/^https?:\/\//i', $request->input('url', '')) ? $request->input('url') : 'https://' . ltrim($request->input('url', ''), '/');
            $parsedUrl = parse_url($resolvedUrl);
            $host = $parsedUrl['host'] ?? '';
            $validDomains = [];

            // Collect valid domains from reference_domains
            $referenceDomains = $request->input('reference_domains', []);
            foreach ($referenceDomains as $domain) {
                if (filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) && (dns_get_record($domain, DNS_A) || dns_get_record($domain, DNS_CNAME))) {
                    $validDomains[] = $domain;
                }
            }

            // Add DNS error and typo suggestions
            if ($host && !dns_get_record($host, DNS_A) && !dns_get_record($host, DNS_CNAME)) {
                $errors['url'] = array_merge($errors['url'] ?? [], ['The domain does not exist or is not reachable for ' . $resolvedUrl]);
                $suggestions = [];
                foreach ($validDomains as $validDomain) {
                    $distance = levenshtein(strtolower($host), strtolower($validDomain));
                    if ($distance <= 5 && $distance > 0) {
                        $suggestions[] = 'Did you mean https://' . $validDomain . '?';
                    }
                }
                $errors['url'] = array_merge($errors['url'], $suggestions);
                Log::error('DNS resolution failed for URL', ['url' => $resolvedUrl, 'host' => $host]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $errors,
                'url' => $resolvedUrl ?: null,
                'is_indexed' => false,
            ], 400);
        }

        // Process the URL
        $resolvedUrl = preg_match('/^https?:\/\//i', $request->input('url')) ? $request->input('url') : 'https://' . ltrim($request->input('url'), '/');
        $urlData = [
            'url' => $resolvedUrl,
            'is_indexed' => false,
            'errors' => [],
        ];

        // Collect valid domains (including the input URL if valid)
        $parsedUrl = parse_url($resolvedUrl);
        $host = $parsedUrl['host'] ?? '';
        $validDomains = [];
        if ($host && (dns_get_record($host, DNS_A) || dns_get_record($host, DNS_CNAME))) {
            $validDomains[] = $host;
        }
        foreach ($request->input('reference_domains', []) as $domain) {
            if (filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) && (dns_get_record($domain, DNS_A) || dns_get_record($domain, DNS_CNAME))) {
                $validDomains[] = $domain;
            }
        }

        // Check DNS resolution
        if (!$host || (!dns_get_record($host, DNS_A) && !dns_get_record($host, DNS_CNAME))) {
            $urlData['errors'][] = 'The domain does not exist or is not reachable for ' . $resolvedUrl;
            $suggestions = [];
            foreach ($validDomains as $validDomain) {
                $distance = levenshtein(strtolower($host), strtolower($validDomain));
                if ($distance <= 5 && $distance > 0) {
                    $suggestions[] = 'Did you mean https://' . $validDomain . '?';
                }
            }
            $urlData['errors'] = array_merge($urlData['errors'], $suggestions);
            Log::error('DNS resolution failed for URL', ['url' => $resolvedUrl, 'host' => $host]);

            return response()->json([
                'status' => 'error',
                'message' => 'Index check failed due to invalid domain.',
                'errors' => $urlData['errors'],
                'url' => $resolvedUrl,
                'is_indexed' => false,
            ], 400);
        }

        // Check Google index
        $client = new Client([
            'timeout' => 5,
            'verify' => $caCertPath,
        ]);

        try {
            $searchUrl = 'https://www.google.com/search?q=site:' . urlencode($resolvedUrl);
            $response = $client->get($searchUrl, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                ],
            ]);

            $body = $response->getBody()->getContents();
            if (strpos($body, 'did not match any documents') !== false) {
                $urlData['errors'][] = 'URL not found in Google index.';
            } elseif (strpos($body, 'CAPTCHA') !== false) {
                $urlData['errors'][] = 'Google returned a CAPTCHA. Please use Google Search Console for accurate results.';
            } else {
                $urlData['is_indexed'] = true;
            }
        } catch (RequestException $e) {
            $urlData['errors'][] = $e->getMessage();
            if (strpos($e->getMessage(), 'cURL error 60') !== false) {
                $urlData['errors'][] = 'SSL verification failed. Ensure cacert.pem is valid at ' . $caCertPath;
            }
            Log::error('Index check failed', ['url' => $resolvedUrl, 'error' => $e->getMessage()]);
        }

        // Determine status
        $status = empty($urlData['errors']) ? 'success' : 'error';
        $message = $status === 'success' ? 'Index check completed.' : 'Index check completed with errors.';

        Log::info('Google index check completed', ['url' => $resolvedUrl, 'is_indexed' => $urlData['is_indexed']]);

        return response()->json([
            'status' => $status,
            'message' => $message,
            'errors' => $urlData['errors'],
            'url' => $resolvedUrl,
            'is_indexed' => $urlData['is_indexed'],
        ], $status === 'success' ? 200 : 400);
    }
}
