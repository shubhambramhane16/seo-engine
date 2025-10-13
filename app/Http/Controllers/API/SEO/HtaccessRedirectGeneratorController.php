<?php

namespace App\Http\Controllers\API\SEO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class HtaccessRedirectGeneratorController extends Controller
{
    public function generateRedirect(Request $request)
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
            ], 500);
        }

        // Custom validation
        $validator = Validator::make($request->all(), [
            'domain' => [
                'required',
                function ($attribute, $value, $fail) use ($caCertPath) {
                    // Resolve URL by adding https:// if no scheme
                    $resolvedUrl = $value;
                    if (!preg_match('/^https?:\/\//i', $value)) {
                        $resolvedUrl = 'https://' . ltrim($value, '/');
                    }

                    // Validate URL format
                    if (!filter_var($resolvedUrl, FILTER_VALIDATE_URL)) {
                        $fail('The domain format is invalid.');
                        return;
                    }

                    // Validate domain
                    $parsedUrl = parse_url($resolvedUrl);
                    if (!$parsedUrl || !isset($parsedUrl['host']) || !filter_var($parsedUrl['host'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                        $fail('The domain must be a valid domain.');
                        return;
                    }

                    // Check DNS resolution
                    $host = $parsedUrl['host'];
                    if (!dns_get_record($host, DNS_A) && !dns_get_record($host, DNS_CNAME)) {
                        $fail('The domain is not accessible: The domain does not exist or is not reachable. Please check the spelling or DNS configuration.');
                        return;
                    }

                    // Check if the resolved URL returns 200 OK
                    try {
                        $clientOptions = [
                            'timeout' => 5,
                            'allow_redirects' => true,
                            'verify' => $caCertPath,
                        ];
                        $client = new Client($clientOptions);
                        $response = $client->head($resolvedUrl);
                        if ($response->getStatusCode() !== 200) {
                            $fail('The domain is not accessible (status code: ' . $response->getStatusCode() . ').');
                        }
                    } catch (RequestException $e) {
                        $errorMessage = $e->getMessage();
                        if (strpos($errorMessage, 'cURL error 60') !== false) {
                            $errorMessage .= ' (Ensure cacert.pem is valid and placed at ' . $caCertPath . ')';
                        }
                        $fail('The domain is not accessible: ' . $errorMessage);
                    }
                },
            ],
            'type' => 'required|in:nonwww,www',
        ], [
            'domain.required' => 'The domain is required.',
            'type.required' => 'The redirect type is required.',
            'type.in' => 'The redirect type must be either "nonwww" or "www".',
        ]);

        // Return validation errors
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Resolve domain
        $domain = $request->input('domain');
        if (!preg_match('/^https?:\/\//i', $domain)) {
            $domain = 'https://' . ltrim($domain, '/');
        }
        $parsedUrl = parse_url($domain);
        $host = $parsedUrl['host'];
        $type = $request->input('type');

        try {
            // Generate .htaccess content
            $htaccessContent = $this->generateHtaccess($host, $type);

            // Prepare response
            $responseData = [
                'status' => 'success',
                'domain' => $domain,
                'redirect_type' => $type === 'nonwww' ? 'www to non-www' : 'non-www to www',
                'htaccess' => [
                    'content' => $htaccessContent,
                    'filename' => '.htaccess',
                ],
            ];

            Log::info('Redirect generated successfully', ['domain' => $domain, 'type' => $type]);

            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            Log::error('Error generating .htaccess', ['domain' => $domain, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while generating the .htaccess file.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate .htaccess content for the specified redirect type.
     */
    private function generateHtaccess($host, $type)
    {
        $htaccess = "# Generated .htaccess Redirect\n";
        $htaccess .= "RewriteEngine On\n";

        if ($type === 'nonwww') {
            // Redirect from www to non-www
            $htaccess .= "# Redirect www to non-www\n";
            $htaccess .= "RewriteCond %{HTTP_HOST} ^www\.{$host} [NC]\n";
            $htaccess .= "RewriteRule ^(.*)$ https://{$host}/$1 [L,R=301]\n";
        } else {
            // Redirect from non-www to www
            $htaccess .= "# Redirect non-www to www\n";
            $htaccess .= "RewriteCond %{HTTP_HOST} !^www\. [NC]\n";
            $htaccess .= "RewriteRule ^(.*)$ https://www.{$host}/$1 [L,R=301]\n";
        }

        return $htaccess;
    }
}
