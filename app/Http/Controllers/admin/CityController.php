<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Locality;
use App\Models\CityDetails;
use App\Models\State;
use App\Models\CityImport;
use DB;
use Validator;

use App\Exports\Excel\CityExport;
use Maatwebsite\Excel\Facades\Excel;
class CityController extends Controller
{
    public function index()
    {
        $page_title = 'City';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'City Management',
                'url' => '',
            ]
        ];
        $stateId = request('state_id');
        $status = request('status');
        if ($status == '0') {
            $status = '2';
        }
        $cities = City::with(['state'])
            ->when($stateId, function ($cities) use ($stateId) {
                if (!empty($stateId)) {
                    $cities->where('state_id', '=', $stateId);
                }
            })
            ->when($status, function ($cities) use ($status) {
                if ($status != '-1') {
                    $status = conditionalStatus($status);
                    $cities->where('status', '=', $status);
                }
            })
            ->orderBy('name', 'asc')->get();
        return view('admin.pages.city.list', compact('page_title', 'page_description', 'breadcrumbs', 'cities'));
    }
    public function add(Request $request)
    {
        try {

            if ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'state_id' => 'required',
                    'name' => 'required',
                ], [
                    'state_id.required' => 'Select state.',
                    'name.required' => 'City name is required.'
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                }

                DB::beginTransaction();

                $state = explode(',', $request->state_id);
                $insertArr = [
                    'state_id' => $state[0],
                    'state_name' => $state[1],
                    'name' => $request->name,
                    'local_schema_markup' => $request->local_schema_markup,
                    'country_id' => 1,
                    'slug' => \Str::slug($state[1] . ' ' . $request->name),
                    'description' => $request->description,
                ];
                if (!City::where('slug', $insertArr['slug'])->exists()) {
                    $response = City::Create($insertArr);
                    DB::commit();
                    return redirect('admin/city/list')->with('success', 'City details updated successfully.');
                } else {
                    return redirect()->back()->withErrors(['City name or slug already exist.'])->withInput($request->all());
                }
            }


            $pageSettings = $this->pageSetting('add');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];

            $states = State::where('status', 1)->get();
            return view('admin.pages.city.add', compact('page_title', 'page_description', 'breadcrumbs', 'states'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('admin/create-offer')->with('error', $e->getMessage())->withInput($request->all());
        }
    }
    public function edit(Request $request, $cityId)
    {
        try {
            if ($cityId) {
                if ($request->isMethod('post')) {
                    // dd($request->all());
                    $validator = Validator::make($request->all(), [
                        'state_id' => 'required',
                        'name' => 'required',
                    ], [
                        'state_id.required' => 'Select state.',
                        'name.required' => 'City name is required.'
                    ]);
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput($request->all());
                    }

                    DB::beginTransaction();

                    $state = explode(',', $request->state_id);
                    $updateArr = [
                        'state_id' => $state[0],
                        'state_name' => $state[1],
                        'slug' => \Str::slug($state[1] . ' ' . $request->name),
                        'name' => $request->name,
                        'local_schema_markup' => $request->local_schema_markup,
                        'description' => $request->description,
                    ];
                    if (City::where('slug', $updateArr['slug'])->exists()) {
                        $insertArr['slug'] = \Str::slug($updateArr['state_name'] . ' ' . $updateArr['name']);
                    }

                    $response = City::UpdateOrCreate(['id' => $cityId], $updateArr);
                    DB::commit();
                    return redirect('admin/city/list')->with('success', 'City details updated successfully.');
                }

                $pageSettings = $this->pageSetting('edit');



                $page_title =  $pageSettings['page_title'];
                $page_description = $pageSettings['page_description'];
                $breadcrumbs = $pageSettings['breadcrumbs'];

                $states = State::where('status', 1)->get();
                $cityDetail = City::where('id', $cityId)->first();
                if ($cityDetail) {
                    return view('admin.pages.city.edit', compact('page_title', 'page_description', 'breadcrumbs', 'cityDetail', 'states'));
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


                        DB::table('cities_import')->truncate();
                        foreach ($rawData as $key => $row) {

                            // note that these fields are completely different for you as your database fields and excel fields so replace them with your own database fields
                            if ($key > 0) {

                                if (!empty($row[0])) {
                                    $state = State::where('name', strtoupper($row[0]))->first();
                                }
                                else{
                                    $state = null;
                                }


                                //  dd($row);
                                $insertArr[] = [
                                    'name' => strtoupper($row[1]),
                                    'state_id' => $state ? $state->id : null,
                                    // 'slug' => \Str::slug($row[1]) . '-' . $state ? $state->name : null,
                                    'slug' => \Str::slug($row[1] ?? ''),
                                    'status' => 1,
                                    'created_by' => auth()->user()->id,
                                    'updated_by' => auth()->user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'created_at' => date('Y-m-d H:i:s'),
                                ];
                            }
                        }
                        // dd($insertArr);
                        $inds = CityImport::insert($insertArr);
                        // dd($insertArr);

                        // DB::statement("UPDATE seo_states_import SET slug = REPLACE(slug, '-', ' ') where slug is NULL");

                        DB::statement("SET sql_mode = ''");


                        $results =   DB::select("SELECT name,COUNT(name) as duplicates FROM seo_cities_import group by name having count(*) >= 2");

                        if (count($results) > 0) {
                            $i = 1;
                            $html = '<h3>Followings are duplicate state name</h3>';
                            foreach ($results as $key => $list) {
                                $html .=  $i++ . ') ' . $list->name . ' - ' . $list->name . ' - ' . $list->duplicates . '<br>';
                            }
                            return redirect()->back()->withErrors([$html]);
                        }
                        // dd($results);

                        DB::statement("UPDATE seo_cities as t JOIN seo_cities_import itt ON itt.name = t.name SET t.name = itt.name ,t.state_id = itt.state_id, t.status = itt.status , t.slug = itt.slug,t.created_by = itt.created_by , t.updated_by = itt.updated_by , t.created_at = itt.created_at , t.updated_at = itt.updated_at
                        WHERE t.name = itt.name");

                        // DB::statement("INSERT INTO seo_cities (name,status,state_id,slug,created_by,updated_by,created_at,updated_at) SELECT name,status,state_id,slug,created_by,updated_by,created_at,updated_at FROM seo_cities_import WHERE name NOT IN (SELECT name FROM seo_cities) AND deleted_at is NULL");

                        DB::statement("INSERT INTO seo_cities (name,status,state_id,slug,created_by,updated_by,created_at,updated_at) SELECT name,status,state_id,slug,created_by,updated_by,created_at,updated_at FROM seo_cities_import WHERE name NOT IN (SELECT name FROM seo_cities) AND deleted_at is NULL");

                        DB::commit();
                        return redirect('admin/city/list')->with('success', 'City details updated successfully.');
                    }
                }
            }

            $pageSettings = $this->pageSetting('import');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];

            return view('admin.pages.city.import', compact('page_title', 'page_description', 'breadcrumbs'));
        } catch (\Exception $e) {
            dd($e);
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
                $response = City::UpdateOrCreate(['id' => $cityId], $updateArr);
                DB::commit();
                return redirect('admin/city/list')->with('success', 'City status updated successfully.');
            } else {
                return redirect()->back()->with('error', 'City details not found.');
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
            $data['page_title'] = 'City';
            $data['page_description'] = 'Edit City';
            $data['breadcrumbs'] = [
                [
                    'title' => 'City Management',
                    'url' => url('admin/city/list'),
                ],
                [
                    'title' => 'Edit City',
                    'url' => '',
                ],
            ];
            return $data;
        }

        if ($action == 'add') {
            $data['page_title'] = 'City';
            $data['page_description'] = 'Add a New City';
            $data['breadcrumbs'] = [
                [
                    'title' => 'City Management',
                    'url' => url('admin/city/list'),
                ],
                [
                    'title' => 'Add City',
                    'url' => '',
                ],
            ];
            return $data;
        }

        if ($action == 'import') {
            $data['page_title'] = 'City';
            $data['page_description'] = 'Import City';
            $data['breadcrumbs'] = [
                [
                    'title' => 'City Management',
                    'url' => url('admin/city/list'),
                ],
                [
                    'title' => 'Import City',
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
            return Excel::download(new CityExport, 'Cities.xlsx');
        else
            return redirect()->back()->withErrors(['Export type not defined.']);
    }



    function CityAPi(){

        $settings = DB::table('settings')->first();
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $settings->base_url2 . '/getAllCities' ,
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
        $data = CityAPi();
        // dd($data);
        if($data){
            $data = json_decode($data,true);
            if($data != null){
            if($data['Success'] == true){
                // dd($data['Result']);
                // dd($data['Result'] && $data['Result']['Cities']);
                if($data['Result'] && $data['Result']['Cities']){
                    $cities = $data['Result']['Cities'];
                    // dd($localities);
                    foreach($cities as $city){
                        $insertArr = [
                            "name" => $city['name'],
                            "description" => $city['description'],
                            "country_id" => $city['country_id'],
                            "state_id" => $city['state_id'],
                            "code" => $city['code'],
                            "slug" => $city['slug'],
                            "status" => 1,
                            'created_by' => auth()->user()->id,
                            'updated_by' => auth()->user()->id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'created_at' => date('Y-m-d H:i:s'),
                            "max_order_limit" => $city['max_order_limit'],
                            "is_payment_active" => $city['is_payment_active'],
                        ];
                        $response = City::UpdateOrCreate(['slug' => $city['slug']], $insertArr);
                    }
                    return redirect('admin/city/list')->with('success', 'City details sync successfully.');
                }
                else{
                    return redirect('admin/city/list')->with('error', 'Something went wrong.');
                }
            }}
            else{
                return redirect('admin/city/list')->with('error', 'Something went wrong.');
            }
        }
    }


}
