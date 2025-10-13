<?php

use Aws\S3\S3Client;
function getAge($dob)
{
    if (!empty($dob)) {
        $birthdate = new DateTime($dob);
        $today   = new DateTime('today');
        $age = $birthdate->diff($today)->y;
        return $age;
    } else {
        return 0;
    }
}

function getDOBFromAge($age)
{
    if (!empty($age)) {

        return date('Y-m-d', strtotime('-' . $age . ' years'));
    } else {
        return 0;
    }
}
function displayDate($dateTime)
{
    if ($dateTime) {
        $date = date('d-m-Y',  strtotime($dateTime));
        $date = date('d-m-Y',   strtotime($date));
        return $date;
    }
}
// function displayDate($dateTime)
// {
//     if ($dateTime) {
//         $date = date('d-m-Y', strtotime("+5 hours", strtotime($dateTime)));
//         $date = date('d-m-Y', strtotime("+30 minutes",  strtotime($date)));
//         return $date;
//     }
// }
function displayDateTime($dateTime)
{
    if ($dateTime) {
        $date = date('d-m-Y h:i A', strtotime($dateTime));
        $date = date('d-m-Y h:i A', strtotime($date));
        return $date;
    }
}
function displayDateTime2($dateTime)
{
    if ($dateTime) {
        $date = date('d-m-Y h:i A', strtotime($dateTime));
        $date = date('d-m-Y h:i A', strtotime($date));
        return $date;
    }
}
function runTimeSelection($myId, $matchId)
{
    if ($myId == $matchId)
        return 'selected';
}
function runTimeChecked($myId, $matchId)
{
    if ($myId == $matchId)
        return 'checked';
}
function getAllStates()
{
    return 'App\Models\State'::all();
}
function getAllFaqs()
{
    return 'App\Models\Faq'::where('status', 1)->get();
}
function getTestFaqs()
{
    return 'App\Models\Faq'::where('status', 1)->where('faq_type', 3)->get();
}
function getPackageFaqs()
{
    return 'App\Models\Faq'::where('status', 1)->where('faq_type', 2)->get();
}
function getOrderStatus($statusId)
{
    $data = 'App\Models\OrderStatus'::where('id', $statusId)->first();
    return $data ?  $data->status_title : 'NA';
}
function getPaymentStatus()
{
    return [
        "1" => 'Credit',
        "2" => 'Debit'
    ];
}
function getUserName($id)
{
    $data = 'App\Models\User'::where('id', $id)->first();
    return $data ?  $data->name : 'NA';
}
function getCentreName($id)
{
    $data = App\Models\Centre::where('id', $id)->first();
    return $data ?  $data->centre_name : 'NA';
}
function getUserIdByEmail($email)
{
    $data = 'App\Models\Customer'::where('email_id', $email)->first();
    return $data ?  $data->id : null;
}
function getAllOrderStatus()
{
    return 'App\Models\OrderStatus'::get();
}
function getStateName($stateId)
{
    $data = 'App\Models\State'::where('id', $stateId)->first();
    return $data ? $data->name : null;
}
function getStateIdByCityId($cityId)
{
    $data = 'App\Models\City'::where('id', $cityId)->first();
    return $data ? $data->state_id : null;
}
function getLocality($cityId)
{
    $data = 'App\Models\Locality'::where(['city_id' => $cityId, 'status' => 1])->get();
    return $data ?? array();
}
function getAllCities()
{
    $data = 'App\Models\City'::where('status', 1)->get();
    return $data ? $data : null;
}
function getCityListArray()
{
    $data = 'App\Models\City'::where('status', 1)->pluck('name', 'id')->all();
    return $data ? $data : null;
}
function getcategoriesids($names)
{
    if (trim($names)) {
        $namesPlace = explode(',', $names);
        if (count($namesPlace) > 0) {
            $sqlStr = " ( ";
            foreach ($namesPlace as $key => $list) {
                if ($key == 0) {
                    $_or = "";
                } else {
                    $_or = " OR ";
                }
                $sqlStr .= $_or . " category_name like '%" . trim(mb_strtolower($list)) . "%' ";
            }
            $sqlStr .= " ) ";
            $data = 'App\Models\Category'::where('parent_id', 0)
                ->whereRaw($sqlStr)
                ->pluck('id');
            return $data ? json_encode($data) : null;
        }
    }
}
function getCategoriesName($arr)
{
    if (count($arr) > 0) {
        $data = 'App\Models\Category'::whereIn('id', $arr)
            ->pluck('category_name')->toArray();
        return (count($data) > 0) ? implode(', ', $data) : null;
    }
}
function getsubcategoriesids($names)
{
    if (trim($names)) {
        $namesPlace = explode(',', $names);
        if (count($namesPlace) > 0) {
            $sqlStr = " ( ";
            foreach ($namesPlace as $key => $list) {
                if ($key == 0) {
                    $_or = "";
                } else {
                    $_or = " OR ";
                }
                $sqlStr .= $_or . " category_name like '%" . trim(mb_strtolower($list)) . "' ";
            }
            $sqlStr .= " ) ";
            $data = 'App\Models\Category'::where('parent_id', '<>', 0)
                ->whereRaw($sqlStr)
                ->pluck('id');
            return $data ? json_encode($data) : null;
        }
    }
}
function getdepartmentidsfirst($names)
{
    if (trim($names)) {
        $namesPlace = explode(',', $names);
        if (count($namesPlace) > 0) {
            return $namesPlace[0];
        }
    }
}
function getdepartmentids($names)
{
    if (trim($names)) {
        $namesPlace = explode(',', $names);
        if (count($namesPlace) > 0) {
            $sqlStr = " ( ";
            foreach ($namesPlace as $key => $list) {
                if ($key == 0) {
                    $_or = "";
                } else {
                    $_or = " OR ";
                }
                $sqlStr .= $_or . " department_name like '%" . trim(mb_strtolower($list)) . "' ";
            }
            $sqlStr .= " ) ";
            // dd( $sqlStr);
            $data = 'App\Models\Department'::whereRaw($sqlStr)
                ->pluck('id')->toArray();
            // dd( $data );
            return ($data && count($data) > 0) ? implode(',', $data) : null;
        }
    }
}
function getDepartments($arr)
{
    if (count($arr) > 0) {
        $data = 'App\Models\Department'::whereIn('id', $arr)
            ->pluck('department_name')->toArray();
        return ($data && count($data) > 0) ? implode(', ', $data) : null;
    }
}
function getSubCategories()
{
    $data = 'App\Models\Category'::where('status', 1)->where('parent_id', '<>', 0)->get();
    return $data ? $data : null;
}
function getTotalPartnerEnquires($fromToDate, $type = null)
{
    if ($fromToDate) {
        $fromToDateArr = explode(' - ', $fromToDate);
        if (count($fromToDateArr) == 2) {
            $From = date('Y-m-d H:i:s', strtotime($fromToDateArr[0]));
            $To = date('Y-m-d 23:59:59', strtotime($fromToDateArr[1]));
            $isBet = true;
        } else {
            $From = date('Y-m-d 00:00:01');
            $To = date('Y-m-d 23:59:58');
            $isBet = false;
        }
    } else {
        $From = date('Y-m-d 00:00:01');
        $To = date('Y-m-d 23:59:58');
        $isBet = false;
    }

    $data = 'App\Models\PartnerEnquiry'::when($type, function ($data) use ($type) {
        if ($type) {

            $data->where('status', '=',  $type);
        }
    })->where('created_at', '>=', $From)->where('created_at', '<', $To)->count();

    return $data ? $data : 0;
}
function getTotalPartnerEnquiresUpdated($fromToDate, $type = null)
{
    if ($fromToDate) {
        $fromToDateArr = explode(' - ', $fromToDate);
        if (count($fromToDateArr) == 2) {
            $From = date('Y-m-d H:i:s', strtotime($fromToDateArr[0]));
            $To = date('Y-m-d 23:59:59', strtotime($fromToDateArr[1]));
            $isBet = true;
        } else {
            $From = date('Y-m-d 00:00:01');
            $To = date('Y-m-d 23:59:58');
            $isBet = false;
        }
    } else {
        $From = date('Y-m-d 00:00:01');
        $To = date('Y-m-d 23:59:58');
        $isBet = false;
    }

    $data = 'App\Models\PartnerEnquiry'::when($type, function ($data) use ($type, $From,  $To) {
        if ($type) {

            $data->where('status', '=',  $type);
            $data->where('updated_at', '>=', $From)->where('updated_at', '<', $To);
        }
    })->count();

    return $data ? $data : 0;
}
function getTotalEnquires($fromToDate, $type = null)
{
    if ($fromToDate) {
        $fromToDateArr = explode(' - ', $fromToDate);
        if (count($fromToDateArr) == 2) {
            $From = date('Y-m-d H:i:s', strtotime($fromToDateArr[0]));
            $To = date('Y-m-d 23:59:59', strtotime($fromToDateArr[1]));
            $isBet = true;
        } else {
            $From = date('Y-m-d 00:00:01');
            $To = date('Y-m-d 23:59:58');
            $isBet = false;
        }
    } else {
        $From = date('Y-m-d 00:00:01');
        $To = date('Y-m-d 23:59:58');
        $isBet = false;
    }

    $data = 'App\Models\Query'::where('type', 2)->when($type, function ($data) use ($type) {
        if ($type) {
            if ($type == 'new') {
                $is_new = 0;
            } elseif ($type == 'pending') {
                $is_new = 1;
            } elseif ($type == 'converted') {
                $is_new = 2;
            }
            $data->where('is_lead_converted', '=',  $is_new);
        }
    })->where('created_at', '>=', $From)->where('created_at', '<', $To)->count();

    return $data ? $data : 0;
}
function getTotalEnquiresUpdated($fromToDate, $type = null)
{
    if ($fromToDate) {
        $fromToDateArr = explode(' - ', $fromToDate);
        if (count($fromToDateArr) == 2) {
            $From = date('Y-m-d H:i:s', strtotime($fromToDateArr[0]));
            $To = date('Y-m-d 23:59:59', strtotime($fromToDateArr[1]));
            $isBet = true;
        } else {
            $From = date('Y-m-d 00:00:01');
            $To = date('Y-m-d 23:59:58');
            $isBet = false;
        }
    } else {
        $From = date('Y-m-d 00:00:01');
        $To = date('Y-m-d 23:59:58');
        $isBet = false;
    }

    $data = 'App\Models\Query'::where('type', 2)->when($type, function ($data) use ($type) {
        if ($type) {
            if ($type == 'new') {
                $is_new = 0;
            } elseif ($type == 'pending') {
                $is_new = 1;
            } elseif ($type == 'converted') {
                $is_new = 2;
            }
            $data->where('is_lead_converted', '=',  $is_new);
        }
    })->where('updated_at', '>=', $From)->where('updated_at', '<', $To)->count();

    return $data ? $data : 0;
}
function getTotalCentres($fromToDate = null, $type = null)
{
    if ($fromToDate) {
        $fromToDateArr = explode(' - ', $fromToDate);
        if (count($fromToDateArr) == 2) {
            $From = date('Y-m-d H:i:s', strtotime($fromToDateArr[0]));
            $To = date('Y-m-d 23:59:59', strtotime($fromToDateArr[1]));
            $isBet = true;
        } else {
            $From = date('Y-m-d 00:00:01');
            $To = date('Y-m-d 23:59:58');
            $isBet = false;
        }
    } else {
        $From = date('Y-m-d 00:00:01');
        $To = date('Y-m-d 23:59:58');
        $isBet = false;
    }

    $data = 'App\Models\Centre'::when($type, function ($data) use ($type) {
        if ($type) {
            if ($type == 'active') {
                $is_new = 1;
            } elseif ($type == 'inactive') {
                $is_new = 0;
            }
            $data->where('status', '=',  $is_new);
        }
    })->when($isBet, function ($data) use ($isBet, $From, $To) {
        if ($isBet) {
            $data->where('created_at', '>=', $From)->where('created_at', '<', $To);
        }
    })->count();

    return $data ? $data : 0;
}
function getTotalDoctors($fromToDate = null, $type = null)
{
    if ($fromToDate) {
        $fromToDateArr = explode(' - ', $fromToDate);
        if (count($fromToDateArr) == 2) {
            $From = date('Y-m-d H:i:s', strtotime($fromToDateArr[0]));
            $To = date('Y-m-d 23:59:59', strtotime($fromToDateArr[1]));
            $isBet = true;
        } else {
            $From = null;
            $To = null;
            $isBet = false;
        }
    } else {
        $From = null;
        $To = null;
        $isBet = false;
    }

    $data = 'App\Models\Doctor'::when($type, function ($data) use ($type) {
        if ($type) {
            if ($type == 'active') {
                $is_new = 1;
            } elseif ($type == 'inactive') {
                $is_new = 0;
            }
            $data->where('status', '=',  $is_new);
        }
    })->when($isBet, function ($data) use ($isBet, $From, $To) {
        if ($isBet) {
            $data->where('created_at', '>=', $From)->where('created_at', '<', $To);
        }
    })->count();

    return $data ? $data : 0;
}
function getTotalQueries($fromToDate, $type = null)
{
    if ($fromToDate) {
        $fromToDateArr = explode(' - ', $fromToDate);

        if (count($fromToDateArr) == 2) {
            $From = date('Y-m-d H:i:s', strtotime($fromToDateArr[0]));
            $To = date('Y-m-d 23:59:59', strtotime($fromToDateArr[1]));
            $isBet = true;
        } else {
            $From = date('Y-m-d 00:00:01');
            $To = date('Y-m-d 23:59:59');
            $isBet = true;
        }
    } else {
        $From = null;
        $To = null;
        $isBet = false;
    }

    $data = 'App\Models\Query'::where('type', '=', 1)->when($type, function ($data) use ($type) {
        if ($type) {
            if ($type == 'active') {
                $is_new = 1;
            } elseif ($type == 'inactive') {
                $is_new = 0;
            }
            $data->where('status', '=',  $is_new);
        }
    })->when($isBet, function ($data) use ($isBet, $From, $To) {
        if ($isBet) {
            $data->where('created_at', '>=', $From)->where('created_at', '<', $To);
        }
    })->count();

    return $data ? $data : 0;
}
function getTotalOrders($fromToDate, $type = null)
{
    if ($fromToDate) {
        $fromToDateArr = explode(' - ', $fromToDate);

        if (count($fromToDateArr) == 2) {
            $From = date('Y-m-d H:i:s', strtotime($fromToDateArr[0]));
            $To = date('Y-m-d 23:59:59', strtotime($fromToDateArr[1]));
            $isBet = true;
        } else {
            $From = date('Y-m-d 00:00:01');
            $To = date('Y-m-d 23:59:59');
            $isBet = true;
        }
    } else {
        $From = null;
        $To = null;
        $isBet = false;
    }

    $data = 'App\Models\Order'::when($type, function ($data) use ($type) {
        if ($type) {
            $data->where('order_status', '=',  $type);
        }
    })->when($isBet, function ($data) use ($isBet, $From, $To) {
        if ($isBet) {
            $data->where('created_at', '>=', $From)->where('created_at', '<', $To);
        }
    })->count();

    return $data ? $data : 0;
}
function getTotalOrdersUpdated($fromToDate, $type = null)
{
    if ($fromToDate) {
        $fromToDateArr = explode(' - ', $fromToDate);

        if (count($fromToDateArr) == 2) {
            $From = date('Y-m-d H:i:s', strtotime($fromToDateArr[0]));
            $To = date('Y-m-d 23:59:59', strtotime($fromToDateArr[1]));
            $isBet = true;
        } else {
            $From = date('Y-m-d 00:00:01');
            $To = date('Y-m-d 23:59:59');
            $isBet = true;
        }
    } else {
        $From = null;
        $To = null;
        $isBet = false;
    }

    $data = 'App\Models\Order'::when($type, function ($data) use ($type) {
        if ($type) {
            $data->where('order_status', '=',  $type);
        }
    })->when($isBet, function ($data) use ($isBet, $From, $To) {
        if ($isBet) {
            $data->where('updated_at', '>=', $From)->where('updated_at', '<', $To);
        }
    })->count();

    return $data ? $data : 0;
}
function getSystemRoles($role = null)
{
    $data = 'App\Models\Role'::when($role, function ($data) use ($role) {
        if ($role) {
            $data->where('id', '=',  $role);
        }
    })->get();
    return $data;
}
function generateHistory($array)
{
    $data = 'App\Models\OrderHistory'::create($array);
    return $data;
}
function getFacilities()
{
    $data = 'App\Models\Facility'::get();
    return $data;
}
function getActiveTests($limit)
{
    $data = 'App\Models\PathologyTest'::when($limit, function ($data) use ($limit) {
        if ($limit) {
            $data->limit($limit);
        }
    })
        ->get();
    return $data;
}
function GenerateEnquireHistory($array)
{
    $data = 'App\Models\EnquireHistory'::insert($array);
    return $data;
}
function getJobTitle($JobId)
{
    $cateDetails = App\Models\Job::where('id', $JobId)->first();
    if ($cateDetails) {
        return  $cateDetails->job_title;
    }
}
function getCityName($cityId)
{
    $cateDetails = 'App\Models\City'::where('id', $cityId)->first();
    if ($cateDetails) {
        return  $cateDetails->name;
    }
}

