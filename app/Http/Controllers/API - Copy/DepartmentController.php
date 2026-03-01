<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use DB;
use Response;
use Validator;

class DepartmentController extends Controller
{

    function departmentLists(Request $request)
    {
        try {
            $lists  = Department::where('status', 1)->where('is_department_page', 1)->orderBy('department_name', 'asc')->get();
            $departmentArray = [];
            if ($lists) {
                foreach ($lists as $tKey => $list) {
                    $departmentArray[] = [
                        'Id' => $list->id,
                        'DepartmentName' => $list->department_name,
                        'ShortDescription' => $list->short_description,
                        'Slug' => $list->slug,
                        'MainBanner' => $list->main_banner ? url('public/uploads/departments/main_banner/' . $list->main_banner) : '',
                        'TeamImage' => $list->team_image ? url('public/uploads/departments/team_image/' . $list->team_image) : '',
                        'Structure' => $list->structure ? json_decode($list->structure, true) : [],
                        'Description' => $list->description, 
                        'Status' => $list->status,
                        'Icon' => $list->icon ? url('public/uploads/departments/icons/' . $list->icon) : ''
                    ];
                }
            }
            $result['Result'] = [
                'InstrumentsBaseUrl' => url('/') . '/public/uploads/departments/',
                'Departments' => $departmentArray,
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
    function departmentDetails(Request $request, $departmentId)
    {
        try {

            if ($departmentId) {
                $details  = Department::where('status', 1)->where('id', $departmentId)->first();
                $detailsArray = [];
                if ($details) {
                    $detailsArray = [
                        'Id' => $details->id,
                        'DepartmentName' => $details->department_name,
                        'ShortDescription' => $details->short_description,
                        'Slug' => $details->slug,
                        'MainBanner' => $details->main_banner ? url('public/uploads/departments/main_banner/' . $details->main_banner) : '',
                        'TeamImage' => $details->team_image ? url('public/uploads/departments/team_image/' . $details->team_image) : '',
                        'Structure' => $details->structure ? json_decode($details->structure, true) : [],
                        'Instruments' => $details->instruments ? json_decode($details->instruments, true) : [],
                        'Description' => $details->description,

                        'Status' => $details->status
                    ];
                    $result['Result'] = [
                        'InstrumentsBaseUrl' => url('/') . '/public/uploads/departments/',
                        'DepartmentDetails' => $detailsArray,
                    ];
                    $result['Success'] = 'True';
                    $result['Message'] = 'details.';
                } else {
                    $result['Result'] = (object) [];
                    $result['Success'] = 'False';
                    $result['Message'] = 'Department details not found.';
                }


                return response()->json($result);
            } else {
                $result['Result'] =  (object) [];
                $result['Success'] = 'False';
                $result['Message'] = 'Department id is missing.';
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
    function departmentDetailsBySlug(Request $request, $slug)
    {
        try {

            if ($slug) {
                $details  = Department::where('status', 1)->where('slug', $slug)->first();
                $detailsArray = [];
                if ($details) {
                    $detailsArray = [
                        'Id' => $details->id,
                        'DepartmentName' => $details->department_name,
                        'ShortDescription' => $details->short_description,
                        'Slug' => $details->slug,
                        'MainBanner' => $details->main_banner ? url('public/uploads/departments/main_banner/' . $details->main_banner) : '',
                        'TeamImage' => $details->team_image ? url('public/uploads/departments/team_image/' . $details->team_image) : '',
                        'Structure' => $details->structure ? json_decode($details->structure, true) : [],
                        'Instruments' => $details->instruments ? json_decode($details->instruments, true) : [],
                        'Description' => $details->description,

                        'Status' => $details->status
                    ];
                    $result['Result'] = [
                        'InstrumentsBaseUrl' => url('/') . '/public/uploads/departments/',
                        'DepartmentDetails' => $detailsArray,
                    ];
                    $result['Success'] = 'True';
                    $result['Message'] = 'details.';
                } else {
                    $result['Result'] = (object) [];
                    $result['Success'] = 'False';
                    $result['Message'] = 'Department details not found.';
                }


                return response()->json($result);
            } else {
                $result['Result'] =  (object) [];
                $result['Success'] = 'False';
                $result['Message'] = 'Department id is missing.';
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
    function departmentBrochuresLists(Request $request)
    {
        try {
            $slug = request('Slug');
            if ($slug) {
                $details  = Department::where('status', 1)->where('slug', $slug)->first();
                $detailsArray = [];
                if ($details) {
                    $detailsArray = [ 
                        'Brochures' => $details->brochures ? json_decode($details->brochures, true) : [], 
                    ];
                    $result['Result'] = [
                        'InstrumentsBaseUrl' => url('/') . '/public/uploads/departments/brochures/',
                        'DepartmentDetails' => $detailsArray,
                    ];
                    $result['Success'] = 'True';
                    $result['Message'] = 'details.';
                } else {
                    $result['Result'] = (object) [];
                    $result['Success'] = 'False';
                    $result['Message'] = 'Department details not found.';
                }


                return response()->json($result);
            } else {
                $result['Result'] =  (object) [];
                $result['Success'] = 'False';
                $result['Message'] = 'Department slug is missing.';
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
