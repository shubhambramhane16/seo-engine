<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use DB;
use Response;
use Validator;
use File;

class FAQsController extends Controller
{

    function list(Request $request)
    {
        try {
            $page = (int)$request->get('page') ?? 1;
            $limit = 20;
            $faqList  = FaqCategory::with(['faqs'])->where('status', 1)->orderBy('category', 'asc');
            $total = $faqList->count();
            $lists = $faqList->offset(($page-1)*$limit)->take($limit)->get();
            
            $array = [];
            if ($lists) {
                foreach ($lists as $tKey => $tList) {
                    $array[] = [
                        'CategoryId' => $tList->id,
                        'Category' => $tList->category,
                        'Faqs' =>  $tList->faqs
                    ];
                }
            }
            $result['Result'] = $array;
            $result['page'] = $page;
            $result['limit'] = $limit;
            $result['total'] = $total;

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
}