function getCategoryName($catId)
{
    $cateDetails = 'App\Models\Category'::where('id', $catId)->first();
    if ($cateDetails) {
        return  $cateDetails->category_name;
    }
}
function conditionalStatus($status)
{
    if ($status == '1') {
        $status = 1;
    }
    if ($status == '2') {
        $status = 0;
    }
    return $status;
}
function weekDaysArray()
{
    $array = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    ];
    return $array;
}

function modulesList()
{
    return [
        [
            'id' => 1,
            'slug' => 'customers'
        ],
        [
            'id' => 2,
            'slug' => 'patients'
        ],
        [
            'id' => 3,
            'slug' => 'items'
        ],
        [
            'id' => 4,
            'slug' => 'packages'
        ],
        [
            'id' => 5,
            'slug' => 'city'
        ],
        [
            'id' => 6,
            'slug' => 'categories'
        ],
        [
            'id' => 7,
            'slug' => 'departments'
        ],
        [
            'id' => 8,
            'slug' => 'specialities'
        ],
        [
            'id' => 9,
            'slug' => 'pgoptions'
        ],
        [
            'id' => 10,
            'slug' => 'facility'
        ],
        [
            'id' => 11,
            'slug' => 'centres'
        ],
        [
            'id' => 12,
            'slug' => 'doctors'
        ],
        [
            'id' => 13,
            'slug' => 'events'
        ],
        [
            'id' => 14,
            'slug' => 'cme'
        ],
        [
            'id' => 15,
            'slug' => 'queries'
        ],
        [
            'id' => 16,
            'slug' => 'enquires'
        ],
        [
            'id' => 17,
            'slug' => 'faqs'
        ],
        [
            'id' => 18,
            'slug' => 'testimonials'
        ],
        [
            'id' => 19,
            'slug' => 'seo'
        ],
        [
            'id' => 20,
            'slug' => 'jobs'
        ],
        [
            'id' => 21,
            'slug' => 'job-applications'
        ],
        [
            'id' => 22,
            'slug' => 'roles'
        ],
        [
            'id' => 23,
            'slug' => 'users'
        ],
        [
            'id' => 24,
            'slug' => 'settings'
        ],
        [
            'id' => 25,
            'slug' => 'dashboard'
        ],
        [
            'id' => 26,
            'slug' => 'orders'
        ],
        [
            'id' => 27,
            'slug' => 'offers'
        ],
        [
            'id' => 28,
            'slug' => 'pressrelease'
        ],
        [
            'id' => 29,
            'slug' => 'subcategories'
        ],
        [
            'id' => 30,
            'slug' => 'faqcategories'
        ],
        [
            'id' => 31,
            'slug' => 'partnerenquiry'
        ],
        [
            'id' => 32,
            'slug' => 'newslettersubscription'
        ],
        [
            'id' => 33,

            'slug' => 'test-faqs'
        ],
        [
            'id' => 36,
            'slug' => 'package-faqs'
        ],
        [
            'id' => 37,
            'slug' => 'locality'
        ],
        [
            'id' => 38,
            'slug' => 'transaction'
        ],
        [
            'id' => 39,
            'slug' => 'rules'
        ],
        [
            'id' => 40,
            'slug' => 'templates'
        ],
        [
            'id' => 41,
            'slug' => 'state'
        ],
        [
            'id' => 42,
            'slug' => 'page'
        ],
        // [
        //     'id' => 43,
        //     'parent_id' => 0,
        //     'slug' => 'module'
        // ],
        [
            'id' => 44,
            'parent_id' => 0,
            'slug' => 'enquiry'
        ]

    ];
}

