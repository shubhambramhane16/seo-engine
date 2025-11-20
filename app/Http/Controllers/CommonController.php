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
        $cityId = request('cityId');
        if ($ruleId) {
            $rule = Rules::find($ruleId);
            if ($rule && $rule->properties) {
            $properties = json_decode($rule->properties);

            // map properties to models (support both "city" and "city-name" styles)
            $models = [];
            foreach ($properties as $property) {
                if (in_array($property, ['city', 'city-name'])) {
                $models[] = City::class;
                } elseif (in_array($property, ['locality', 'locality-name'])) {
                $models[] = Locality::class;
                } elseif (in_array($property, ['category', 'category-name'])) {
                $models[] = Category::class;
                } elseif (in_array($property, ['item', 'item-name'])) {
                $models[] = Tests::class;
                }
            }

            // gather counts for each selected model
            $counts = [];
            foreach ($models as $model) {
                if ($model === Category::class) {
                // count only child categories with a name
                $counts[] = Category::where('status', 1)
                    ->where('parent_id', '!=', 0)
                    ->where('category_name', '!=', '')
                    ->count();
                } elseif ($model === City::class) {
                $counts[] = City::where('status', 1)->where('slug', '!=', '')->where('id', $cityId)->count();
                } elseif ($model === Locality::class) {
                $counts[] = Locality::where('status', 1)->where('slug', '!=', '')->count();
                } else { // Tests (items)
                $counts[] = Tests::where('status', 1)->where('slug', '!=', '')->count();
                }
            }

            // compute product of counts (total possible combinations)
            $product = 1;
            if (count($counts) === 0) {
                $product = 0;
            } else {
                foreach ($counts as $c) {
                $product *= (int) $c;
                }
            }

            return $product;
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
