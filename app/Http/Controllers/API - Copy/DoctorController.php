<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Doctor;
use DB;
use Response;
use Validator;
use File;

class DoctorController extends Controller
{

    function doctorList(Request $request)
    {
        try {
            $departmentId = request('DepartmentId');
            $doctors  = Doctor::with(['department'])->where('status', 1)->when($departmentId, function ($doctors) use ($departmentId) {
                if ($departmentId) {
                    $doctors->where('department_id', '=', $departmentId);
                }
            })->orderBy('id', 'desc')->get();
            $doctorsArray = [];
            if ($doctors) {
                foreach ($doctors as $tKey => $tList) {
                    $doctorsArray[] = [
                        'Id' => $tList->id,
                        'DoctorCode' => $tList->doctor_code,
                        'DepartmentId' => $tList->department_id,
                        'Name' => $tList->name,
                        'Email' => $tList->email,
                        'Mobile' => $tList->mobile,
                        'DOB' => $tList->dob,
                        'Gender' => $tList->gender,
                        'Qualification' => $tList->qualification,
                        'AreaInterest' => $tList->area_of_interest,
                        'Expertise' => $tList->expertise,
                        'Details' => $tList->details,
                        'ResearchPublication' => $tList->research_publication,
                        'Awards' => $tList->awards ? json_decode($tList->awards, 1) : [],
                        'MainVideo' => $tList->main_video,
                        'MainVideoYoutubeLink' => $tList->main_video_youtube_link,
                        'OtherVideos' => $tList->other_videos ? json_decode($tList->other_videos, 1) : [],
                        'ProfileImage' => $tList->profile_image ?  url('/public/uploads/doctors/profiles/' . $tList->profile_image) : '',
                        'Status' => $tList->status,
                        'Designation' => $tList->designation,
                        'DepartmentName' => $tList->department ? $tList->department->department_name : '',
                    ];
                }
            }
            $result['Result'] = [
                'DoctorVideosBaseUrl' => url('/') . '/public/uploads/doctors/videos/',
                'Doctors' => $doctorsArray,
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

    function doctorDetails(Request $request, $doctorCode)
    {
        try {
            if ($doctorCode = $doctorCode) {
                $doctor  = Doctor::with(['department'])->where('status', 1)->where('doctor_code', $doctorCode)->first();
                $doctorArray = [];
                if ($doctor) {
                    $doctorArray = [
                        'Id' => $doctor->id,
                        'DoctorCode' => $doctor->doctor_code,
                        'DepartmentId' => $doctor->department_id,
                        'Name' => $doctor->name,
                        'Email' => $doctor->email,
                        'Mobile' => $doctor->mobile,
                        'DOB' => $doctor->dob,
                        'Gender' => $doctor->gender,
                        'Qualification' => $doctor->qualification,
                        'AreaInterest' => $doctor->area_of_interest,
                        'Expertise' => $doctor->expertise,
                        'Details' => $doctor->details,
                        'ResearchPublication' => $doctor->research_publication,
                        'Awards' => $doctor->awards ? json_decode($doctor->awards, 1) : [],
                        'MainVideo' => $doctor->main_video,
                        'MainVideoYoutubeLink' => $doctor->main_video_youtube_link,
                        'OtherVideos' => $doctor->other_videos ? json_decode($doctor->other_videos, 1) : [],
                        'ProfileImage' => $doctor->profile_image ?  url('/public/uploads/doctors/profiles/' . $doctor->profile_image)  : '',
                        'Status' => $doctor->status,
                        'Designation' => $doctor->designation,
                        'DepartmentName' => $doctor->department ? $doctor->department->department_name : '',
                    ];
                }
                $result['Result'] = [
                    'DoctorImageBaseUrl' => url('/') . '/public/uploads/doctors/profiles/',
                    'DoctorVideosBaseUrl' => url('/') . '/public/uploads/doctors/videos/',
                    'DoctorDetails' => $doctorArray,
                ];
                $result['Success'] = 'True';
                $result['Message'] = 'Details.';

                return response()->json($result);
            } else {
                $result['Result'] = [];
                $result['Success'] = 'False';
                $result['Message'] = 'Doctor id is missing.';
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
