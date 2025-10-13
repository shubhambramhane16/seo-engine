<?php

namespace App\Http\Controllers\API\SEO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Iodev\Whois\Factory;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;
use Pdp\Rules;
use Pdp\Domain;

class DomainAgeCheckerController extends Controller
{
    public function checkDomainAge(Request $request)
    {
        // Path to CA certificate bundle
        $caCertPath = storage_path('app/cacert.pem');

        // Check if CA bundle exists
        if (!file_exists($caCertPath)) {
            Log::error('CA certificate bundle not found', ['path' => $caCertPath]);
            return response()->json([
                'status' => 'error',
                'message' => 'Server configuration error: CA certificate bundle not found.',
                'errors' => ['Please download cacert.pem from https://curl.se/ca/cacert.pem and place it at ' . $caCertPath],
                'url' => null,
                'domain_age' => null,
                'created_date' => null,
                'expiration_date' => null,
            ], 500);
        }

        // Validate URL
        $validator = Validator::make($request->all(), [
            'url' => [
                'required',
                'url',
                function ($attribute, $value, $fail) {
                    // Normalize URL
                    $resolvedUrl = preg_match('/^https?:\/\//i', $value) ? $value : 'https://' . ltrim($value, '/');

                    // Validate URL format
                    if (!filter_var($resolvedUrl, FILTER_VALIDATE_URL)) {
                        $fail('The URL format is invalid: ' . $value);
                        return;
                    }

                    // Validate domain
                    $parsedUrl = parse_url($resolvedUrl);
                    if (!$parsedUrl || !isset($parsedUrl['host']) || !filter_var($parsedUrl['host'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                        $fail('The URL must contain a valid domain: ' . $value);
                        return;
                    }
                },
            ],
        ], [
            'url.required' => 'The URL is required.',
            'url.url' => 'The URL must be a valid URL.',
        ]);

        // Return validation errors
        if ($validator->fails()) {
            $resolvedUrl = preg_match('/^https?:\/\//i', $request->input('url', '')) ? $request->input('url') : 'https://' . ltrim($request->input('url', ''), '/');
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors()->toArray(),
                'url' => $resolvedUrl ?: null,
                'domain_age' => null,
                'created_date' => null,
                'expiration_date' => null,
            ], 400);
        }

        // Process the URL
        $resolvedUrl = preg_match('/^https?:\/\//i', $request->input('url')) ? $request->input('url') : 'https://' . ltrim($request->input('url'), '/');
        $parsedUrl = parse_url($resolvedUrl);
        $host = $parsedUrl['host'] ?? '';

        // Extract root domain using php-domain-parser
        try {
            $publicSuffixList = Rules::fromPath(storage_path('app/public_suffix_list.dat'));
            $domain = $publicSuffixList->resolve($host);
            $rootDomain = $domain->registrableDomain()->toString();
            if (!$rootDomain) {
                throw new \Exception('Unable to extract registrable domain');
            }
        } catch (\Exception $e) {
            Log::error('Failed to parse root domain', ['url' => $resolvedUrl, 'host' => $host, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to parse domain for ' . $resolvedUrl . '.',
                'errors' => ['Domain parsing failed: ' . $e->getMessage()],
                'url' => $resolvedUrl,
                'domain_age' => null,
                'created_date' => null,
                'expiration_date' => null,
            ], 500);
        }

        // Check DNS resolution
        if (!dns_get_record($rootDomain, DNS_A) && !dns_get_record($rootDomain, DNS_CNAME)) {
            Log::error('DNS resolution failed for URL', ['url' => $resolvedUrl, 'root_domain' => $rootDomain]);
            return response()->json([
                'status' => 'error',
                'message' => 'Domain does not exist or is not reachable: ' . $rootDomain,
                'errors' => ['No DNS records found for ' . $rootDomain],
                'url' => $resolvedUrl,
                'domain_age' => null,
                'created_date' => null,
                'expiration_date' => null,
            ], 400);
        }

        // Query WHOIS data with caching and retries
        try {
            $whois = Factory::get()->createWhois();
            $cacheKey = "whois_$rootDomain";
            $info = Cache::remember($cacheKey, now()->addHours(24), function () use ($whois, $rootDomain) {
                $maxRetries = 3;
                $retryCount = 0;
                while ($retryCount < $maxRetries) {
                    try {
                        return $whois->loadDomainInfo($rootDomain);
                    } catch (ConnectionException $e) {
                        $retryCount++;
                        if ($retryCount === $maxRetries) {
                            throw $e;
                        }
                        sleep(2); // Wait 2 seconds before retrying
                    }
                }
            });

            if (!$info) {
                Log::warning('WHOIS data not found', ['url' => $resolvedUrl, 'root_domain' => $rootDomain]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'WHOIS data not found for ' . $rootDomain . '. The domain may have restricted WHOIS data or the server may be unreachable.',
                    'errors' => ['No WHOIS data returned for ' . $rootDomain],
                    'url' => $resolvedUrl,
                    'domain_age' => null,
                    'created_date' => null,
                    'expiration_date' => null,
                ], 500);
            }

            // Extract creation and expiration dates
            $createdDate = $info->creationDate ? date('Y-m-d', $info->creationDate) : null;
            $expirationDate = $info->expirationDate ? date('Y-m-d', $info->expirationDate) : null;

            // Calculate domain age
            $domainAge = null;
            if ($createdDate) {
                try {
                    $created = new \DateTime($createdDate);
                    $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata')); // IST timezone
                    $interval = $now->diff($created);
                    $domainAge = [
                        'years' => $interval->y,
                        'months' => $interval->m,
                        'days' => $interval->d,
                    ];
                } catch (\Exception $e) {
                    Log::warning('Failed to parse creation date', ['url' => $resolvedUrl, 'root_domain' => $rootDomain, 'error' => $e->getMessage()]);
                }
            }

            // Prepare response
            $responseData = [
                'status' => 'success',
                'message' => 'Domain age check completed.',
                'errors' => [],
                'url' => $resolvedUrl,
                'domain_age' => $domainAge,
                'created_date' => $createdDate,
                'expiration_date' => $expirationDate,
            ];

            Log::info('Domain age check completed', [
                'url' => $resolvedUrl,
                'root_domain' => $rootDomain,
                'created_date' => $createdDate,
                'expiration_date' => $expirationDate,
                'domain_age' => $domainAge,
            ]);

            return response()->json($responseData, 200);

        } catch (ConnectionException $e) {
            Log::error('WHOIS connection error', [
                'url' => $resolvedUrl,
                'root_domain' => $rootDomain,
                'error' => $e->getMessage(),
                'server_ip' => gethostbyname('whois.registry.in'),
                'port_check' => @fsockopen('whois.registry.in', 43, $errno, $errstr, 5) ? 'open' : "closed ($errno: $errstr)",
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'WHOIS connection error for ' . $rootDomain . '. The server may be unreachable or port 43 may be blocked.',
                'errors' => ['Disconnect or connection timeout: ' . $e->getMessage()],
                'url' => $resolvedUrl,
                'domain_age' => null,
                'created_date' => null,
                'expiration_date' => null,
            ], 500);
        } catch (ServerMismatchException $e) {
            Log::error('WHOIS server mismatch', [
                'url' => $resolvedUrl,
                'root_domain' => $rootDomain,
                'error' => $e->getMessage(),
                'server_ip' => gethostbyname('whois.registry.in'),
                'port_check' => @fsockopen('whois.registry.in', 43, $errno, $errstr, 5) ? 'open' : "closed ($errno: $errstr)",
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'WHOIS server not found for ' . $rootDomain . '.',
                'errors' => ['TLD server not found: ' . $e->getMessage()],
                'url' => $resolvedUrl,
                'domain_age' => null,
                'created_date' => null,
                'expiration_date' => null,
            ], 500);
        } catch (WhoisException $e) {
            Log::error('WHOIS server error', [
                'url' => $resolvedUrl,
                'root_domain' => $rootDomain,
                'error' => $e->getMessage(),
                'server_ip' => gethostbyname('whois.registry.in'),
                'port_check' => @fsockopen('whois.registry.in', 43, $errno, $errstr, 5) ? 'open' : "closed ($errno: $errstr)",
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'WHOIS server error for ' . $rootDomain . '.',
                'errors' => ['WHOIS server responded with error: ' . $e->getMessage()],
                'url' => $resolvedUrl,
                'domain_age' => null,
                'created_date' => null,
                'expiration_date' => null,
            ], 500);
        } catch (\Exception $e) {
            Log::error('WHOIS lookup failed', [
                'url' => $resolvedUrl,
                'root_domain' => $rootDomain,
                'error' => $e->getMessage(),
                'server_ip' => gethostbyname('whois.registry.in'),
                'port_check' => @fsockopen('whois.registry.in', 43, $errno, $errstr, 5) ? 'open' : "closed ($errno: $errstr)",
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while checking domain age for ' . $rootDomain . '.',
                'errors' => [$e->getMessage()],
                'url' => $resolvedUrl,
                'domain_age' => null,
                'created_date' => null,
                'expiration_date' => null,
            ], 500);
        }
    }
}
