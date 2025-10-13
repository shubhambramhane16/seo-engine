<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Locality;
use App\Models\CityDetails;
use App\Models\State;
use App\Models\StateImport;
use DB;
use Validator;

use App\Exports\Excel\StateExport;
use Maatwebsite\Excel\Facades\Excel;

class StateController extends Controller
{
    public function index()
    {
        $page_title = 'State';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'State Management',
                'url' => '',
            ]
        ];
        $status = request('status');
        if ($status == '0') {
            $status = '2';
        }
        $states = State::when($status, function ($states) use ($status) {
            if ($status != '-1') {
                $status = conditionalStatus($status);
                $states->where('status', '=', $status);
            }
        })->orderBy('name', 'asc')->get();
        return view('admin.pages.state.list', compact('page_title', 'page_description', 'breadcrumbs', 'states'));
    }

    public function add(Request $request)
    {
        try {

            if ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                ], [
                    'name.required' => 'State name is required.'
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                }

                DB::beginTransaction();


                $insertArr = [

                    'name' => $request->name,
                    'slug' => \Str::slug($request->name),
                    'country_id' => 1,
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,

                ];
                if (!State::where('slug', $insertArr['slug'])->exists()) {
                    $response = State::Create($insertArr);
                    DB::commit();
                    return redirect('admin/state/list')->with('success', 'State details updated successfully.');
                } else {
                    return redirect()->back()->withErrors(['State name or slug already exist.'])->withInput($request->all());
                }
            }


            $pageSettings = $this->pageSetting('add');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];

            $states = State::where('status', 1)->get();
            return view('admin.pages.state.add', compact('page_title', 'page_description', 'breadcrumbs', 'states'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('admin/create-offer')->with('error', $e->getMessage())->withInput($request->all());
        }
    }
    public function edit(Request $request, $stateId)
    {
        try {
            if ($stateId) {
                if ($request->isMethod('post')) {
                    $validator = Validator::make($request->all(), [
                        'name' => 'required',
                    ], [
                        'name.required' => 'State name is required.'
                    ]);
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput($request->all());
                    }

                    DB::beginTransaction();


                    $updateArr = [
                        'name' => $request->name,
                        'slug' => \Str::slug($request->name),
                        'country_id' => 1,
                        'updated_by' => auth()->user()->id,
                    ];


                    $response = State::UpdateOrCreate(['id' => $stateId], $updateArr);
                    DB::commit();
                    return redirect('admin/state/list')->with('success', 'State details updated successfully.');
                }

                $pageSettings = $this->pageSetting('edit');



                $page_title =  $pageSettings['page_title'];
                $page_description = $pageSettings['page_description'];
                $breadcrumbs = $pageSettings['breadcrumbs'];

                $stateDetail = State::where('id', $stateId)->first();
                if ($stateDetail) {

                    return view('admin.pages.state.edit', compact('page_title', 'page_description', 'breadcrumbs', 'stateDetail'));
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

    function localityList(Request $request, $cityId,$stateId){
          try {

             $page_title = 'City';
             $page_description = '';
             $breadcrumbs = [
                 [
                   'title' => 'City Locality Management',
                   'url' => '',
                 ]
             ];

            $stateId = request('state_id') ?? $stateId;
            $status = request('status');
            if ($status == '0') {
               $status = '2';
            }

            if ($cityId) {
              $localities = Locality::with(['city','state'])->when($stateId, function ($cities) use ($stateId) {
                if (!empty($stateId)) {
                    $cities->where('state_id', '=', $stateId);
                }
              })->when($status, function ($cities) use ($status) {
                if ($status != '-1') {
                    $status = conditionalStatus($status);
                    $cities->where('status', '=', $status);
                }
              })->where('city_id', '=', $cityId)->orderBy('name', 'asc')->get();


             return view('admin.pages.locality.list', compact('page_title', 'page_description', 'breadcrumbs', 'localities','cityId','stateId'));

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


                        DB::table('states_import')->truncate();
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
                        $inds = StateImport::insert($insertArr);
                        // dd($insertArr);

                        // DB::statement("UPDATE seo_states_import SET slug = REPLACE(slug, '-', ' ') where slug is NULL");

                        DB::statement("SET sql_mode = ''");


                        $results =   DB::select("SELECT name,COUNT(name) as duplicates FROM seo_states_import group by name having count(*) >= 2");

                        if (count($results) > 0) {
                            $i = 1;
                            $html = '<h3>Followings are duplicate state name</h3>';
                            foreach ($results as $key => $list) {
                                $html .=  $i++ . ') ' . $list->name . ' - ' . $list->name . ' - ' . $list->duplicates . '<br>';
                            }
                            return redirect()->back()->withErrors([$html]);
                        }
                        // dd($results);

                        DB::statement("UPDATE seo_states as t JOIN seo_states_import itt ON itt.name = t.name SET t.name = itt.name , t.status = itt.status , t.slug = itt.slug,t.created_by = itt.created_by , t.updated_by = itt.updated_by , t.created_at = itt.created_at , t.updated_at = itt.updated_at
                        WHERE t.name = itt.name");

                        DB::statement("INSERT INTO seo_states (name,status,slug,created_by,updated_by,created_at,updated_at) SELECT name,status,slug,created_by,updated_by,created_at,updated_at FROM seo_states_import WHERE name NOT IN (SELECT name FROM seo_states) AND deleted_at is NULL");

                        DB::commit();
                        return redirect('admin/state/list')->with('success', 'State details updated successfully.');
                    }
                }
            }

            $pageSettings = $this->pageSetting('import');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];

            return view('admin.pages.state.import', compact('page_title', 'page_description', 'breadcrumbs'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }


    public function updateStatus($stateId, $status)
    {
        try {
            if ($stateId) {


                DB::beginTransaction();
                $status = ($status == 1) ? $status = 0 : $status = 1;
                $updateArr = [
                    'status' => $status,
                ];
                // dump($updateArr , $stateId);
                $response = State::UpdateOrCreate(['id' => $stateId], $updateArr);
                // dd($response);
                DB::commit();
                // dd($response);
                return redirect('admin/state/list')->with('success', 'state status updated successfully.');
            } else {
                return redirect()->back()->with('error', 'state details not found.');
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
            $data['page_title'] = 'State';
            $data['page_description'] = 'Edit State';
            $data['breadcrumbs'] = [
                [
                    'title' => 'State Management',
                    'url' => url('admin/state/list'),
                ],
                [
                    'title' => 'Edit State',
                    'url' => '',
                ],
            ];
            return $data;
        }

        if ($action == 'add') {
            $data['page_title'] = 'State';
            $data['page_description'] = 'Add a New State';
            $data['breadcrumbs'] = [
                [
                    'title' => 'State Management',
                    'url' => url('admin/state/list'),
                ],
                [
                    'title' => 'Add State',
                    'url' => '',
                ],
            ];
            return $data;
        }

        if ($action == 'import') {
            $data['page_title'] = 'State';
            $data['page_description'] = 'Import State';
            $data['breadcrumbs'] = [
                [
                    'title' => 'State Management',
                    'url' => url('admin/state/list'),
                ],
                [
                    'title' => 'Import State',
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
            return Excel::download(new StateExport, 'States.xlsx');
        else
            return redirect()->back()->withErrors(['Export type not defined.']);
    }




    function StateAPi(){

        $settings = DB::table('settings')->first();
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $settings->base_url2 . '/getAllStates',
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



    public function sync(){
        // dd('sync');
        $data =$this->StateAPi();
        // dd($data);
        if($data){
            $data = json_decode($data,true);

            if($data != null){

            if($data['Success'] == true){
                // dd($data['Result']);
                // dd($data['Result'] && $data['Result']['States']);
                if($data['Result'] && $data['Result']['States']){
                    $states = $data['Result']['States'];
                    // dd($states);


                    foreach($states as $state){

                        DB::beginTransaction();
                        $insertArr = [


                            'name' => $state['name'],
                            'slug' => \Str::slug($state['name']),
                            'country_id' => $state['country_id'],
                            "status" => 1,
                            'created_by' => auth()->user()->id,
                            'updated_by' => auth()->user()->id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'created_at' => date('Y-m-d H:i:s'),

                        ];
                        // dump($insertArr);
                        $response = State::UpdateOrCreate(['name' => $state['name']], $insertArr);
                        // dump($response);
                        // dd($response);
                        DB::commit();
                        // db.refresh();
                    }

                    return redirect('admin/state/list')->with('success', 'State details sync successfully.');
                }
                else{
                    return redirect('admin/state/list')->with('error', 'Something went wrong.');
                }
            }
        }
        else {
            return redirect('admin/state/list')->with('error', 'Something went wrong.');
        }
        }
    }

}