function jobApplicationStatus()
{
    $array = [
        1 => 'Pending',
        2 => 'Shortlisted',
        3 =>  'Rejected',
        4 =>  'Selected',
    ];
    return $array;
}

function getBrochures($condition = "")
{
    if ($condition) {
        return 'App\Models\Department'::select('brochures', 'is_brochures_page', 'department_name', 'id')->where($condition)->get();
    }
    return 'App\Models\Department'::select('brochures', 'is_brochures_page', 'department_name', 'id')->get();
}
function getMaxOrderLimitCityWise($cityId)
{
    if ($cityId) {
        $cityData = App\Models\City::select('max_order_limit')->where('id', $cityId)->first();
        if ($cityData) {
            return  $cityData->max_order_limit;
        }
    }
    return false;
}
function getIsCityActive($cityId)
{
    if ($cityId) {
        $cityData = App\Models\City::select('id', 'status', 'is_payment_active')->where('id', $cityId)->where('status', 1)->first();
        if ($cityData) {
            return  $cityData;
        }
    }
    return false;
}
function getOrderPlacedCountByPreferredDate($PreferredDate)
{
    if ($PreferredDate) {
        $orderCount = App\Models\Order::select('id')->where('schedule_date', $PreferredDate)->count();
        if ($orderCount) {
            return  $orderCount;
        }
    }
    return false;
}
function getPriorhoursCapping()
{

    $orderCount = App\Models\Setting::select('prior_hours_preferred_time')->first();

    return  $orderCount->prior_hours_preferred_time;
}
function getSlots($data)
{
    $currentHours = date('H');
    // dd($data);
    if (date('Y-m-d') == $data) {
        $slots = [];
        if ($currentHours >= 7 && $currentHours <= 10) {
            $slots = [
                '7.00 AM to 10.00 AM',
                '10.00 AM to 1.00 PM',
                '1.00 PM to 4.00 PM',
                '4.00 PM to 7.00 PM',
            ];
        }
        if ($currentHours >= 10 && $currentHours < 13) {
            $slots = [
                '10.00 AM to 1.00 PM',
                '1.00 PM to 4.00 PM',
                '4.00 PM to 7.00 PM',
            ];
        }
        if ($currentHours >= 13 && $currentHours < 16) {
            $slots = [
                '1.00 PM to 4.00 PM',
                '4.00 PM to 7.00 PM',
            ];
        }
        if ($currentHours >= 16 && $currentHours < 19) {
            $slots = [
                '4.00 PM to 7.00 PM',
            ];
        }
    } else {
        $slots = [
            '7.00 AM to 10.00 AM',
            '10.00 AM to 1.00 PM',
            '1.01 PM to 4.00 PM',
            '4.00 PM to 7.00 PM',
        ];
    }
    return     $slots;
}
function bookingValidation($request)
{

    $orderLimitPerDay = null;
    $orderPlacedCount = null;
    $isSlotAvaiable = false;
    /**
     * Validate per order limit citywise
     */
    /**
     * Get max order limit by cityid
     */
    if ($request['CityId']) {
        $orderLimitPerDay = getMaxOrderLimitCityWise($request['CityId']);
        /**
         * get order placed preferred date
         */
        $orderPlacedCount = getOrderPlacedCountByPreferredDate($request['PreferredDate']);
        /**
         * Service Avaibale in the user selected address city or main city
         * is city active.
         */
        $isCityActive = getIsCityActive($request['CityId']);
    }

    /**
     * Preferred time slot Capping
     */
    /**
     * Get prior hours
     */
    $priorHoursCapping = getPriorhoursCapping();
    /**
     * Validating / capping prior hours
     */
    $slots = getSlots($request['PreferredDate']);
    if (($request['PreferredDate'] != date('Y-m-d'))) {
        $isSlotAvaiable = true;
    }
    if ($slots && ($request['PreferredDate'] == date('Y-m-d')) && isset($request['PreferredTime']) && !empty($request['PreferredTime']) && $request['PreferredTime'] != '4.00 PM to 7.00 PM') {
        if (in_array(($request['PreferredTime']), $slots)) {
            $currentTime = date('H');
            $explodeSlot = explode(' to ', $request['PreferredTime']);
            $time = date('H', strtotime($explodeSlot[1])) - $priorHoursCapping;
            if ($currentTime <  $time) {
                $isSlotAvaiable = true;
            }
        }
    }




    $isPaymentActive = ($isCityActive && $isCityActive->is_payment_active == 1) ? true : false;
    $message = 'User can order.';
    $isServiceActive = ($isCityActive && $isCityActive->status == 1) ? true : false;
    if (!$isServiceActive) {
        $message = 'Currently service not avaiable in selected city.';
    }
    $status_  = $orderLimitPerDay >= $orderPlacedCount ? true : false;
    if (!$status_) {
        $message = 'No more slots avaiable on selected preferred date: ' . $request['PreferredDate'] . ', kindly choose another home collection date.';
    }
    $result['Result'] = [
        'orderStatusInCity' => [
            'isServiceActive' =>  $isServiceActive,
            'isPaymentActive' =>  $isPaymentActive,
            'orderLimitPerDay' => $orderLimitPerDay,
            'orderPlacedCount' => $orderPlacedCount,
            'status' => $status_,
            'message' => $message
        ],
        'currentHours' => date('H'),
        'priorHoursCapping' => $priorHoursCapping,
        'priorHoursCappingStatus' =>  $isSlotAvaiable
    ];
    $result['Success'] = 'True';
    $result['Message'] = '';
    return $result;
}



