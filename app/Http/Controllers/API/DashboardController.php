<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PathologyPackages;
use App\Models\Category;
use App\Models\Testimonial;
use App\Models\Coupon;
use DB;
use Response;
use Validator;
use File;
use Storage;

class DashboardController extends Controller
{
    function generateBasicToken(Request $request)
    {
        // dd(env('DB_PASSWORD'));
        $result['Result'] = ['Token' => env('BASIC_TOKEN'), 'Session' => bcrypt('LAR_' . time() . rand(111111111, 999999999))];
        $result['Success'] = 'True';
        $result['Message'] = 'API Access Token';
        return response()->json($result);
    }
    function dashboard(Request $request)
    {
        try {
            $packageLimit = 3;
            $packages = PathologyPackages::getActiveItems($packageLimit);
            $categories = Category::with(['SubCategory'])->where('parent_id', 0)->where('status', 1)->orderBy('priority_sequence', 'asc')->get();
            $testimonials  = Testimonial::where('status', 1)
                ->where('segment', 1)
                ->limit(12)
                ->orderBy('id', 'desc')
                ->get();
            $offers  = Coupon::where('status', 1)->limit(12)->orderBy('id', 'desc')->get();
            $categoryArray = [];
            $testimonialsArray = [];
            if ($categories) {
                foreach ($categories as $key => $list) {
                    $subCategories = [];
                    if (count($list->SubCategory) > 0) {
                        foreach ($list->SubCategory as $subKey => $subList) {
                            if (str_contains($subList->category_icon, 'AWS')) {
                                $category_icon = \Storage::disk('s3')->url($subList->category_icon);
                            } else {
                                $category_icon = asset('uploads/category/icons/' .  $subList->category_icon);
                            }
                            $subCategories[] = [
                                'Id' => $subList->id,
                                'ParentId' => $subList->parent_id,
                                'CategoryName' => $subList->category_name,
                                'CategorySlug' => $subList->category_slug,
                                'ShortDescription' => $subList->category_short_description,
                                'CategoryIcon' => $category_icon ? $category_icon : url('public/media/no_image_found.jpg'),
                                'Tags' => $subList->tags,
                                'SeoTitle' => $subList->seo_title,
                                'SeoDescription' => $subList->seo_description,
                                'SeoKewords' => $subList->seo_kewords,
                                'Status' => $subList->status,
                            ];
                        }
                    }
                    if (str_contains($list->category_icon, 'AWS')) {
                        $categoryIcon = \Storage::disk('s3')->url($list->category_icon);
                    } else {
                        $categoryIcon = asset('uploads/category/icons/' .  $list->category_icon);
                    }
                    $categoryArray[] = [
                        'Id' => $list->id,
                        'ParentId' => $list->parent_id,
                        'CategoryName' => $list->category_name,
                        'CategorySlug' => $list->category_slug,
                        'ShortDescription' => $list->category_short_description,
                        'CategoryIcon' => $categoryIcon ? $categoryIcon : url('public/media/no_image_found.jpg'),
                        'Tags' => $list->tags,
                        'SeoTitle' => $list->seo_title,
                        'SeoDescription' => $list->seo_description,
                        'SeoKewords' => $list->seo_kewords,
                        'Status' => $list->status,
                        'SubCategories' => $subCategories,
                    ];
                }
            }
            if ($testimonials) {
                foreach ($testimonials as $tKey => $tList) {
                    if (str_contains($tList->profile_image, 'AWS')) {
                        $ProfileImage = \Storage::disk('s3')->url($tList->profile_image);
                    } else {
                        if ($tList->profile_image) {
                            $ProfileImage = asset('uploads/testimonials/profile_images/' .  $tList->profile_image);
                        } else {
                            $ProfileImage = '';
                        }
                    }
                    $testimonialsArray[] = [
                        'Id' => $tList->id,
                        'Rating' => $tList->rating,
                        'Comments' => $tList->comments,
                        'Name' => $tList->name,
                        'ProfileImage' =>  $ProfileImage ? $ProfileImage : url('public/media/no_image_found.jpg'),
                        'TestimonialType' => $tList->testimonial_type,
                        'VideoURL' => $tList->video_url,
                        'Content' => $tList->content,
                        'Status' => $tList->status,
                        'Gender' => $tList->gender,
                    ];
                }
            }
            $offerArray = [];
            if ($offers) {
                foreach ($offers as $tKey => $tList) {
                    if (str_contains($tList->coupon_icon, 'AWS')) {
                        $CouponBanner = \Storage::disk('s3')->url($tList->coupon_icon);
                    } else {
                        if ($tList->coupon_icon) {
                            $CouponBanner = url('/') . '/public/uploads/coupons/icons/' .  $tList->coupon_icon;
                        } else {
                            $CouponBanner = '';
                        }
                    }
                    $offerArray[] = [
                        'Id' => $tList->id,
                        'CouponCode' => $tList->coupon_code,
                        'CouponType ' => $tList->coupon_type,
                        'CouponTitle' => $tList->coupon_title,
                        'ShortDescription' => $tList->short_desc,
                        'Description' => $tList->description,
                        'CouponBanner' => $CouponBanner ? $CouponBanner : url('public/media/no_image_found.jpg'),
                        'ValidTo' => $tList->valid_to,
                        'ValidFrom' => $tList->valid_from,
                        'MinValue' => $tList->min_value,
                        'MaxOff' => $tList->max_off,
                        'CityId' => $tList->city_id,

                        'Status' => $tList->status,
                    ];
                }
            }
            $result['Result'] = [
                'PackageBaseUrl' => url('/') . '/public/uploads/packages/',
                'Packages' => $packages,
                'Categories' => $categoryArray,
                'Testimonials' => $testimonialsArray,
                'Offers' => $offerArray,
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
}
