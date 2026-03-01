<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Validator;

class OrderController extends Controller
{
    function list(Request $request)
    {
        try {

           

                if ($request->isMethod('post')) {
                    $validator = Validator::make($request->all(), [
                        'status'=>'required|numeric',
                        'page'=>'numeric',
                    ], [
                        'status.required' => 'Status is required.',
                    ]);

                    if ($validator->fails()) {
                        $result['Result'] = ['error' => $validator->errors()];
                        $result['Success'] = 'Failed';
                        $result['Message'] = 'Fields are missing.';
                        return response()->json($result);
                    } else {
                        $limit = 20;
                        $page = $request->post('page') ?? 1;
                        $status  = $request->post('status');
                        $list  = Order::with(['orderHistory'=>function($query) use ($status){
                            $query->with('orderStatus')->where('order_status','=',$status);
                        }]);
                        $total = $list->count();
                        $orderList = $list->offset(($page-1)*$limit)->take($limit)->get();
                    }

                  
                }else{
                    $orderList  = Order::with(['orderHistory'=>function($query){
                        $query->with('orderStatus');
                    }])->get();
                }
                $result['Result'] = [
                    'Order' => $orderList,
                    'page' => $page,
                    'limit'=> $limit,
                    'total'=> $total
                ];
                $result['Success'] = 'True';
                $result['Message'] = 'Order List.';
    
                return response()->json($result);
            // }
            
        } catch (\Exception $e) {
            dd($e);
            $result['Result'] = [];
            $result['Success'] = 'False';
            $result['Message'] = $e;
            return response()->json($result);
        }
    }
}
