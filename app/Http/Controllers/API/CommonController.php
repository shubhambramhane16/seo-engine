<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use App\Models\Coupon;
// use DB;
// use Response;
use Validator;
use File;
use Storage;

class CommonController extends Controller
{
    function availableSlots(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'Date' => 'date|required',
                // 'Id' => 'required',
                // 'Type' => 'required',
            ], [
                'Type.required' => 'package or test.',
                'Id.required' => 'Id must be package id or test id.',
            ]);
            if ($validator->fails()) {
                $result['Result'] = ['error' => $validator->errors()];
                $result['Success'] = 'Failed';
                $result['Message'] = 'Fields are missing.';
                return response()->json($result);
            } else {
                $slots = [
                    'Morning' => [
                        '9:30 AM',
                        '10:00 AM',
                        '10:30 AM',
                        '11:00 AM',
                        '11:30 PM',
                        '12:00 PM',
                    ],
                    'Evening' => [
                        '6:30 PM',
                        '7:00 PM',
                        '7:30 PM',
                        '8:30 PM',
                        '9:00 PM',
                        '9:30 PM',
                    ],
                ];
                $result['Result'] = [
                    'Slots' => $slots,
                ];
                $result['Success'] = 'True';
                $result['Message'] = 'slots.';
            }

            return response()->json($result);
        } catch (\Exception $e) {
            dd($e);
            $result['Result'] = [];
            $result['Success'] = 'False';
            $result['Message'] = $e;
            return response()->json($result);
        }
    }
    function brochureList()
    {
        try {
            $brochuresArr = [];
            $brochure = getBrochures(['is_brochures_page' => 1]);
            if ($brochure) {
                foreach ($brochure as $key => $list) {
                    if ($list['is_brochures_page'] == 1) {
                        if ($list->brochures) {
                            $brochuresL = json_decode($list->brochures, 1);
                            foreach ($brochuresL as $skey => $slist) {
                                if (str_contains($slist['image'], 'AWS')) {
                                    $imgSrc = \Storage::disk('s3')->url($slist['image']);
                                } else {
                                    $imgSrc = url('/') . '/public/uploads/departments/brochures/' . $slist['image'];
                                }
                                $brochuresArr[] = [
                                    'image' =>  $imgSrc,
                                    'title' => $slist['title'],
                                    'department_name' => $list['department_name'],
                                    'department_id' => $list['id'],
                                ];
                            }
                        }
                    }
                }
            }
            $result['Result'] = [
                'List' =>  $brochuresArr,
            ];
            $result['Success'] = 'True';
            $result['Message'] = 'slots.';

            return response()->json($result);
        } catch (\Exception $e) {
            dd($e);
            $result['Result'] = [];
            $result['Success'] = 'False';
            $result['Message'] = $e;
            return response()->json($result);
        }
    }
    function termsConditions()
    {

        try {
            $terms_condition = 'termsandconditions.txt';
            $fileStorePath = public_path($terms_condition);
            $contents = File::get($fileStorePath);
            $result['Result'] = [
                'terms&conditions' =>  $contents,
            ];
            $result['Success'] = 'True';
            $result['Message'] = 'termsConditions.';

            return response()->json($result);
        } catch (\Exception $e) {
            dd($e);
            $result['Result'] = [];
            $result['Success'] = 'False';
            $result['Message'] = $e;
            return response()->json($result);
        }
    }

    function getMasterArr()
    {
        try {
            $curl = curl_init();
            $url = env('CRM_URL') . 'getMasterArr';
            $headers = [];
            $headers[] = 'Content-Type:application/json';
            $token = "650a9655c288d650a966282b97650a96689a362";
            $headers[] = "x-token:" . $token;
            curl_setopt_array($curl, array(
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_URL => str_replace(' ', '+', $url),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => $headers
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $result['Result'] = json_decode($response);
            return response()->json($result);
        } catch (\Exception $e) {
            dd($e);
            $result['Result'] = [];
            $result['Success'] = 'False';
            $result['Message'] = $e;
            return response()->json($result);
        }
    }
}
