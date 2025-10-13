<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Locality;
use App\Models\City;
use DB;
use Response;
use Validator;

class LocalityController extends Controller
{
    function localityList(Request $request)
    {
        
        try {
            // $localities  = Locality::where('status', 1)->when($stateId, function ($doctors) use ($stateId,$cityId) {
            //     if ($stateId && $cityId) {
            //       //  $doctors->where('state_id', '=', $stateId)->orWhere('city_id','=', $cityId);
            //         $doctors->where(['state_id' => $stateId,'city_id'=> $cityId]);
            //     }
            // })->orderBy('name', 'asc')->get();
            if ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'city'=>'required',
                ], [
                    'city.required' => 'City is required.',
                ]);

                if ($validator->fails()) {
                    $result['Result'] = ['error' => $validator->errors()];
                    $result['Success'] = 'Failed';
                    $result['Message'] = 'Fields are missing.';
                    return response()->json($result);
                } else {
                    $city  = $request->post('city') ?? null;
                    $localities  = City::with(['locality'])->where('status', 1)->when($city, function ($query) use ($city) {
                        if (!empty($city)) {
                            $query->Where('slug', 'like', '%'.$city.'%');;
                        }
                    })->orderBy('name', 'asc')->get();
                }
              
            }else{
                $localities  = City::with(['locality'])->where('status', 1)->orderBy('name', 'asc')->get();
            }

         
           
            $localityArray = [];
            if ($localities) {
                foreach ($localities as $tKey => $tList) {
                    $localityArray[] = [
                        'CityId' => $tList->id,
                        'CityName' => $tList->name,
                        'CityLocalities' => $tList->locality,
                    ];
                }
            }
            $result['Result'] = [
                'data' =>$localityArray,
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
}