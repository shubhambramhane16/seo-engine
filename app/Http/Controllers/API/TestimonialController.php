<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use DB;
use Response;
use Validator;
use File;

class TestimonialController extends Controller
{

    function lists(Request $request)
    {
        try {
            $array = [];
            $lists = Testimonial::where('status', 1)->get();
            if ($lists) {
                foreach ($lists as $key => $list) {
                    $array[] = [
                        'Rating' => $list->rating,
                        'Comments' => $list->comments,
                        'Name' => $list->name,
                        'ProfileImage' => $list->profile_image ? url('/') . '/public/uploads/testimonials/profile_images/' . $list->profile_image : url('/') . '/public/media/no_person.png',
                        'TestimonialType' => $list->testimonial_type,
                        'VideoUrl' => $list->video_url,
                        'Content' => $list->content,
                        'Segment' => $list->segment,
                        'Gender' => $list->gender,
                    ];
                }
                $result['Result'] = $array;
                $result['Success'] = 'True';
                $result['Message'] = 'list.';
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

    function submitEnquiry(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'Name' => 'required',
                'Mobile' => 'required',
                'Email' => 'required',
                'Message' => 'required',
                'CityId' => 'required',
            ], [
                'Name.required' => 'Name is required.',
                'Mobile.required' => 'Mobile number is required.',
                'Email.required' => 'Email address is required.',
                'CityId.required' => 'Select your city.',
            ]);
            if ($validator->fails()) {
                $result['Result'] = ['error' => $validator->errors()];
                $result['Success'] = 'Failed';
                $result['Message'] = 'Fields are missing.';
                return response()->json($result);
            } else {
                $array = [
                    'customer_name' => $request->Name,
                    'customer_mobile' => $request->Mobile,
                    'customer_email' => $request->Email,
                    'message' => $request->Message,
                    'city_id' => $request->CityId,
                    'type' => 2,
                ];
                Query::create($array);
                $result['Result'] = (object) [];
                $result['Success'] = 'True';
                $result['Message'] = 'Thank you for contacting us, we will contact you soon.';
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
