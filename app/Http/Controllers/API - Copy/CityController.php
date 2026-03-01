<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\City;
use DB;
use Response;
use Validator;

class CityController extends Controller
{

    function cityList(Request $request, $stateId = null)
    {

        try {
            $cities  = City::where('status', 1)->when($stateId, function ($doctors) use ($stateId) {
                if ($stateId) {
                    $doctors->where('state_id', '=', $stateId);
                }
            })->orderBy('name', 'asc')->get();
            $cityArray = [];
            if ($cities) {
                foreach ($cities as $tKey => $tList) {
                    $cityArray[] = [
                        'Id' => $tList->id,
                        'Name' => $tList->name,
                        'StateId' => $tList->state_id,
                    ];
                }
            }
            $result['Result'] = [
                'Cities' => $cityArray,
            ];
            $result['Success'] = 'True';
            $result['Message'] = 'list.';

            return response()->json($result);
        } catch (\Exception $e) {
            dd($e);
            $result['Result'] = [];
            $result['Success'] = 'False';
            $result['Message'] = $e;
            return response()->json($result);
        }
    }

    function getCityList(Request $request)
    {



        try {

            $validator = Validator::make($request->all(), [
                'StateId' => 'required|numeric',
                'CrmStateId' => 'required|numeric',
            ], [
                'StateId.required' => 'State Id is required.',
                'CrmStateId.required' => 'CrmState Id is required.',
            ]);
            if ($validator->fails()) {
                $result['Result'] = ['error' => $validator->errors()];
                $result['Success'] = 'Failed';
                $result['Message'] = 'Fields are missing.';
                return response()->json($result);
            } else {


                $curl = curl_init();
                $url = env('CRM_URL') . 'getCityList?state_id=' . $request->CrmStateId . '&city=' . $request->City;
                $headers = [];
                $headers[] = 'Content-Type:application/json';
                $token =  env('CRM_TOKEN');
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
                $res = json_decode($response);
                $result['Result'] = [
                    'CrmCities' => $res,
                ];
                $cities = City::select('id as CityId', 'name as CityName')
                    ->where(function ($query) use ($request) {
                        if ($request->get('StateId')) {
                            $query->where('state_id', $request->StateId);
                        }
                        if ($request->get('City')) {
                            $query->where('name', "like", "%" . $request->City . "%");
                        }
                    })
                    ->where('status',1)
                    ->get();
                $result['Cities'] = $cities;
                $result['Success'] = 'True';
                $result['Message'] = 'list.';
                return response()->json($result);
            }
        } catch (\Exception $e) {
            dd($e);
            $result['Result'] = [];
            $result['Success'] = 'False';
            $result['Message'] = $e;
            return response()->json($result);
        }
    }
}
