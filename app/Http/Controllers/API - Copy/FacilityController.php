<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Facility;
use DB;
use Response;
use Validator;

class FacilityController extends Controller
{

    function facilityLists(Request $request)
    {
        try {
            $lists  = Facility::where('status', 1)->orderBy('facility_name', 'asc')->get();
            $facilityArray = [];
            if ($lists) {
                foreach ($lists as $tKey => $list) {
                    $facilityArray[] = [
                        'Id' => $list->id,
                        'FacilityName' => $list->facility_name,
                        'Icon' => $list->facility_icon ? url('public/uploads/facility/' . $list->facility_icon) : '', 
                        'Status' => $list->status
                    ];
                }
            }
            $result['Result'] = [
                'Facilities' => $facilityArray,
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
    function facilityDetails(Request $request, $facilityId)
    {
        try {

            if ($facilityId) {
                $details  = Facility::where('status', 1)->where('id', $facilityId)->first();
                $detailsArray = [];
                if ($details) {
                    $detailsArray = [
                        'Id' => $details->id,
                        'FacilityName' => $details->facility_name,
                        'Icon' => $details->facility_icon ? url('public/uploads/facility/' . $details->facility_icon) : '', 
                        'Status' => $details->status
                    ];
                    $result['Result'] = [
                        'Details' => $detailsArray,
                    ];
                    $result['Success'] = 'True';
                    $result['Message'] = 'details.';
                } else {
                    $result['Result'] = (object) [];
                    $result['Success'] = 'False';
                    $result['Message'] = 'Facility details not found.';
                }


                return response()->json($result);
            } else {
                $result['Result'] =  (object) [];
                $result['Success'] = 'False';
                $result['Message'] = 'Facility id is missing.';
                return response()->json($result);
            }
        } catch (\Exception $e) {
            dd($e);
            $result['Result'] = (object)  [];
            $result['Success'] = 'False';
            $result['Message'] = $e;
            return response()->json($result);
        }
    }
}
