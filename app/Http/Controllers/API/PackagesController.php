<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PathologyPackages as Packages;
use App\Models\Faq as Faqs;
use App\Models\PathologyTest as Tests;
// use DB;
// use Response;
// use Validator;
// use File;

class PackagesController extends Controller
{
    function list(Request $request)
    {
        try {

            $subCategories = request('SubCategories');
            $offset = request('Offset');
            $limit = request('Limit');
            $searchTerm = request('SearchTerm');
            if (!$limit)
                $limit = 20;
            $lists  = Packages::where('status', 1)
                ->when($subCategories, function ($data) use ($subCategories) {
                    $subCategoriesArray = explode(',', $subCategories);
                    $condition = '';
                    if (count($subCategoriesArray) > 0) {
                        foreach ($subCategoriesArray as  $key =>  $subCategory) {
                            if ($key == 0) {
                                $condition .= ' ( ';
                            } else {
                                $condition .= ' OR ';
                            }
                            $condition .= ' FIND_IN_SET(' . $subCategory . ',sub_category_ids) ';
                            if (count($subCategoriesArray) - 1 ==  $key) {
                                $condition .= ' ) ';
                            }
                        }
                    }
                    $data->whereRaw($condition);
                })->when($searchTerm, function ($data) use ($searchTerm) {
                    $data->whereRaw("(package_name like '%" . $searchTerm . "%' OR package_code like '%" . $searchTerm . "%' )");
                })
                ->orderByRaw(" ISNULL(priority_sequence), priority_sequence ASC ")
                ->offset($offset)
                ->limit($limit)
                ->get();
            $totalPackages  = Packages::where('status', 1)
                ->when($subCategories, function ($data) use ($subCategories) {
                    $subCategoriesArray = explode(',', $subCategories);
                    $condition = '';
                    if (count($subCategoriesArray) > 0) {
                        foreach ($subCategoriesArray as  $key =>  $subCategory) {
                            if ($key == 0) {
                                $condition .= ' ( ';
                            } else {
                                $condition .= ' OR ';
                            }
                            $condition .= ' FIND_IN_SET(' . $subCategory . ',sub_category_ids) ';
                            if (count($subCategoriesArray) - 1 ==  $key) {
                                $condition .= ' ) ';
                            }
                        }
                    }
                    $data->whereRaw($condition);
                })->when($searchTerm, function ($data) use ($searchTerm) {
                    $data->whereRaw("(package_name like '%" . $searchTerm . "%' OR package_code like '%" . $searchTerm . "%' )");
                })
                ->count();
            $array = [];
            if ($lists) {
                foreach ($lists as $tKey => $tList) {
                    $array[] = [
                        'Id' => $tList->id,
                        'Slug' => $tList->slug,
                        'PackageName' => $tList->package_name,
                        'PackageCode' => $tList->package_code,
                        'LabName' => $tList->lab_name,
                        'ComponentCount' => $tList->component_count,
                        'Recommendation' => $tList->recommendation,
                        'AgeGroup' => $tList->age_group,
                        'MRP' => $tList->mrp,
                        'SellingPrice' => $tList->selling_price,
                        'CitywisePrices' => $tList->citywise_prices ? json_decode($tList->citywise_prices, 1) : [],
                        'Description' => $tList->description,
                        'ShowOnTop' => $tList->show_ontop,
                        'PrioritySequence' => $tList->priority_sequence,
                        'Components' => $tList->components  ? json_decode($tList->components, 1) : [],
                        'ReportTAT' => $tList->report_tat,

                        'StateId' => $tList->state_id,
                        'StateName' => $tList->state_name,
                        'CityId' => $tList->city_id,
                        'CityName' => $tList->city_name,
                        'SampleType' => $tList->sample_type,
                        'Gender' => $tList->gender,
                        'Banner' => $tList->banner ? url('/') . '/public/uploads/packages/' . $tList->banner : '',

                        'Status' => $tList->status,
                        'SampleReport' => $tList->sample_report ? url('/') . '/public/uploads/sample/' . $tList->sample_report  : null,
                    ];
                }
            }
            $result['Result'] = [
                'Packages' => $array,
                'TotalPackages' => $totalPackages,
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
    function details(Request $request, $slug)
    {
        try {
            if ($slug) {
                $details  = Packages::with(['departmentData'])->where('status', 1)->where('slug', $slug)->first();
                $components = [['title' => $details->components]];
                if ($details->components) {
                    $componentsA = json_decode($details->components, 1);

                    if (is_array($componentsA)) {
                        $components = $componentsA;
                    }
                }
                //  dd($componentsA);
                $array = [];
                if ($details) {
                    $department =  $details->department;
                    if (!$department) {
                        if (isset($details->departmentData) && !empty($details->departmentData)) {
                            $department = $details->departmentData->department_name;
                        }
                    }
                    $array = [
                        'Id' => $details->id,
                        'Slug' => $details->slug,
                        'PackageName' => $details->package_name,
                        'PackageCode' => $details->package_code,
                        'LabName' => $details->lab_name,
                        'ComponentCount' => $details->component_count,
                        'Recommendation' => $details->recommendation,
                        'AgeGroup' => $details->age_group,
                        'MRP' => $details->mrp,
                        'SellingPrice' => $details->selling_price,

                        'Description' => $details->description,
                        'ShowOnTop' => $details->show_ontop,
                        'PrioritySequence' => $details->priority_sequence,
                        'Components' => $components,
                        'Tests' => $details->tests  ? json_decode($details->tests, 1) : [],
                        'ReportTAT' => $details->report_tat,

                        'StateId' => $details->state_id,
                        'StateName' => $details->state_name,
                        'CityId' => $details->city_id,
                        'CityName' => $details->city_name,
                        'Banner' => $details->banner ? url('/') . '/public/uploads/packages/' . $details->banner : '',

                        'Status' => $details->status,
                        'SampleType' => $details->sample_type,
                        'Gender' => $details->gender,
                        'Department' => $department,
                        'Technique' => $details->technique,
                        'Temperature' => $details->temperature,
                        'SampleRemarks' => $details->sample_remarks,
                        'CutOffTime' => $details->cut_off,
                        'BillingCategory' => $details->billing_category,
                        'Schedule' => $details->schedule,
                        'SampleReport' => $details->sample_report ? url('/') . '/public/uploads/sample/' . $details->sample_report  : null,
                        'CitywisePrices' => $details->citywise_prices ? json_decode($details->citywise_prices, 1) : [],
                    ];
                    $faqIds =   $details->faqs_ids ?   json_decode($details->faqs_ids, 1) : [];
                    if (count($faqIds) > 0) {
                        $faqs = Faqs::whereIn('id', $faqIds)->get();
                        $array['Faqs'] =  $faqs;
                    }
                    $result['Result'] = [
                        'Details' => $array,
                    ];
                    $result['Success'] = 'True';
                    $result['Message'] = 'Details.';
                } else {

                    $result['Result'] = [];
                    $result['Success'] = 'False';
                    $result['Message'] = 'No result found.';
                }


                return response()->json($result);
            } else {
                $result['Result'] = [];
                $result['Success'] = 'False';
                $result['Message'] = 'Package id is missing.';
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
    function detailsById(Request $request, $id)
    {
        try {
            if ($id) {
                $details  = Packages::where('status', 1)->where('id', $id)->first();
                $array = [];
                if ($details) {
                    $array = [
                        'Id' => $details->id,
                        'Slug' => $details->slug,
                        'PackageName' => $details->package_name,
                        'PackageCode' => $details->package_code,
                        'LabName' => $details->lab_name,
                        'ComponentCount' => $details->component_count,
                        'Recommendation' => $details->recommendation,
                        'AgeGroup' => $details->age_group,
                        'MRP' => $details->mrp,
                        'SellingPrice' => $details->selling_price,

                        'Description' => $details->description,
                        'ShowOnTop' => $details->show_ontop,
                        'PrioritySequence' => $details->priority_sequence,
                        'Components' => $details->components  ? json_decode($details->components, 1) : [],
                        'Tests' => $details->tests  ? json_decode($details->tests, 1) : [],
                        'ReportTAT' => $details->report_tat,

                        'StateId' => $details->state_id,
                        'StateName' => $details->state_name,
                        'CityId' => $details->city_id,
                        'CityName' => $details->city_name,
                        'Banner' => $details->banner ? url('/') . '/public/uploads/packages/' . $details->banner : '',

                        'Status' => $details->status,
                        'SampleType' => $details->sample_type,
                        'Gender' => $details->gender,
                        'SampleReport' => $details->sample_report ? url('/') . '/public/uploads/sample/' . $details->sample_report  : null,
                        'CitywisePrices' => $details->citywise_prices ? json_decode($details->citywise_prices, 1) : [],
                    ];
                    $result['Result'] = [
                        'Details' => $array,
                    ];
                    $result['Success'] = 'True';
                    $result['Message'] = 'Details.';
                } else {

                    $result['Result'] = [];
                    $result['Success'] = 'False';
                    $result['Message'] = 'No result found.';
                }

                return response()->json($result);
            } else {
                $result['Result'] = [];
                $result['Success'] = 'False';
                $result['Message'] = 'Package id is missing.';
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
    function packageComponents(Request $request)
    {
        try {
            $testIds = request('TestIds');
            $testIdsArray  = [];
            if ($testIds) {
                $testIdsArray = explode(',',  $testIds);
            }
            if ($testIdsArray) {
                $lists = Tests::where('status', 1)->whereIn('id', $testIdsArray)->get();
                $array = [];
                if ($lists) {
                    foreach ($lists as $key => $details) {
                        $array[] = [
                            'Id' => $details->id,
                            'Name' => $details->test_name,
                            'Code' => $details->test_code,
                            'Components' => $details->components ? json_decode($details->components) : [],
                        ];
                    }
                    $result['Result'] = [
                        'Lists' => $array,
                    ];
                    $result['Success'] = 'True';
                    $result['Message'] = '';
                } else {

                    $result['Result'] = [];
                    $result['Success'] = 'False';
                    $result['Message'] = 'No result found.';
                }
            } else {
                $result['Result'] = [];
                $result['Success'] = 'False';
                $result['Message'] = 'No result found.';
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
