<?php

namespace App\Http\Controllers\API\SEO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Pdp\Rules;
use Pdp\Domain;
use SimpleXMLElement;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Cache;

class CheckKeywordDensityController extends Controller
{
    public function checkKeywordDensity(Request $request)
    {
        // Path to CA certificate bundle
        $caCertPath = storage_path('app/cacert.pem'); // D:\xampp\htdocs\lpl_seoengine\storage\app\cacert.pem

        // Ensure CA certificate file exists
        if (!file_exists($caCertPath)) {
            Log::error('CA certificate bundle not found', ['path' => $caCertPath]);
            return response()->json([
                'status' => 'error',
                'message' => 'Server configuration error: CA certificate bundle not found.',
                'error' => 'Please download cacert.pem from https://curl.se/ca/cacert.pem and place it at ' . $caCertPath,
            ], 500);
        }

        // Validation rules
        $validator = Validator::make($request->all(), [
            'url' => 'nullable|url',
            'urls' => 'nullable|array',
            'urls.*' => 'url',
            'target_keywords' => 'nullable|array',
            'target_keywords.*' => 'string',
            'language' => 'nullable|string|in:english,spanish,french,german',
        ], [
            'url.url' => 'The base URL format is invalid.',
            'urls.array' => 'The URLs must be an array.',
            'urls.*.url' => 'Each URL must be a valid URL.',
            'target_keywords.array' => 'Target keywords must be an array.',
            'target_keywords.*.string' => 'Each target keyword must be a string.',
            'language.in' => 'The selected language is not supported.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Inputs
        $baseUrl = $request->input('url');
        $inputUrls = $request->input('urls', []);
        $targetKeywords = $request->input('target_keywords', []);
        $language = $request->input('language', 'english');

        // Ensure either base URL or URLs list is provided
        if (!$baseUrl && empty($inputUrls)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Either a base URL or a list of URLs is required.',
            ], 400);
        }

        try {
            // Initialize Guzzle client
            $client = new Client([
                'timeout' => 5,
                'allow_redirects' => true,
                'verify' => $caCertPath,
            ]);

            // Get URLs to analyze
            $urlsToAnalyze = [];
            if ($baseUrl) {
                // Resolve root domain
                $parsedUrl = parse_url($baseUrl);
                $host = $parsedUrl['host'] ?? '';
                try {
                    $publicSuffixList = Rules::fromPath(storage_path('app/public_suffix_list.dat'));
                    $domain = $publicSuffixList->resolve($host);
                    $rootDomain = $domain->registrableDomain()->toString();
                    if (!$rootDomain) {
                        throw new \Exception('Unable to extract registrable domain');
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to parse root domain', ['url' => $baseUrl, 'host' => $host, 'error' => $e->getMessage()]);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Unable to parse domain for ' . $baseUrl . '.',
                        'errors' => ['Domain parsing failed: ' . $e->getMessage()],
                    ], 500);
                }

                $baseUrl = $parsedUrl['scheme'] . '://' . $rootDomain;
                $urlsToAnalyze = $this->getSitemapUrls($client, $baseUrl, $caCertPath);
            } else {
                $urlsToAnalyze = array_slice($inputUrls, 0, 5); // Limit to 5 URLs
            }

            if (empty($urlsToAnalyze)) {
                Log::warning('No valid URLs found for analysis', ['base_url' => $baseUrl]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'No valid URLs found for analysis.',
                ], 400);
            }

            // Analyze keyword density for each URL
            $results = [];
            $requests = array_map(function ($url) {
                return new GuzzleRequest('GET', $url);
            }, $urlsToAnalyze);

