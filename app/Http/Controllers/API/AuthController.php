<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $startTime = microtime(true);
            Log::info('Starting login process', [
                'request' => $request->all(),
                'timestamp' => now()->toDateTimeString(),
                'ip' => $request->ip()
            ]);

            // Validate request
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                Log::warning('Login validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'request' => $request->all()
                ]);
                return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()]);
            }

            $data = [
                'loginType' => 'byMobileNumber',
                'phone' => '8341300634',
                'password' => 'Test@123',
            ];

            Log::info('Preparing pre-login script', ['data' => $data]);
            $preLoginStartTime = microtime(true);
            $preLoginScript = $this->preLoginScript($data);
            $preLoginEndTime = microtime(true);
            
            Log::info('Pre-login script execution completed', [
                'execution_time_ms' => round(($preLoginEndTime - $preLoginStartTime) * 1000, 2),
                'result' => $preLoginScript
            ]);

            if ($preLoginScript['x-signature'] == '' || $preLoginScript['x-nonce'] == '') {
                Log::error('Pre-login script error: Empty signature or nonce', ['preLoginScript' => $preLoginScript]);
                return response()->json(['status' => 'error', 'message' => 'Pre login script error']);
            }

            $xSignature = $preLoginScript['x-signature'];
            $xNonce = $preLoginScript['x-nonce'];

            $xContentType = env('CONTENT_TYPE');
            $xRequestedWith = env('X_REQUESTED_WITH');
            $xSource = env('X_SOURCE');
            $xAppId = env('X_APP_ID');

            Log::debug('Environment variables being used', [
                'XCUBE_LOGIN_BASE_URL' => env('XCUBE_LOGIN_BASE_URL'),
                'X_APP_ID' => $xAppId,
                'X_SOURCE' => $xSource,
                'CONTENT_TYPE' => $xContentType
            ]);

            $headers = [
                'Content-Type: ' . $xContentType,
                'X-Requested-With: ' . $xRequestedWith,
                'x-source: ' . $xSource,
                'x-app-id: ' . $xAppId,
                'x-signature: ' . $xSignature,
                'x-nonce: ' . $xNonce,
            ];

            $url = env('XCUBE_LOGIN_BASE_URL') . '/api/auth/patient/login';
            $postFields = '{"loginType":"byMobileNumber","phone":"' . $data['phone'] . '","password":"' . $data['password'] . '"}';
            
            Log::info('Preparing cURL request for login', [
                'url' => $url,
                'headers' => $headers,
                'postFields' => $postFields
            ]);

            $curlStartTime = microtime(true);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postFields,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_HEADER => false,
                CURLOPT_FAILONERROR => true,
            ));

            $response = curl_exec($curl);
            $curlEndTime = microtime(true);
            
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $totalTime = curl_getinfo($curl, CURLINFO_TOTAL_TIME);

            Log::info('cURL request completed', [
                'http_code' => $httpCode,
                'total_time' => $totalTime,
                'execution_time_ms' => round(($curlEndTime - $curlStartTime) * 1000, 2)
            ]);

            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                $error_code = curl_errno($curl);
                Log::error('cURL error in login', [
                    'error' => $error_msg,
                    'error_code' => $error_code,
                    'http_code' => $httpCode
                ]);
                curl_close($curl);
                return response()->json(['status' => 'error', 'message' => 'cURL error: ' . $error_msg]);
            }

            curl_close($curl);
            
            // Try to decode JSON response for better logging
            $decodedResponse = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                Log::info('Login response received (JSON)', [
                    'response' => $decodedResponse,
                    'http_code' => $httpCode
                ]);
            } else {
                Log::info('Login response received (Raw)', [
                    'response' => $response,
                    'http_code' => $httpCode,
                    'json_error' => json_last_error_msg()
                ]);
            }

            $endTime = microtime(true);
            Log::info('Login process completed', [
                'total_execution_time_ms' => round(($endTime - $startTime) * 1000, 2)
            ]);

            return $response;

        } catch (\Exception $e) {
            Log::error('Exception in login', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function preLoginScript($request)
    {
        $startTime = microtime(true);
        Log::info('Generating pre-login script', [
            'request' => $request,
            'timestamp' => now()->toDateTimeString()
        ]);

        $secretKey = env('secretKey');
        $nonce = rand(100000, 999999);
        
        Log::debug('Pre-login script components', [
            'secretKey_length' => strlen($secretKey),
            'nonce' => $nonce,
            'request_json' => json_encode($request)
        ]);
        
        $md5Data = md5($secretKey . $nonce . json_encode($request));

        $result = [
            'x-signature' => $md5Data,
            'x-nonce' => $nonce,
        ];

        $endTime = microtime(true);
        Log::info('Pre-login script generated', [
            'result' => $result,
            'execution_time_ms' => round(($endTime - $startTime) * 1000, 2)
        ]);
        return $result;
    }

    public function loginbyGuest(Request $request)
    {
        try {
            $startTime = microtime(true);
            Log::info('Starting guest login process', [
                'request' => $request->all(),
                'timestamp' => now()->toDateTimeString(),
                'ip' => $request->ip()
            ]);

            $data = [
                'loginType' => 'byGuest',
            ];

            Log::info('Preparing pre-login script for guest login', ['data' => $data]);
            $preLoginStartTime = microtime(true);
            $preLoginScript = $this->preLoginScript($data);
            $preLoginEndTime = microtime(true);
            
            Log::info('Pre-login script execution completed for guest login', [
                'execution_time_ms' => round(($preLoginEndTime - $preLoginStartTime) * 1000, 2),
                'result' => $preLoginScript
            ]);

            if ($preLoginScript['x-signature'] == '' || $preLoginScript['x-nonce'] == '') {
                Log::error('Pre-login script error for guest login: Empty signature or nonce', ['preLoginScript' => $preLoginScript]);
                return response()->json(['status' => 'error', 'message' => 'Pre login script error']);
            }

            $xSignature = $preLoginScript['x-signature'];
            $xNonce = $preLoginScript['x-nonce'];

            $xContentType = env('CONTENT_TYPE');
            $xRequestedWith = env('X_REQUESTED_WITH');
            $xSource = env('X_SOURCE');
            $xAppId = env('X_APP_ID');

            Log::info('Environment variables', [
                'secretKey_length' => strlen(env('secretKey')),
                'X_APP_ID' => env('X_APP_ID'),
                'X_SOURCE' => env('X_SOURCE'),
                'XCUBE_LOGIN_BASE_URL' => env('XCUBE_LOGIN_BASE_URL'),
                'ORIGIN' => env('ORIGIN'),
            ]);

            $headers = [
                'accept: application/json, text/plain, */*',
                'accept-language: en',
                'content-type: application/json',
                'origin: ' . env('ORIGIN'),
                'priority: u=1, i',
                'sec-ch-ua: "Chromium";v="124", "Google Chrome";v="124", "Not-A.Brand";v="99"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-site',
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                'x-app-id: ' . env('X_APP_ID'),
                'x-app-version: v1.4.5.2',
                'x-auth-token: ',
                'x-device-type: WEB',
                'x-language: en',
                'x-requested-with: XMLHttpRequest',
                'x-signature-version: v2',
                'x-source: 7',
                'x-timezone: -330',
                'x-signature: ' . $xSignature,
                'x-nonce: ' . $xNonce,
            ];

            $url = env('XCUBE_LOGIN_BASE_URL') . '/patient/login/guest';
            $postFields = '{"loginType":"byGuest"}';
            
            Log::info('Preparing cURL request for guest login', [
                'url' => $url,
                'headers_count' => count($headers),
                'postFields' => $postFields
            ]);

            $curlStartTime = microtime(true);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postFields,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_HEADER => false,
                CURLOPT_FAILONERROR => true,
            ));

            $response = curl_exec($curl);
            $curlEndTime = microtime(true);
            
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $totalTime = curl_getinfo($curl, CURLINFO_TOTAL_TIME);

            Log::info('cURL request completed for guest login', [
                'http_code' => $httpCode,
                'total_time' => $totalTime,
                'execution_time_ms' => round(($curlEndTime - $curlStartTime) * 1000, 2)
            ]);

            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                $error_code = curl_errno($curl);
                Log::error('cURL error in guest login', [
                    'error' => $error_msg,
                    'error_code' => $error_code,
                    'http_code' => $httpCode
                ]);
                curl_close($curl);
                return response()->json(['status' => 'error', 'message' => 'cURL error: ' . $error_msg]);
            }

            curl_close($curl);
            
            // Try to decode JSON response for better logging
            $decodedResponse = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                Log::info('Guest login response received (JSON)', [
                    'response' => $decodedResponse,
                    'http_code' => $httpCode
                ]);
            } else {
                Log::info('Guest login response received (Raw)', [
                    'response' => $response,
                    'http_code' => $httpCode,
                    'json_error' => json_last_error_msg()
                ]);
            }

            $endTime = microtime(true);
            Log::info('Guest login process completed', [
                'total_execution_time_ms' => round(($endTime - $startTime) * 1000, 2)
            ]);

            return $response;

        } catch (\Exception $e) {
            Log::error('Exception in guest login', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}