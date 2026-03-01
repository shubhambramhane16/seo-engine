<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
     function generateSignature($request, $queryParams = [])
    {
        $requestId = uniqid('sig_', true);
        Log::info('=== generateSignature STARTED ===', [
            'request_id' => $requestId,
            'query_params' => $queryParams
        ]);

        $secretKey = config('api.secretKey');
        if (empty($secretKey)) {
            throw new \Exception('secretKey missing in config');
        }

        $cacheKey = 'guest_token6';
        $cachedToken = Cache::get($cacheKey);

        if ($cachedToken && $this->isTokenValid($cachedToken)) {
            Log::info('Using CACHED Guest Token (HIT!)', ['request_id' => $requestId]);
            $xAuthToken = $cachedToken;
        } else {
            Log::warning('Generating FRESH guest token', ['request_id' => $requestId]);

            $auth = new \App\Http\Controllers\API\AuthController();
            $response = $auth->loginbyGuest($request);
            $data = json_decode($response, true);

            if (empty($data['data']['token'])) {
                Log::error('Guest token API failed', ['response' => $response]);
                throw new \Exception('Unable to get guest token');
            }

            $xAuthToken = $data['data']['token'];

            // FIXED: CACHE FOR ~9 MINUTES (Token is 10 min valid)
            $parts = explode('.', $xAuthToken);
            if (count($parts) === 3) {
                $payload = json_decode(base64_decode($parts[1]), true);
                $exp = $payload['exp'] ?? now()->addHour()->timestamp;
                
                // YE LINE SABSE SAHI HAI
                $cacheUntil = now()->addMinutes(9); // Simple, safe, no timezone issue

                Cache::put($cacheKey, $xAuthToken, $cacheUntil);

                Log::info('GUEST TOKEN CACHED SUCCESSFULLY', [
                    'request_id' => $requestId,
                    'expires_at' => $cacheUntil->toDateTimeString(),
                    'cache_key' => $cacheKey,
                    'valid_for_minutes' => 9
                ]);
            }
        }

        $nonce = rand(100000, 999999);
        ksort($queryParams);
        $queryStr = json_encode($queryParams);
        $body = $request->getContent() ?: '';
        $signature = md5($xAuthToken . $secretKey . $nonce . $body . $queryStr);

        Log::info('=== generateSignature SUCCESS ===', [
            'request_id' => $requestId,
            'nonce' => $nonce,
            'signature' => $signature,
            'token_source' => isset($cachedToken) && $this->isTokenValid($cachedToken) ? 'cache' : 'fresh'
        ]);

        return [
            'x-signature' => $signature,
            'x-nonce' => $nonce,
            'x-auth-token' => $xAuthToken
        ];
    }

    private function isTokenValid($token)
    {
        if (empty($token)) return false;
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;
        $payload = json_decode(base64_decode($parts[1] ?? ''), true);
        if (!$payload || empty($payload['exp'])) return false;
        
        // 60 seconds buffer — agar 1 min se kam bacha to fresh bana do
        return $payload['exp'] > (now()->timestamp + 60);
    }

    private function callApi($request, $endpoint, $params = [])
    {
        $startTime = microtime(true);
        $requestId = uniqid('api_');
        
        Log::info('API Request Initiated', [
            'request_id' => $requestId,
            'endpoint' => $endpoint,
            'params' => $params,
            'timestamp' => now()->toDateTimeString()
        ]);

        try {
            $sigStartTime = microtime(true);
            $sig = $this->generateSignature($request, $params);
            $sigEndTime = microtime(true);
            
            Log::debug('Signature generation completed', [
                'request_id' => $requestId,
                'execution_time_ms' => round(($sigEndTime - $sigStartTime) * 1000, 2)
            ]);
            
            $url = config('api.XCUBE_BASE_URL') . $endpoint . '?' . http_build_query($params);

            $headers = [
                'authority: ' . config('api.X_AUTHORITY'),
                'x-app-id: ' . config('api.X_APP_ID'),
                'x-timezone: ' . config('api.X_TIMEZONE'),
                'x-app-version: ' . config('api.X_APP_VERSION'),
                'x-device-type: ' . config('api.X_DEVICE_TYPE'),
                'accept-language: ' . config('api.ACCEPT_LANGUAGE'),
                'sec-ch-ua-mobile: ' . config('api.SEC_CH_UA_MOBILE'),
                'user-agent: ' . config('api.USER_AGENT'),
                'x-source: ' . config('api.X_SOURCE'),
                'Accept: ' . config('api.ACCEPT'),
                'x-auth-token: ' . $sig['x-auth-token'],
                'x-language: ' . config('api.X_LANGUAGE'),
                'data_area_id: ' . config('api.DATA_AREA_ID'),
                'origin: ' . config('api.ORIGIN'),
                'sec-fetch-site: ' . config('api.SEC_FETCH_SITE'),
                'sec-fetch-mode: ' . config('api.SEC_FETCH_MODE'),
                'sec-fetch-dest: ' . config('api.SEC_FETCH_DEST'),
                'x-signature-version: ' . config('api.X_SIGNATURE_VERSION'),
                'x-signature: ' . $sig['x-signature'],
                'x-nonce: ' . $sig['x-nonce'],
            ];

            Log::debug('API Request Details', [
                'request_id' => $requestId,
                'url' => $url,
                'city_id' => $params['city_id'] ?? 'unknown',
                'headers_count' => count($headers),
                'token_preview' => substr($sig['x-auth-token'], 0, 20) . '...'
            ]);

            $curlStartTime = microtime(true);
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_HEADER => false,
                CURLOPT_FAILONERROR => true,
            ]);

            $response = curl_exec($ch);
            $curlEndTime = microtime(true);
            
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
            curl_close($ch);

            Log::info('cURL Request Completed', [
                'request_id' => $requestId,
                'http_code' => $httpCode,
                'execution_time_ms' => round(($curlEndTime - $curlStartTime) * 1000, 2),
                'total_time' => $totalTime,
                'response_size' => strlen($response)
            ]);

            if ($curlError) {
                Log::error('cURL Error', [
                    'request_id' => $requestId,
                    'error' => $curlError,
                    'url' => $url,
                    'http_code' => $httpCode
                ]);
                
                $endTime = microtime(true);
                Log::info('API Request Failed', [
                    'request_id' => $requestId,
                    'failure_reason' => 'curl_error',
                    'total_execution_time_ms' => round(($endTime - $startTime) * 1000, 2)
                ]);
                
                return json_encode([
                    'status' => false,
                    'message' => 'Network error',
                    'data' => null
                ]);
            }

            if ($httpCode >= 400 || stripos($response, '<!DOCTYPE html>') === 0 || stripos($response, '<html') === 0 || stripos($response, 'Server Error') !== false) {
                Log::warning('Lalpath API Error', [
                    'request_id' => $requestId,
                    'city_id' => $params['city_id'] ?? 'unknown',
                    'http_code' => $httpCode,
                    'preview' => substr($response, 0, 300)
                ]);

                $endTime = microtime(true);
                Log::info('API Request Failed', [
                    'request_id' => $requestId,
                    'failure_reason' => 'api_error',
                    'total_execution_time_ms' => round(($endTime - $startTime) * 1000, 2)
                ]);

                return json_encode([
                    'status' => false,
                    'message' => 'City not supported',
                    'data' => [
                        'result' => [],
                        'packagelist' => [],
                        'pagination' => ['total' => 0, 'per_page' => 20]
                    ]
                ]);
            }

            // Try to decode JSON response for better logging
            $decodedResponse = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                Log::info('API Success', [
                    'request_id' => $requestId,
                    'city_id' => $params['city_id'] ?? 'unknown',
                    'response_type' => 'json',
                    'response_data_keys' => array_keys($decodedResponse)
                ]);
            } else {
                Log::info('API Success', [
                    'request_id' => $requestId,
                    'city_id' => $params['city_id'] ?? 'unknown',
                    'response_type' => 'raw',
                    'response_size' => strlen($response)
                ]);
            }

            $endTime = microtime(true);
            Log::info('API Request Completed Successfully', [
                'request_id' => $requestId,
                'total_execution_time_ms' => round(($endTime - $startTime) * 1000, 2)
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error('API Request Exception', [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $endTime = microtime(true);
            Log::info('API Request Failed', [
                'request_id' => $requestId,
                'failure_reason' => 'exception',
                'total_execution_time_ms' => round(($endTime - $startTime) * 1000, 2)
            ]);
            
            return json_encode([
                'status' => false,
                'message' => 'An unexpected error occurred',
                'data' => null
            ]);
        }
    }

    // ========================================
    // 3. PUBLIC METHODS (Same as old code)
    // ========================================

    public function getTestbyItemId(Request $request, $city_id, $slug_name)
    {
        $requestId = uniqid('req_');
        Log::info('getTestbyItemId Called', [
            'request_id' => $requestId,
            'city_id' => $city_id,
            'slug' => $slug_name,
            'timestamp' => now()->toDateTimeString()
        ]);
        
        $startTime = microtime(true);
        $result = $this->callApi($request, '/v1/test/city', [
            'city_id' => (string)$city_id,
            'slug_name' => (string)$slug_name
        ]);
        
        $endTime = microtime(true);
        Log::info('getTestbyItemId Completed', [
            'request_id' => $requestId,
            'execution_time_ms' => round(($endTime - $startTime) * 1000, 2)
        ]);
        
        return $result;
    }

    public function globalSearch(Request $request, $city_id, $search_string)
    {
        $requestId = uniqid('req_');
        Log::info('globalSearch Called', [
            'request_id' => $requestId,
            'city_id' => $city_id,
            'search' => $search_string,
            'timestamp' => now()->toDateTimeString()
        ]);
        
        $startTime = microtime(true);
        $result = $this->callApi($request, '/v1/test/global-search', [
            'city_id' => (string)$city_id,
            'search_string' => (string)$search_string
        ]);
        
        $endTime = microtime(true);
        Log::info('globalSearch Completed', [
            'request_id' => $requestId,
            'execution_time_ms' => round(($endTime - $startTime) * 1000, 2)
        ]);
        
        return $result;
    }

    public function packageList(Request $request, $city_id, $package)
    {
        $requestId = uniqid('req_');
        Log::info('packageList Called', [
            'request_id' => $requestId,
            'city_id' => $city_id,
            'package' => $package,
            'timestamp' => now()->toDateTimeString()
        ]);
        
        $startTime = microtime(true);
        $result = $this->callApi($request, '/v1/test/city', [
            'city_id' => (string)$city_id,
            'package' => (string)$package
        ]);
        
        $endTime = microtime(true);
        Log::info('packageList Completed', [
            'request_id' => $requestId,
            'execution_time_ms' => round(($endTime - $startTime) * 1000, 2)
        ]);
        
        return $result;
    }

    public function getTestbyCategory(Request $request, $city_id, $test_category)
    {
        $requestId = uniqid('req_');
        Log::info('getTestbyCategory Called', [
            'request_id' => $requestId,
            'city_id' => $city_id,
            'category' => $test_category,
            'timestamp' => now()->toDateTimeString()
        ]);
        
        $startTime = microtime(true);
        $result = $this->callApi($request, '/v1/test/city', [
            'city_id' => (string)$city_id,
            'test_category' => (string)$test_category
        ]);
        
        $endTime = microtime(true);
        Log::info('getTestbyCategory Completed', [
            'request_id' => $requestId,
            'execution_time_ms' => round(($endTime - $startTime) * 1000, 2)
        ]);
        
        return $result;
    }

    public function getTestbyCityId(Request $request, $city_id, $page)
    {
        $requestId = uniqid('req_');
        Log::info('getTestbyCityId Called', [
            'request_id' => $requestId,
            'city_id' => $city_id,
            'page' => $page,
            'timestamp' => now()->toDateTimeString()
        ]);
        
        $startTime = microtime(true);
        $result = $this->callApi($request, '/v1/test/city', [
            'city_id' => (string)$city_id,
            'page' => (string)$page
        ]);
        
        $endTime = microtime(true);
        Log::info('getTestbyCityId Completed', [
            'request_id' => $requestId,
            'execution_time_ms' => round(($endTime - $startTime) * 1000, 2)
        ]);
        
        return $result;
    }

    public function getRelatedTestPackage(Request $request, $city_id)
    {
        $requestId = uniqid('req_');
        Log::info('getRelatedTestPackage Called', [
            'request_id' => $requestId,
            'city_id' => $city_id,
            'timestamp' => now()->toDateTimeString()
        ]);
        
        $startTime = microtime(true);
        $result = $this->callApi($request, '/v1/test/city', [
            'city_id' => (string)$city_id,
            'item_id' => 'B001,Z021,Z318,B131'
        ]);
        
        $endTime = microtime(true);
        Log::info('getRelatedTestPackage Completed', [
            'request_id' => $requestId,
            'execution_time_ms' => round(($endTime - $startTime) * 1000, 2)
        ]);
        
        return $result;
    }
}