            $results['pages'] = [];
            $pool = new Pool($client, $requests, [
                'concurrency' => 5,
                'fulfilled' => function ($response, $index) use (&$results, $urlsToAnalyze, $targetKeywords, $language, $caCertPath) {
                    $url = $urlsToAnalyze[$index];
                    $content = $response->getBody()->getContents();

                    // Cache content
                    $cacheKey = 'keyword_density_content_' . md5($url);
                    Cache::put($cacheKey, $content, 3600);

                    // Parse content
                    $pageResult = $this->analyzePageContent($content, $url, $targetKeywords, $language, $caCertPath);
                    if ($pageResult) {
                        $results['pages'][] = $pageResult;
                    }
                },
                'rejected' => function ($reason, $index) use (&$results, $urlsToAnalyze, $caCertPath) {
                    $url = $urlsToAnalyze[$index];
                    $errorMessage = $reason->hasResponse()
                        ? $reason->getResponse()->getStatusCode() . ' ' . $reason->getResponse()->getReasonPhrase()
                        : 'Failed to fetch: ' . $reason->getMessage();
                    if (strpos($errorMessage, 'cURL error 60') !== false) {
                        $errorMessage .= ' (SSL verification failed. Ensure cacert.pem is valid at ' . $caCertPath . ')';
                    }
                    Log::warning('Failed to fetch page for keyword density', ['url' => $url, 'error' => $errorMessage]);
                    $results['pages'][] = [
                        'url' => $url,
                        'status' => 'error',
                        'error' => $errorMessage,
                        'total_words' => 0,
                        'one_word_phrases' => [],
                        'two_word_phrases' => [],
                        'three_word_phrases' => [],
                        'target_keywords' => [],
                        'warnings' => [],
                    ];
                },
            ]);

            $promise = $pool->promise();
            $promise->wait();

