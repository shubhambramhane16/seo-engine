<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Speciality;
use DB;
use Response;
use Validator;

class SpecilityController extends Controller
{

    function specilityLists(Request $request)
    {
        try {
            $departmentId = $request->DepartmentId;
            $lists  = Speciality::where('status', 1)->when($departmentId, function ($lists) use ($departmentId) {
                if ($departmentId) {
                    $lists->where('department_id', '=', $departmentId);
                }
            })->orderBy('speciality_name', 'asc')->get();
            $SpecilitiesArray = [];
            if ($lists) {
                foreach ($lists as $tKey => $list) {
                    $SpecilitiesArray[] = [
                        'Id' => $list->id,
                        'DepartmentId' => $list->department_id,
                        'SpecialityName ' => $list->speciality_name,
                        'Slug' => $list->slug,
                        'Status' => $list->status
                    ];
                }
            }
            $result['Result'] = [
                'Specilities' => $SpecilitiesArray,
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
    function specilityDetails(Request $request, $specilityId)
    {
        try {

            if ($specilityId) {
                $details  = Speciality::where('status', 1)->where('id', $specilityId)->first();
                $detailsArray = [];
                if ($details) {
                    $detailsArray = [
                        'Id' => $details->id,
                        'DepartmentId' => $details->department_id,
                        'SpecialityName ' => $details->speciality_name,
                        'Slug' => $details->slug,
                        'Status' => $details->status
                    ];
                    $result['Result'] = [
                        'SpecialityDetails' => $detailsArray,
                    ];
                    $result['Success'] = 'True';
                    $result['Message'] = 'details.';
                } else {
                    $result['Result'] = (object) [];
                    $result['Success'] = 'False';
                    $result['Message'] = 'Speciality details not found.';
                }


                return response()->json($result);
            } else {
                $result['Result'] =  (object) [];
                $result['Success'] = 'False';
                $result['Message'] = 'Speciality id is missing.';
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
    function specilityDetailsBySlug(Request $request, $slug)
    {
        try {

            if ($slug) {
                $details  = Speciality::where('status', 1)->where('slug', $slug)->first();
                $detailsArray = [];
                if ($details) {
                    $detailsArray = [
                        'Id' => $details->id,
                        'DepartmentId' => $details->department_id,
                        'SpecialityName ' => $details->speciality_name,
                        'Slug' => $details->slug,
                        'Status' => $details->status
                    ];
                    $result['Result'] = [
                        'SpecialityDetails' => $detailsArray,
                    ];
                    $result['Success'] = 'True';
                    $result['Message'] = 'details.';
                } else {
                    $result['Result'] = (object) [];
                    $result['Success'] = 'False';
                    $result['Message'] = 'Speciality details not found.';
                }


                return response()->json($result);
            } else {
                $result['Result'] =  (object) [];
                $result['Success'] = 'False';
                $result['Message'] = 'Speciality id is missing.';
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
