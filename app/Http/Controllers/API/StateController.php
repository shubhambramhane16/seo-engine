<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\State;
use DB;
use Response;
use Validator;

class StateController extends Controller
{

    function stateList(Request $request)
    {
        try {
            $states  = State::where('status', 1)->orderBy('name', 'asc')->get();
            $statesArray = [];
            if ($states) {
                foreach ($states as $tKey => $tList) {
                    $statesArray[] = [
                        'Id' => $tList->id,
                        'Name' => $tList->name,
                        'CountryId' => $tList->country_id,
                    ];
                }
            }
            $result['Result'] = [
                'States' => $statesArray,
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

    function getStateList(Request $request)
    {
        try {
            $curl = curl_init();
                $url = env('CRM_URL').'getStateList?state='.$request->state;
                $headers = [];
                $headers[] = 'Content-Type:application/json';
                $token =  env('CRM_TOKEN');
                $headers[] = "x-token:".$token;
                curl_setopt_array($curl, array(
                    CURLOPT_SSL_VERIFYHOST=>0,
                    CURLOPT_SSL_VERIFYPEER=>0,
                    CURLOPT_URL => str_replace(' ', '+', $url),
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER=>$headers
                ));
        
            $response = curl_exec($curl);
            curl_close($curl);
            $res = json_decode($response);
            $result['Result'] = [
                'CrmStates' => $res,
            ];
            $states = State::select('id as StateId','name as StateName')->where(function ($query) use ($request) {
                        if($request->get('state')){
                            $query->where('name', "like", "%" . $request->state . "%");
                        }
                    })->get();  
            $result['States'] = $states;
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
