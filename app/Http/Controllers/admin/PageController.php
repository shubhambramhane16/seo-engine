<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Rules;
use App\Models\Pages;
use App\Models\City;
use App\Models\PagesHistory;
use App\Models\Locality;
use App\Models\Category;
use App\Models\PathologyTest as Items;
use DB;
use Validator;
use Image;
use File;
use App\Exports\Excel\CategoryExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        try {
            $page_title = 'Page Management';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Page Management',
                    'url' => '',
                ],
            ];
            $status = request('status');
            if ($status == '0') {
                $status = '2';
            }

            $pages = Pages::with(['rule'])->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })->orderBy('id', 'desc')->get();

            return view('admin.pages.page.list', compact('page_title', 'page_description', 'breadcrumbs', 'pages'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function add(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                // dd($request->all());
                $validator = Validator::make($request->all(), [
                    'rule_id' => 'required',
                    'number_of_combination' => 'required',
                ], [
                    'rule_id.required' => 'Rule is required.',
                    'number_of_combination.required' => 'Number of combination is required.',
                    'rule_id.unique' => 'Rule already exists.',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
                }
                DB::beginTransaction();


                $array = [
                    'rule_id' => $request->rule_id,
                    'no_of_pages' => $request->number_of_combination,
                    'created_by' => auth()->user()->id,
                ];
                // dd($array);
                $response = PagesHistory::updateOrCreate($array);
                $rule = Rules::where('id', $request->rule_id)->first();

                // if rule->properties having  first city and then locality ["item-name","city-name"] then select only city and locality from below models if first locality and then city then select only locality and city from below models if first category and then items then select only category and items from below models
                $models = [City::class, Locality::class, Category::class, Items::class];
                if ($rule->properties) {
                    $properties = json_decode($rule->properties);
                    $models = [];
                    foreach ($properties as $property) {
                        if ($property == 'city-name') {
                            $models[] = City::class;
                        } elseif ($property == 'locality-name') {
                            $models[] = Locality::class;
                        } elseif ($property == 'category-name') {
                            $models[] = Category::class;
                        } elseif ($property == 'item-name') {
                            $models[] = Items::class;
                        }
                    }
                }
                // dd($models);
                $data = [];

                foreach ($models as $model) {
                    if ($model == Category::class) {
                        // if category then select category_name as a slug in small case and replace space with hyphen
                        $data[] = $model::where('status', 1)->where('parent_id', '!=', 0)->where('category_name', '!=', '')->pluck('category_name')->map(function ($name) {
                            return strtolower(str_replace(' ', '-', $name));
                        })->toArray();
                        continue;
                    }
                    $data[] = $model::where('status', 1)->where('slug', '!=', '')->pluck('slug')->toArray();
                }
                // dd($data);
                if ($rule) {
                    $properties = json_decode($rule->properties);
                    $propertiesArray = [];

                    foreach ($properties as $key => $property) {
                        if (!isset($data[$key])) {
                            continue;
                        }

                        $previousArray = $key > 0 ? $propertiesArray[$key - 1] : [];

                        foreach ($data[$key] as $slug) {
                            // return $slug;
                            if ($previousArray) {
                                foreach ($previousArray as $prevSlug) {
                                    // Modify this line to concatenate slugs in the desired order
                                    $propertiesArray[$key][] = $prevSlug . '/' . $slug;
                                }
                            } else {
                                $propertiesArray[$key][] = $slug;
                            }
                        }
                    }

                    $lastArray = end($propertiesArray);
                    // dd($lastArray);
                    foreach ($lastArray as $newSlug) {
                        $dumpArray = [
                            'page_name' => ucwords(str_replace('-', ' ', $newSlug)),
                            'rule_id' => $request->rule_id,
                            'slug' => $rule->prefix . '/' . $newSlug,
                            'page_url' => env('FRONTENT_URI') . $rule->prefix . '/' . $newSlug,
                            'created_by' => auth()->user()->id,
                            'status' => 1,
                        ];

                        // return $dumpArray;
                        // Pages::updateOrCreate(['slug' => $newSlug], $dumpArray);
                        Pages::updateOrCreate(['slug' => $dumpArray['slug']], $dumpArray);
                    }
                }
                // dd($propertiesArray);


                DB::commit();
                return redirect('admin/page/list')->with('success', 'Page added successfully.');
            }

            $page_title = 'Page Management';
            $page_description = 'Add Page';

            $pageSettings = $this->pageSetting('add');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];
            $rules = Rules::orderBy('id', 'desc')->where('status',1)->get();
            $pagesHistory = PagesHistory::orderBy('id', 'desc')->get();
            return view('admin.pages.page.add', compact('page_title', 'page_description', 'breadcrumbs', 'rules', 'pagesHistory'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }







    public function edit(Request $request, $id)
    {
        try {
            if ($id) {
                if ($request->isMethod('post')) {
                    $validator = Validator::make($request->all(), [
                        'page_url' => '',
                        'page_name' => '',
                        'slug' => '',
                        'seo_title' => '',
                        'seo_description' => '',
                        'seo_keywords' => '',
                        'og_meta_title' => '',
                        'og_meta_description' => '',
                        'og_meta_image_url' => '',
                        'twitter_card_title' => '',
                        'twitter_card_description' => '',
                        'schema_markup' => '',
                        'header_content' => '',
                        'center_content' => '',
                        'footer_content' => '',
                        'page_script' => '',
                    ], [
                        'page_url.required' => 'Page Url is required.',
                        'page_name.required' => 'Reference Name is required.',
                        'slug.required' => 'Slug is required.',
                        'seo_title.required' => 'Title is required.',
                        'seo_description.required' => 'Meta Description is required.',
                        'seo_keywords.required' => 'Meta Keywords is required.',
                        'og_meta_title.required' => 'OG Meta Title is required.',
                        'og_meta_description.required' => 'OG Meta Description is required.',
                        'og_meta_image_url.required' => 'OG Meta Image Url is required.',
                        'twitter_card_title.required' => 'Twitter card Title is required.',
                        'twitter_card_description.required' => 'Twitter card Description is required.',
                        'schema_markup.required' => 'Schema Markup is required.',
                        'header_content.required' => 'Header Content is required.',
                        'center_content.required' => 'Center Content is required.',
                        'footer_content.required' => 'Footer Content is required.',
                        'page_script.required' => 'Page Script is required.',
                    ]);
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
                    }
                    DB::beginTransaction();

                    $array = [
                        'page_url' => $request->page_url,
                        'page_name' => $request->page_name,
                        'slug' => $request->slug,
                        'seo_title' => $request->seo_title,
                        'seo_description' => $request->seo_description,
                        'seo_keywords' => $request->seo_keywords,
                        'og_meta_title' => $request->og_meta_title,
                        'og_meta_description' => $request->og_meta_description,
                        'og_meta_image_url' => $request->og_meta_image_url,
                        'twitter_card_title' => $request->twitter_card_title,
                        'twitter_card_description' => $request->twitter_card_description,
                        'schema_markup' => $request->schema_markup,
                        'header_content' => $request->header_content,
                        'center_content' => $request->center_content,
                        'footer_content' => $request->footer_content,
                        'page_script' => $request->page_script,
                        'updated_by' => auth()->user()->id,
                    ];

                    $response = Pages::updateOrCreate(['id' => $id], $array);
                    DB::commit();
                    return redirect('admin/page/list')->with('success', 'Page updated successfully.');
                }
                $page_title = 'Page Management';
                $page_description = 'Edit Page';
                $details = Pages::where('id', $id)->first();
                if ($details) {
                    $pageSettings = $this->pageSetting('edit', ['slug' => $details->slug]);

                    $page_title =  $pageSettings['page_title'];
                    $page_description = $pageSettings['page_description'];
                    $breadcrumbs = $pageSettings['breadcrumbs'];
                    return view('admin.pages.page.edit', compact('page_title', 'page_description', 'breadcrumbs', 'details'));
                } else {
                    return redirect()->back()->withErrors(['Category details not found.']);
                }
            } else {
                return redirect()->back()->withErrors(['Category id is missing.']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            if ($id) {
                DB::beginTransaction();
                $cat = Pages::find($id);
                if ($cat->delete()) {
                    DB::commit();
                    return redirect()->back()->with('success', 'Page deleted successfully.');
                } else {
                    return redirect()->back()->with('error', 'Failed to delete try again.');
                }
            } else {
                return redirect()->back()->with('error', 'Page details not found.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function updateStatus($pageId, $status)
    {
        try {
            if ($pageId) {
                // dd($pageId);
                DB::beginTransaction();
                $status = ($status == 1) ? $status = 0 : $status = 1;
                $updateArr = [
                    'status' => $status,
                ];
                $response = Pages::UpdateOrCreate(['id' => $pageId], $updateArr);
                // dd($response);
                DB::commit();
                return redirect('admin/page/list')->with('success', 'Page status updated successfully.');
            } else {
                return redirect()->back()->with('error', 'Page details not found.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage())->withInput($request->all());
        }
    }



    public function pageSetting($action, $dataArray = [])
    {
        if ($action == 'edit') {
            $data['page_title'] = 'Page';
            $data['page_description'] = 'Edit Page';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Page Management',
                    'url' => url('admin/page/list'),
                ]
            ];
            if (isset($dataArray['title']) && !empty($dataArray['title'])) {
                $data['breadcrumbs'][] =
                    [
                        'title' => $dataArray['title'],
                        'url' => '',

                    ];
            } else {
                $data['breadcrumbs'][] = [

                    'title' => 'Edit Page',
                    'url' => '',

                ];
            }
            return $data;
        }

        if ($action == 'add') {
            $data['page_title'] = 'Page';
            $data['page_description'] = 'Add a New Page';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Page Management',
                    'url' => url('admin/page/list'),
                ],
                [
                    'title' => 'Add Page',
                    'url' => '',
                ],
            ];
            return $data;
        }
    }
}
