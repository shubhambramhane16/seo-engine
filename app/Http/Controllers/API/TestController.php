<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\AuthController;

use Illuminate\Support\Facades\Validator;

class TestController extends Controller
{

    public function generateSignature($request, $queryParams = [])
    {

        // dd($request);
        $secretKey = env('secretKey');

        $authController = new AuthController();

        $token = $authController->loginbyGuest($request);

        $token = json_decode($token, true);

        $xAuthToken = $token['data']['token'];
        // dd($xAuthToken);

        $nonce = rand(100000, 999999);

        // $queryParams = $request->query->all();


        ksort($queryParams);
        $reqQueryParams = json_encode($queryParams);

        $reqBody = $request->getContent();

        $apiData = $xAuthToken . $secretKey . $nonce . $reqBody . $reqQueryParams;

        $md5Data = md5($apiData);


        return [
            'x-signature' => $md5Data,
            'x-nonce' => $nonce,
            'x-auth-token' => $xAuthToken
        ];
    }


    public function getTestbyItemId(Request $request, $city_id, $slug_name)
    {
        //dd('testtttt');
        try {
            $queryParams = [
                'city_id' => strval($city_id),
                'slug_name' => strval($slug_name)
            ];

            $preLoginScript = $this->generateSignature($request, $queryParams);
            //dd($preLoginScript);

            if (isset($preLoginScript['x-signature']) && !empty($preLoginScript['x-signature']) && isset($preLoginScript['x-nonce']) && !empty($preLoginScript['x-signature'])) {
                //dd($preLoginScript);
                //return response()->json(['status' => 'error', 'message' => 'Pre login script error']);




                $xAuthToken = $preLoginScript['x-auth-token'];

                $xSignature = $preLoginScript['x-signature'];
                $xNonce = $preLoginScript['x-nonce'];
                // dd($xAuthToken , $xSignature , $xNonce);

                //echo " Nonce:"; print_r($xNonce);

                $xContentType = env('CONTENT_TYPE');
                $xRequestedWith = env('X_REQUESTED_WITH');
                $xSource = env('X_SOURCE');
                $xAppId = env('X_APP_ID');
                $xAuthority = env('X_AUTHORITY');
                $xTimezone = env('X_TIMEZONE');
                $xAppVersion = env('X_APP_VERSION');
                $xDeviceType = env('X_DEVICE_TYPE');
                $acceptLanguage = env('ACCEPT_LANGUAGE');
                $secChUaMobile = env('SEC_CH_UA_MOBILE');
                $userAgent = env('USER_AGENT');
                $accept = env('ACCEPT');
                $xLanguage = env('X_LANGUAGE');
                $dataAreaId = env('DATA_AREA_ID');
                $origin = env('ORIGIN');
                $secFetchSite = env('SEC_FETCH_SITE');
                $secFetchMode = env('SEC_FETCH_MODE');
                $secFetchDest = env('SEC_FETCH_DEST');
                $xSignatureVersion = env('X_SIGNATURE_VERSION');


                $headers = array(
                    'authority:' . $xAuthority,
                    'x-app-id:' . $xAppId,
                    'x-timezone: ' . $xTimezone,
                    'x-app-version:' . $xAppVersion,
                    'x-device-type: ' . $xDeviceType,
                    'accept-language:' . $acceptLanguage,
                    'sec-ch-ua-mobile: ' . $secChUaMobile,
                    'user-agent: ' . $userAgent,
                    'x-source: ' . $xSource,
                    'Accept: ' . $accept,
                    'x-auth-token: ' . $xAuthToken,
                    'x-language: ' . $xLanguage,
                    'data_area_id: ' . $dataAreaId,
                    'origin: ' . $origin,
                    'sec-fetch-site: ' . $secFetchSite,
                    'sec-fetch-mode: ' . $secFetchMode,
                    'sec-fetch-dest:' . $secFetchDest,
                    'x-signature-version:' . $xSignatureVersion,
                    'x-signature: ' . $xSignature,
                    'x-nonce: ' . $xNonce,
                );

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    // CURLOPT_URL => 'https://uat1xviewmaster.drlallab.com/v1/test/city?city_id='. $queryParams['city_id'] .'&slug_name='. $queryParams['slug_name'],
                    CURLOPT_URL => env('XCUBE_BASE_URL') . '/v1/test/city?city_id=' . $queryParams['city_id'] . '&slug_name=' . $queryParams['slug_name'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => $headers,
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                if (curl_errno($curl)) {
                    $error_msg = curl_error($curl);
                    dd($error_msg);
                }
                //dd($response);
                return $response;
            } else {
                dd('Error');
            }
        } catch (\Exception $e) {
            //echo '<pre>'; print_r($e->message()); die;
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function globalSearch(Request $request, $city_id, $search_string)
    {

        try {
            $queryParams = [
                'city_id' => strval($city_id),
                'search_string' => strval($search_string)
            ];
            // dd($queryParams);
            $preLoginScript = $this->generateSignature($request, $queryParams);

            //dd($preLoginScript);
            if ($preLoginScript['x-signature'] == '' || $preLoginScript['x-nonce'] == '') {
                return response()->json(['status' => 'error', 'message' => 'Pre login script error']);
            }
            if ($preLoginScript['x-auth-token'] == '' || $preLoginScript['x-auth-token'] == null) {
                return response()->json(['status' => 'error', 'message' => 'x-auth-token is required']);
            }


            $xAuthToken = $preLoginScript['x-auth-token'];

            $xSignature = $preLoginScript['x-signature'];
            $xNonce = $preLoginScript['x-nonce'];
            // dd($xAuthToken , $xSignature , $xNonce);


            $xContentType = env('CONTENT_TYPE');
            $xRequestedWith = env('X_REQUESTED_WITH');
            $xSource = env('X_SOURCE');
            $xAppId = env('X_APP_ID');
            $xAuthority = env('X_AUTHORITY');
            $xTimezone = env('X_TIMEZONE');
            $xAppVersion = env('X_APP_VERSION');
            $xDeviceType = env('X_DEVICE_TYPE');
            $acceptLanguage = env('ACCEPT_LANGUAGE');
            $secChUaMobile = env('SEC_CH_UA_MOBILE');
            $userAgent = env('USER_AGENT');
            $accept = env('ACCEPT');
            $xLanguage = env('X_LANGUAGE');
            $dataAreaId = env('DATA_AREA_ID');
            $origin = env('ORIGIN');
            $secFetchSite = env('SEC_FETCH_SITE');
            $secFetchMode = env('SEC_FETCH_MODE');
            $secFetchDest = env('SEC_FETCH_DEST');
            $xSignatureVersion = env('X_SIGNATURE_VERSION');

            $curl = curl_init();

            curl_setopt_array($curl, array(
                // CURLOPT_URL => 'https://uat1xviewmaster.drlallab.com/v1/test/global-search?city_id='. $queryParams['city_id'] .'&search_string='. $queryParams['search_string'],
                CURLOPT_URL => env('XCUBE_BASE_URL') . '/v1/test/global-search?city_id=' . $queryParams['city_id'] . '&search_string=' . $queryParams['search_string'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'authority:' . $xAuthority,
                    'x-app-id:' . $xAppId,
                    'x-timezone: ' . $xTimezone,
                    'x-app-version: ' . $xAppVersion,
                    'x-device-type: ' . $xDeviceType,
                    'accept-language: ' . $acceptLanguage,
                    'sec-ch-ua-mobile: ' . $secChUaMobile,
                    'user-agent: ' . $userAgent,
                    'x-source: ' . $xSource,
                    'Accept: ' . $accept,
                    'x-auth-token: ' . $xAuthToken,
                    'x-language: ' . $xLanguage,
                    'data_area_id: ' . $dataAreaId,
                    'origin: ' . $origin,
                    'sec-fetch-site: ' . $secFetchSite,
                    'sec-fetch-mode: ' . $secFetchMode,
                    'sec-fetch-dest: ' . $secFetchDest,
                    'x-signature-version: ' . $xSignatureVersion,
                    'x-signature: ' . $xSignature,
                    'x-nonce: ' . $xNonce,
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            return $response;
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function packageList(Request $request, $city_id, $package)
    {
        try {
            $queryParams = [
                'city_id' => strval($city_id),
                'package' => strval($package)
            ];
            // dd($queryParams);
            $preLoginScript = $this->generateSignature($request, $queryParams);


            if ($preLoginScript['x-signature'] == '' || $preLoginScript['x-nonce'] == '') {
                return response()->json(['status' => 'error', 'message' => 'Pre login script error']);
            }
            if ($preLoginScript['x-auth-token'] == '' || $preLoginScript['x-auth-token'] == null) {
                return response()->json(['status' => 'error', 'message' => 'x-auth-token is required']);
            }


            $xAuthToken = $preLoginScript['x-auth-token'];

            $xSignature = $preLoginScript['x-signature'];
            $xNonce = $preLoginScript['x-nonce'];
            // dd($xAuthToken , $xSignature , $xNonce);

            $xContentType = env('CONTENT_TYPE');
            $xRequestedWith = env('X_REQUESTED_WITH');
            $xSource = env('X_SOURCE');
            $xAppId = env('X_APP_ID');
            $xAuthority = env('X_AUTHORITY');
            $xTimezone = env('X_TIMEZONE');
            $xAppVersion = env('X_APP_VERSION');
            $xDeviceType = env('X_DEVICE_TYPE');
            $acceptLanguage = env('ACCEPT_LANGUAGE');
            $secChUaMobile = env('SEC_CH_UA_MOBILE');
            $userAgent = env('USER_AGENT');
            $accept = env('ACCEPT');
            $xLanguage = env('X_LANGUAGE');
            $dataAreaId = env('DATA_AREA_ID');
            $origin = env('ORIGIN');
            $secFetchSite = env('SEC_FETCH_SITE');
            $secFetchMode = env('SEC_FETCH_MODE');
            $secFetchDest = env('SEC_FETCH_DEST');
            $xSignatureVersion = env('X_SIGNATURE_VERSION');

            $curl = curl_init();

            curl_setopt_array($curl, array(
                // CURLOPT_URL => 'https://uat1xviewmaster.drlallab.com/v1/test/city?city_id='. $queryParams['city_id'] .'&package='. $queryParams['package'],
                CURLOPT_URL => env('XCUBE_BASE_URL') . '/v1/test/city?city_id=' . $queryParams['city_id'] . '&package=' . $queryParams['package'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'authority: ' . $xAuthority,
                    'x-app-id: ' . $xAppId,
                    'x-timezone: ' . $xTimezone,
                    'x-app-version: ' . $xAppVersion,
                    'x-device-type: ' . $xDeviceType,
                    'accept-language: ' . $acceptLanguage,
                    'sec-ch-ua-mobile: ' . $secChUaMobile,
                    'user-agent: ' . $userAgent,
                    'x-source: ' . $xSource,
                    'Accept: ' . $accept,
                    'x-auth-token: ' . $xAuthToken,
                    'x-language: ' . $xLanguage,
                    'data_area_id: ' . $dataAreaId,
                    'origin: ' . $origin,
                    'sec-fetch-site: ' . $secFetchSite,
                    'sec-fetch-mode: ' . $secFetchMode,
                    'sec-fetch-dest: ' . $secFetchDest,
                    'x-signature-version: ' . $xSignatureVersion,
                    'x-signature: ' . $xSignature,
                    'x-nonce: ' . $xNonce,
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            return $response;
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }




    public function getTestbyCategory(Request $request, $city_id, $test_category)
    {
        try {
            // dd($city_id , $test_category);
            $queryParams = [
                'city_id' => strval($city_id),
                'test_category' => strval($test_category)
            ];
            // dd($queryParams);

            $preLoginScript = $this->generateSignature($request, $queryParams);


            if ($preLoginScript['x-signature'] == '' || $preLoginScript['x-nonce'] == '') {
                return response()->json(['status' => 'error', 'message' => 'Pre login script error']);
            }
            if ($preLoginScript['x-auth-token'] == '' || $preLoginScript['x-auth-token'] == null) {
                return response()->json(['status' => 'error', 'message' => 'x-auth-token is required']);
            }


            $xAuthToken = $preLoginScript['x-auth-token'];

            $xSignature = $preLoginScript['x-signature'];
            $xNonce = $preLoginScript['x-nonce'];
            // dd($xAuthToken , $xSignature , $xNonce);

            $xContentType = env('CONTENT_TYPE');
            $xRequestedWith = env('X_REQUESTED_WITH');
            $xSource = env('X_SOURCE');
            $xAppId = env('X_APP_ID');
            $xAuthority = env('X_AUTHORITY');
            $xTimezone = env('X_TIMEZONE');
            $xAppVersion = env('X_APP_VERSION');
            $xDeviceType = env('X_DEVICE_TYPE');
            $acceptLanguage = env('ACCEPT_LANGUAGE');
            $secChUaMobile = env('SEC_CH_UA_MOBILE');
            $userAgent = env('USER_AGENT');
            $accept = env('ACCEPT');
            $xLanguage = env('X_LANGUAGE');
            $dataAreaId = env('DATA_AREA_ID');
            $origin = env('ORIGIN');
            $secFetchSite = env('SEC_FETCH_SITE');
            $secFetchMode = env('SEC_FETCH_MODE');
            $secFetchDest = env('SEC_FETCH_DEST');
            $xSignatureVersion = env('X_SIGNATURE_VERSION');

            $curl = curl_init();

            curl_setopt_array($curl, array(
                // CURLOPT_URL => 'https://uat1xviewmaster.drlallab.com/v1/test/city?city_id='. $queryParams['city_id'] .'&test_category='. $queryParams['test_category'],
                CURLOPT_URL => env('XCUBE_BASE_URL') . '/v1/test/city?city_id=' . $queryParams['city_id'] . '&test_category=' . $queryParams['test_category'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'authority: ' . $xAuthority,
                    'x-app-id: ' . $xAppId,
                    'x-timezone: ' . $xTimezone,
                    'x-app-version: ' . $xAppVersion,
                    'x-device-type: ' . $xDeviceType,
                    'accept-language: ' . $acceptLanguage,
                    'sec-ch-ua-mobile: ' . $secChUaMobile,
                    'user-agent: ' . $userAgent,
                    'x-source: ' . $xSource,
                    'Accept: ' . $accept,
                    'x-auth-token: ' . $xAuthToken,
                    'x-language:' . $xLanguage,
                    'data_area_id: ' . $dataAreaId,
                    'origin: ' . $origin,
                    'sec-fetch-site: ' . $secFetchSite,
                    'sec-fetch-mode: ' . $secFetchMode,
                    'sec-fetch-dest: ' . $secFetchDest,
                    'x-signature-version: ' . $xSignatureVersion,
                    'x-signature: ' . $xSignature,
                    'x-nonce: ' . $xNonce,
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            return $response;
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getTestbyCityId(Request $request, $city_id, $page)
    {
        //var_dump(extension_loaded('gd'));
        try {
            $queryParams = [
                'city_id' => strval($city_id),
                'page' => strval($page)
            ];

            $preLoginScript = $this->generateSignature($request, $queryParams);

            if ($preLoginScript['x-signature'] == '' || $preLoginScript['x-nonce'] == '') {
                return response()->json(['status' => 'error', 'message' => 'Pre login script error']);
            }

            if ($preLoginScript['x-auth-token'] == '' || $preLoginScript['x-auth-token'] == null) {
                return response()->json(['status' => 'error', 'message' => 'x-auth-token is required']);
            }

            $xSignature = $preLoginScript['x-signature'];
            $xNonce = $preLoginScript['x-nonce'];
            $xAuthToken = $preLoginScript['x-auth-token'];


            $xContentType = env('CONTENT_TYPE');
            $xRequestedWith = env('X_REQUESTED_WITH');
            $xSource = env('X_SOURCE');
            $xAppId = env('X_APP_ID');
            $xAuthority = env('X_AUTHORITY');
            $xTimezone = env('X_TIMEZONE');
            $xAppVersion = env('X_APP_VERSION');
            $xDeviceType = env('X_DEVICE_TYPE');
            $acceptLanguage = env('ACCEPT_LANGUAGE');
            $secChUaMobile = env('SEC_CH_UA_MOBILE');
            $userAgent = env('USER_AGENT');
            $accept = env('ACCEPT');
            $xLanguage = env('X_LANGUAGE');
            $dataAreaId = env('DATA_AREA_ID');
            $origin = env('ORIGIN');
            $secFetchSite = env('SEC_FETCH_SITE');
            $secFetchMode = env('SEC_FETCH_MODE');
            $secFetchDest = env('SEC_FETCH_DEST');
            $xSignatureVersion = env('X_SIGNATURE_VERSION');
            //print_r();
            //var_dump(extension_loaded('curl'));
            //dd();
            $curl = curl_init();

            curl_setopt_array($curl, array(
                // CURLOPT_URL => 'https://uat1xviewmaster.drlallab.com/v1/test/city?city_id='. $queryParams['city_id'] .'&page='. $queryParams['page'],
                //CURLOPT_URL => env('XCUBE_BASE_URL').'/v1/test/city?city_id='. $queryParams['city_id'] .'&page='. $queryParams['page'],
                CURLOPT_URL => 'https://1xviewapimaster.lalpathlabs.com/v1/test/city?city_id=' . $queryParams['city_id'] . '&page=' . $queryParams['page'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTPHEADER => array(
                    'authority: ' . $xAuthority,
                    'x-app-id: ' . $xAppId,
                    'x-timezone: ' . $xTimezone,
                    'x-app-version: ' . $xAppVersion,
                    'x-device-type: ' . $xDeviceType,
                    'accept-language: ' . $acceptLanguage,
                    'sec-ch-ua-mobile: ' . $secChUaMobile,
                    'user-agent: ' . $userAgent,
                    'x-source: ' . $xSource,
                    'Accept: ' . $accept,
                    'x-auth-token: ' . $xAuthToken,
                    'x-language: ' . $xLanguage,
                    'data_area_id: ' . $dataAreaId,
                    'origin: ' . $origin,
                    'sec-fetch-site: ' . $secFetchSite,
                    'sec-fetch-mode: ' . $secFetchMode,
                    'sec-fetch-dest: ' . $secFetchDest,
                    'x-signature-version: ' . $xSignatureVersion,
                    'x-signature: ' . $xSignature,
                    'x-nonce: ' . $xNonce,
                ),
            ));
            //echo "<pre>"; print_r($curl);
            $response = curl_exec($curl);
            //echo "<pre>"; print_r($response); dd($response);
            curl_close($curl);
            return $response;
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getRelatedTestPackage(Request $request, $city_id)
    {
        try {
            $queryParams = [
                'city_id' => strval($city_id),
                'item_id' => 'B001,Z021,Z318,B131'
            ];
            // dd($queryParams);
            $preLoginScript = $this->generateSignature($request, $queryParams);


            if ($preLoginScript['x-signature'] == '' || $preLoginScript['x-nonce'] == '') {
                return response()->json(['status' => 'error', 'message' => 'Pre login script error']);
            }

            if ($preLoginScript['x-auth-token'] == '' || $preLoginScript['x-auth-token'] == null) {
                return response()->json(['status' => 'error', 'message' => 'x-auth-token is required']);
            }

            $xAuthToken = $preLoginScript['x-auth-token'];

            $xSignature = $preLoginScript['x-signature'];

            $xNonce = $preLoginScript['x-nonce'];

            $xContentType = env('CONTENT_TYPE');
            $xRequestedWith = env('X_REQUESTED_WITH');
            $xSource = env('X_SOURCE');
            $xAppId = env('X_APP_ID');
            $xAuthority = env('X_AUTHORITY');
            $xTimezone = env('X_TIMEZONE');
            $xAppVersion = env('X_APP_VERSION');
            $xDeviceType = env('X_DEVICE_TYPE');
            $acceptLanguage = env('ACCEPT_LANGUAGE');
            $secChUaMobile = env('SEC_CH_UA_MOBILE');
            $userAgent = env('USER_AGENT');
            $accept = env('ACCEPT');
            $xLanguage = env('X_LANGUAGE');
            $dataAreaId = env('DATA_AREA_ID');
            $origin = env('ORIGIN');
            $secFetchSite = env('SEC_FETCH_SITE');
            $secFetchMode = env('SEC_FETCH_MODE');
            $secFetchDest = env('SEC_FETCH_DEST');
            $xSignatureVersion = env('X_SIGNATURE_VERSION');



            $curl = curl_init();

            curl_setopt_array($curl, array(
                // CURLOPT_URL => 'https://uat1xviewmaster.drlallab.com/v1/test/city?city_id='. $queryParams['city_id'] .'&item_id='. $queryParams['item_id'],
                CURLOPT_URL => env('XCUBE_BASE_URL') . '/v1/test/city?city_id=' . $queryParams['city_id'] . '&item_id=' . $queryParams['item_id'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTPHEADER => array(
                    'authority:' . $xAuthority,
                    'x-app-id: ' . $xAppId,
                    'x-timezone: ' . $xTimezone,
                    'x-app-version: ' . $xAppVersion,
                    'x-device-type: ' . $xDeviceType,
                    'accept-language: ' . $acceptLanguage,
                    'sec-ch-ua-mobile: ' . $secChUaMobile,
                    'user-agent: ' . $userAgent,
                    'x-source: ' . $xSource,
                    'Accept: ' . $accept,
                    'x-auth-token: ' . $xAuthToken,
                    'x-language: ' . $xLanguage,
                    'data_area_id: ' . $dataAreaId,
                    'origin: ' . $origin,
                    'sec-fetch-site: ' . $secFetchSite,
                    'sec-fetch-mode: ' . $secFetchMode,
                    'sec-fetch-dest: ' . $secFetchDest,
                    'x-signature-version: ' . $xSignatureVersion,
                    'x-signature: ' . $xSignature,
                    'x-nonce: ' . $xNonce,
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            return $response;
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
