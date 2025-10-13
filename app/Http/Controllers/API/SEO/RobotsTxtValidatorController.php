<?php
namespace App\Http\Controllers\API\SEO;
use DOMDocument;
use DOMXPath;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Validator;
class RobotsTxtValidatorController extends Controller
{
    public function robotsValidator(Request $request)
    {
        // Path to CA certificate bundle (download from https://curl.se/ca/cacert.pem)
        $caCertPath = storage_path('app/cacert.pem'); // Adjust path as needed

        // Ensure CA certificate file exists
        if (!file_exists($caCertPath)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server configuration error: CA certificate bundle not found.',
                'error' => 'Please ensure cacert.pem is available at ' . $caCertPath,
            ], 500);
        }

        // Custom validation
        $validator = Validator::make($request->all(), [
            'url' => [
                'required',
                function ($attribute, $value, $fail) use ($caCertPath) {
                    // Resolve URL by adding https:// if no scheme
                    $resolvedUrl = $value;
                    if (!preg_match('/^https?:\/\//i', $value)) {
                        $resolvedUrl = 'https://' . ltrim($value, '/');
                    }

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

                    // Check if the resolved URL returns 200 OK
                    try {
                        $client = new Client([
                            'timeout' => 5,
                            'allow_redirects' => true,
                            'verify' => $caCertPath, // Use CA bundle for SSL verification
                        ]);
                        $response = $client->head($resolvedUrl);
                        if ($response->getStatusCode() !== 200) {
                            $fail('The URL is not accessible (status code: ' . $response->getStatusCode() . ').');
                        }
                    } catch (RequestException $e) {
                        $errorMessage = $e->getMessage();
                        if (strpos($errorMessage, 'cURL error 60') !== false) {
                            $errorMessage .= ' (This may indicate an issue with the server’s SSL certificate or local CA bundle configuration. Ensure cacert.pem is up-to-date.)';
                        }
                        $fail('The URL is not accessible: ' . $errorMessage);
                    }
                },
            ],
            'user_agent_token' => 'required|string',
            'user_agent_string' => 'nullable|string',
            'live_test' => 'required|boolean',
            'check_resources' => 'required|boolean',
            'robots_txt' => 'nullable|string|required_if:live_test,false',
        ], [
            'url.required' => 'The URL is required.',
            'user_agent_token.required' => 'The user agent token is required.',
            'live_test.required' => 'The live test option is required.',
            'check_resources.required' => 'The check resources option is required.',
            'robots_txt.required_if' => 'The robots.txt content is required when live_test is false.',
        ]);

        // Return validation errors
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Resolve URL
        $url = $request->input('url');
        if (!preg_match('/^https?:\/\//i', $url)) {
            $url = 'https://' . ltrim($url, '/');
        }

        $userAgentToken = $request->input('user_agent_token');
        $userAgentString = $request->input('user_agent_string', 'Unknown/1.0');
        $liveTest = $request->input('live_test');
        $checkResources = $request->input('check_resources');
        $robotsTxtContent = $request->input('robots_txt');

        try {
            // Parse URL
            $parsedUrl = parse_url($url);
            $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
            $robotsTxtUrl = rtrim($baseUrl, '/') . '/robots.txt';
            $urlPath = $parsedUrl['path'] ?? '/';

            // Fetch robots.txt if live_test is true
            $robotsTxtStatus = null;
            if ($liveTest) {
                try {
                    $client = new Client([
                        'timeout' => 5,
                        'allow_redirects' => true,
                        'verify' => $caCertPath, // Use CA bundle for SSL verification
                    ]);
                    $robotsResponse = $client->get($robotsTxtUrl);
                    $robotsTxtContent = $robotsResponse->getBody()->getContents();
                    $robotsTxtStatus = $robotsResponse->getStatusCode() . ' ' . $robotsResponse->getReasonPhrase();
                } catch (RequestException $e) {
                    $robotsTxtContent = '';
                    $robotsTxtStatus = $e->hasResponse()
                        ? $e->getResponse()->getStatusCode() . ' ' . $e->getResponse()->getReasonPhrase()
                        : 'Failed to fetch robots.txt: ' . $e->getMessage();
                    if (strpos($robotsTxtStatus, 'cURL error 60') !== false) {
                        $robotsTxtStatus .= ' (This may indicate an issue with the server’s SSL certificate or local CA bundle configuration. Ensure cacert.pem is up-to-date.)';
                    }
                }
            } else {
                $robotsTxtStatus = 'Provided (not fetched)';
            }

            // Parse robots.txt (handle empty or invalid content)
            $robotsResult = $this->parseRobotsTxt($robotsTxtContent, $urlPath, $userAgentToken);
            $sitemaps = $this->extractSitemaps($robotsTxtContent);

            // Check sitemap statuses with user agent validation
            $sitemapResults = [];
            $client = new Client(['timeout' => 5, 'verify' => $caCertPath]);
            foreach ($sitemaps as $sitemapUrl) {
                // Resolve relative sitemap URLs
                $absoluteSitemapUrl = $this->resolveUrl($baseUrl, $sitemapUrl);
                $sitemapPath = parse_url($absoluteSitemapUrl, PHP_URL_PATH) ?? '/';

                // Check if sitemap is allowed for this user agent
                $sitemapRuleResult = $this->parseRobotsTxt($robotsTxtContent, $sitemapPath, $userAgentToken);

                try {
                    $sitemapResponse = $client->head($absoluteSitemapUrl);
                    $httpStatus = $sitemapResponse->getStatusCode() . ' ' . $sitemapResponse->getReasonPhrase();
                    $isAccessible = true;
                    $isValid = $sitemapResponse->getStatusCode() < 400 && !$sitemapRuleResult['is_blocked'];
                } catch (RequestException $e) {
                    $httpStatus = $e->hasResponse()
                        ? $e->getResponse()->getStatusCode() . ' ' . $e->getResponse()->getReasonPhrase()
                        : 'Failed to fetch: ' . $e->getMessage();
                    if (strpos($httpStatus, 'cURL error 60') !== false) {
                        $httpStatus .= ' (This may indicate an issue with the server’s SSL certificate or local CA bundle configuration. Ensure cacert.pem is up-to-date.)';
                    }
                    $isAccessible = false;
                    $isValid = false;
                }

                $sitemapResults[] = [
                    'url' => $absoluteSitemapUrl,
                    'status' => $httpStatus,
                    'is_accessible' => $isAccessible,
                    'is_valid' => $isValid,
                    'is_allowed' => !$sitemapRuleResult['is_blocked'],
                    'blocking_rule' => $sitemapRuleResult['blocking_rule'],
                    'errors' => $sitemapRuleResult['is_blocked'] ? ['Blocked by robots.txt'] : [],
                ];
            }

            // Prepare response
            $responseData = [
                'user_agent' => $userAgentToken,
                'user_agent_string' => $userAgentString,
                'robots_txt' => [
                    'url' => $liveTest ? $robotsTxtUrl : 'Custom Input',
                    'status' => $robotsTxtStatus,
                    'content' => $robotsTxtContent ?: 'No robots.txt content available.',
                ],
                'url_path' => $urlPath,
                'result' => $robotsResult['is_blocked'] ? 'Disallowed' : 'Allowed',
                'blocking_rule' => $robotsResult['blocking_rule'] ?? null,
                'sitemaps' => $sitemapResults,
            ];

            // Include resource validation if check_resources is true
            if ($checkResources) {
                $responseData['resources'] = $this->checkPageResources($url, $robotsTxtContent, $userAgentToken, $caCertPath);
            }

            return response()->json($responseData, 200);
        } catch (RequestException $e) {
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'cURL error 60') !== false) {
                $errorMessage .= ' (This may indicate an issue with the server’s SSL certificate or local CA bundle configuration. Ensure cacert.pem is up-to-date.)';
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to fetch resources.',
                'error' => $errorMessage,
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing the request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Parse robots.txt and check if the URL is allowed for the user agent.
     */
    private function parseRobotsTxt($robotsTxtContent, $urlPath, $userAgent)
    {
        $lines = explode("\n", $robotsTxtContent ?: '');
        $rules = [];
        $currentUserAgent = null;
        $isBlocked = false;
        $blockingRule = null;

        // Parse robots.txt line by line
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) {
                continue; // Skip empty lines or comments
            }

            // Check for User-agent
            if (preg_match('/^User-agent:\s*(.+)$/i', $line, $matches)) {
                $currentUserAgent = trim($matches[1]);
                $rules[$currentUserAgent] = ['allow' => [], 'disallow' => []];
                continue;
            }

            // Check for Allow/Disallow rules
            if ($currentUserAgent && preg_match('/^(Allow|Disallow):\s*(.+)$/i', $line, $matches)) {
                $type = strtolower($matches[1]);
                $path = trim($matches[2]);
                $rules[$currentUserAgent][$type][] = $path;
            }
        }

        // Check rules for the specified user agent or wildcard (*)
        $applicableUserAgents = [$userAgent, '*'];
        foreach ($applicableUserAgents as $agent) {
            if (isset($rules[$agent])) {
                // Check Disallow rules first
                foreach ($rules[$agent]['disallow'] as $disallowPath) {
                    if ($this->pathMatches($urlPath, $disallowPath)) {
                        $isBlocked = true;
                        $blockingRule = "Disallow: $disallowPath";
                        break;
                    }
                }

                // Check Allow rules (can override Disallow)
                foreach ($rules[$agent]['allow'] as $allowPath) {
                    if ($this->pathMatches($urlPath, $allowPath)) {
                        $isBlocked = false;
                        $blockingRule = "Allow: $allowPath";
                        break;
                    }
                }

                if ($isBlocked) {
                    break; // Stop if a rule is found
                }
            }
        }

        return [
            'is_blocked' => $isBlocked,
            'blocking_rule' => $blockingRule,
        ];
    }

    /**
     * Extract sitemap URLs from robots.txt.
     */
    private function extractSitemaps($robotsTxtContent)
    {
        $sitemaps = [];
        $lines = explode("\n", $robotsTxtContent ?: '');
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/^Sitemap:\s*(.+)$/i', $line, $matches)) {
                $sitemaps[] = trim($matches[1]);
            }
        }
        return $sitemaps;
    }

    /**
     * Check if a path matches a robots.txt rule (supports wildcards).
     */
    private function pathMatches($path, $rule)
    {
        if (empty($rule)) {
            return false; // Empty rule does not block
        }

        // Convert robots.txt rule to regex (handle * and $)
        $rule = preg_quote($rule, '/');
        $rule = str_replace('\*', '.*', $rule);
        if (substr($rule, -1) === '\$') {
            $rule = rtrim($rule, '\$') . '$';
        } else {
            $rule .= '.*';
        }

        return preg_match("/^$rule/", $path);
    }

    /**
     * Fetch and validate page resources (CSS, JS, images).
     */
    private function checkPageResources($url, $robotsTxtContent, $userAgent, $caCertPath)
    {
        $resources = [];
        try {
            $client = new Client([
                'timeout' => 5,
                'allow_redirects' => true,
                'verify' => $caCertPath, // Use CA bundle for SSL verification
            ]);
            $response = $client->get($url);
            $html = $response->getBody()->getContents();

            // Parse HTML using DOMDocument
            $doc = new DOMDocument();
            @$doc->loadHTML($html); // Suppress warnings for malformed HTML
            $xpath = new DOMXPath($doc);

            // Find CSS, JS, and image resources
            $cssLinks = $xpath->query('//link[@rel="stylesheet"]/@href');
            $jsLinks = $xpath->query('//script[@src]/@src');
            $imgLinks = $xpath->query('//img[@src]/@src');

            // Process CSS
            foreach ($cssLinks as $link) {
                $resourceUrl = $this->resolveUrl($url, $link->value);
                $result = $this->parseRobotsTxt($robotsTxtContent, parse_url($resourceUrl, PHP_URL_PATH) ?? '/', $userAgent);
                $resources[] = [
                    'type' => 'css',
                    'url' => $resourceUrl,
                    'is_blocked' => $result['is_blocked'],
                    'blocking_rule' => $result['blocking_rule'],
                ];
            }

            // Process JS
            foreach ($jsLinks as $link) {
                $resourceUrl = $this->resolveUrl($url, $link->value);
                $result = $this->parseRobotsTxt($robotsTxtContent, parse_url($resourceUrl, PHP_URL_PATH) ?? '/', $userAgent);
                $resources[] = [
                    'type' => 'js',
                    'url' => $resourceUrl,
                    'is_blocked' => $result['is_blocked'],
                    'blocking_rule' => $result['blocking_rule'],
                ];
            }

            // Process Images
            foreach ($imgLinks as $link) {
                $resourceUrl = $this->resolveUrl($url, $link->value);
                $result = $this->parseRobotsTxt($robotsTxtContent, parse_url($resourceUrl, PHP_URL_PATH) ?? '/', $userAgent);
                $resources[] = [
                    'type' => 'image',
                    'url' => $resourceUrl,
                    'is_blocked' => $result['is_blocked'],
                    'blocking_rule' => $result['blocking_rule'],
                ];
            }
        } catch (RequestException $e) {
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'cURL error 60') !== false) {
                $errorMessage .= ' (This may indicate an issue with the server’s SSL certificate or local CA bundle configuration. Ensure cacert.pem is up-to-date.)';
            }
            $resources[] = [
                'type' => 'error',
                'message' => 'Failed to fetch or parse page resources: ' . $errorMessage,
            ];
        } catch (\Exception $e) {
            $resources[] = [
                'type' => 'error',
                'message' => 'Failed to fetch or parse page resources: ' . $e->getMessage(),
            ];
        }
        return $resources;
    }

    /**
     * Resolve relative URLs to absolute URLs.
     */
    private function resolveUrl($baseUrl, $relativeUrl)
    {
        $parsedBase = parse_url($baseUrl);
        $baseScheme = $parsedBase['scheme'] ?? 'https';
        $baseHost = $parsedBase['host'];
        $basePath = isset($parsedBase['path']) ? dirname($parsedBase['path']) : '';

        if (parse_url($relativeUrl, PHP_URL_SCHEME) !== null) {
            return $relativeUrl; // Absolute URL
        }
        if (strpos($relativeUrl, '//') === 0) {
            return $baseScheme . ':' . $relativeUrl; // Protocol-relative URL
        }
        if (strpos($relativeUrl, '/') === 0) {
            return $baseScheme . '://' . $baseHost . $relativeUrl; // Root-relative URL
        }
        return $baseScheme . '://' . $baseHost . rtrim($basePath, '/') . '/' . ltrim($relativeUrl, '/');
    }
}
