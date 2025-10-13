<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Category;
use DB;
use Validator;
use Image;
use File;

class DashboardController extends Controller
{
    public function dashboard()
    {
        try {
            $page_title = 'Dashboard';
            $page_description = '';
            $breadcrumbs = [
                // [
                //     'title' => 'Dashboard',
                //     'url' => '',
                // ],
            ];
            $status = request('status');
            if ($status == '0') {
                $status = '2';
            }
            $categories = Category::where('parent_id', 0)->when($status, function ($cities) use ($status) {
                if ($status != '-1') {
                    $status = conditionalStatus($status);
                    $cities->where('status', '=', $status);
                }
            })
                ->orderBy('id', 'desc')->get();
            $ordersMonthWise = Order::select(
                DB::raw("(COUNT(*)) as count"),
                DB::raw("MONTH(created_at) as month_name")
            )
                ->whereYear('created_at', date('Y'))
                ->groupBy('month_name')
                ->get()
                ->toArray();
            $ordersMonthWiseData = [];
            if (count($ordersMonthWise) > 0) {
                for ($i = 1; $i <= 12; $i++) {
                    $key = array_search($i, array_column($ordersMonthWise, 'month_name'));
                    if (isset($ordersMonthWise[$key]['month_name']) && $ordersMonthWise[$key]['month_name'] == $i) {
                        $ordersMonthWiseData[] = $ordersMonthWise[$key]['count'];
                    } else {
                        $ordersMonthWiseData[] = 0;
                    }
                }
            }
            // dd($ordersMonthWiseData);
            return view('admin.pages.dashboard.list', compact('page_title', 'page_description', 'breadcrumbs', 'categories', 'ordersMonthWise', 'ordersMonthWiseData'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
