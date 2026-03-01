<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PgOptions;
use DB;
use Response;
use Validator;

class PGOptionsController extends Controller
{

    function pgoptionsLists(Request $request)
    {
        try {
            $lists  = PgOptions::where('status', 1)->orderBy('option_name', 'asc')->get();
            $PgOptionsArray = [];
            if ($lists) {
                foreach ($lists as $tKey => $list) {
                    $PgOptionsArray[] = [
                        'Id' => $list->id,
                        'OptionName' => $list->option_name,
                        'Icon' => $list->option_icon ? url('public/uploads/pgoptions/' . $list->option_icon) : '',

                        'Status' => $list->status
                    ];
                }
            }
            $result['Result'] = [
                'PgOptions' => $PgOptionsArray,
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
    function pgoptionsDetails(Request $request, $specilityId)
    {
        try {

            if ($specilityId) {
                $details  = PgOptions::where('status', 1)->where('id', $specilityId)->first();
                $detailsArray = [];
                if ($details) {
                    $detailsArray = [
                        'Id' => $details->id,
                        'OptionName' => $details->option_name,
                        'Icon' => $details->option_icon ? url('public/uploads/pgoptions/' . $details->option_icon) : '',
                        'Status' => $details->status
                    ];
                    $result['Result'] = [
                        'PgOptions' => $detailsArray,
                    ];
                    $result['Success'] = 'True';
                    $result['Message'] = 'details.';
                } else {
                    $result['Result'] = (object) [];
                    $result['Success'] = 'False';
                    $result['Message'] = 'PgOption details not found.';
                }


                return response()->json($result);
            } else {
                $result['Result'] =  (object) [];
                $result['Success'] = 'False';
                $result['Message'] = 'PgOption id is missing.';
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
