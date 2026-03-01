<?php

return [
    'add_lead_api_url' => env('ADD_LEAD_API_URL', ''),
    'auth_token' => env('AUTH_TOKEN', ''),
    'client_name' => env('CLIENT_NAME', ''),
    'client_token' => env('CLIENT_TOKEN', ''),
 
    'XCUBE_BASE_URL' => env('XCUBE_BASE_URL', 'https://1xviewapimaster.lalpathlabs.com'),
    'XCUBE_LOGIN_BASE_URL' => env('XCUBE_LOGIN_BASE_URL', 'https://1xviewapiauth.lalpathlabs.com'),

// 'XCUBE_BASE_URL' => env('XCUBE_BASE_URL', 'https://api.lalpathlabs.com'),
// 'XCUBE_LOGIN_BASE_URL' => env('XCUBE_LOGIN_BASE_URL', 'https://auth.lalpathlabs.com'),

    'secretKey' => env('secretKey', '4fe950b7-9cfc-4fe0-b970-25f38d444c5f'),

    'X_SOURCE' => env('X_SOURCE', '7'),
    'X_APP_ID' => env('X_APP_ID', '4a297e9d970d42eeb6c0d07d198cd31c'),
    'X_AUTHORITY' => env('X_AUTHORITY', '1xviewapimaster.lalpathlabs.com'),
    'X_TIMEZONE' => env('X_TIMEZONE', '-330'),
    'X_APP_VERSION' => env('X_APP_VERSION', 'v1.1.5_6_UAT'),
    'X_DEVICE_TYPE' => env('X_DEVICE_TYPE', 'WEB'),
    'ACCEPT_LANGUAGE' => env('ACCEPT_LANGUAGE', 'en'),
    'SEC_CH_UA_MOBILE' => env('SEC_CH_UA_MOBILE', '?0'),
    'USER_AGENT' => env('USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
    'ACCEPT' => env('ACCEPT', 'application/json, text/plain, */*'),
    'X_LANGUAGE' => env('X_LANGUAGE', 'en'),
    'DATA_AREA_ID' => env('DATA_AREA_ID', 'live'),
    'ORIGIN' => env('ORIGIN', 'https://www.lalpathlabs.com'),
    'SEC_FETCH_SITE' => env('SEC_FETCH_SITE', 'same-site'),
    'SEC_FETCH_MODE' => env('SEC_FETCH_MODE', 'cors'),
    'SEC_FETCH_DEST' => env('SEC_FETCH_DEST', 'empty'),
    'X_SIGNATURE_VERSION' => env('X_SIGNATURE_VERSION', 'v2'),
];