function uploadFileAwsBucket($filePath, $requestedFile)
{
    try {
        if ($filePath && $requestedFile) {
            $path = Storage::disk('s3')->put('AWS/' . $filePath, $requestedFile, 'public');
            // $path = Storage::disk('s3')->url($path);
            return   $path;
        }
        return false;
    } catch (Exception $error) {
        dd($error);
    }
}

function uploadFileAwsBucketSdk($request)
{
    try {
        
        $awsCredentials = [
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
        ];

        // Initialize the S3 client
        $s3 = new S3Client($awsCredentials);

        $bucket = env('AWS_BUCKET');
        $fileOrgName = $request->file('file')->getClientOriginalName();
        $filePathInS3 = 'AWS/seo-engine/' . md5($fileOrgName . '-' . date('YYYY-MM-DD HH:ii:ss')) . '.jpg';

        $localFilePath = $request->file('file');


        // Upload the image to the S3 bucket
        $result = $s3->putObject([
            'Bucket' => $bucket,
            'Key' => $filePathInS3,
            'Body' => fopen($localFilePath, 'rb'),
            // 'ACL'    => 'public-read', // Optionally, set the ACL to make the object publicly accessible
        ]);

        if ($result['@metadata']['statusCode'] === 200) {
           return $s3UploadRes = 'https://media.lalpathlabs.com/' . $filePathInS3;
        }
    } catch (Exception $error) {
        dd($error);
    }
}



