<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Locality;
use App\Models\CityDetails;
use App\Models\State;
use App\Models\LocalityImport;
use DB;
use Validator;

use App\Exports\Excel\LocalityExport;
use Maatwebsite\Excel\Facades\Excel;

class LocalityController extends Controller
{
    public function index()
    {
        $page_title = 'City';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Locality Management',
                'url' => '',
            ]
        ];
        $stateId = request('state_id');
        $cityId = request('city_id');
        $status = request('status');
        if ($status == '0') {
            $status = '2';
        }
        $localities = Locality::with(['city', 'state'])
            ->when($stateId, function ($cities) use ($stateId) {
                if (!empty($stateId)) {
                    $cities->where('state_id', '=', $stateId);
                }
            })
            ->when($status, function ($cities) use ($cityId) {
                if (!empty($cityId)) {
                    $cities->where('city_id', '=', $cityId);
                }
            })
            ->when($status, function ($cities) use ($status) {
                if ($status != '-1') {
                    $status = conditionalStatus($status);
                    $cities->where('status', '=', $status);
                }
            })
            ->orderBy('name', 'asc')->get();

        return view('admin.pages.locality.list', compact('page_title', 'page_description', 'breadcrumbs', 'localities'));
    }
    public function add(Request $request, $stateId = null, $cityId = null)
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




                $insertArr = [
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'name' => $request->name,
                    'country_id' => 1,
                    'slug' => \Str::slug($request->name),
                    'description' => $request->description,
                ];

                if (!Locality::where('slug', $insertArr['slug'])->exists()) {
                    $response = Locality::Create($insertArr);
                    DB::commit();
                    return redirect('admin/city/' . $request->city_id . '/locality/' . $request->state_id)->with('success', 'Locality add successfully.');
                    // return redirect('admin/locality/list')->with('success', 'Locality details updated successfully.');
                } else {
                    return redirect()->back()->withErrors(['Locality name or slug already exist.'])->withInput($request->all());
                }
            }

            $pageSettings = $this->pageSetting('add');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];

            $states = State::where('status', 1)->get();

            return view('admin.pages.locality.add', compact('page_title', 'page_description', 'breadcrumbs', 'states', 'stateId', 'cityId'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('admin/create-offer')->with('error', $e->getMessage())->withInput($request->all());
        }
    }
    public function edit(Request $request, $stateId, $cityId, $localityId)
    {
        try {

            if ($localityId) {
                if ($request->isMethod('post')) {
                    $validator = Validator::make($request->all(), [
                        'state_id' => 'required',
                        'name' => 'required',
                    ], [
                        'state_id.required' => 'Select state.',
                        'name.required' => 'Locality name is required.'
                    ]);
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput($request->all());
                    }

                    DB::beginTransaction();


                    $updateArr = [
                        'state_id' => $request->state_id,
                        'city_id' => $request->city_id,
                        'slug' => \Str::slug($request->name),
                        'name' => $request->name,
                        'description' => $request->description,
                    ];


                    $response = Locality::UpdateOrCreate(['id' => $localityId], $updateArr);
                    DB::commit();
                    return redirect('admin/city/' . $request->city_id . '/locality/' . $request->state_id)->with('success', 'Locality details updated successfully.');
                    // return redirect('admin/locality/list')->with('success', 'Locality details updated successfully.');
                }

                $pageSettings = $this->pageSetting('edit');



                $page_title =  $pageSettings['page_title'];
                $page_description = $pageSettings['page_description'];
                $breadcrumbs = $pageSettings['breadcrumbs'];

                $states = State::where('status', 1)->get();
                $locality = Locality::where(['status' => 1, 'id' => $localityId])->first();

                if ($locality) {

                    return view('admin.pages.locality.edit', compact('page_title', 'page_description', 'breadcrumbs', 'states', 'locality', 'stateId', 'cityId'));
                } else {
                    return redirect()->back()->with('error', 'Locality details not found.');
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


                        DB::table('locality_import')->truncate();
                        foreach ($rawData as $key => $row) {

                            // note that these fields are completely different for you as your database fields and excel fields so replace them with your own database fields
                            if ($key > 0) {

                                if (!empty($row[0])) {
                                    $state = State::where('name', strtoupper($row[0]))->first();
                                } else {
                                    $state = null;
                                }

                                if (!empty($row[1])) {
                                    $city = City::where('name', strtoupper($row[1]))->first();
                                } else {
                                    $city = null;
                                }



                                //  dd($row);
                                $insertArr[] = [
                                    'state_id' => $state ? $state->id : null,
                                    'city_id' => $city ? $city->id : null,
                                    'name' => strtoupper($row[2]),
                                    'slug' => \Str::slug($row[2]),
                                    'status' => 1,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'created_at' => date('Y-m-d H:i:s'),
                                ];
                            }
                        }
                        // dd($insertArr);
                        $inds = LocalityImport::insert($insertArr);
                        // dd($insertArr);
                        // dd($inds);

                        // DB::statement("UPDATE seo_states_import SET slug = REPLACE(slug, '-', ' ') where slug is NULL");

                        DB::statement("SET sql_mode = ''");
                        $stateId = $state->id ? $state->id : null;
                        $cityId = $city->id ? $city->id : null;
                        $name = strtoupper($row[2]) ?? null;

                        $results = DB::select(" SELECT name, state_id, city_id as duplicates FROM seo_locality_import WHERE name = ? AND state_id = ? AND city_id = ? ", [$name, $stateId, $cityId]);

                        if (count($results) > 1) {
                            $i = 1;
                            $html = '<h3>Followings are duplicate locality names</h3>';
                            foreach ($results as $key => $list) {
                                $html .=  $i++ . ') ' . $list->name . ' - ' . $list->name . ' - ' . $list->duplicates . '<br>';
                            }
                            return redirect()->back()->withErrors([$html]);
                        }
                        // dd($results);

                        DB::statement("UPDATE seo_locality as t JOIN seo_locality_import itt ON itt.name = t.name SET t.name = itt.name , t.status = itt.status , t.slug = itt.slug, t.created_at = itt.created_at , t.updated_at = itt.updated_at, t.state_id = itt.state_id, t.city_id = itt.city_id
                        WHERE t.name = itt.name");

                        DB::statement("INSERT INTO seo_locality (name,status,state_id,city_id,slug,created_at,updated_at) SELECT name,status,state_id,city_id,slug,created_at,updated_at FROM seo_locality_import WHERE name NOT IN (SELECT name FROM seo_locality ) AND deleted_at is NULL");

                        DB::commit();
                        return redirect('admin/locality/list')->with('success', 'Locality details updated successfully.');
                    }
                }
            }

            $pageSettings = $this->pageSetting('import');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];

            return view('admin.pages.locality.import', compact('page_title', 'page_description', 'breadcrumbs'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }










    public function updateStatus($localityId, $status)
    {
        try {
            if ($localityId) {

                DB::beginTransaction();
                $status = ($status == 1) ? $status = 0 : $status = 1;
                $updateArr = [
                    'status' => $status,
                ];
                $response = Locality::UpdateOrCreate(['id' => $localityId], $updateArr);
                DB::commit();
                return redirect('admin/locality/list')->with('success', 'Locality status updated successfully.');
            } else {
                return redirect()->back()->with('error', 'Locality details not found.');
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
            $data['page_title'] = 'Locality';
            $data['page_description'] = 'Edit Locality';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Locality Management',
                    'url' => url('admin/locality/list'),
                ],
                [
                    'title' => 'Edit Locality',
                    'url' => '',
                ],
            ];
            return $data;
        }

        if ($action == 'add') {
            $data['page_title'] = 'Locality';
            $data['page_description'] = 'Add a New Locality';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Locality Management',
                    'url' => url('admin/locality/list'),
                ],
                [
                    'title' => 'Add Locality',
                    'url' => '',
                ],
            ];
            return $data;
        }

        if ($action == 'import') {
            $data['page_title'] = 'Locality';
            $data['page_description'] = 'Import Locality';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Locality Management',
                    'url' => url('admin/locality/list'),
                ],
                [
                    'title' => 'Import Locality',
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
            return Excel::download(new LocalityExport, 'Localities.xlsx');
        else
            return redirect()->back()->withErrors(['Export type not defined.']);
    }


    function LocalityAPi()
    {
        $settings = DB::table('settings')->first();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $settings->base_url2 . '/getAllLocality',
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



    public function sync()
    {
        // dd('sync');
        $data = $this->LocalityAPi();
        if ($data) {
            // dd($data);
            $data = json_decode($data, true);
            if ($data != null) {
                if ($data['Success'] == true) {

                    // dd($data['Result'] && $data['Result']['Localities']);
                    if ($data['Result'] && $data['Result']['Localities']) {
                        $localities = $data['Result']['Localities'];
                        // dd($localities);
                        foreach ($localities as $locality) {
                            $insertArr = [
                                'name' => strtoupper($locality['name']),
                                "description" => $locality['description'],
                                "country_id" => $locality['country_id'],
                                "state_id" => $locality['state_id'],
                                "city_id" => $locality['city_id'],
                                "code" => $locality['code'],
                                "slug" => \Str::slug($locality['name']),
                                "status" => 1,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'created_at' => date('Y-m-d H:i:s'),
                            ];
                            $response = Locality::UpdateOrCreate(['name' => strtoupper($locality['name'])], $insertArr);
                        }
                        return redirect('admin/locality/list')->with('success', 'Locality details updated successfully.');
                    } else {
                        return redirect('admin/locality/list')->with('error', 'Something went wrong.');
                    }
                }
            } else {
                return redirect('admin/locality/list')->with('error', 'Something went wrong.');
            }
        }
    }
}