            Log::info('Keyword density analysis completed', [
                'urls' => $urlsToAnalyze,
                'page_count' => count($results['pages']),
                'warnings' => array_column($results['pages'], 'warnings'),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Keyword density analysis completed for up to 5 URLs.',
                'data' => $results,
            ], 200);

        } catch (\Symfony\Component\ErrorHandler\Error\FatalError $e) {
            Log::error('Timeout during keyword density analysis', ['url' => $baseUrl, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Request timed out while processing URLs.',
                'error' => 'Maximum execution time exceeded. Partial results returned.',
                'data' => $results ?? [],
            ], 504);
        } catch (\Exception $e) {
            Log::error('Error during keyword density analysis', ['url' => $baseUrl, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing the request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fetch URLs from sitemap or robots.txt.
     */
    private function getSitemapUrls($client, $baseUrl, $caCertPath)
    {
        $urls = [];
        $sitemapCandidates = [
            rtrim($baseUrl, '/') . '/sitemap.xml',
            rtrim($baseUrl, '/') . '/sitemap_index.xml',
        ];

        // Fetch robots.txt
        $robotsTxtUrl = rtrim($baseUrl, '/') . '/robots.txt';
        try {
            $robotsResponse = $client->get($robotsTxtUrl);
            $robotsTxtContent = $robotsResponse->getBody()->getContents();
            $sitemapCandidates = array_merge($sitemapCandidates, $this->extractSitemaps($robotsTxtContent));
        } catch (RequestException $e) {
            Log::warning('Failed to fetch robots.txt', ['url' => $robotsTxtUrl, 'error' => $e->getMessage()]);
        }

        $sitemapCandidates = array_unique(array_filter($sitemapCandidates, function ($url) {
            return filter_var($url, FILTER_VALIDATE_URL);
        }));
        $sitemapCandidates = array_slice($sitemapCandidates, 0, 5); // Limit to 5 sitemaps

        foreach ($sitemapCandidates as $sitemapUrl) {
            try {
                $cacheKey = 'sitemap_' . md5($sitemapUrl);
                $xmlContent = Cache::remember($cacheKey, 3600, function () use ($client, $sitemapUrl) {
                    $response = $client->get($sitemapUrl);
                    return $response->getStatusCode() === 200 ? $response->getBody()->getContents() : null;
                });

                if (!$xmlContent) {
                    Log::warning('Failed to fetch sitemap', ['sitemap_url' => $sitemapUrl]);
                    continue;
                }

                libxml_use_internal_errors(true);
                $xml = simplexml_load_string($xmlContent);
                if ($xml === false) {
                    Log::error('Invalid sitemap XML', ['sitemap_url' => $sitemapUrl]);
                    continue;
                }

                if ($xml->getName() === 'sitemapindex') {
                    $subSitemaps = array_slice(iterator_to_array($xml->sitemap), 0, 5);
                    foreach ($subSitemaps as $sitemap) {
                        $subSitemapUrl = (string)$sitemap->loc;
                        if (!filter_var($subSitemapUrl, FILTER_VALIDATE_URL)) {
                            continue;
                        }
                        try {
                            $subXmlContent = Cache::remember('sitemap_' . md5($subSitemapUrl), 3600, function () use ($client, $subSitemapUrl) {
                                $subResponse = $client->get($subSitemapUrl);
                                return $subResponse->getStatusCode() === 200 ? $subResponse->getBody()->getContents() : null;
                            });

                            if ($subXmlContent) {
                                $subXml = simplexml_load_string($subXmlContent);
                                if ($subXml && $subXml->getName() === 'urlset') {
                                    foreach (array_slice(iterator_to_array($subXml->url), 0, 5) as $urlEntry) {
                                        $url = (string)$urlEntry->loc;
                                        if (filter_var($url, FILTER_VALIDATE_URL)) {
                                            $urls[] = $url;
                                        }
                                    }
                                }
                            }
                        } catch (RequestException $e) {
                            Log::warning('Failed to fetch sub-sitemap', ['sitemap_url' => $subSitemapUrl, 'error' => $e->getMessage()]);
                        }
                    }
                } elseif ($xml->getName() === 'urlset') {
                    foreach (array_slice(iterator_to_array($xml->url), 0, 5) as $urlEntry) {
                        $url = (string)$urlEntry->loc;
                        if (filter_var($url, FILTER_VALIDATE_URL)) {
                            $urls[] = $url;
                        }
                    }
                }
            } catch (RequestException $e) {
                $errorMessage = $e->hasResponse()
                    ? $e->getResponse()->getStatusCode() . ' ' . $e->getResponse()->getReasonPhrase()
                    : 'Failed to fetch: ' . $e->getMessage();
                Log::warning('Failed to fetch sitemap', ['sitemap_url' => $sitemapUrl, 'error' => $errorMessage]);
            }
        }

        return array_slice(array_unique($urls), 0, 5); // Return up to 5 unique URLs
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
                $sitemapUrl = trim($matches[1]);
                if (filter_var($sitemapUrl, FILTER_VALIDATE_URL)) {
                    $sitemaps[] = $sitemapUrl;
                }
            }
        }
        return $sitemaps;
    }

    /**
     * Analyze page content for keyword density.
     */
    private function analyzePageContent($content, $url, $targetKeywords, $language, $caCertPath)
    {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        @$doc->loadHTML($content);
        libxml_clear_errors();

        $xpath = new DOMXPath($doc);
        $body = $xpath->query('//body')->item(0);
        if (!$body) {
            Log::warning('No body content found', ['url' => $url]);
            return null;
        }

        // Remove scripts, styles, header, footer, and nav
        foreach ($xpath->query('//script | //style | //header | //footer | //nav') as $node) {
            $node->parentNode->removeChild($node);
        }

        $text = $this->getCleanText($body->textContent);
        $words = str_word_count($text, 1);
        $words = array_slice($words, 0, 10000); // Limit to 10,000 words
        $totalWordCount = count($words);

        if ($totalWordCount === 0) {
            Log::warning('No valid words found in content', ['url' => $url]);
            return [
                'url' => $url,
                'status' => 'error',
                'error' => 'No valid words found in content.',
                'total_words' => 0,
                'one_word_phrases' => [],
                'two_word_phrases' => [],
                'three_word_phrases' => [],
                'target_keywords' => [],
                'warnings' => [],
            ];
        }

        // Filter stop words
        $stopWords = $this->getStopWords($language);
        $words = array_filter($words, function ($word) use ($stopWords) {
            return !in_array(strtolower($word), $stopWords);
        });

        // Calculate n-grams
        $oneWordFreq = $this->calculateNGrams($words, 1);
        $twoWordFreq = $this->calculateNGrams($words, 2);
        $threeWordFreq = $this->calculateNGrams($words, 3);

        // Prepare results
        $pageResult = [
            'url' => $url,
            'status' => 'success',
            'total_words' => $totalWordCount,
            'one_word_phrases' => $this->formatKeywordResults($oneWordFreq, $totalWordCount),
            'two_word_phrases' => $this->formatKeywordResults($twoWordFreq, $totalWordCount),
            'three_word_phrases' => $this->formatKeywordResults($threeWordFreq, $totalWordCount),
            'target_keywords' => [],
            'warnings' => [],
        ];

        // Analyze target keywords
        foreach ($targetKeywords as $keyword) {
            $keyword = strtolower(trim($keyword));
            $count = $this->countKeywordOccurrences($text, $keyword);
            $density = $totalWordCount > 0 ? ($count / $totalWordCount) * 100 : 0;
            $pageResult['target_keywords'][] = [
                'keyword' => $keyword,
                'count' => $count,
                'density' => round($density, 2),
            ];
            if ($density > 3) {
                $pageResult['warnings'][] = "Keyword '$keyword' has high density ({$density}%) and may be considered keyword stuffing.";
            }
        }

        // Check for keyword stuffing
        foreach (array_merge($pageResult['one_word_phrases'], $pageResult['two_word_phrases'], $pageResult['three_word_phrases']) as $phrase) {
            if ($phrase['density'] > 3) {
                $pageResult['warnings'][] = "Phrase '{$phrase['phrase']}' has high density ({$phrase['density']}%) and may be considered keyword stuffing.";
            }
        }

        return $pageResult;
    }

    /**
     * Clean text by removing extra whitespace and non-text characters.
     */
    private function getCleanText($text)
    {
        $text = preg_replace('/[\r\n\t]+/', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    /**
     * Load stop words for the specified language.
     */
    private function getStopWords($language)
    {
        $englishStopWords = [
            'a', 'an', 'and', 'are', 'as', 'at', 'be', 'by', 'for', 'from', 'has', 'he',
            'in', 'is', 'it', 'its', 'of', 'on', 'that', 'the', 'to', 'was', 'were',
            'will', 'with', 'you', 'your',
        ];

        $stopWords = [
            'english' => $englishStopWords,
            'spanish' => ['y', 'o', 'el', 'la', 'los', 'las', 'un', 'una', 'es', 'en'],
            'french' => ['et', 'ou', 'le', 'la', 'les', 'un', 'une', 'est', 'en'],
            'german' => ['und', 'oder', 'der', 'die', 'das', 'ein', 'eine', 'ist', 'in'],
        ];

        return $stopWords[$language] ?? $englishStopWords;
    }

    /**
     * Calculate n-grams (1, 2, or 3 words) and their frequencies.
     */
    private function calculateNGrams($words, $n)
    {
        $ngrams = [];
        $wordCount = count($words);

        for ($i = 0; $i <= $wordCount - $n; $i++) {
            $ngram = implode(' ', array_slice($words, $i, $n));
            $ngrams[$ngram] = ($ngrams[$ngram] ?? 0) + 1;
        }

        arsort($ngrams);
        return array_slice($ngrams, 0, 50); // Limit to top 50 n-grams
    }

    /**
     * Format n-gram results with density.
     */
    private function formatKeywordResults($ngrams, $totalWordCount)
    {
        $results = [];
        foreach ($ngrams as $phrase => $count) {
            $density = $totalWordCount > 0 ? ($count / $totalWordCount) * 100 : 0;
            $results[] = [
                'phrase' => $phrase,
                'count' => $count,
                'density' => round($density, 2),
            ];
        }
        return $results;
    }

    /**
     * Count occurrences of a specific keyword in text (case-insensitive).
     */
    private function countKeywordOccurrences($text, $keyword)
    {
        $text = strtolower($text);
        $keyword = strtolower($keyword);
        return substr_count($text, $keyword);
    }
}

?>
