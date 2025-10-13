<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryImport;
use DB;
use Validator;
use Image;
use File;
use App\Exports\Excel\CategoryExport;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $page_title = 'Category Management';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Category Management',
                    'url' => '',
                ],
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
            return view('admin.pages.category.list', compact('page_title', 'page_description', 'breadcrumbs', 'categories'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function subCategoryList()
    {
        try {
            $page_title = 'Category Management';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Category Management',
                    'url' => '',
                ],
            ];
            $status = request('status');
            $parent_id = request('category_id');
            if ($status == '0') {
                $status = '2';
            }
            $categories = Category::where('parent_id', $parent_id)->when($status, function ($cities) use ($status) {
                if ($status != '-1') {
                    $status = conditionalStatus($status);
                    $cities->where('status', '=', $status);
                }
            })
                ->orderBy('id', 'desc')->get();
            return view('admin.pages.subcategory.list', compact('page_title', 'page_description', 'breadcrumbs', 'categories','parent_id'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function add(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'category_name' => 'required',
                    'icon' => 'nullable|mimes:jpg,png,jpeg | required',
                ], [
                    'icon.required' => 'Attachment is missing.',
                    'category_name.required' => 'Category name is required.'
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                }

                DB::beginTransaction();
                $updateArr = [
                    'parent_id' => 0,
                    'category_name' => $request->category_name,
                    'category_short_description' => $request->category_short_description,
                    'slug' => \Str::slug($request->category_name),
                ];
                if ($request->hasFile('icon')) {
                    $pathString = 'uploads/category/icons/';
                    $s3UploadRes = uploadFileAwsBucket($pathString, $request->file('icon'));
                    // $image = $request->file('icon');
                    // $iconImageName = \Str::slug($request->category_name) . time() . '.' . $image->getClientOriginalExtension();
                    // $image_resize = Image::make($image->getRealPath());

                    // $height = Image::make($image)->height();
                    // $width = Image::make($image)->width();
                    // $path = public_path($pathString);

                    // if (!File::isDirectory($path)) {

                    //     File::makeDirectory($path, 0777, true, true);
                    // }
                    // if ($width > $height) {
                    //     $image_resize->resize(692, null, function ($constraint) use ($image_resize) {
                    //         $constraint->aspectRatio();
                    //     })->save(public_path($pathString . $iconImageName));
                    // } else {
                    //     $image_resize->resize(null, 274, function ($constraint) use ($image_resize) {
                    //         $constraint->aspectRatio();
                    //     })->save(public_path($pathString . $iconImageName));
                    // }
                    $icon = $s3UploadRes;
                    $updateArr['category_icon'] =  $icon;
                }

                if (Category::where('category_name', $updateArr['category_name'])->where('parent_id', 0)->whereNull('deleted_at')->exists()) {
                    return redirect()->back()->withErrors(['Category name already exist.'])->withInput($request->all());
                }
                $response = Category::UpdateOrCreate(['id' => null], $updateArr);
                DB::commit();
                return redirect('admin/categories/list')->with('success', 'Category details updated successfully.');
            }

            $page_title = 'Category Management';
            $page_description = 'Add Category';

            $pageSettings = $this->pageSetting('add');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];

            return view('admin.pages.category.add', compact('page_title', 'page_description', 'breadcrumbs'));
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
                        'category_name' => 'required',
                        // 'icon' => 'nullable|mimes:jpg,png,jpeg | required',
                    ], [
                        'icon.required' => 'Attachment is missing.',
                        'category_name.required' => 'Category name is required.'
                    ]);
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput($request->all());
                    }

                    $updateArr = [
                        'parent_id' => 0,
                        'category_name' => $request->category_name,
                        'category_short_description' => $request->category_short_description,
                        'slug' => \Str::slug($request->category_name),
                    ];
                    DB::beginTransaction();
                    if ($request->hasFile('icon')) {
                        $pathString = 'uploads/category/icons';
                        $s3UploadRes = uploadFileAwsBucket($pathString, $request->file('icon'));
                        $icon = $s3UploadRes;
                        $updateArr['category_icon'] =  $icon;
                    }

                    if (Category::where('category_name', $updateArr['category_name'])->where('parent_id', 0)->where('id', '!=', $id)->exists()) {
                        return redirect()->back()->withErrors(['Category name already exist.'])->withInput($request->all());
                    }
                    $response = Category::UpdateOrCreate(['id' => $id], $updateArr);
                    DB::commit();
                    return redirect('admin/categories/list')->with('success', 'Category details updated successfully.');
                }

                $page_title = 'Category Management';
                $page_description = 'Edit Category';
                $details = Category::where('id', $id)->first();
                if ($details) {
                    $pageSettings = $this->pageSetting('edit', ['title' => $details->category_name]);

                    $page_title =  $pageSettings['page_title'];
                    $page_description = $pageSettings['page_description'];
                    $breadcrumbs = $pageSettings['breadcrumbs'];
                    return view('admin.pages.category.edit', compact('page_title', 'page_description', 'breadcrumbs', 'details'));
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

    public function import(Request $request)
    {
        // DB::beginTransaction();

        try {
            if ($request->isMethod('post')) {
                $file = $request->file('uploaded_file');
                if ($file) {
                    $validatedData = $request->validate([

                        'uploaded_file' => 'required',

                    ]);
                    $insertArr = [];
                    $rawData = Excel::toArray('', $request->file('uploaded_file'), null, \Maatwebsite\Excel\Excel::TSV)[0];
                    // dd($rawData);
                    if (count($rawData) > 0) {


                        DB::table('categories_import')->truncate();
                        foreach ($rawData as $key => $row) {

                            // note that these fields are completely different for you as your database fields and excel fields so replace them with your own database fields
                            if ($key > 0) {

                                //  dd($row);
                                $insertArr[] = [
                                    'category_name' => strtoupper($row[0]),
                                    'slug' => \Str::slug($row[0]),
                                    'status' => 1,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'created_at' => date('Y-m-d H:i:s'),
                                ];
                            }
                        }
                        // dd($insertArr);
                        $inds = CategoryImport::insert($insertArr);
                        // dd($insertArr);

                        // DB::statement("UPDATE seo_categories_import SET slug = REPLACE(slug, '-', ' ') where slug is NULL");

                        DB::statement("SET sql_mode = ''");


                        $results =   DB::select("SELECT category_name,COUNT(category_name) as duplicates FROM seo_categories_import group by category_name having count(*) >= 2");

                        if (count($results) > 0) {
                            $i = 1;
                            $html = '<h3>Followings are duplicate state name</h3>';
                            foreach ($results as $key => $list) {
                                $html .=  $i++ . ') ' . $list->name . ' - ' . $list->name . ' - ' . $list->duplicates . '<br>';
                            }
                            return redirect()->back()->withErrors([$html]);
                        }
                        // dd($results);

                        DB::statement("UPDATE seo_categories as t JOIN seo_categories_import itt ON itt.category_name = t.category_name SET t.category_name = itt.category_name , t.status = itt.status , t.slug = itt.slug, t.created_at = itt.created_at , t.updated_at = itt.updated_at
                        WHERE t.category_name = itt.category_name");

                        DB::statement("INSERT INTO seo_categories (category_name,status,slug,created_at,updated_at) SELECT category_name,status,slug,created_at,updated_at FROM seo_categories_import WHERE category_name NOT IN (SELECT category_name FROM seo_categories) AND deleted_at is NULL");

                        DB::commit();
                        return redirect('admin/categories/list')->with('success', 'Category details updated successfully.');
                    }
                }
            }

            $pageSettings = $this->pageSetting('import');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];

            return view('admin.pages.category.import', compact('page_title', 'page_description', 'breadcrumbs'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
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
                    return redirect()->back()->with('success', 'Category deleted successfully.');
                } else {
                    return redirect()->back()->with('error', 'Failed to delete try again.');
                }
            } else {
                return redirect()->back()->with('error', 'Category details not found.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
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
                return redirect('admin/categories/list')->with('success', 'City status updated successfully.');
            } else {
                return redirect()->back()->with('error', 'City details not found.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage())->withInput($request->all());
        }
    }
    public function pageSetting($action, $dataArray = [])
    {
        if ($action == 'edit') {
            $data['page_title'] = 'Category';
            $data['page_description'] = 'Edit Category';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Category Management',
                    'url' => url('admin/categories/list'),
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

                    'title' => 'Edit Category',
                    'url' => '',

                ];
            }
            return $data;
        }

        if ($action == 'add') {
            $data['page_title'] = 'Category';
            $data['page_description'] = 'Add a New Category';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Category Management',
                    'url' => url('admin/categories/list'),
                ],
                [
                    'title' => 'Add Category',
                    'url' => '',
                ],
            ];
            return $data;
        }

        if ($action == 'import') {
            $data['page_title'] = 'Category';
            $data['page_description'] = 'Import Category';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Category Management',
                    'url' => url('admin/categories/list'),
                ],
                [
                    'title' => 'Import Category',
                    'url' => '',
                ],
            ];
            return $data;
        }




    }
    function exportExcel()
    {
        $type = request('type');
        if ($type == 'excel')
            return Excel::download(new CategoryExport, 'Categories.xlsx');
        else
            return redirect()->back()->withErrors(['Export type not defined.']);
    }



    function CategoryAPi(){
        $settings = DB::table('settings')->first();
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $settings->base_url2 . '/getAllCategories' ,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Cookie: XSRF-TOKEN=eyJpdiI6IjJsOWJBRWlHYjRLdlRGSWZIT2Q2bWc9PSIsInZhbHVlIjoidE5UVC8weCsydkFJZEEyalVLTk40YTZ6Ym1laS9XQStKczk5Y1FocDIrcTJjcDVwVUkvWmFrT2FVRTBwUFlEeU5Va25XY0dtVS9naG9mcGFpRXROckZPT0t5L3g0WVMyRTQ3Rm9ZRTVkTmtpK2FjK1pCa0tiR2ZOd3IxK0pNV1giLCJtYWMiOiI4YWY1ZmI4MzMxNzEwNzZhNzExOGRjMTE3ZGUyZWM5OTRmM2Y4ZTNlZDA5YTg4MzRkYzIwYWNkNDYxNDI1OTQyIiwidGFnIjoiIn0%3D; seo_engine_master_session=eyJpdiI6ImE0aUEwc0dyS1EzWVZ6VmR6ZmdJNXc9PSIsInZhbHVlIjoib3JaMDdEblZaUUJUd0NqSTdocjE3aGo2S3QxWUMwazc0bHRZZjhnUjdoWEtaZ2Q5MlQ4cGIwWjZ6aEh2bDk5M0ZrVVRwaEdBaEtvMXoxM1Q5dk1kZ0FiZFVyS0pCNFA5bHAxZGtvcTVZbS9RT0tVTSs5dlMxaVRHcTBuS1p4ZnMiLCJtYWMiOiI3NGNjNzY0NGNiNGNhMTU5YjZjM2YxMTc1OTQwNDBhMTBmZTcxN2FmZjBmMzcxNWQyNGNkODIxMDkyYTUxNWEyIiwidGFnIjoiIn0%3D'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }





    public function sync(){
        // dd('sync');
        $data = CategoryAPi();
       // return  $data;
        if($data){
            $data = json_decode($data,true);
            if($data != null){
            if($data['Success'] == true){
                // dd($data['Result']);
                // dd($data['Result'] && $data['Result']['Categories']);
                if($data['Result'] && $data['Result']['Categories']){
                    $categories = $data['Result']['Categories'];
                    // dd($localities);
                    foreach($categories as $key => $category){
                        $updateArr = [
                            "parent_id" => $category['parent_id'],
                            "category_name" => $category['category_name'],
                            "slug" => $category['slug'],
                            "category_banner" => $category['category_banner'],
                            "category_icon" => $category['category_icon'],
                            "category_heading" => $category['category_heading'],
                            "category_short_description" => $category['category_short_description'],
                            "category_details" => $category['category_details'],
                            "show_on_home" => $category['show_on_home'],
                            "position" => $category['position'],
                            "sequence" => $category['sequence'],
                            "tags" => $category['tags'],
                            "seo_title" => $category['seo_title'],
                            "seo_description" => $category['seo_description'],
                            "seo_kewords" => $category['seo_kewords'],
                            "status" => 1,
                            "created_at" => $category['created_at'],
                            "updated_at" => $category['updated_at'],
                            "deleted_at" => $category['deleted_at'],
                            "priority_sequence" => $category['priority_sequence'],
                        ];
                        // dd($updateArr);

                        $response = Category::UpdateOrCreate(['category_name' => $updateArr['category_name']], $updateArr);
                    }

                    return redirect('admin/categories/list')->with('success', 'category details sync successfully.');
                }
                else{
                    return redirect('admin/categories/list')->with('error', 'Something went wrong.');
                }
            }}
            else{
                return redirect('admin/categories/list')->with('error', 'Something went wrong.');
            }
        }
    }




}
