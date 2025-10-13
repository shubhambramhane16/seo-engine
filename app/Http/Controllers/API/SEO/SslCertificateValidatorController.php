<?php

namespace App\Http\Controllers\API\SEO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SslCertificateValidatorController extends Controller
{
    public function validateCertificate(Request $request)
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
                'is_valid' => false,
                'certificate_details' => null,
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
                        $fail('The URL format is invalid.');
                        return;
                    }

                    // Validate domain
                    $parsedUrl = parse_url($resolvedUrl);
                    if (!$parsedUrl || !isset($parsedUrl['host']) || !filter_var($parsedUrl['host'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                        $fail('The URL must contain a valid domain.');
                        return;
                    }
                },
            ],
        ], [
            'url.required' => 'The URL is required.',
            'url.url' => 'The URL must be a valid URL.',
        ]);

        // Collect valid domains from the request
        $resolvedUrl = preg_match('/^https?:\/\//i', $request->input('url')) ? $request->input('url') : 'https://' . ltrim($request->input('url'), '/');
        $parsedUrl = parse_url($resolvedUrl);
        $host = $parsedUrl['host'] ?? '';
        $validDomains = [];
        if ($host && (dns_get_record($host, DNS_A) || dns_get_record($host, DNS_CNAME))) {
            $validDomains[] = $host;
        }

        // Return validation errors
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            if (isset($errors['url']) && $host && !dns_get_record($host, DNS_A) && !dns_get_record($host, DNS_CNAME)) {
                $suggestions = [];
                foreach ($validDomains as $validDomain) {
                    $distance = levenshtein(strtolower($host), strtolower($validDomain));
                    if ($distance <= 5 && $distance > 0) {
                        $suggestions[] = 'Did you mean https://' . $validDomain . '?';
                    }
                }
                $errors['url'] = array_merge($errors['url'], ['The domain does not exist or is not reachable for ' . $resolvedUrl], $suggestions);
                Log::error('DNS resolution failed for URL', ['url' => $resolvedUrl, 'host' => $host]);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'URL validation failed.',
                'is_valid' => false,
                'errors' => $errors,
                'certificate_details' => null,
            ], 400);
        }

        // Proceed with SSL validation
        try {
            $url = $resolvedUrl;
            $context = stream_context_create([
                'ssl' => [
                    'capture_peer_cert' => true,
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                    'cafile' => $caCertPath,
                ],
            ]);

            $client = stream_socket_client('ssl://' . $host . ':443', $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
            if (!$client) {
                Log::error('SSL connection failed', ['url' => $url, 'error' => $errstr]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to connect to the server.',
                    'is_valid' => false,
                    'errors' => [$errstr],
                    'certificate_details' => null,
                ], 400);
            }

            $params = stream_context_get_params($client);
            $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
            fclose($client);

            // Extract certificate details
            $certificateDetails = [
                'subject' => $cert['subject'] ?? [],
                'issuer' => $cert['issuer'] ?? [],
                'valid_from' => isset($cert['validFrom_time_t']) ? date('Y-m-d H:i:s', $cert['validFrom_time_t']) : null,
                'valid_to' => isset($cert['validTo_time_t']) ? date('Y-m-d H:i:s', $cert['validTo_time_t']) : null,
                'serial_number' => $cert['serialNumber'] ?? null,
            ];

            // Check validity
            $isValid = time() >= $cert['validFrom_time_t'] && time() <= $cert['validTo_time_t'];

            Log::info('SSL certificate validation completed', ['url' => $url, 'is_valid' => $isValid]);

            return response()->json([
                'status' => 'success',
                'url' => $url,
                'is_valid' => $isValid,
                'certificate_details' => $certificateDetails,
                'errors' => [],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Unexpected error during SSL validation', ['url' => $url, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while validating the SSL certificate.',
                'is_valid' => false,
                'errors' => [$e->getMessage()],
                'certificate_details' => null,
            ], 500);
        }
    }
}
