<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Validator;

class CustomerController extends Controller
{
    function updatePatient(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'pid' => 'required',
                'salutation' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'mobile_no' => 'required|numeric',
                'gender' => 'required',
                'relation' => 'required',
                'email_id' => 'required|email',
            ], [
                'pid.required' => 'Patient Id is required.',
                'salutation.required' => 'Salutation is required.',
                'first_name.required' => 'First Name is required.',
                'last_name.required' => 'Last Name is required.',
                'mobile_no.required' => 'Mobile is required.',
                'gender.required' => 'Gender is required.',
                'relation.required' => 'Relation is required.',
                'email_id.required' => 'Email Id is required.',

            ]);
            if ($validator->fails()) {
                $result['Result'] = ['error' => $validator->errors()];
                $result['Success'] = 'Failed';
                $result['Message'] = 'Fields are missing.';
                return response()->json($result);
            } else {
                // $curl = curl_init();
                $url = env('CRM_URL') . 'updatePatient';
                // $post = $request->all();
                $post = [
                    "salutation" => $request->salutation,
                    "first_name" => $request->first_name,
                    "last_name" => $request->last_name,
                    "mobile_no" => $request->mobile_no,
                    "gender" => $request->gender,
                    "dob" => $request->dob,
                    "relation" => $request->relation,
                    "pid" => $request->pid,
                ];
                // $data_string = json_encode($post);

                // $headers = [];
                // $headers[] = 'Content-Type:application/json';
                $token =  env('CRM_TOKEN');
                // $headers[] = "x-token:".$token;
                $verify = CurlCall($url, $token, 'POST', $post);
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
                if ($verify->error == false) {
                    $check =  Customer::where(['mobile_no' => $request->mobile_no])->first();
                    if (!empty($check)) {
                        $userDetail  = Customer::where('mobile_no', $request->mobile_no)->update($post);
                    } else {
                        $customer = $verify->data->results;
                        $patient = [
                            "salutation" => $customer->salutation,
                            "pid" => $customer->pid,
                            "first_name" => $customer->first_name,
                            "last_name" => $customer->last_name,
                            "mobile_no" => $customer->mobile_no,
                            "gender" => $customer->gender,
                            "dob" => $customer->dob,
                            "relation" => $customer->relation,
                            "relation_txt" => $customer->relation_txt,
                            "salutation_txt" => $customer->salutation_txt,
                            "status" => $customer->status,
                            "email_id" => $request->email_id,
                        ];
                        $userDetail  = Customer::create($patient);
                    }
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

    function createPatient(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                "salutation" => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'mobile_no' => 'required|numeric|digits:10',
                'gender' => 'required',
                'dob' => 'required|date_format:Y-m-d',

                'email_id' => 'required|email',
                'relation' => 'numeric'
            ], [
                "salutation.required" => 'Salutation name is required.',
                'first_name.required' => 'First name is required.',
                'last_name.required' => 'Last name is required.',
                'mobile_no.required' => 'Mobile No is required.',
                'gender.required' => 'Gender is required.',
                'dob.required' => 'Date of birth is required.',
                'relation.required' => 'Relation Id is required.',
                'email_id.required' => 'Email Id is required.',
            ]);

            if ($validator->fails()) {
                $result['Result'] = ['error' => $validator->errors()];
                $result['Success'] = 'Failed';
                $result['Message'] = 'Fields are missing.';
                return response()->json($result);
            } else {
                $post = [
                    "salutation" => $request->salutation,
                    "first_name" => $request->first_name,
                    "last_name" => $request->last_name,
                    "mobile_no" => $request->mobile_no,
                    "gender" => $request->gender,
                    "dob" => $request->dob,
                    "relation" => $request->relation,
                ];
                $url = env('CRM_URL') . 'createPatient';
                $token =  env('CRM_TOKEN');
                $verify = CurlCall($url, $token, 'POST', $post);
                if ($verify->error == false) {
                    $patient = [
                        "salutation" => $request->salutation,
                        "pid" => $verify->data->results->pid,
                        "first_name" => $request->first_name,
                        "last_name" => $request->last_name,
                        "mobile_no" => $request->mobile_no,
                        "gender" => $request->gender,
                        "dob" => $request->dob,
                        "relation" => $request->relation,
                        "email_id" => $request->email_id,
                    ];
                    $checkCustomer =  Customer::where(['mobile_no' => $request->mobile_no])->first();
                    if (empty($checkCustomer)) {
                        $userDetail  = Customer::create($patient);
                    } else {
                        unset($patient['mobile_no']);
                        Customer::UpdateOrCreate(['id' => $checkCustomer->id], $patient);
                        $userDetail = Customer::where(['id' => $checkCustomer->id])->first();
                    }
                } else {
                    $userDetail = Customer::where(['mobile_no' => $request->mobile_no])->first();
                }
                $result['Result'] =  $userDetail;
                $result['Success'] = 'True';
                $result['Message'] = 'Patient Created.';
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


    function profile(Request $request, $userId)
    {
        try {

            $userDetail  = Customer::where(['status' => 1, 'id' => $userId])->first();

            $result['Result'] = [
                'User' => $userDetail,
            ];
            $result['Success'] = 'True';
            $result['Message'] = 'User Profile.';

            return response()->json($result);
            // }

        } catch (\Exception $e) {
            dd($e);
            $result['Result'] = [];
            $result['Success'] = 'False';
            $result['Message'] = $e;
            return response()->json($result);
        }
    }

    function editProfile(Request $request, $userId)
    {
        try {

            $validator = Validator::make($request->all(), [
                "salutation" => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'mobile_no' => 'required|numeric|digits:10',
                'gender' => 'required',
                'dob' => 'required|date_format:Y-m-d',
                'email_id' => 'required|email',
            ], [
                'salutation.required' => 'Salutation is required.',
                'first_name.required' => 'First name is required.',
                'last_name.required' => 'Last name is required.',
                'mobile_no.required' => 'Mobile No is required.',
                'gender.required' => 'Gender is required.',
                'dob.required' => 'Date of birth is required.',
                'email_id.required' => 'Email name is required.',
            ]);

            if ($validator->fails()) {
                $result['Result'] = ['error' => $validator->errors()];
                $result['Success'] = 'Failed';
                $result['Message'] = 'Fields are missing.';
                return response()->json($result);
            } else {
                $post = [
                    "pid" => $request->pid,
                    "salutation" => $request->salutation,
                    "first_name" => $request->first_name,
                    "last_name" => $request->last_name,
                    "mobile_no" => $request->mobile_no,
                    "gender" => $request->gender,
                    "dob" => $request->dob,
                    "relation" => $request->relation,
                    "email_id" => $request->email_id,
                ];
                $user = Customer::where('id', $userId);
                $uData =  $user->first();
                $previous_fields = [
                    "pid" => $uData->pid,
                    "salutation" => $uData->salutation,
                    "first_name" => $uData->first_name,
                    "last_name" => $uData->last_name,
                    "mobile_no" => $uData->mobile_no,
                    "gender" => $uData->gender,
                    "dob" => $uData->dob,
                    "relation" => $uData->relation,
                    "email_id" => $uData->email_id,
                ];
                $user->update($post);
                $userDetail = $user->first();
                /**
                 * Generate UserActivity Log
                 */
                $logsArr = [
                    'customer_id' => $userId,
                    'action' => 'Update profile',
                    'previous_fields' => json_encode($previous_fields),
                    'new_fields' => json_encode($post),
                    'created_by' => $userId,
                    'updated_by' => $userId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                CustomerActivityLog($logsArr);
                if ($request->pid) {
                    $url = env('CRM_URL') . 'updatePatient';
                    $token =  env('CRM_TOKEN');
                    $res = CurlCall($url, $token, 'POST', $post);
                }

                $result['Result'] = [
                    'User' => $userDetail,
                    'CRMUser' => ($request->pid) ? $res  : NULL
                ];
                $result['Success'] = 'True';
                $result['Message'] = 'User Profile.';

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
