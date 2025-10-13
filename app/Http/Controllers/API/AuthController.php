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
            Log::info('Starting login process', ['request' => $request->all()]);

            $data = [
                'loginType' => 'byMobileNumber',
                'phone' => '8341300634',
                'password' => 'Test@123',
            ];

            Log::info('Preparing pre-login script', ['data' => $data]);
            $preLoginScript = $this->preLoginScript($data);

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

            $headers = [
                'Content-Type: ' . $xContentType,
                'X-Requested-With: ' . $xRequestedWith,
                'x-source: ' . $xSource,
                'x-app-id: ' . $xAppId,
                'x-signature: ' . $xSignature,
                'x-nonce: ' . $xNonce,
            ];

            Log::info('Preparing cURL request for login', [
                'url' => env('XCUBE_LOGIN_BASE_URL') . '/api/auth/patient/login',
                'headers' => $headers,
                'postFields' => json_encode($data)
            ]);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('XCUBE_LOGIN_BASE_URL') . '/api/auth/patient/login',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{"loginType":"byMobileNumber","phone":"' . $data['phone'] . '","password":"' . $data['password'] . '"}',
                CURLOPT_HTTPHEADER => $headers,
            ));

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                Log::error('cURL error in login', ['error' => $error_msg]);
                curl_close($curl);
                return response()->json(['status' => 'error', 'message' => 'cURL error: ' . $error_msg]);
            }

            curl_close($curl);
            Log::info('Login response received', ['response' => $response]);

            return $response;

        } catch (\Exception $e) {
            Log::error('Exception in login', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function preLoginScript($request)
    {
        Log::info('Generating pre-login script', ['request' => $request]);

        $secretKey = env('secretKey');
        $nonce = rand(100000, 999999);
        $md5Data = md5($secretKey . $nonce . json_encode($request));

        $result = [
            'x-signature' => $md5Data,
            'x-nonce' => $nonce,
        ];

        Log::info('Pre-login script generated', ['result' => $result]);
        return $result;
    }

    public function loginbyGuest(Request $request)
    {
        try {
            Log::info('Starting guest login process', ['request' => $request->all()]);

            $data = [
                'loginType' => 'byGuest',
            ];

            Log::info('Preparing pre-login script for guest login', ['data' => $data]);
            $preLoginScript = $this->preLoginScript($data);

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
                'secretKey' => env('secretKey'),
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

            Log::info('Preparing cURL request for guest login', [
                'url' => env('XCUBE_LOGIN_BASE_URL') . '/patient/login/guest',
                'headers' => $headers,
                'postFields' => json_encode($data)
            ]);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('XCUBE_LOGIN_BASE_URL') . '/patient/login/guest',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{"loginType":"byGuest"}',
                CURLOPT_HTTPHEADER => $headers,
            ));

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                Log::error('cURL error in guest login', ['error' => $error_msg]);
                curl_close($curl);
                return response()->json(['status' => 'error', 'message' => 'cURL error: ' . $error_msg]);
            }

            curl_close($curl);
            Log::info('Guest login response received', ['response' => $response]);

            return $response;

        } catch (\Exception $e) {
            Log::error('Exception in guest login', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
