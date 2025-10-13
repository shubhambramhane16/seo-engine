<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use DB;
use Response;
use Validator;
use File;

class SettingsController extends Controller
{

    function generalDetails(Request $request)
    {
        try {
            $array = [];
            $details = Setting::first();
            if ($details) {

                $array = [
                    'RegisteredOfficeAddress' => $details->registered_office_address,
                    'OfficeAddress' => $details->office_address,
                    'PhoneNumber' => $details->phone_number,
                    'WhatsApp' => $details->whatsapp,
                    'CustomerCare' => $details->customer_care,
                    'EmailId' => $details->email_id,
                ];

                $result['Result'] = $array;
                $result['Success'] = 'True';
                $result['Message'] = 'details.';
            } else {
                $result['Result'] = [];
                $result['Success'] = 'False';
                $result['Message'] = 'No testimonials found.';
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
 
}