function CallPaymentRefund($Payload)
{
    try {
        if ($Payload) {
            // dd($Payload);
            generateLog('CallPaymentRefund - Payload', $Payload);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('UnifiedPaymentGatewayUrl') . '/api/v1/payment/refund',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; rv:19.0) Gecko/20100101 Firefox/19.0",
                CURLOPT_POSTFIELDS => json_encode($Payload),
                CURLOPT_HTTPHEADER => array(
                    'Token: 6f905f5fc8577f0b4baee213e2f43f46',
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            // if (curl_errno($curl)) {
            //     generateLog('CallPaymentRefund - cURL Error', curl_error($curl));
            // }
            if ($response) {
                $response = json_decode($response, 1);
                generateLog('CallPaymentRefund - cURL Response IN', $response);
                return  $response;
            }
            generateLog('CallPaymentRefund - cURL Response Out', $response);
            return 500;
        }
    } catch (Exception $e) {
        generateLog('CallPaymentRefund - catch Error', $e);
    }
}
function CancelBookingCRM($PID, $bookingId)
{
    if ($PID && $bookingId) {
        $Payload = [
            "pid" => $PID,
            "id" => $bookingId
        ];
        // dd( $Payload);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            // CURLOPT_URL => 'https://ollcrm.thecustombuild.com/website/api/cancelBooking',
            // CURLOPT_URL => 'https://crm-backend.thecustombuild.com/website/api/cancelBooking',
            CURLOPT_URL => env('CRM_URL') . 'cancelBooking',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($Payload),
            CURLOPT_HTTPHEADER => array(
                'x-token: 650a9655c288d650a966282b97650a96689a362',
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        // if (curl_errno($curl)) {
        //     echo 'cURL Error: ' . curl_error($curl);
        //     /**
        //      * Log errors in text file
        //      */
        // }

        if ($response) {
            $response = json_decode($response, 1);

            if ($response['error'] == true) {
                return $response['message'];
            } else {
                return true;
            }
        }
        return false;
    }
}
function CrmBookingAPI($bookingData)
{
    if ($bookingData) {
        $Payload = $bookingData;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            // CURLOPT_URL => 'https://ollcrm.thecustombuild.com/website/api/createBooking',
            // CURLOPT_URL => 'https://crm-backend.thecustombuild.com/website/api/createBooking',
            CURLOPT_URL => env('CRM_URL') . 'createBooking',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($Payload),
            CURLOPT_HTTPHEADER => array(
                'x-token: 650a9655c288d650a966282b97650a96689a362',
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        // if (curl_errno($curl)) {
        //     // echo 'cURL Error: ' . curl_error($curl);
        //     // die;

        //     /**
        //      * Log errors in text file
        //      */
        // }
        generateLog('CrmBookingAPI- URL: ' . env('CRM_URL') . 'createBooking', ['Request' => $Payload, 'Response' => $response]);
        if ($response) {
            $response = json_decode($response, 1);
            if ($response['error'] == false) {
                return  $response['data']['results']['id'];
            } else {
                return false;
            }
        }
        return false;
    }
}

function UpdateCrmBookingAPI($bookingData)
{
    if ($bookingData) {
        $Payload = $bookingData;
        // dd($Payload);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            // CURLOPT_URL => 'https://ollcrm.thecustombuild.com/website/api/createBooking',
            // CURLOPT_URL => 'https://crm-backend.thecustombuild.com/website/api/createBooking',
            CURLOPT_URL => env('CRM_URL') . 'updateBooking',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($Payload),
            CURLOPT_HTTPHEADER => array(
                'x-token: 650a9655c288d650a966282b97650a96689a362',
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        // if (curl_errno($curl)) {
        //     echo 'cURL Error: ' . curl_error($curl);
        //     die;

        //     /**
        //      * Log errors in text file
        //      */
        // }
        // echo $response; die;

        generateLog('UpdateCrmBookingAPI- URL: ' . env('CRM_URL') . 'updateBooking', ['Request' => $Payload, 'Response' => $response]);
        if ($response) {
            $response = json_decode($response, 1);
            if ($response['error'] == false) {
                return  true;
            } else {
                return false;
            }
        }
        return false;
    }
}

function CurlCall($url,  $method, $post = null)
{
	//for temporary 
    $curl = curl_init();
    // $url = env('CRM_URL').'createPatient';
    $data_string = json_encode($post);
    $headers = [];
    $headers[] = 'Content-Type:application/json';
    $token =  env('CRM_TOKEN');
    $headers[] = "x-token:" . $token;

    if ($method == 'POST') {
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_URL => str_replace(' ', '+', $url),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $data_string
        ));
    } else {
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_URL => str_replace(' ', '+', $url),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $headers
        ));
    }


    $response = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($response);
    return $res;
}
function CrmBookingHistory($data)
{
    $curl = curl_init();
    $url = env('CRM_URL') . 'bookingHistory';
    $data_string = json_encode([
        'pid' => $data['PId']
    ]);
    $headers = [];
    $headers[] = 'Content-Type:application/json';
    $token =  env('CRM_TOKEN');
    $headers[] = "x-token:" . $token;
    curl_setopt_array($curl, array(
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => $data_string
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($response);
    return $res;
}

function CrmBookingDetails($data)
{
    $curl = curl_init();
    $url = env('CRM_URL') . 'bookingDetails';
    $data_string = json_encode([
        'pid' => $data['PId'],
        'id' => $data['Id'],
    ]);
    $headers = [];
    $headers[] = 'Content-Type:application/json';
    $token =  env('CRM_TOKEN');
    $headers[] = "x-token:" . $token;
    curl_setopt_array($curl, array(
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => $data_string
    ));

    $response = curl_exec($curl);
    // if (curl_errno($curl)) {
    //     $error_msg = curl_error($curl);
    //     generateLog('CrmBookingDetails- URL: ' . $url, $error_msg);
    // }
    curl_close($curl);
    $res = json_decode($response);
    generateLog('CrmBookingDetails- URL: ' . $url, ['Request' => $data_string, 'Response' => $res]);
    return $res;
}
function generateLog($method, $text)
{
    try {
        $logMessage = json_encode($text);
        // The path to the log file

        // Get the current timestamp
        $timestamp = date("Y-m-d H:i:s");
        $timestamp2 = date("Y-m-d-H");
        $filename = "log_$timestamp2.txt";
        // Create the log entry with timestamp
        $logEntry = "[$timestamp] - $method \n $logMessage" . PHP_EOL;
        // Use file_put_contents to append the log entry to the file
        // file_put_contents('/var/www/html/admin/preprod/'.$filename, $logEntry, FILE_APPEND);
        $file = fopen(public_path() . '/' . 'logs/' . $filename, "a");
        // Check if the log entry was written successfully
        fwrite($file, $logEntry);
        fclose($file);
    } catch (Exception $error) {
    }
}
function CustomerActivityLog($array)
{
    $data = App\Models\CustomerActivityLog::insert($array);
    return true;
}
function isDateInRange($dateToCheck, $startDate, $endDate)
{
    $dateToCheck = new DateTime($dateToCheck);
    $startDate = new DateTime($startDate);
    $endDate = new DateTime($endDate);

    return $dateToCheck >= $startDate && $dateToCheck <= $endDate;
}
function getPastDate($days)
{
    $currentDate = new DateTime();
    // Subtract 90 days
    $currentDate->sub(new DateInterval('P' . $days . 'D'));

    // Format the result
    return $result = $currentDate->format('Y-m-d');
}

function getStateIdByName($state)
{
    $data = 'App\Models\State'::where('name', 'like', '%' . $state . '%')->first();
    return $data ? $data->id : null;
}

function getCityIdByName($city)
{
    $data = 'App\Models\City'::where('name', 'like', '%' . $city . '%')->first();
    return $data ? $data->id : null;
}
function CRMStateId($stateId = null)
{
    $stateArray = '[
        {
            "tag": "1",
            "title": "Andaman and Nicobar Islands"
        },
        {
            "tag": "2",
            "title": "Andhra Pradesh"
        },
        {
            "tag": "3",
            "title": "Arunachal Pradesh"
        },
        {
            "tag": "4",
            "title": "Assam"
        },
        {
            "tag": "5",
            "title": "Bihar"
        },
        {
            "tag": "6",
            "title": "Chandigarh"
        },
        {
            "tag": "7",
            "title": "Chhattisgarh"
        },
        {
            "tag": "8",
            "title": "Dadra and Nagar Haveli"
        },
        {
            "tag": "9",
            "title": "Daman and Diu"
        },
        {
            "tag": "10",
            "title": "Delhi"
        },
        {
            "tag": "11",
            "title": "Goa"
        },
        {
            "tag": "12",
            "title": "Gujarat"
        },
        {
            "tag": "13",
            "title": "Haryana"
        },
        {
            "tag": "14",
            "title": "Himachal Pradesh"
        },
        {
            "tag": "15",
            "title": "Jammu and Kashmir"
        },
        {
            "tag": "16",
            "title": "Jharkhand"
        },
        {
            "tag": "17",
            "title": "Karnataka"
        },
        {
            "tag": "18",
            "title": "Kenmore"
        },
        {
            "tag": "19",
            "title": "Kerala"
        },
        {
            "tag": "20",
            "title": "Lakshadweep"
        },
        {
            "tag": "21",
            "title": "Madhya Pradesh"
        },
        {
            "tag": "22",
            "title": "Maharashtra"
        },
        {
            "tag": "23",
            "title": "Manipur"
        },
        {
            "tag": "24",
            "title": "Meghalaya"
        },
        {
            "tag": "25",
            "title": "Mizoram"
        },
        {
            "tag": "26",
            "title": "Nagaland"
        },
        {
            "tag": "27",
            "title": "Narora"
        },
        {
            "tag": "28",
            "title": "Natwar"
        },
        {
            "tag": "29",
            "title": "Odisha"
        },
        {
            "tag": "30",
            "title": "Paschim Medinipur"
        },
        {
            "tag": "31",
            "title": "Pondicherry"
        },
        {
            "tag": "32",
            "title": "Punjab"
        },
        {
            "tag": "33",
            "title": "Rajasthan"
        },
        {
            "tag": "34",
            "title": "Sikkim"
        },
        {
            "tag": "35",
            "title": "Tamil Nadu"
        },
        {
            "tag": "36",
            "title": "Telangana"
        },
        {
            "tag": "37",
            "title": "Tripura"
        },
        {
            "tag": "38",
            "title": "Uttar Pradesh"
        },
        {
            "tag": "39",
            "title": "Uttarakhand"
        },
        {
            "tag": "40",
            "title": "Vaishali"
        },
        {
            "tag": "41",
            "title": "West Bengal"
        }
    ]';
    $stateArray = json_decode($stateArray, 1);
    if ($stateId) {
        if ($stateArray) {
            $stateData = null;
            foreach ($stateArray  as $key => $list) {
                if ($list['tag'] == $stateId) {
                    /**
                     * Get stateid from db
                     */
                    $stateData = getStateIdByName($list['title']);
                }
            }
            if ($stateData) {
                return  $stateData;
            }
        }
    } else {
        return  $stateArray;
    }
}

function CRMCityData($stateId)
{
    $curl = curl_init();
    $url = env('CRM_URL') . 'getCityList';
    // dd( env(''));
    $data_string = json_encode([
        'state_id' => $stateId
    ]);
    $headers = [];
    $headers[] = 'Content-Type:application/json';
    $token =  env('CRM_TOKEN');
    $headers[] = "x-token:" . $token;
    curl_setopt_array($curl, array(
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => $data_string
    ));

    $response = curl_exec($curl);
    // if (curl_errno($curl)) {
    //     $error_msg = curl_error($curl);
    //     // dd($error_msg);
    //     generateLog('CRMCityData- URL: ' . $url, $error_msg);
    // }
    curl_close($curl);
    $res = json_decode($response, 1);
    generateLog('CRMCityData- URL: ' . $url, ['Request' => $data_string, 'Response' => $res]);
    return $res;
}
function CRMCityId($stateId, $cityId)
{
    $CityId = null;
    if ($stateId && $cityId) {
        $cityData = CRMCityData($stateId);
        // dd( $cityData);
        if ($cityData['error'] == false && count($cityData['data']['results']) > 0) {
            foreach ($cityData['data']['results'] as $key =>  $list) {
                if ($list['tag'] == $cityId && $list['state_id'] == $stateId) {
                    $CityId =  getCityIdByName($list['title']);
                }
            }
        }
        if ($CityId) {
            return $CityId;
        }
    }
}

function ruleCombinations($ruleId){
    $rule= App\Models\Rules::where('id',$ruleId)->first();
    if($rule){
        $rule->properties = json_decode($rule->properties,1);  // ['category','item']

       return $rule->properties;
    }
}

function executeCurlRequest($url, $headers) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => $headers,
    ));

    $response = curl_exec($curl);
    $error = curl_error($curl);

    if ($error) {
        // Handle cURL error
        // Log or throw an exception, depending on your needs
        // For simplicity, we're just printing the error here
        echo "cURL Error: $error\n";
    }

    curl_close($curl);

    return $response;
}

