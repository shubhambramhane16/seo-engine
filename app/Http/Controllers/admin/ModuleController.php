<?php

namespace App\Http\Controllers\admin;

use DB;
use Validator;
use App\Models\City;
use App\Models\Module;
use App\Models\Locality;
use App\Models\CityDetails;
use Illuminate\Support\Str;
use App\Models\ModuleImport;
use Illuminate\Http\Request;

use App\Exports\Excel\ModuleExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ModuleController extends Controller
{
    public function index()
    {
        $page_title = 'Module';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Module Management',
                'url' => '',
            ]
        ];
        $status = request('status');
        if ($status == '0') {
            $status = '2';
        }
        $modules = Module::when($status, function ($modules) use ($status) {
            if ($status != '-1') {
                $status = conditionalStatus($status);
                $modules->where('status', '=', $status);
            }
        })->orderBy('name', 'asc')->get();
        return view('admin.pages.module.list', compact('page_title', 'page_description', 'breadcrumbs', 'modules'));
    }

    public function add(Request $request)
    {
        try {

            if ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                ], [
                    'name.required' => 'Module name is required.'
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                }

                DB::beginTransaction();


                $insertArr = [

                    'name' => $request->name,
                    'slug' => \Str::slug($request->name),
                    'table_name' => str_replace(' ', '_', Str::snake($request->name)),
                    'status' => 1,
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,

                ];
                if (!Module::where('slug', $insertArr['slug'])->exists()) {
                    $response = Module::Create($insertArr);
                    DB::commit();
                    return redirect('admin/module/edit/'.$response->id)->with('success', 'Module details updated successfully.');
                } else {
                    return redirect()->back()->withErrors(['Module name or slug already exist.'])->withInput($request->all());
                }
            }


            $pageSettings = $this->pageSetting('add');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];

            $modules = Module::where('status', 1)->get();
            return view('admin.pages.module.add', compact('page_title', 'page_description', 'breadcrumbs', 'modules'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('admin/create-offer')->with('error', $e->getMessage())->withInput($request->all());
        }
    }

    public function edit(Request $request, $moduleId)
    {
        try {
            if ($moduleId) {
                if ($request->isMethod('post')) {

                    // return $request->all();
                    $validator = Validator::make($request->all(), [
                        'name' => 'required',
                        'code' => 'required',
                    ], [
                        'name.required' => 'Module name is required.',
                        'code.required' => 'Module form is required.'
                    ]);
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput($request->all());
                    }

                    DB::beginTransaction();

                    // Generate snake_case table name

                    $updateArr = [
                        'name' => $request->name,
                        'slug' => \Str::slug($request->name),
                        'module_code' => $request->code,
                        'updated_by' => auth()->user()->id,
                    ];


                    $response = Module::UpdateOrCreate(['id' => $moduleId], $updateArr);

                    // Convert JSON array to array
                    $columns = json_decode($request->code, true);

                    $module = Module::find($moduleId);

                    // Check if the table doesn't exist
                    if (!Schema::hasTable($module->table_name)) {
                        Schema::create($module->table_name, function (Blueprint $table) use ($columns) {
                            $table->id();

                            foreach ($columns as $column) {
                                $columnName = $column['name'] ?? null;

                                if ($columnName) {
                                    // Adjust column type accordingly
                                    $table->string($columnName);
                                }
                            }

                            $table->timestamps();
                        });

                        // return 'Table created successfully.';
                    } else {
                        // Table exists, add missing columns
                        $existingColumns = Schema::getColumnListing($module->table_name);

                        foreach ($columns as $column) {
                            $columnName = $column['name'] ?? null;

                            if ($columnName && !in_array($columnName, $existingColumns)) {
                                // Adjust column type accordingly
                                Schema::table($module->table_name, function (Blueprint $table) use ($columnName) {
                                    $table->string($columnName);
                                });
                            }
                        }

                        // return 'Table updated with missing columns.';
                    }


                    DB::commit();
                    // return redirect('admin/module/list')->with('success', 'Module details updated successfully.');
                    return response()->json([
                        'status' => true,
                        'message' => 'Module details updated successfully.',
                    ]);
                }

                $pageSettings = $this->pageSetting('edit');



                $page_title =  $pageSettings['page_title'];
                $page_description = $pageSettings['page_description'];
                $breadcrumbs = $pageSettings['breadcrumbs'];

                $moduleDetail = Module::where('id', $moduleId)->first();
                if ($moduleDetail) {

                    return view('admin.pages.module.edit', compact('page_title', 'page_description', 'breadcrumbs', 'moduleDetail'));
                } else {
                    return redirect()->back()->with('error', 'City details not found.');
                }
            } else {
                return redirect()->back()->with('error', 'City details not found.');
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('admin/create-offer')->with('error', $e->getMessage())->withInput($request->all());
        }
    }




    function localityList(Request $request, $cityId, $moduleId)
    {
        try {

            $page_title = 'City';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'City Locality Management',
                    'url' => '',
                ]
            ];

            $moduleId = request('module_id') ?? $moduleId;
            $status = request('status');
            if ($status == '0') {
                $status = '2';
            }

            if ($cityId) {
                $localities = Locality::with(['city', 'module'])->when($moduleId, function ($cities) use ($moduleId) {
                    if (!empty($moduleId)) {
                        $cities->where('module_id', '=', $moduleId);
                    }
                })->when($status, function ($cities) use ($status) {
                    if ($status != '-1') {
                        $status = conditionalStatus($status);
                        $cities->where('status', '=', $status);
                    }
                })->where('city_id', '=', $cityId)->orderBy('name', 'asc')->get();


                return view('admin.pages.locality.list', compact('page_title', 'page_description', 'breadcrumbs', 'localities', 'cityId', 'moduleId'));
            }

            return  redirect()->back();
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('admin/create-offer')->with('error', $e->getMessage())->withInput($request->all());
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


                        DB::table('modules_import')->truncate();
                        foreach ($rawData as $key => $row) {

                            // note that these fields are completely different for you as your database fields and excel fields so replace them with your own database fields
                            if ($key > 0) {

                                //  dd($row);
                                $insertArr[] = [
                                    'name' => strtoupper($row[0]),
                                    'slug' => \Str::slug($row[0]),
                                    'status' => 1,
                                    'created_by' => auth()->user()->id,
                                    'updated_by' => auth()->user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'created_at' => date('Y-m-d H:i:s'),
                                ];
                            }
                        }
                        // dd($insertArr);
                        $inds = ModuleImport::insert($insertArr);
                        // dd($insertArr);

                        // DB::modulement("UPDATE seo_modules_import SET slug = REPLACE(slug, '-', ' ') where slug is NULL");

                        DB::modulement("SET sql_mode = ''");


                        $results =   DB::select("SELECT name,COUNT(name) as duplicates FROM seo_modules_import group by name having count(*) >= 2");

                        if (count($results) > 0) {
                            $i = 1;
                            $html = '<h3>Followings are duplicate module name</h3>';
                            foreach ($results as $key => $list) {
                                $html .=  $i++ . ') ' . $list->name . ' - ' . $list->name . ' - ' . $list->duplicates . '<br>';
                            }
                            return redirect()->back()->withErrors([$html]);
                        }
                        // dd($results);

                        DB::modulement("UPDATE seo_modules as t JOIN seo_modules_import itt ON itt.name = t.name SET t.name = itt.name , t.status = itt.status , t.slug = itt.slug,t.created_by = itt.created_by , t.updated_by = itt.updated_by , t.created_at = itt.created_at , t.updated_at = itt.updated_at
                        WHERE t.name = itt.name");

                        DB::modulement("INSERT INTO seo_modules (name,status,slug,created_by,updated_by,created_at,updated_at) SELECT name,status,slug,created_by,updated_by,created_at,updated_at FROM seo_modules_import WHERE name NOT IN (SELECT name FROM seo_modules) AND deleted_at is NULL");

                        DB::commit();
                        return redirect('admin/module/list')->with('success', 'Module details updated successfully.');
                    }
                }
            }

            $pageSettings = $this->pageSetting('import');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];

            return view('admin.pages.module.import', compact('page_title', 'page_description', 'breadcrumbs'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }


    public function updateStatus($moduleId, $status)
    {
        try {
            if ($moduleId) {


                DB::beginTransaction();
                $status = ($status == 1) ? $status = 0 : $status = 1;
                $updateArr = [
                    'status' => $status,
                ];
                // dump($updateArr , $moduleId);
                $response = Module::UpdateOrCreate(['id' => $moduleId], $updateArr);
                // dd($response);
                DB::commit();
                // dd($response);
                return redirect('admin/module/list')->with('success', 'module status updated successfully.');
            } else {
                return redirect()->back()->with('error', 'module details not found.');
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('admin/create-offer')->with('error', $e->getMessage())->withInput($request->all());
        }
    }

    public function pageSetting($action, $dataArray = [])
    {
        if ($action == 'edit') {
            $data['page_title'] = 'Module';
            $data['page_description'] = 'Edit Module';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Module Management',
                    'url' => url('admin/module/list'),
                ],
                [
                    'title' => 'Edit Module',
                    'url' => '',
                ],
            ];
            return $data;
        }

        if ($action == 'add') {
            $data['page_title'] = 'Module';
            $data['page_description'] = 'Add a New Module';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Module Management',
                    'url' => url('admin/module/list'),
                ],
                [
                    'title' => 'Add Module',
                    'url' => '',
                ],
            ];
            return $data;
        }

        if ($action == 'import') {
            $data['page_title'] = 'Module';
            $data['page_description'] = 'Import Module';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Module Management',
                    'url' => url('admin/module/list'),
                ],
                [
                    'title' => 'Import Module',
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
            return Excel::download(new ModuleExport, 'Modules.xlsx');
        else
            return redirect()->back()->withErrors(['Export type not defined.']);
    }




    function ModuleAPi()
    {

        $settings = DB::table('settings')->first();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $settings->base_url2 . '/getAllModules',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: XSRF-TOKEN=eyJpdiI6ImRPaTczRFVhc0lXZzQ1ampmZDVnSFE9PSIsInZhbHVlIjoiclUxY1RvQzIvS3ZEdForZnRjYXJxZnV3aE10VS9rZmVhaHROUHduMytHQlptQ2l4RDNKTjZvMFByOVN4Vk11YWppM3pmR3VVVFFJTDhiSFMvMHM3cEprQ21UYndpMHVIKzFJdTlyVlBVdFp4VjF2Z2tpNmpBeGJEWXdmREJ6L3MiLCJtYWMiOiJmYWNhOTRjZTJiMzhlNWM4Yzc5YjBlNDcyYmZjODUzNThjYjVkZWJhOGY0MmEwNjI4ZTYxODUzNzI1Njk0MWJkIiwidGFnIjoiIn0%3D; seo_engine_master_session=eyJpdiI6Im5pZ0I1NmNQS3hWUnJFczd1SEZtMnc9PSIsInZhbHVlIjoiQ2src2RxODJMbm5Rc3RObGF3N1NVUGlvUE5qeVNUVytDQW90ekJ5V25ob3NyT1pNczJLZDZxajFTNlJsZlgvQ2I2ZjBCZ1J6VUZXcDY4RjB1c2VvSlhpbkE2ZXRUSllDVEN2TTBIS1VibzU1d0NjRnBTckladjF4K0REcUpualMiLCJtYWMiOiJmYzJhYmFjMjUwM2RhYzFiNDgyYWZlYmQwM2M0ZjE5ZTY2NDc4NmZkODJhOWNiNWNmMjRhNGVlZjI2MzAyNGQ3IiwidGFnIjoiIn0%3D'

            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }



    public function sync()
    {
        // dd('sync');
        $data = $this->ModuleAPi();
        // dd($data);
        if ($data) {
            $data = json_decode($data, true);

            if ($data != null) {

                if ($data['Success'] == true) {
                    // dd($data['Result']);
                    // dd($data['Result'] && $data['Result']['Modules']);
                    if ($data['Result'] && $data['Result']['Modules']) {
                        $modules = $data['Result']['Modules'];
                        // dd($modules);


                        foreach ($modules as $module) {

                            DB::beginTransaction();
                            $insertArr = [


                                'name' => $module['name'],
                                'slug' => \Str::slug($module['name']),
                                'country_id' => $module['country_id'],
                                "status" => 1,
                                'created_by' => auth()->user()->id,
                                'updated_by' => auth()->user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'created_at' => date('Y-m-d H:i:s'),

                            ];
                            // dump($insertArr);
                            $response = Module::UpdateOrCreate(['name' => $module['name']], $insertArr);
                            // dump($response);
                            // dd($response);
                            DB::commit();
                            // db.refresh();
                        }

                        return redirect('admin/module/list')->with('success', 'Module details sync successfully.');
                    } else {
                        return redirect('admin/module/list')->with('error', 'Something went wrong.');
                    }
                }
            } else {
                return redirect('admin/module/list')->with('error', 'Something went wrong.');
            }
        }
    }
}
