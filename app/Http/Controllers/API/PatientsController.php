<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Customer;
use Validator;

class PatientsController extends Controller
{
    function addFamilyMember(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                // 'customer_id'=>'required|numeric',
                "pid" => 'required',
                "salutation" => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'mobile_no' => 'required|numeric|digits:10',
                'gender' => 'required',
                'dob' => 'required|date_format:Y-m-d',
                'relation' => 'numeric'
            ], [
                // 'customer_id.required' => 'Customer is required.',
                "pid.required" => 'pid is required.',
                "salutation.required" => 'Salutation name is required.',
                'first_name.required' => 'First name is required.',
                'last_name.required' => 'Last name is required.',
                'mobile_no.required' => 'Mobile No is required.',
                'gender.required' => 'Gender is required.',
                'dob.required' => 'Date of birth is required.',
                'relation.required' => 'Relation Id is required.',
                // 'email_id.required' => 'Email name is required.',
                // 'age.required' => 'Age name is required.',
            ]);

            if ($validator->fails()) {
                $result['Result'] = ['error' => $validator->errors()];
                $result['Success'] = 'Failed';
                $result['Message'] = 'Fields are missing.';
                return response()->json($result);
            } else {
                // 
                $post = [
                    "pid" => $request->pid,
                    "salutation" => $request->salutation,
                    "first_name" => $request->first_name,
                    "last_name" => $request->last_name,
                    "mobile_no" => $request->mobile_no,
                    "gender" => $request->gender,
                    "dob" => $request->dob,
                    "relation" => $request->relation
                ];
                $url = env('CRM_URL') . 'addFamilyMember';
                $token = env('CRM_TOKEN');

                $res = CurlCall($url, $token, 'POST', $post);
                if ($res->error == false) {
                    $family = $res->data->results->family[0];
                    $patient = [
                        "pid" => $request->pid,
                        "salutation" => $family->salutation,
                        "salutation_txt" => $family->salutation_txt,
                        "first_name" => $family->first_name,
                        "last_name" => $family->last_name,
                        "mobile_no" => $family->mobile_no,
                        "gender" => $family->gender,
                        // "dob"=> $family->dob,
                        "relation" => $family->relation,
                        "relation_txt" => $family->relation_txt,
                    ];
                    // $checkCustomer =  Customer::where(['pid' => $request->pid])->first();
                    // if (empty($checkPatient)) {
                    //     $userDetail  = Customer::create($patient);
                    // }
                    $checkPatient =  Patient::where(['pid' => $request->pid, 'mobile_no' => $family->mobile_no])->first();
                    if (!empty($checkPatient)) {
                        $userDetail  = Patient::where(['pid' => $request->pid, 'mobile_no' => $family->mobile_no])->update($patient);
                    } else {
                        $userDetail  = Patient::create($patient);
                    }
                }
                $result['Result'] = $res;
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

    function updateFamilyMember(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'pid' => 'required',
                'id' => 'required',
                "salutation" => 'required',
                "first_name" => 'required',
                "last_name" => 'required',
                "mobile_no" => 'required',
                "gender" => 'required',
                "dob" => 'required',
                "relation" => 'required'
            ], [
                'pid.required' => 'Patient Id is required.',
                'id.required' => 'Id is required.',
                "salutation.required" => 'Salutation is required',
                "first_name.required" => 'First Name is required',
                "last_name.required" => 'Last Name is required',
                "mobile_no.required" => 'Mobile is required',
                "gender.required" => 'Gender is required',
                "dob.required" => 'DOB is required',
                "relation.required" => 'Relation is required'
            ]);
            if ($validator->fails()) {
                $result['Result'] = ['error' => $validator->errors()];
                $result['Success'] = 'Failed';
                $result['Message'] = 'Fields are missing.';
                return response()->json($result);
            } else {
                // $curl = curl_init();
                $url =  env('CRM_URL') . 'updateFamilyMember';
                // $post = $request->all();
                $post = [
                    "pid" => $request->pid,
                    "id" => $request->id,
                    "salutation" => $request->salutation,
                    "first_name" => $request->first_name,
                    "last_name" => $request->last_name,
                    "mobile_no" => $request->mobile_no,
                    "gender" => $request->gender,
                    "dob" => $request->dob,
                    "relation" => $request->relation
                ];
                // $data_string = json_encode($post);

                // $headers = [];
                // $headers[] = 'Content-Type:application/json';
                $token =  env('CRM_TOKEN');
                // $headers[] = "x-token:".$token;
                // curl_setopt_array($curl, array(
                //     CURLOPT_SSL_VERIFYHOST=>0,
                //     CURLOPT_SSL_VERIFYPEER=>0,
                //     CURLOPT_URL => str_replace(' ', '+', $url),
                //     CURLOPT_RETURNTRANSFER => true,
                //     CURLOPT_ENCODING => '',
                //     CURLOPT_MAXREDIRS => 10,
                //     CURLOPT_TIMEOUT => 0,
                //     CURLOPT_FOLLOWLOCATION => true,
                //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //     CURLOPT_CUSTOMREQUEST => 'POST',
                //     CURLOPT_HTTPHEADER=>$headers,
                //     CURLOPT_POSTFIELDS=>$data_string
                // ));

                // $response = curl_exec($curl);
                // curl_close($curl);
                // $res = json_decode($response);
                $res = CurlCall($url, $token, 'POST', $post);
                if ($res->error == false) {
                    $family = $res->data->results->family[0];
                    $patient = [
                        "pid" => $request->pid,
                        "salutation" => $family->salutation,
                        "salutation_txt" => $family->salutation_txt,
                        "first_name" => $family->first_name,
                        "last_name" => $family->last_name,
                        "mobile_no" => $family->mobile_no,
                        "gender" => $family->gender,
                        // "dob"=> $family->dob,
                        "relation" => $family->relation,
                        "relation_txt" => $family->relation_txt,
                    ];

                    // $checkCustomer =  Customer::where(['pid'=>$request->pid])->first();
                    // if(empty($checkPatient)){
                    //     $userDetail  = Customer::create($patient);
                    // }
                    $checkPatient =  Patient::where(['pid' => $request->pid, 'mobile_no' => $family->mobile_no])->first();
                    if (!empty($checkPatient)) {
                        $userDetail  = Patient::where(['pid' => $request->pid, 'mobile_no' => $family->mobile_no])->update($patient);
                    } else {
                        $userDetail  = Patient::create($patient);
                    }
                    // $userDetail  = Patient::where(['pid'=>$request->pid,'mobile_no'=>$family->mobile_no])->update($patient);
                    // $userDetail  = Patient::updateOrCreate(['id'=>$res->data->results->family->id],$request->all());
                }
                // $family = $res->data->results;
                $result['Result'] = $res;
                return response()->json($result);

                // return $response;
            }
        } catch (\Exception $e) {
            dd($e);
            $result['Result'] = [];
            $result['Success'] = 'False';
            $result['Message'] = $e;
            return response()->json($result);
        }
    }

    function deleteFamilyMember(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'pid' => 'required',
                'id' => 'required'
            ], [
                'pid.required' => 'Patient Id is required.',
                'id.required' => 'Id is required.',
            ]);
            if ($validator->fails()) {
                $result['Result'] = ['error' => $validator->errors()];
                $result['Success'] = 'Failed';
                $result['Message'] = 'Fields are missing.';
                return response()->json($result);
            } else {
                // $curl = curl_init();
                $url = env('CRM_URL') . 'deleteFamilyMember';
                $post = [
                    'pid' => $request->pid,
                    'id' => $request->id
                ];
                // $data_string = json_encode($post);
                // $headers = [];
                // $headers[] = 'Content-Type:application/json';
                $token =  env('CRM_TOKEN');
                // $headers[] = "x-token:".$token;
                // curl_setopt_array($curl, array(
                //     CURLOPT_SSL_VERIFYHOST=>0,
                //     CURLOPT_SSL_VERIFYPEER=>0,
                //     CURLOPT_URL => str_replace(' ', '+', $url),
                //     CURLOPT_RETURNTRANSFER => true,
                //     CURLOPT_ENCODING => '',
                //     CURLOPT_MAXREDIRS => 10,
                //     CURLOPT_TIMEOUT => 0,
                //     CURLOPT_FOLLOWLOCATION => true,
                //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //     CURLOPT_CUSTOMREQUEST => 'POST',
                //     CURLOPT_HTTPHEADER=>$headers,
                //     CURLOPT_POSTFIELDS=>$data_string
                // ));

                // $response = curl_exec($curl);
                // curl_close($curl);
                // $verify = json_decode($response);
                $verify = CurlCall($url, $token, 'POST', $post);
                if ($verify->error == false) {
                    $userDetail  = Patient::where(['id' => $request->id, 'pid' => $request->pid])->delete();
                }
                $result['Result'] = $verify;
                return response()->json($result);

                // return $response;
            }
        } catch (\Exception $e) {
            dd($e);
            $result['Result'] = [];
            $result['Success'] = 'False';
            $result['Message'] = $e;
            return response()->json($result);
        }
    }

    function patientList(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mobile_no' => 'required|numeric|digits:10',
            ], [
                'mobile_no.required' => 'Mobile No is required.',
            ]);
            if ($validator->fails()) {
                $result['Result'] = ['error' => $validator->errors()];
                $result['Success'] = 'Failed';
                $result['Message'] = 'Fields are missing.';
                return response()->json($result);
            } else {
                $url = env('CRM_URL') . 'getPatientList?mobile_no=' . $request->mobile_no;
                $token =  env('CRM_TOKEN');
                $res = CurlCall($url, $token, 'GET');
                $result['Result'] = $res;
                if ($result['Result']->error == false && isset($result['Result']->data->results) && count($result['Result']->data->results) > 0) {
                    $patientNos = [];
                    if (count($result['Result']->data->results[0]->family) > 0) {
                        foreach ($result['Result']->data->results[0]->family as $key =>  $list) {
                            // dd($list);
                            $patientNos[] = $list->mobile_no;
                            $familyDetails =  Patient::where(['mobile_no' =>  $list->mobile_no])->first();
                            $result['Result']->data->results[0]->family[$key]->details =  $familyDetails;
                        }
                    }

                    $customer =  Customer::where(['mobile_no' => $result['Result']->data->results[0]->mobile_no])->first();
                    $result['Result']->data->results[0]->details = $customer;
                }
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


    function delete(Request $request, $userId)
    {
        try {


            $user = Patient::find($userId);
            $user->delete();

            $result['Result'] = [
                'User' => $userId,
            ];
            $result['Success'] = 'True';
            $result['Message'] = 'User delete.';

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
