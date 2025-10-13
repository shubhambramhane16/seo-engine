<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;
use App\Models\City;
use App\Models\Category;
use App\Models\Speciality;
use App\Models\SeoPage;
use App\Models\Rules;
use App\Models\Locality;
use App\Models\PathologyTest as Tests;

use DB;
use Validator;

class CommonController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getAjaxCities($stateId = null)
    {
        if ($stateId) {
            // return City::where('state_id', $stateId)->where('status', 1)->get();
            return City::where('state_id', $stateId)->where('status', 0)->get();
        }
    }
    public function getAjaxSubCategories()
    {
        $categoryId = request('categoryId');
        if ($categoryId) {
            $categoryId = explode(',', $categoryId);
            return Category::whereIn('parent_id', $categoryId)->orderBy('category_name', 'asc')->get();
        }
    }

    public function ruleCombinations(){

        $ruleId = request('ruleId');
        if($ruleId){
            $rule = Rules::find($ruleId);
            if($rule){
                $properties = json_decode($rule->properties);
                $counts = [
                    'city' => 1,
                    'locality' => 1,
                    'category' => 1,
                    'item' => 1,
                ];
                $count=0;
                foreach ($properties as $property) {
                    if (array_key_exists($property, $counts)) {
                        switch ($property) {
                            case 'city':
                                $count = City::where('status', 1)->where('slug' , '!=','')->count();
                                break;
                            case 'locality':
                                $count = Locality::where('status', 1)->where('slug' , '!=','')->count();
                                break;
                            case 'category':
                                $count = Category::where('status', 1)->where('category_name' , '!=','')->count();
                                break;
                            case 'item':
                                $count = Tests::where('status', 1)->where('slug' , '!=','')->count();
                                break;
                        }
                    }
                }
                
                // foreach ($properties as $property) {
                //     if (array_key_exists($property, $counts)) {
                //         switch ($property) {
                //             case 'city':
                //                 $counts['city'] = City::where('status', 1)->where('slug' , '!=','')->count();
                //                 break;
                //             case 'locality':
                //                 $counts['locality'] = Locality::where('status', 1)->where('slug' , '!=','')->count();
                //                 break;
                //             case 'category':
                //                 $counts['category'] = Category::where('status', 1)->where('category_name' , '!=','')->count();
                //                 break;
                //             case 'item':
                //                 $counts['item'] = Tests::where('status', 1)->where('slug' , '!=','')->count();
                //                 break;
                //         }
                //     }
                // }
                // $data = array_product($counts);
                $data = $count;

                return $data;
            }
        }

    }





    public function getAjaxSpecilities()
    {
        $departmentId = request('departmentId');
        if ($departmentId) {
            $departmentId = explode(',', $departmentId);
            return Speciality::whereIn('department_id', $departmentId)->orderBy('speciality_name', 'asc')->get();
        }
    }
    public function checkSeoSlug()
    {
        $slug = request('slug');
        $id = request('id');
        if ($slug) {
            $data = SeoPage::where('slug', $slug)->when($id, function ($data) use ($id) {
                if ($id) {
                    $data->where('id', '<>', $id);
                }
            })->first();
            if ($data) {
                return [
                    'Success' => true,
                    'Data' => $data,
                    'Message' => 'Slug already exist.',
                ];
            } else {
                return [
                    'Success' => false,
                    'Data' => '',
                    'Message' => 'Slug available.',
                ];
            }
        } else {
            return [
                'Success' => false,
                'Data' => '',
                'Message' => 'Slug available.',
            ];
        }
    }
}
