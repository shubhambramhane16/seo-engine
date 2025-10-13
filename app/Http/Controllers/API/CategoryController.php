<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use DB;
use Response;
use Validator;

class CategoryController extends Controller
{

    function categoryList(Request $request)
    {
        try {
            $categories  = Category::where('status', 1)->where('parent_id', 0)->orderBy('priority_sequence', 'asc')->get();
            $categoriesArray = [];
            if ($categories) {
                foreach ($categories as $tKey => $list) {
                    $categoriesArray[] = [
                        'Id' => $list->id,
                        'ParentId' => $list->parent_id,
                        'CategoryName' => $list->category_name,
                        'CategorySlug' => $list->category_slug,
                        'CategoryIcon' => $list->category_icon,
                        'ShortDescription' => $list->category_short_description,
                        'Tags' => $list->tags,
                        'SeoTitle' => $list->seo_title,
                        'SeoDescription' => $list->seo_description,
                        'SeoKewords' => $list->seo_kewords,
                        'CategoryIcon' => $list->category_icon ? url('public/uploads/category/icons/' . $list->category_icon) : '',
                        'Status' => $list->status
                    ];
                }
            }
            $result['Result'] = [
                'Categories' => $categoriesArray,
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
    function categoryDetails(Request $request, $categoryId)
    {
        try {

            if ($categoryId) {
                $details  = Category::where('status', 1)->where('id', $categoryId)->first();
                $detailsArray = [];
                if ($details) {
                    $detailsArray = [
                        'Id' => $details->id,
                        'ParentId' => $details->parent_id,
                        'CategoryName' => $details->category_name,
                        'CategorySlug' => $details->category_slug,
                        'CategoryIcon' => $details->category_icon,
                        'ShortDescription' => $details->category_short_description,
                        'Tags' => $details->tags,
                        'SeoTitle' => $details->seo_title,
                        'SeoDescription' => $details->seo_description,
                        'SeoKewords' => $details->seo_kewords,
                        'CategoryIcon' => $details->category_icon ? url('public/uploads/category/icons/' . $details->category_icon) : '',
                        'Status' => $details->status
                    ];
                    $result['Result'] = [
                        'CategoryDetails' => $detailsArray,
                    ];
                    $result['Success'] = 'True';
                    $result['Message'] = 'details.';
                } else {
                    $result['Result'] = (object) [];
                    $result['Success'] = 'False';
                    $result['Message'] = 'Category details not found.';
                }


                return response()->json($result);
            } else {
                $result['Result'] =  (object) [];
                $result['Success'] = 'False';
                $result['Message'] = 'Category id is missing.';
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
    function categoryDetailsBySlug(Request $request, $categoryId)
    {
        try {
            if ($categoryId) {
                $details  = Category::where('status', 1)->where('category_slug', $categoryId)->first();
                $detailsArray = [];
                if ($details) {
                    $detailsArray = [
                        'Id' => $details->id,
                        'ParentId' => $details->parent_id,
                        'CategoryName' => $details->category_name,
                        'CategorySlug' => $details->category_slug,
                        'CategoryIcon' => $details->category_icon,
                        'ShortDescription' => $details->category_short_description,
                        'Tags' => $details->tags,
                        'SeoTitle' => $details->seo_title,
                        'SeoDescription' => $details->seo_description,
                        'SeoKewords' => $details->seo_kewords,
                        'CategoryIcon' => $details->category_icon ? url('public/uploads/category/icons/' . $details->category_icon) : '',
                        'Status' => $details->status
                    ];
                    $result['Result'] = [
                        'CategoryDetails' => $detailsArray,
                    ];
                    $result['Success'] = 'True';
                    $result['Message'] = 'details.';
                } else {
                    $result['Result'] = (object) [];
                    $result['Success'] = 'False';
                    $result['Message'] = 'Category details not found.';
                }


                return response()->json($result);
            } else {
                $result['Result'] =  (object) [];
                $result['Success'] = 'False';
                $result['Message'] = 'Category id is missing.';
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
    function subcategoryList(Request $request, $categoryId = null)
    {
        try {
            if ($categoryId) {
                $categories  = Category::where('status', 1)->when($categoryId, function ($categories) use ($categoryId) {
                    if ($categoryId) {
                        $categories->where('parent_id', '=', $categoryId);
                    }
                })->orderBy('category_name', 'asc')->get();
            } else {
                $categories  = Category::where('status', 1)->where('parent_id', '!=', 0)->orderBy('category_name', 'asc')->get();
            }
            $categoriesArray = [];
            if ($categories) {
                foreach ($categories as $tKey => $list) {
                    if (str_contains($list->category_icon, 'AWS')) {
                        $category_icon = \Storage::disk('s3')->url($list->category_icon);
                    } else {
                        $category_icon = asset('uploads/category/icons/' .  $list->category_icon);
                    }
                    $categoriesArray[] = [
                        'Id' => $list->id,
                        'ParentId' => $list->parent_id,
                        'CategoryName' => $list->category_name,
                        'CategorySlug' => $list->category_slug,
                        'ShortDescription' => $list->category_short_description,
                        'CategoryIcon' => $category_icon ? $category_icon : '',
                        'Tags' => $list->tags,
                        'SeoTitle' => $list->seo_title,
                        'SeoDescription' => $list->seo_description,
                        'SeoKewords' => $list->seo_kewords,
                        'Status' => $list->status
                    ];
                }
            }
            $result['Result'] = [
                'Categories' => $categoriesArray,
            ];
            $result['Success'] = 'True';
            $result['Message'] = 'list.';

            return response()->json($result);
            // } else {
            //     $result['Result'] = [];
            //     $result['Success'] = 'False';
            //     $result['Message'] = 'Category id is missing.';
            //     return response()->json($result);
            // }
        } catch (\Exception $e) {
            dd($e);
            $result['Result'] = [];
            $result['Success'] = 'False';
            $result['Message'] = $e;
            return response()->json($result);
        }
    }



    function subCategoryDetails(Request $request, $subCategoryId)
    {
        try {

            if ($subCategoryId) {
                $details  = Category::where('status', 1)->where('parent_id', '<>', 0)->where('id', $subCategoryId)->first();
                $detailsArray = [];
                if ($details) {
                    $detailsArray = [
                        'Id' => $details->id,
                        'ParentId' => $details->parent_id,
                        'CategoryName' => $details->category_name,
                        'CategorySlug' => $details->category_slug,
                        'CategoryIcon' => $details->category_icon,
                        'ShortDescription' => $details->category_short_description,
                        'Tags' => $details->tags,
                        'SeoTitle' => $details->seo_title,
                        'SeoDescription' => $details->seo_description,
                        'SeoKewords' => $details->seo_kewords,
                        'CategoryIcon' => $details->category_icon ? url('public/uploads/category/icons/' . $details->category_icon) : '',
                        'Status' => $details->status
                    ];
                    $result['Result'] = [
                        'CategoryDetails' => $detailsArray,
                    ];
                    $result['Success'] = 'True';
                    $result['Message'] = 'details.';
                } else {
                    $result['Result'] = (object) [];
                    $result['Success'] = 'False';
                    $result['Message'] = 'Category details not found.';
                }


                return response()->json($result);
            } else {
                $result['Result'] =  (object) [];
                $result['Success'] = 'False';
                $result['Message'] = 'Category id is missing.';
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

    function subCategoryDetailsBySlug(Request $request, $slug)
    {
        try {
            if ($slug) {
                $details  = Category::where('status', 1)->where('parent_id', '<>', 0)->where('category_slug', $slug)->first();
                $detailsArray = [];
                if ($details) {
                    $detailsArray = [
                        'Id' => $details->id,
                        'ParentId' => $details->parent_id,
                        'CategoryName' => $details->category_name,
                        'CategorySlug' => $details->category_slug,
                        'CategoryIcon' => $details->category_icon,
                        'ShortDescription' => $details->category_short_description,
                        'Tags' => $details->tags,
                        'SeoTitle' => $details->seo_title,
                        'SeoDescription' => $details->seo_description,
                        'SeoKewords' => $details->seo_kewords,
                        'CategoryIcon' => $details->category_icon ? url('public/uploads/category/icons/' . $details->category_icon) : '',
                        'Status' => $details->status
                    ];
                    $result['Result'] = [
                        'CategoryDetails' => $detailsArray,
                    ];
                    $result['Success'] = 'True';
                    $result['Message'] = 'details.';
                } else {
                    $result['Result'] = (object) [];
                    $result['Success'] = 'False';
                    $result['Message'] = 'Category details not found.';
                }


                return response()->json($result);
            } else {
                $result['Result'] =  (object) [];
                $result['Success'] = 'False';
                $result['Message'] = 'Category id is missing.';
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
