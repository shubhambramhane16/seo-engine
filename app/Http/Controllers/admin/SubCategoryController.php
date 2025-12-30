<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use DB;
use Validator;
use Image;
use File;

use App\Exports\Excel\SubCategoryExport;
use Maatwebsite\Excel\Facades\Excel;

class SubCategoryController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');
    }

    public function index($categoryId = null)
    {
        try {
            $parent_id = $categoryId;
            $page_title = 'Category Management';
            $page_description = 'Add Category';
            if ($categoryId) {
                $catDetails = Category::where('id', $categoryId)->where('parent_id', 0)->first();
                $pageSettings = $this->pageSetting('list', ['title' => $catDetails ? $catDetails->category_name : '', 'url' => $catDetails ? url('admin/subcategories/list/' . $catDetails->id) : '']);
            } else {
                $pageSettings = $this->pageSetting('list');
            }

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];

            $status = request('status');
            if ($status == '0') {
                $status = '2';
            }
            $categories = Category::with(['category'])->where('parent_id', '!=', 0)->when($status, function ($cities) use ($status) {
                if ($status != '-1') {
                    $status = conditionalStatus($status);
                    $cities->where('status', '=', $status);
                }
            })->when($parent_id, function ($cities) use ($parent_id) {
                if ($parent_id) {
                    $cities->where('parent_id', '=', $parent_id);
                }
            })
                ->orderBy('id', 'desc')->get();
            // dd( $categories);
            return view('admin.pages.subcategory.list', compact('page_title', 'page_description', 'breadcrumbs', 'categories', 'parent_id'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function add(Request $request, $categoryId = null)
    {

        try {
            if ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'category_name' => 'required',
                    'icon' => 'nullable|mimes:jpg,png',
                ], [
                    'category_name.required' => 'Category name is required.'
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                }

                DB::beginTransaction();
                $updateArr = [
                    'parent_id' => $request->parent_id,
                    'category_name' => $request->category_name,
                    'category_short_description' => $request->category_short_description,
                    'slug' => \Str::slug($request->category_name),
                ];
                if ($request->hasFile('icon')) {
                    $pathString = 'uploads/category/icons';
                    $image = $request->file('icon');
                    $s3UploadRes = uploadFileAwsBucket($pathString, $image);
                    $icon = $s3UploadRes;
                    $updateArr['category_icon'] =  $icon;
                }

                if (Category::where('category_name', $updateArr['category_name'])->where('parent_id', 0)->whereNull('deleted_at')->exists()) {
                    return redirect()->back()->withErrors(['Category name already exist.'])->withInput($request->all());
                }
                $response = Category::UpdateOrCreate(['id' => null], $updateArr);
                DB::commit();
                return redirect('admin/subcategories/list/' . $categoryId)->with('success', 'Category details updated successfully.');
            }

            $page_title = 'Sub Category Management';
            $page_description = 'Add Sub Category';

            $pageSettings = $this->pageSetting('add', ['categoryId' => $categoryId]);

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];
            $categories = Category::where('parent_id', 0)->get();
            $parent_id = $categoryId;
            return view('admin.pages.subcategory.add', compact('page_title', 'page_description', 'breadcrumbs', 'categories', 'parent_id'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }












        // $page_title = ' Sub Category ';
        // $page_description = 'Add Sub Category';
        // $breadcrumbs = [
        //     [
        //         'title' => 'Sub Category',
        //         'url' => url('admin/subcategories/list'),
        //     ],
        //     [
        //         'title' => 'Add Sub Category',
        //         'url' => '',
        //     ],
        // ];

        // return view('admin.pages.subcategory.add', compact('page_title', 'page_description', 'breadcrumbs'));
    }
    public function edit(Request $request, $id, $categoryId)
    {

        try {
            // echo $categoryId; die;
            if ($id) {
                if ($request->isMethod('post')) {
                    // dd($request->all());
                    $validator = Validator::make($request->all(), [
                        'category_name' => 'required',
                        'parent_id' => 'required',
                        'icon' => 'nullable|mimes:jpg,png',
                    ], [
                        'category_name.required' => 'Category name is required.',
                        'parent_id.required' => 'Please select category.'
                    ]);
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput($request->all());
                    }

                    $updateArr = [
                        'parent_id' => $request->parent_id,
                        'category_short_description' => $request->category_short_description,
                        'category_name' => $request->category_name,
                        'slug' => \Str::slug(getCategoryName($request->parent_id) . ' ' . $request->category_name),
                    ];
                    DB::beginTransaction();
                    if ($request->hasFile('icon')) {
                        $pathString = 'uploads/category/icons';
                        $image = $request->file('icon');
                        $s3UploadRes = uploadFileAwsBucket($pathString, $image);
                        $icon = $s3UploadRes;
                        $updateArr['category_icon'] =  $icon;
                    }

                    // dd($updateArr);
                    if (Category::where('category_name', $updateArr['category_name'])->where('parent_id', '!=', 0)->where('id', '!=', $id)->exists()) {
                        return redirect()->back()->withErrors(['Category name already exist.'])->withInput($request->all());
                    }
                    $response = Category::UpdateOrCreate(['id' => $id], $updateArr);
                    DB::commit();
                    return redirect('admin/subcategories/list/' . $categoryId)->with('success', 'Sub Category details updated successfully.');
                }

                $page_title = 'Category Management';
                $page_description = 'Edit Category';
                $categories = Category::where('parent_id', 0)->get();
                $details = Category::where('id', $id)->first();
                if ($details) {
                    $pageSettings = $this->pageSetting('edit', ['title' => $details->category_name, 'categoryId' => $categoryId]);

                    $page_title =  $pageSettings['page_title'];
                    $page_description = $pageSettings['page_description'];
                    $breadcrumbs = $pageSettings['breadcrumbs'];

                    return view('admin.pages.subcategory.edit', compact('page_title', 'page_description', 'breadcrumbs', 'details', 'categories'));
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
    public function updateStatus($cityId, $status)
    {
        try {
            if ($cityId) {

                DB::beginTransaction();
                $status = ($status == 1) ? $status = 0 : $status = 1;
                $updateArr = [
                    'status' => $status,
                ];
                $response = Category::UpdateOrCreate(['id' => $cityId], $updateArr);
                DB::commit();
                return redirect()->back()->with('success', 'City status updated successfully.');
            } else {
                return redirect()->back()->with('error', 'City details not found.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage())->withInput($request->all());
        }
    }

    public function delete($id)
    {
        try {
            if ($id) {
                DB::beginTransaction();
                $cat = Category::find($id);
                if ($cat->delete()) {
                    DB::commit();
                    return redirect()->back()->with('success', 'Sub Category deleted successfully.');
                } else {
                    return redirect()->back()->with('error', 'Failed to delete try again.');
                }
            } else {
                return redirect()->back()->with('error', 'Sub Category details not found.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function pageSetting($action, $dataArray = [])
    {
        if (isset($dataArray['categoryId'])) {
            $categoryId = $dataArray['categoryId'];
        } else {
            $categoryId = '';
        }
        if ($action == 'edit') {

            $data['page_title'] = 'Sub Category';
            $data['page_description'] = 'Sub Category';
            $data['breadcrumbs'] = [

                [
                    'title' => 'Category Management',
                    'url' => url('admin/categories/list'),
                ],
            ];
            if ($categoryId) {
                $data['breadcrumbs'][] =
                    [
                        'title' => getCategoryName($categoryId),
                        'url' => url('admin/subcategories/list/' .  $categoryId),

                    ];
            }
            if (isset($dataArray['title']) && !empty($dataArray['title'])) {
                $data['breadcrumbs'][] =
                    [
                        'title' => $dataArray['title'],
                        'url' => url('admin/subcategories/list/' .  $categoryId),

                    ];
            }
            return $data;
        }

        if ($action == 'add') {

            $data['page_title'] = 'Sub Category';
            $data['page_description'] = 'Sub Category';
            $data['breadcrumbs'] = [

                [
                    'title' => 'Category Management',
                    'url' => url('admin/categories/list'),
                ],
                [
                    'title' => getCategoryName($categoryId),
                    'url' => url('admin/subcategories/list' . ($categoryId) ?? $categoryId)
                ],
                [
                    'title' => 'Add Sub Category',
                    'url' => '',
                ],
            ];
            return $data;
        }
        if ($action == 'list') {
            $data['page_title'] = 'Sub Category';
            $data['page_description'] = 'Sub Category';

            if (isset($dataArray['title']) && !empty($dataArray['title'])) {
                $data['breadcrumbs'][] =
                    [
                        'title' => $dataArray['title'],
                        'url' => isset($dataArray['url']) ? $dataArray['url'] : '',

                    ];
            } else {
                $data['breadcrumbs'][] = [

                    'title' => 'Category',
                    'url' => '',

                ];
            }
            $data['breadcrumbs'][] = [

                'title' => 'Sub Category',
                'url' => '',

            ];
            return $data;
        }
    }
    function exportExcel()
    {
        $type = request('type');
        if ($type == 'excel')
            return Excel::download(new SubCategoryExport, 'SubCategories.xlsx');
        else
            return redirect()->back()->withErrors(['Export type not defined.']);
    }
}
