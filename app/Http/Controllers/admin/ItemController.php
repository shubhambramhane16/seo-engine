<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\PathologyTest as Tests;
use App\Models\PathologyTestImport as TestsImport;
use App\Models\Category;
use App\Models\City;
use DB;
use Validator;
use Image;
use File;
use App\Exports\Excel\TestExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\Paginator;

class ItemController extends Controller
{
    public function index()
    {
        try {
            Paginator::useBootstrapThree();
            $page_title = 'Items';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Items',
                    'url' => '',
                ]
            ];
            $categoryId = request('category_id');
            $subCategoryId = request('sub_category_id');
            $departmentId = request('department_id');
            $status = request('status');
            if ($status == '0') {
                $status = '2';
            }
            $searchTerm = request('test_name');
            $tests = Tests::with(['department_data'])->when($departmentId, function ($doctors) use ($departmentId) {
                $doctors->where('department_id', '=', $departmentId);
            })->when($status, function ($doctors) use ($status) {
                if ($status != '-1') {
                    $status = conditionalStatus($status);
                    $doctors->where('status', '=', $status);
                }
            })->when($categoryId, function ($doctors) use ($categoryId) {
                if ($categoryId) {
                    $doctors->whereRaw(DB::raw('(categories REGEXP "' . $categoryId . '")'));
                }
            })->when($subCategoryId, function ($doctors) use ($subCategoryId) {
                if ($subCategoryId) {
                    $doctors->whereRaw(DB::raw('(sub_categories REGEXP "' . $subCategoryId . '")'));
                }
            })->when($searchTerm, function ($data) use ($searchTerm) {
                $data->whereRaw("(test_name like '%" . $searchTerm . "%' OR test_code like '%" . $searchTerm . "%' )");
            })->orderBy('id', 'desc')->paginate(100);
            // dd(  $tests);
            $departments = Department::where('status', 1)->orderBy('id', 'desc')->get();
            $categories = Category::where('status', 1)->where('parent_id', 0)->orderBy('id', 'desc')->get();
            // dd($doctors);
            return view('admin.pages.items.list', compact('page_title', 'page_description', 'breadcrumbs',  'tests',  'departments', 'categories'));
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
                    'test_code' => 'required',
                    'test_name' => 'required',
                    'lab_name' => 'required',
                    // 'test_type' => 'required',
                    'sample_type' => 'required',
                    'sample_remarks' => 'required',
                    'components' => 'required',
                    // 'component_count' => 'required',
                    'recommendation' => 'required',
                    // 'age_group' => 'required',
                    'mrp' => 'required',
                    'selling_price' => 'required',
                    'report_tat' => 'required',
                    'technique' => 'required',
                    'temperature' => 'required',
                    'cut_off' => 'required',
                    // 'category_id' => 'required',
                    // 'sub_category_id' => 'required',
                    'department_id' => 'required',
                    'billing_category' => 'required',
                    'schedule' => 'required',
                    'test_alias_name' => '',
                    // 'specialities' => 'required',
                ], [
                    'test_code.required' => 'Test code is required.',
                    'test_name.required' => 'Test name is required.',
                    'lab_name.required' => 'Lab name is required.',
                    'test_type.required' => 'Test type is required.',
                    'sample_type.required' => 'Sample type is required.',
                    'sample_remarks.required' => 'Sample remarks is required.',
                    // 'component_count.required' => 'Component count is required.',
                    'components.required' => 'Component is required.',
                    'recommendation.required' => 'Recommendation is required.',
                    'age_group.required' => 'Age group is required.',
                    'selling_price.required' => 'Selling price is required.',
                    'mrp.required' => 'MRP is required.',
                    'report_tat.required' => 'Report Tat is required.',
                    'technique.required' => 'Technique is required.',
                    'temperature.required' => 'Temperature is required.',
                    'cut_off.required' => 'Cut off is required.',
                    'category_id.required' => 'Category is required.',
                    'sub_category_id.required' => 'Sub Category is required.',
                    'department_id.required' => 'Department is required.',
                    'billing_category.required' => 'Billing Category is required.',
                    'schedule.required' => 'Schedule is required.',
                    'specialities.required' => 'Speciality is required.',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                }

                DB::beginTransaction();


                $slug_name = $request->test_name;
                $ltrim = ltrim($slug_name);
                $rtrim = rtrim($ltrim);
                $slug = \Str::slug($rtrim);



                $array = [
                    'test_name' => $request->test_name,
                    'slug' => $slug,
                    'test_code' => $request->test_code,
                    'test_type' => $request->test_type,
                    'lab_name' => $request->lab_name,
                    // 'category_id' => $request->category_id,
                    // 'department_id' => $request->department_id,
                    'recommendation' => $request->recommendation,
                    'age_group' => $request->age_group,
                    'report_tat' => $request->report_tat,
                    'technique' => $request->technique,
                    'specimen' => $request->specimen,
                    'temperature' => $request->temperature,
                    'cut_off' => $request->cut_off,
                    'description' => $request->description,
                    'remarks' => $request->remarks,
                    'sample_remarks' => $request->sample_remarks,
                    'sample_report' => $request->sample_report,
                    'sample_type' => $request->sample_type,
                    'billing_category' => $request->billing_category,
                    'mrp' => $request->mrp,
                    'selling_price' => $request->selling_price,
                    'profile' => $request->profile,
                    'container' => $request->container,
                    'volume' => $request->volume,
                    'method' => $request->method,
                    'gender' => $request->gender,
                    'schedule' => $request->schedule,
                    'instructions' => $request->instructions,
                    'run_days_at_section' => $request->run_days_at_section,
                    'test_alias_name' => $request->test_alias_name,

                ];
                if (isset($request->department_id) && count($request->department_id) > 0) {
                    $array['department_id'] = $request->department_id[0];
                    $array['other_departments'] = implode(',', $request->department_id);
                }
                if (isset($request->priority)) {
                    $array['priority_sequence'] = $request->prioritysequence;
                }
                if (isset($request->is_trending)) {
                    $array['is_trending'] = 1;
                }
                if (isset($request->category_id) && count($request->category_id) > 0) {
                    $array['categories'] = json_encode($request->category_id);
                }
                if (isset($request->sub_category_id) && count($request->sub_category_id) > 0) {
                    $array['sub_categories'] = json_encode($request->sub_category_id);
                }
                if (isset($request->specialities) && count($request->specialities) > 0) {
                    $array['specialities'] = json_encode($request->specialities);
                }
                if (isset($request->components) && count($request->components) > 0) {
                    $components = [];
                    foreach ($request->components as $sKey => $sList) {
                        if ($sList) {
                            $components[] = [
                                'title' => $sList,
                            ];
                        }
                    }
                    if ($component_count = count($components) > 0) {
                        $array['components'] = json_encode($components);
                        $array['component_count'] = $component_count;
                    }
                }

                if (isset($request->faqs_ids) && count($request->faqs_ids) > 0) {
                    $array['faqs_ids'] = json_encode($request->faqs_ids);
                }
                // dd($array);
                $TestCodeExist = Tests::where('test_code', $array['test_code'])->exists();
                if ($TestCodeExist) {
                    return redirect()->back()->withErrors(['Test code already exist.'])->withInput($request->all());
                }

                $response = Tests::UpdateOrCreate(['id' => null], $array);
                DB::commit();
                return redirect('admin/tests/list')->with('success', 'Test details added successfully.');
            }

            $pageSettings = $this->pageSetting('add');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];

            $departments = Department::where('status', 1)->orderBy('id', 'desc')->get();
            $categories = Category::where('status', 1)->where('parent_id', 0)->orderBy('id', 'desc')->get();
            return view('admin.pages.items.add', compact('page_title', 'page_description', 'breadcrumbs', 'departments', 'categories'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage())->withInput($request->all());
        }
    }
    public function edit(Request $request, $id)
    {

        try {
            if ($request->isMethod('post')) {
                // dd($request->all());
                $validator = Validator::make($request->all(), [
                    'test_code' => 'required',
                    'test_name' => 'required',
                    'lab_name' => 'required',
                    // 'test_type' => 'required',
                    'sample_type' => 'required',
                    'sample_remarks' => 'required',
                    'components' => 'required',
                    // 'component_count' => 'required',
                    'recommendation' => 'required',
                    // 'age_group' => 'required',
                    'mrp' => 'required',
                    'selling_price' => 'required',
                    'report_tat' => 'required',
                    'technique' => 'required',
                    'temperature' => 'required',
                    'cut_off' => 'required',
                    // 'category_id' => 'required',
                    // 'sub_category_id' => 'required',
                    'department_id' => 'required',
                    'billing_category' => 'required',
                    'schedule' => 'required',
                    // 'specialities' => 'required',
                ], [
                    'test_code.required' => 'Test code is required.',
                    'test_name.required' => 'Test name is required.',
                    'lab_name.required' => 'Lab name is required.',
                    'test_type.required' => 'Test type is required.',
                    'sample_type.required' => 'Sample type is required.',
                    'sample_remarks.required' => 'Sample remarks is required.',
                    // 'component_count.required' => 'Component count is required.',
                    'components.required' => 'Component is required.',
                    'recommendation.required' => 'Recommendation is required.',
                    'age_group.required' => 'Age group is required.',
                    'selling_price.required' => 'Selling price is required.',
                    'mrp.required' => 'MRP is required.',
                    'report_tat.required' => 'Report Tat is required.',
                    'technique.required' => 'Technique is required.',
                    'temperature.required' => 'Temperature is required.',
                    'cut_off.required' => 'Cut off is required.',
                    'category_id.required' => 'Category is required.',
                    'sub_category_id.required' => 'Sub Category is required.',
                    'department_id.required' => 'Department is required.',
                    'billing_category.required' => 'Billing Category is required.',
                    'schedule.required' => 'Schedule is required.',
                    'specialities.required' => 'Speciality is required.',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                }

                DB::beginTransaction();

                if (isset($request->slug)) {
                    if ($request->slug !== null && $request->slug !== "") {
                        $slug_name = $request->slug;
                        $ltrim = ltrim($slug_name);
                        $rtrim = rtrim($ltrim);
                        $slug = \Str::slug($rtrim);
                    }
                } else {
                    $slug_name = $request->test_name;
                    $ltrim = ltrim($slug_name);
                    $rtrim = rtrim($ltrim);
                    $slug = \Str::slug($rtrim);
                }

                $array = [
                    'test_name' => $request->test_name,
                    'slug' => $slug,
                    'test_code' => $request->test_code,
                    'test_type' => $request->test_type,
                    'lab_name' => $request->lab_name,
                    // 'category_id' => $request->category_id,
                    // 'department_id' => $request->department_id,
                    'recommendation' => $request->recommendation,
                    'age_group' => $request->age_group,
                    'report_tat' => $request->report_tat,
                    'technique' => $request->technique,
                    'specimen' => $request->specimen,
                    'temperature' => $request->temperature,
                    'cut_off' => $request->cut_off,
                    'description' => $request->description,
                    'remarks' => $request->remarks,
                    'sample_remarks' => $request->sample_remarks,
                    'sample_report' => $request->sample_report,
                    'sample_type' => $request->sample_type,
                    'billing_category' => $request->billing_category,
                    'mrp' => $request->mrp,
                    'selling_price' => $request->selling_price,
                    'profile' => $request->profile,
                    'container' => $request->container,
                    'volume' => $request->volume,
                    'method' => $request->method,
                    'gender' => $request->gender,
                    'schedule' => $request->schedule,
                    'instructions' => $request->instructions,
                    'test_alias_name' => $request->test_alias_name,
                    'run_days_at_section' => $request->run_days_at_section,

                ];
                if (isset($request->department_id) && count($request->department_id) > 0) {
                    $array['department_id'] = $request->department_id[0];
                    $array['other_departments'] = implode(',', $request->department_id);
                }
                if (isset($request->priority)) {
                    $array['priority_sequence'] = $request->prioritysequence;
                }
                if (isset($request->is_trending)) {
                    $array['is_trending'] = 1;
                }
                if (isset($request->category_id) && count($request->category_id) > 0) {
                    $array['categories'] = json_encode($request->category_id);
                }
                if (isset($request->sub_category_id) && count($request->sub_category_id) > 0) {
                    $array['sub_categories'] = json_encode($request->sub_category_id);
                }
                if (isset($request->specialities) && count($request->specialities) > 0) {
                    $array['specialities'] = json_encode($request->specialities);
                }
                if (isset($request->components) && count($request->components) > 0) {
                    $components = [];
                    foreach ($request->components as $sKey => $sList) {
                        if ($sList) {
                            $components[] = [
                                'title' => $sList,
                            ];
                        }
                    }
                    if ($component_count = count($components) > 0) {
                        $array['components'] = json_encode($components);
                        $array['component_count'] = $component_count;
                    }
                }
                if (isset($request->city_wise_price) && count($request->city_wise_price) > 0) {
                    $cityWiseArr = [];
                    foreach ($request->city_wise_price as $cityId => $cityPrice) {

                        if ($cityPrice) {
                            $price = $cityPrice;
                        } else {
                            $price = 0;
                        }
                        if (isset($request['city_id'][$cityId]) && !empty($request['city_id'][$cityId])) {
                            $avail = 1;
                        } else {
                            $avail = 0;
                        }
                        $cityWiseArr[] = [
                            'city_id' => $cityId,
                            'locality_id' => $request['locality_id'][$cityId] ?? 0,
                            'availability' => $avail,
                            'city_price' => $price,
                        ];
                    }
                    if (count($cityWiseArr) > 0) {
                        $array['citywise_prices'] = json_encode($cityWiseArr);
                    }
                }

                if (isset($request->faqs_ids) && count($request->faqs_ids) > 0) {
                    $array['faqs_ids'] = json_encode($request->faqs_ids);
                }
                $TestCodeExist = Tests::where('test_code', $array['test_code'])->where('id', '<>', $id)->exists();
                if ($TestCodeExist) {
                    return redirect()->back()->withErrors(['Test code already exist.'])->withInput($request->all());
                }

                $TestCodeSlug = Tests::where('slug', $array['slug'])->where('id', '<>', $id)->exists();
                if ($TestCodeSlug) {
                    return redirect()->back()->withErrors(['Test or slug already exist.'])->withInput($request->all());
                }

                $response = Tests::UpdateOrCreate(['id' => $id], $array);
                DB::commit();
                return redirect('admin/tests/list')->with('success', 'Test details updated successfully.');
            }

            $pageSettings = $this->pageSetting('edit');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];

            $details = Tests::where('id', $id)->first();
            if ($details) {
                $departments = Department::where('status', 1)->orderBy('id', 'desc')->get();
                $cities = City::where('status', 1)->orderBy('name', 'asc')->get();
                $categories = Category::where('status', 1)->where('parent_id', 0)->orderBy('id', 'desc')->get();
                return view('admin.pages.items.edit', compact('page_title', 'page_description', 'breadcrumbs', 'departments', 'cities', 'categories', 'details'));
            } else {

                return redirect('admin/tests/list')->withErrors(['Test details not found.'])->withInput($request->all());
            }
        } catch (\Exception $e) {
            dd($e);
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
                    if (count($rawData) > 0) {


                        DB::table('pathology_tests_import')->truncate();
                        foreach ($rawData as $key => $row) {

                            // note that these fields are completely different for you as your database fields and excel fields so replace them with your own database fields
                            if ($key > 0) {

                                //  dd($row);
                                $insertArr[] = [
                                    'test_code' => $row[0],
                                    'test_name' => $row[1],
                                    'slug' => null,
                                    'lab_name' => $row[2],
                                    'component_count' => $row[3] ?  $row[3] : 0,
                                    'recommendation' => $row[4],
                                    'age_group' => $row[5],
                                    'mrp' => $row[6] ? intval($row[6]) : 0,
                                    'selling_price' => $row[7] ? intval($row[7]) : 0,
                                    'technique' => $row[8],
                                    'specimen' => $row[9],
                                    'temperature' => $row[10],
                                    'instructions' => $row[11],
                                    'container' => $row[12],
                                    'volume' => $row[13],
                                    'method' => $row[14],
                                    'schedule' => $row[15],
                                    'category' => $row[16],
                                    'profile' => $row[17],
                                    'cut_off' => $row[18],
                                    'gender' => $row[19],
                                    'description' => $row[20],
                                    'categories' => $row[21] ? getcategoriesids($row[21]) : null,
                                    'sub_categories' => $row[22] ? getsubcategoriesids($row[22]) : null,
                                    'department' => (isset($row[23]) && $row[23]) ? getdepartmentidsfirst($row[23]) : null,
                                    'other_departments' => (isset($row[23]) && $row[23]) ?  getdepartmentids($row[23]) : null,
                                    'report_tat' => (isset($row[24]) && $row[24]) ? $row[24] : null,
                                    'components' => (isset($row[25]) && $row[25]) ? $row[25] : null,
                                    'test_alias_name' => (isset($row[26]) && $row[26]) ? $row[26] : null,
                                    'billing_category' => (isset($row[27]) && $row[27]) ? $row[27] : null,
                                    'sample_type' => (isset($row[28]) && $row[28]) ? $row[28] : null,
                                    'sample_remarks' => (isset($row[29]) && $row[29]) ? $row[29] : null,
                                    'status' => 1,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'created_at' => date('Y-m-d H:i:s'),
                                ];
                            }
                        }
                        $inds = TestsImport::insert($insertArr);

                        DB::statement("UPDATE ocq_pathology_tests_import SET slug = lower(CONCAT(test_name,'-',test_code,'-',id)),slug = replace(slug, '.', ' '),slug = replace(slug, '', '-'),slug = replace(slug, '/', ''),slug = replace(slug, '!', ''),slug = replace(slug, '@', ''),slug = replace(slug, '#', ''),slug = replace(slug, '$', ''),slug = replace(slug, '%', ''),slug = replace(slug, '^', ''),slug = replace(slug, '&', ''),slug = replace(slug, '*', ''),slug = replace(slug, '(', ''),slug = replace(slug, ')', ''),slug = replace(slug, '_', ''),slug = replace(slug, '=', ''),slug = replace(slug, '+', ''),slug = replace(slug, '~', ''),slug = replace(slug, '`', ''),slug = replace(slug, '}', ''),slug = replace(slug, '{', ''),slug = replace(slug, '|', ''),slug = replace(slug, '?', ''),slug = replace(slug, '>', ''),slug = replace(slug, '<', ''),slug = replace(slug, '.', ''),slug = trim(slug),slug = replace(slug, ' ', '-'),slug = replace(slug, '--', '-') where slug is NULL");

                        DB::statement("SET sql_mode = ''");


                        $results =   DB::select("SELECT test_name,test_code,COUNT(test_code) as duplicates FROM ocq_pathology_tests_import group by test_code having count(*) >= 2");

                        if (count($results) > 0) {
                            $i = 1;
                            $html = '<h3>Followings are duplicate tests found.</h3> <br>';
                            foreach ($results as $key => $list) {
                                $html .=  $i++ . ') ' . $list->test_name . '[' . $list->test_code . '][duplicate counts - ' . $list->duplicates . ']<br> ';
                            }
                            return redirect()->back()->withErrors([$html]);
                        }

                        DB::statement("UPDATE ocq_pathology_tests as t JOIN ocq_pathology_tests_import itt ON itt.test_code = t.test_code SET t.test_name = itt.test_name, t.slug = itt.slug, t.lab_name = itt.lab_name, t.component_count = itt.component_count, t.recommendation = itt.recommendation, t.age_group = itt.age_group, t.mrp = itt.mrp, t.selling_price = itt.selling_price, t.technique = itt.technique, t.specimen = itt.specimen, t.temperature = itt.temperature, t.instructions = itt.instructions, t.container = itt.container, t.volume = itt.volume, t.method = itt.method, t.test_type = itt.test_type, t.profile = itt.profile, t.cut_off = itt.cut_off, t.gender = itt.gender, t.description = itt.description, t.categories = itt.categories, t.sub_categories = itt.sub_categories, t.department_id = (select d.id from ocq_departments d WHERE d.department_name Like CONCAT('%',itt.department,'%') limit 1), t.department = itt.department, t.schedule = itt.schedule,t.report_tat = itt.report_tat,t.other_departments = itt.other_departments,t.test_alias_name = itt.test_alias_name,t.billing_category = itt.billing_category,t.category = itt.category,t.sample_type = itt.sample_type,t.sample_remarks = itt.sample_remarks,t.components = itt.components WHERE t.test_code = itt.test_code AND t.deleted_at is NULL ");

                        DB::statement("INSERT INTO ocq_pathology_tests (test_code, test_name, slug, lab_name, component_count, recommendation, age_group, mrp, selling_price, technique, specimen, temperature, instructions, container, volume, method, test_type, profile, cut_off, gender, description, categories, sub_categories, department_id, department,schedule,report_tat, other_departments,test_alias_name, billing_category, category,sample_type, sample_remarks, components) SELECT test_code, test_name, slug, lab_name, component_count, recommendation, age_group, mrp, selling_price, technique, specimen, temperature, instructions, container, volume, method, test_type, profile, cut_off, gender, description, categories, sub_categories, department_id, department,schedule,report_tat,other_departments,test_alias_name, billing_category,category,sample_type, sample_remarks, components FROM ocq_pathology_tests_import as ft WHERE ft.test_code NOT IN (SELECT DISTINCT test_code FROM ocq_pathology_tests t WHERE t.test_code = ft.test_code AND t.deleted_at is NULL ) ");

                        DB::commit();
                        return redirect('admin/tests/list')->with('success', 'Test Imported successfully.');
                    }
                }
            }

            $pageSettings = $this->pageSetting('import');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];

            return view('admin.pages.items.import', compact('page_title', 'page_description', 'breadcrumbs'));
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
                $cat = Tests::find($id);
                if ($cat->delete()) {
                    DB::commit();
                    return redirect()->back()->with('success', 'Test deleted successfully.');
                } else {
                    return redirect()->back()->with('error', 'Failed to delete try again.');
                }
            } else {
                return redirect()->back()->with('error', 'Test details not found.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function updateStatus($id, $status)
    {
        try {
            if ($id) {
                DB::beginTransaction();
                $status = ($status == 1) ? $status = 0 : $status = 1;
                $updateArr = [
                    'status' => $status,
                ];
                $response = Tests::UpdateOrCreate(['id' => $id], $updateArr);
                DB::commit();
                return redirect('admin/tests/list')->with('success', 'Test status updated successfully.');
            } else {
                return redirect()->back()->with('error', 'Test details not found.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }




    public function pageSetting($action, $dataArray = [])
    {
        if ($action == 'edit') {
            $data['page_title'] = 'Items';
            $data['page_description'] = 'Edit Items';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Items',
                    'url' => url('admin/items/list'),
                ],
                [
                    'title' => 'Edit Items',
                    'url' => '',
                ],
            ];
            if (isset($dataArray['title']) && !empty($dataArray['title'])) {
                $data['breadcrumbs'][] =
                    [
                        'title' => $dataArray['title'],
                        'url' => '',

                    ];
            }
            return $data;
        }

        if ($action == 'add') {
            $data['page_title'] = 'Items';
            $data['page_description'] = 'Add a Items';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Items',
                    'url' => url('admin/items/list'),
                ],
                [
                    'title' => 'Add a Items',
                    'url' => '',
                ],
            ];
            return $data;
        }
        if ($action == 'import') {
            $data['page_title'] = 'Import Items';
            $data['page_description'] = 'Import Items';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Items',
                    'url' => url('admin/items/list'),
                ],
                [
                    'title' => 'Import Items',
                    'url' => '',
                ],
            ];
            return $data;
        }
    }

    function searchTestsFrontend(Request $request)
    {
        try {

            $departments = Department::where('status', 1)->get();
            if ($request->isMethod('post')) {
                $globalSearch  = request('global_search');
                $searchTerm  = request('test_name');
                $department  = request('Department');
                $errorFlag = false;
                $error = 'Please enter test name or text code.';
                if ($globalSearch) {
                    $errorFlag = true;
                }
                if ($searchTerm) {
                    $errorFlag = true;
                    $globalSearch = false;
                }
                if (!$errorFlag) {
                    // return redirect('search-tests')->withErrors($error)->withInput();
                    $limit = 100;
                } else {
                    $limit = 100;
                }
                if ($globalSearch) {
                    $lists  = Tests::select(
                        '*',
                        'pathology_tests.id as test_id',
                        'pathology_tests.description as test_description'
                    )
                        ->leftJoin('departments as d', 'd.id', 'pathology_tests.department_id')
                        ->where('pathology_tests.status', 1)
                        ->when($globalSearch, function ($data) use ($globalSearch) {
                            $data->whereRaw('(test_name like "%' . $globalSearch . '%" OR test_code like "%' . $globalSearch . '%"  OR department_name like "%' . $globalSearch . '%"  OR department like "%' . $globalSearch . '%"   OR test_alias_name like "%' . $globalSearch . '%"  OR components like "%' . $globalSearch . '%" )');
                        })
                        ->when($department, function ($data) use ($department) {
                            // dd($department);
                            $sqlStr = '(department_id = ' . $department . ' OR FIND_IN_SET("other_departments","' . $department . '"))';
                            // $data->where("pathology_tests.department_id", '=', $department);
                            $data->whereRaw($sqlStr);
                        })
                        ->when($limit, function ($data) use ($limit) {
                            $data->limit($limit);
                        })
                        ->get();
                } else {
                    $lists  = Tests::select(
                        '*',
                        'pathology_tests.id as test_id',
                        'pathology_tests.description as test_description'
                    )
                        ->leftJoin('departments as d', 'd.id', 'pathology_tests.department_id')
                        ->where('pathology_tests.status', 1)
                        ->when($searchTerm, function ($data) use ($searchTerm) {
                            $data->whereRaw('(test_name like "%' . $searchTerm . '%" OR test_code like "%' . $searchTerm . '%"   OR test_alias_name like "%' . $searchTerm . '%"  )');
                        })
                        ->when($department, function ($data) use ($department) {
                            // $data->where("pathology_tests.department_id", '=', $department);
                            // dd($department);
                            $sqlStr = '(department_id = ' . $department . ' OR FIND_IN_SET("' . $department . '",other_departments))';
                            $data->whereRaw($sqlStr);
                        })
                        ->when($limit, function ($data) use ($limit) {
                            $data->limit($limit);
                        })
                        ->get();
                }
                $cityList = getCityListArray();
                return view('frontend.search-tests', compact('lists', 'departments', 'cityList'));
            }
            $lists  = [];
            return view('frontend.search-tests', compact('lists', 'departments'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    function searchTestsAjax(Request $request)
    {
        try {
            $searchTerm  = request('query');
            if ($request->isMethod('post') && $searchTerm) {

                $errorFlag = false;
                $error = 'Please enter test name or text code.';

                $lists  = Tests::select(
                    '*',
                    'pathology_tests.id as test_id',
                    'pathology_tests.description as test_description'
                )->leftJoin('departments as d', 'd.id', 'pathology_tests.department_id')->where('pathology_tests.status', 1)->when($searchTerm, function ($data) use ($searchTerm) {
                    $data->whereRaw("(test_name like '%" . $searchTerm . "%' OR test_code like '%" . $searchTerm . "%' )");
                })->get();

                $result['Result'] =  $lists;
                $result['Success'] = 'True';
                $result['Message'] = '';
                return response()->json($result);
            } else {
                $result['Result'] = [];
                $result['Success'] = 'False';
                $result['Message'] = 'Search string is empty.';
                return response()->json($result);
            }
        } catch (\Exception $e) {
            dd($e);
            $result['Result'] = [];
            $result['Success'] = 'False';
            $result['Message'] = $e->getMessage();
            return response()->json($result);
        }
    }
    function exportExcel()
    {
        $type = request('type');
        if ($type == 'excel')
            return Excel::download(new TestExport, 'items.xlsx');
        else
            return redirect()->back()->withErrors(['Export type not defined.']);
    }



    function ItemsAPi(){

        $settings = DB::table('settings')->first();
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $settings->base_url2 . '/getAllitems' ,
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
        $data = ItemsAPi();
        if($data){
            $data = json_decode($data,true);
            if($data != null){
            if($data['Success'] == true){
                // dd($data['Result']);
                // dd($data['Result'] && $data['Result']['Items']);
                if($data['Result'] && $data['Result']['Items']){
                    $items = $data['Result']['Items'];
                    foreach($items as $key => $item){
                        $updateArr = [
                            "test_code" => $item['test_code'],
                            "test_name" => $item['test_name'],
                            "test_alias_name" => $item['test_alias_name'],
                            "slug" => $item['slug'],
                            "lab_name" => $item['lab_name'],
                            "component_count" => $item['component_count'],
                            "recommendation" => $item['recommendation'],
                            "age_group" => $item['age_group'],
                            "mrp" => $item['mrp'],
                            "selling_price" => $item['selling_price'],
                            "citywise_prices" => $item['citywise_prices'],
                            "description" => $item['description'],
                            "show_ontop" => $item['show_ontop'],
                            "priority_sequence" => $item['priority_sequence'],
                            "components" => $item['components'],
                            "report_tat" => $item['report_tat'],
                            "categories" => $item['categories'],
                            "sub_categories" => $item['sub_categories'],
                            "department_id" => $item['department_id'],
                            "other_departments" => $item['other_departments'],
                            "specialities" => $item['specialities'],
                            "technique" => $item['technique'],
                            "specimen" => $item['specimen'],
                            "temperature" => $item['temperature'],
                            "run_days_at_section" => $item['run_days_at_section'],
                            "remarks" => $item['remarks'],
                            "status" => 1,
                            "deleted_at" => null,
                            'created_by' => auth()->user()->id,
                            'updated_by' => auth()->user()->id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'created_at' => date('Y-m-d H:i:s'),
                            "instructions" => $item['instructions'],
                            "container" => $item['container'],
                            "volume" => $item['volume'],
                            "method" => $item['method'],
                            "schedule" => $item['schedule'],
                            "test_type" => $item['test_type'],
                            "profile" => $item['profile'],
                            "cut_off" => $item['cut_off'],
                            "faqs_ids" => $item['faqs_ids'],
                            "gender" => $item['gender'],
                            "department" => $item['department'],
                            "category" => $item['category'],
                            "sample_report" => $item['sample_report'],
                            "is_trending" => $item['is_trending'],
                            "sample_type" => $item['sample_type'],
                            "billing_category" => $item['billing_category'],
                            "sample_remarks" => $item['sample_remarks'],


                         ];

                        $response = Tests::UpdateOrCreate(['id' => $updateArr['test_code']], $updateArr);
                    }


                    return redirect('admin/items/list')->with('success', 'items details sync successfully.');
                }
                else{
                    return redirect('admin/items/list')->with('error', 'Something went wrong.');
                }
            }}
            else{
                return redirect('admin/items/list')->with('error', 'Something went wrong.');
            }
        }
    }



}
