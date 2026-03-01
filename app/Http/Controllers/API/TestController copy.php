<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    // ========================================
    // 1. GENERATE SIGNATURE (Same as old code)
    // ========================================
    private function generateSignature($request, $queryParams = [])
    {
        Log::info('generateSignature Called', ['params' => $queryParams]);

        $secretKey = config('api.secretKey');
        $cacheKey = 'guest_token';
        $cachedToken = Cache::get($cacheKey);

        // Use cached token if valid
        if ($cachedToken && $this->isTokenValid($cachedToken)) {
            $xAuthToken = $cachedToken;
            Log::info('Using Cached Guest Token');
        } else {
            // Get fresh guest token
            $auth = new AuthController();
            $response = $auth->loginbyGuest($request);
            $data = json_decode($response, true);

            if (empty($data['data']['token'])) {
                Log::error('Guest Token Failed', ['response' => $response]);
                throw new \Exception('Unable to get guest token');
            }

            $xAuthToken = $data['data']['token'];
            $parts = explode('.', $xAuthToken);
            if (count($parts) === 3) {
                $payload = json_decode(base64_decode($parts[1]), true);
                $exp = $payload['exp'] ?? now()->addHour()->timestamp;
                $expiresAt = Carbon::createFromTimestamp($exp - 300); // 5 min buffer
                Cache::put($cacheKey, $xAuthToken, $expiresAt);
                Log::info('Guest Token Cached', ['exp' => $expiresAt]);
            }
        }

        $nonce = rand(100000, 999999);
        ksort($queryParams);
        $queryStr = json_encode($queryParams);
        $body = $request->getContent() ?: '';
        $data = $xAuthToken . $secretKey . $nonce . $body . $queryStr;
        $signature = md5($data);

        Log::info('Signature Generated', ['nonce' => $nonce, 'signature' => $signature]);

        return [
            'x-signature' => $signature,
            'x-nonce' => $nonce,
            'x-auth-token' => $xAuthToken
        ];
    }

    // Check JWT expiry
    private function isTokenValid($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;
        $payload = json_decode(base64_decode($parts[1]), true);
        return ($payload['exp'] ?? 0) > now()->addMinutes(5)->timestamp;
    }

    // ========================================
    // 2. CALL API (Exact same request + safe)
    // ========================================
    private function callApi($request, $endpoint, $params = [])
    {
        $sig = $this->generateSignature($request, $params);
        $url = config('api.XCUBE_BASE_URL') . $endpoint . '?' . http_build_query($params);

        // === EXACT SAME HEADERS AS YOUR OLD CODE ===
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

        Log::info('API Request', ['url' => $url, 'city_id' => $params['city_id'] ?? 'unknown']);

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
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Log cURL error
        if ($curlError) {
            Log::error('cURL Error', ['error' => $curlError, 'url' => $url]);
            return json_encode([
                'status' => false,
                'mesage' => 'Network error',
                'data' => null
            ]);
        }

        // === DETECT HTML 500 ERROR PAGE ===
        if (
            $httpCode >= 400 ||
            stripos($response, '<!DOCTYPE html>') === 0 ||
            stripos($response, '<html') === 0 ||
            stripos($response, 'Server Error') !== false
        ) {
            Log::warning('Lalpath API 500/HTML Error', [
                'city_id' => $params['city_id'] ?? 'unknown',
                'http_code' => $httpCode,
                'preview' => substr($response, 0, 300)
            ]);

            return json_encode([
                'status' => false,
                'mesage' => 'City not supported',
                'data' => [
                    'result' => [],
                    'packagelist' => [],
                    'pagination' => ['total' => 0, 'per_page' => 20]
                ]
            ]);
        }

        Log::info('API Success', ['city_id' => $params['city_id'] ?? 'unknown']);

        return $response; // Valid JSON string
    }

    // ========================================
    // 3. PUBLIC METHODS (Same as old code)
    // ========================================

    public function getTestbyItemId(Request $request, $city_id, $slug_name)
    {
        Log::info('getTestbyItemId', ['city_id' => $city_id, 'slug' => $slug_name]);
        return $this->callApi($request, '/v1/test/city', [
            'city_id' => (string)$city_id,
            'slug_name' => (string)$slug_name
        ]);
    }

    public function globalSearch(Request $request, $city_id, $search_string)
    {
        Log::info('globalSearch', ['city_id' => $city_id, 'search' => $search_string]);
        return $this->callApi($request, '/v1/test/global-search', [
            'city_id' => (string)$city_id,
            'search_string' => (string)$search_string
        ]);
    }

    public function packageList(Request $request, $city_id, $package)
    {
        Log::info('packageList', ['city_id' => $city_id, 'package' => $package]);
        return $this->callApi($request, '/v1/test/city', [
            'city_id' => (string)$city_id,
            'package' => (string)$package
        ]);
    }

    public function getTestbyCategory(Request $request, $city_id, $test_category)
    {
        Log::info('getTestbyCategory', ['city_id' => $city_id, 'category' => $test_category]);
        return $this->callApi($request, '/v1/test/city', [
            'city_id' => (string)$city_id,
            'test_category' => (string)$test_category
        ]);
    }

    public function getTestbyCityId(Request $request, $city_id, $page)
    {
        Log::info('getTestbyCityId', ['city_id' => $city_id, 'page' => $page]);
        return $this->callApi($request, '/v1/test/city', [
            'city_id' => (string)$city_id,
            'page' => (string)$page
        ]);
    }

    public function getRelatedTestPackage(Request $request, $city_id)
    {
        Log::info('getRelatedTestPackage', ['city_id' => $city_id]);
        return $this->callApi($request, '/v1/test/city', [
            'city_id' => (string)$city_id,
            'item_id' => 'B001,Z021,Z318,B131'
        ]);
    }
}