function makeApiRequest($endpoint) {
    $settings = App\Models\Setting::first();
    $url = $settings->base_url2 . $endpoint;
    $headers = [
        'Cookie: XSRF-TOKEN=eyJpdiI6IjJsOWJBRWlHYjRLdlRGSWZIT2Q2bWc9PSIsInZhbHVlIjoidE5UVC8weCsydkFJZEEyalVLTk40YTZ6Ym1laS9XQStKczk5Y1FocDIrcTJjcDVwVUkvWmFrT2FVRTBwUFlEeU5Va25XY0dtVS9naG9mcGFpRXROckZPT0t5L3g0WVMyRTQ3Rm9ZRTVkTmtpK2FjK1pCa0tiR2ZOd3IxK0pNV1giLCJtYWMiOiI4YWY1ZmI4MzMxNzEwNzZhNzExOGRjMTE3ZGUyZWM5OTRmM2Y4ZTNlZDA5YTg4MzRkYzIwYWNkNDYxNDI1OTQyIiwidGFnIjoiIn0%3D; seo_engine_master_session=eyJpdiI6ImE0aUEwc0dyS1EzWVZ6VmR6ZmdJNXc9PSIsInZhbHVlIjoib3JaMDdEblZaUUJUd0NqSTdocjE3aGo2S3QxWUMwazc0bHRZZjhnUjdoWEtaZ2Q5MlQ4cGIwWjZ6aEh2bDk5M0ZrVVRwaEdBaEtvMXoxM1Q5dk1kZ0FiZFVyS0pCNFA5bHAxZGtvcTVZbS9RT0tVTSs5dlMxaVRHcTBuS1p4ZnMiLCJtYWMiOiI3NGNjNzY0NGNiNGNhMTU5YjZjM2YxMTc1OTQwNDBhMTBmZTcxN2FmZjBmMzcxNWQyNGNkODIxMDkyYTUxNWEyIiwidGFnIjoiIn0%3D',
    ];
    return executeCurlRequest($url, $headers);
}

function CentreAPi() {
    return makeApiRequest('/getAllCentres');
}

function CategoryAPi() {
    return makeApiRequest('/getAllCategories');
}

function CityAPi() {
    return makeApiRequest('/getAllCities');
}

function ItemsAPi() {
    return makeApiRequest('/getAllitems');
}

function LocalityAPi() {
    return makeApiRequest('/getAllLocality');
}

function StateAPi() {
    return makeApiRequest('/getAllStates');
}










