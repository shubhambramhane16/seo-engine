<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Centre;
use App\Models\CentreImport;
use App\Models\City;
use App\Models\State;
use DB;
use Validator;
use Image;
use File;
use App\Exports\Excel\CentreExport;
use Maatwebsite\Excel\Facades\Excel;

class CentreController extends Controller
{
    public function index()
    {
        try {
            $page_title = 'Centre Master';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Centre Master',
                    'url' => '',
                ]
            ];
            $status = request('status');
            $city_id = request('city_id');
            if ($status == '0') {
                $status = '2';
            }
            $centres = Centre::when($status, function ($centres) use ($status) {
                if ($status != '-1') {
                    $status = conditionalStatus($status);
                    $centres->where('status', '=', $status);
                }
            })->when($city_id, function ($centres) use ($city_id) {
                $centres->where('city_id', '=', $city_id);
            })->orderBy('id', 'desc')->get();
            $cities = City::where('status', 1)->orderBy('name', 'asc')->get();
            return view('admin.pages.centres.list', compact('page_title', 'page_description', 'breadcrumbs',  'centres',  'cities'));
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
                    'centre_name' => 'required',
                    // 'phone' => 'required',
                    // 'email' => 'required',
                    'address_line1' => 'required',
                    'state_id' => 'required',
                    'city_id' => 'required',
                    'pincode' => 'required',
                    // 'head_name' => 'required',
                    // 'head_mobile' => 'required',
                    // 'head_email' => 'required',
                    'contract_document' => '',
                    'centre_images' => '',
                ], [
                    'centre_name.required' => 'Centre name is required.',
                    'phone.required' => 'Centre phone is required.',
                    'email.required' => 'Centre email is required.',
                    'address_line1.required' => 'Mention centre address.',
                    'state_id.required' => 'Select state.',
                    'city_id.required' => 'Select city.',
                    'pincode.required' => 'Enter pincode.',
                    'icon.required' => 'Attachment is missing.',
                    'centre_images.required' => 'Attachment is missing.',
                    // 'head_name.required' => 'Enter centre head name.',
                    // 'head_mobile.required' => 'Enter centre mobile name.',
                    // 'head_email.required' => 'Enter centre email.',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                }

                DB::beginTransaction();
                $contractDocuments = [];
                if (isset($request['contract_document']) && count($request['contract_document']) > 0) {
                    foreach ($request->contract_document as $sKey => $sList) {
                        $allowedfileExtension = ['pdf', 'PDF'];
                        $extension =  $sList->getClientOriginalExtension();
                        $check = in_array($extension, $allowedfileExtension);
                        if ($check) {
                            if ($sList) {
                                $icon = null;
                                if ($request->hasFile('contract_document')) {
                                    $pathString = 'uploads/centres/documents';
                                    $image = $request->file('contract_document')[$sKey];
                                    $s3UploadRes = uploadFileAwsBucket($pathString,  $image);

                                    $icon = $s3UploadRes;
                                }
                                $contractDocuments[] = [
                                    'contract_document' => $icon,
                                    'contract_details' => $request['contract_details'][$sKey],
                                    'contract_document_type' => $request['contract_document_type'][$sKey],
                                ];
                            }
                        }
                    }
                }



                $centreImages = [];
                if (isset($request['centre_images']) && count($request['centre_images']) > 0) {
                    foreach ($request->centre_images as $key => $list) {
                        $allowedfileExtension = ['jpg', 'png', 'jpeg'];
                        $extension =  $list->getClientOriginalExtension();
                        $check = in_array($extension, $allowedfileExtension);
                        if ($check) {
                            if ($list) {
                                $icon = null;
                                if ($request->hasFile('centre_images')) {
                                    $pathString = 'uploads/centres/centre_images/';
                                    $image = $request->file('centre_images')[$key];
                                    $s3UploadRes = uploadFileAwsBucket($pathString,  $image);
                                    $icon = $s3UploadRes;
                                    $centreImages[] =  $icon;
                                }
                            }
                        }
                    }
                }

                $array = [
                    'centre_name' => $request->centre_name,
                    'display_name' => $request->display_name,
                    'slug' => \Str::slug($request->centre_name . ' in ' . $request->locality),
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address_line1' => $request->address_line1,
                    'address_line2' => $request->address_line2,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'locality' => $request->locality,
                    'landmark' => $request->landmark,
                    'pincode' => $request->pincode,
                    'centre_lat' => $request->centre_lat,
                    'centre_lng' => $request->centre_lng,
                    'head_name' => $request->head_name,
                    'head_mobile' => $request->head_mobile,
                    'head_email' => $request->head_email,
                    'seo_title' => $request->seo_title,
                    'seo_description' => $request->seo_description,
                    'seo_keywords' => $request->seo_keywords,
                    'lead_flow' => $request->lead_flow,
                    'created_by' => auth()->user()->id,
                    'country_id' => 1,
                    'about_us' => $request->about_us,
                    'centre_type' => $request->centre_type,
                    'state_name' => getStateName($request->state_id),
                    'city_name' =>  getCityName($request->city_id),
                    'contract_documents' => (count($contractDocuments) > 0) ? json_encode($contractDocuments) : null,
                    'centre_images' => (count($centreImages) > 0) ? json_encode($centreImages) : null,
                    'centre_facilities' => (isset($request->centre_facilities) && count($request->centre_facilities) > 0) ? json_encode($request->centre_facilities) : null,
                ];
                // if (Centre::where('email', $array['email'])->exists()) {
                //     return redirect()->back()->withErrors(['Centre email already exist.'])->withInput($request->all());
                // }
                if (Centre::where('slug', $array['slug'])->exists()) {
                    return redirect()->back()->withErrors(['Centre or slug already exist.'])->withInput($request->all());
                }
                $response = Centre::UpdateOrCreate(['id' => null], $array);
                DB::commit();
                return redirect('admin/centres/list')->with('success', 'Centre details added successfully.');
            }

            $pageSettings = $this->pageSetting('add');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];
            $states = State::where('status', 1)->get();
            $facilities = getFacilities();

            return view('admin.pages.centres.add', compact('page_title', 'page_description', 'breadcrumbs', 'states', 'facilities'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
    public function edit(Request $request, $id)
    {
        try {
            if ($request->isMethod('post')) {
                // dd($request->all());
                $validator = Validator::make($request->all(), [
                    'centre_name' => 'required',
                    'display_name' => 'required',
                    // 'phone' => 'required',
                    // 'email' => 'required',
                    'address_line1' => 'required',
                    'state_id' => 'required',
                    'city_id' => 'required',
                    'pincode' => 'required',
                    'contract_document' => '',
                    'centre_images' => '',
                    // 'head_name' => 'required',
                    // 'head_mobile' => 'required',
                    // 'head_email' => 'required',
                ], [
                    'centre_name.required' => 'Centre name is required.',
                    'phone.required' => 'Centre phone is required.',
                    'email.required' => 'Centre email is required.',
                    'address_line1.required' => 'Mention centre address.',
                    'state_id.required' => 'Select state.',
                    'city_id.required' => 'Select city.',
                    'pincode.required' => 'Enter pincode.',
                    // 'head_name.required' => 'Enter centre head name.',
                    // 'head_mobile.required' => 'Enter centre mobile name.',
                    // 'head_email.required' => 'Enter centre email.',
                ]);

                // dd($request->all());
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                }

                DB::beginTransaction();

                $array = [
                    'centre_name' => $request->centre_name,
                    'display_name' => $request->display_name,
                    'slug' => \Str::slug($request->centre_name . ' in ' . $request->locality),
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address_line1' => $request->address_line1,
                    'address_line2' => $request->address_line2,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                    'locality' => $request->locality,
                    'landmark' => $request->landmark,
                    'pincode' => $request->pincode,
                    'centre_lat' => $request->centre_lat,
                    'centre_lng' => $request->centre_lng,
                    'head_name' => $request->head_name,
                    'head_mobile' => $request->head_mobile,
                    'head_email' => $request->head_email,
                    'seo_title' => $request->seo_title,
                    'seo_description' => $request->seo_description,
                    'seo_keywords' => $request->seo_keywords,
                    'created_by' => auth()->user()->id,
                    'country_id' => 1,
                    'state_name' => getStateName($request->state_id),
                    'city_name' =>  getCityName($request->city_id),
                    'lead_flow' => $request->lead_flow,
                    'about_us' => $request->about_us,
                    'centre_type' => $request->centre_type,
                    // 'contract_documents' => (count($contractDocuments) > 0) ? json_encode($contractDocuments) : null,
                    // 'centre_images' => (count($centreImages) > 0) ? json_encode($centreImages) : null,
                    // 'centre_facilities' => $centre_facilities,
                ];
                if (isset($request->centre_facilities)  && count($request->centre_facilities) > 0) {
                    $centre_facilities =  json_encode($request->centre_facilities);
                    $array['centre_facilities'] = $centre_facilities;
                }
                $contractDocuments = [];
                if (isset($request['contract_document']) && count($request['contract_document']) > 0) {
                    foreach ($request->contract_document as $sKey => $sList) {
                        $allowedfileExtension = ['pdf', 'PDF'];
                        $extension =  $sList->getClientOriginalExtension();
                        $check = in_array($extension, $allowedfileExtension);
                        if ($check) {
                            if ($sList) {
                                $icon = null;
                                if ($request->hasFile('contract_document')) {
                                    $pathString = 'uploads/centres/documents';
                                    $image = $request->file('contract_document')[$sKey];
                                    $s3UploadRes = uploadFileAwsBucket($pathString,  $image);
                                    $icon = $s3UploadRes;
                                }
                                $contractDocuments[] = [
                                    'contract_document' => $icon,
                                    'contract_details' => isset($request['contract_details'][$sKey]) ? $request['contract_details'][$sKey] : '',
                                    'contract_document_type' => isset($request['contract_document_type'][$sKey]) ? $request['contract_document_type'][$sKey] : '',
                                ];
                            }
                        }
                    }

                    (count($contractDocuments) > 0) ? $array['contract_documents'] =  json_encode($contractDocuments) : null;
                }



                $centreImages = [];
                if (isset($request['centre_images']) && count($request['centre_images']) > 0) {
                    foreach ($request->centre_images as $key => $list) {

                        $allowedfileExtension = ['jpg', 'png', 'jpeg'];
                        $extension =  $list->getClientOriginalExtension();
                        $check = in_array($extension, $allowedfileExtension);
                        if ($check) {
                            if ($list) {
                                $icon = null;
                                if ($request->hasFile('centre_images')) {
                                    $pathString = 'uploads/centres/centre_images';
                                    $image = $request->file('centre_images')[$key];
                                    $s3UploadRes = uploadFileAwsBucket($pathString,  $image);

                                    $icon = $s3UploadRes;
                                    $centreImages[] =  $icon;
                                }
                            }
                        }
                    }
                    if (count($centreImages) > 0) {
                        $centre = Centre::where('id', '=', $id)->first();
                        if ($centre) {
                            $existCentreImages = ($centre->centre_images) ? json_decode($centre->centre_images, true) : [];
                            $newCentreImages = array_merge($existCentreImages, $centreImages);
                            $array['centre_images'] =  json_encode($newCentreImages);
                        } else {
                            $array['centre_images'] =  json_encode($centreImages);
                        }
                    }
                }
                /**
                 * Centre Timings
                 */
                $centreTiming = [];
                $weekDaysArray = weekDaysArray();
                if (isset($weekDaysArray) && count($weekDaysArray) > 0) {
                    foreach ($weekDaysArray as $dKey => $dList) {
                        $timings = [];
                        if (isset($request['open'][$dKey]) && !empty($request['open'][$dKey])) {
                            foreach ($request['open'][$dKey] as $ocKey => $ocList) {
                                $timings[] = [
                                    'open' => $request['open'][$dKey][$ocKey],
                                    'close' => $request['close'][$dKey][$ocKey],
                                ];
                            }
                        }
                        $centreTiming[] = [
                            'day' => $dList,
                            'is_active' => isset($request['day'][$dKey]) ? 1 : 0,
                            'timings' => $timings,
                        ];
                    }
                    if (count($centreTiming) > 0) {
                        $array['centre_timings'] =  json_encode($centreTiming);
                    }
                }
                // if (Centre::where('email', $array['email'])->where('id', '<>', $id)->exists()) {
                //     return redirect()->back()->withErrors(['Centre email already exist.'])->withInput($request->all());
                // }
                if (Centre::where('slug', $array['slug'])->where('id', '<>', $id)->exists()) {
                    return redirect()->back()->withErrors(['Centre or slug already exist.'])->withInput($request->all());
                }
                $response = Centre::UpdateOrCreate(['id' => $id], $array);
                DB::commit();
                return redirect('admin/centres/list')->with('success', 'Centre details updated successfully.');
            }

            $pageSettings = $this->pageSetting('edit');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];
            $states = State::where('status', 1)->get();
            $facilities = getFacilities();
            $details = Centre::where('id', $id)->first();
            return view('admin.pages.centres.edit', compact('page_title', 'page_description', 'breadcrumbs', 'states', 'facilities', 'details'));
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
                $cat = Centre::find($id);
                if ($cat->delete()) {
                    DB::commit();
                    return redirect()->back()->with('success', 'Centre deleted successfully.');
                } else {
                    return redirect()->back()->with('error', 'Failed to delete try again.');
                }
            } else {
                return redirect()->back()->with('error', 'Centre details not found.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
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


                        DB::table('centres_import')->truncate();
                        foreach ($rawData as $key => $row) {

                            // note that these fields are completely different for you as your database fields and excel fields so replace them with your own database fields
                            if ($key > 0) {

                                //  dd($row);
                                $insertArr[] = [
                                    'centre_name' => strtoupper($row[0]),
                                    'slug' => \Str::slug($row[0]),
                                    'status' => 1,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'created_at' => date('Y-m-d H:i:s'),
                                ];
                            }
                        }
                        // dd($insertArr);
                        $inds = CentreImport::insert($insertArr);
                        // dd($insertArr);

                        // DB::statement("UPDATE seo_centres_import SET slug = REPLACE(slug, '-', ' ') where slug is NULL");

                        DB::statement("SET sql_mode = ''");


                        $results =   DB::select("SELECT centre_name,COUNT(centre_name) as duplicates FROM seo_centres_import group by centre_name having count(*) >= 2");

                        if (count($results) > 0) {
                            $i = 1;
                            $html = '<h3>Followings are duplicate state name</h3>';
                            foreach ($results as $key => $list) {
                                $html .=  $i++ . ') ' . $list->name . ' - ' . $list->name . ' - ' . $list->duplicates . '<br>';
                            }
                            return redirect()->back()->withErrors([$html]);
                        }
                        // dd($results);

                        DB::statement("UPDATE seo_centres as t JOIN seo_centres_import itt ON itt.centre_name = t.centre_name SET t.centre_name = itt.centre_name , t.status = itt.status , t.slug = itt.slug , t.created_at = itt.created_at , t.updated_at = itt.updated_at
                        WHERE t.centre_name = itt.centre_name");

                        DB::statement("INSERT INTO seo_centres (centre_name,status,slug,created_at,updated_at) SELECT centre_name,status,slug,created_at,updated_at FROM seo_centres_import WHERE centre_name NOT IN (SELECT centre_name FROM seo_centres) AND deleted_at is NULL");

                        DB::commit();
                        return redirect('admin/centres/list')->with('success', 'State details updated successfully.');
                    }
                }
            }

            $pageSettings = $this->pageSetting('import');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];

            return view('admin.pages.centres.import', compact('page_title', 'page_description', 'breadcrumbs'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
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
                $response = Centre::UpdateOrCreate(['id' => $id], $updateArr);
                DB::commit();
                return redirect('admin/centres/list')->with('success', 'Centre status updated successfully.');
            } else {
                return redirect()->back()->with('error', 'Centre details not found.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    function exportExcel()
    {
        $type = request('type');
        if ($type == 'excel')
            return Excel::download(new CentreExport, 'Centres.xlsx');
        else
            return redirect()->back()->withErrors(['Export type not defined.']);
    }

    public function pageSetting($action, $dataArray = [])
    {
        if ($action == 'edit') {
            $data['page_title'] = 'Centre Master';
            $data['page_description'] = 'Edit Centre Master';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Centre Master',
                    'url' => url('admin/centres/list'),
                ],
                [
                    'title' => 'Edit Centre',
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
            $data['page_title'] = 'Centre Master';
            $data['page_description'] = 'Add a Centre';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Centre Master',
                    'url' => url('admin/centres/list'),
                ],
                [
                    'title' => 'Add a Centre',
                    'url' => '',
                ],
            ];
            return $data;
        }

        if ($action == 'import') {
            $data['page_title'] = 'Import Centre';
            $data['page_description'] = 'Import Centre';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Centre',
                    'url' => url('admin/centres/list'),
                ],
                [
                    'title' => 'Import Centre',
                    'url' => '',
                ],
            ];
            return $data;
        }

    }

    public function CentreAPi(){
        $settings = DB::table('settings')->first();
        // dd($settings);
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://localhost:8080/php-projects/seo-engine-master/getAllCentres',
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
        $data = CentreAPi();
        if($data){
            // dd($data);
            $data = json_decode($data,true);
            if($data != null){
            if($data && $data['Success'] == true){
                // dd($data['Result']);
                // dd($data['Result'] && $data['Result']['Centres']);
                if($data['Result'] && $data['Result']['Centres']){
                    $centres = $data['Result']['Centres'];
                    // dd($localities);
                    foreach($centres as $centre){
                        $insertArr = [

                            "centre_name" => $centre['centre_name'],
                            "slug" => $centre['slug'],
                            "phone" => $centre['phone'],
                            "landline" => $centre['landline'],
                            "email" => $centre['email'],
                            "password" => $centre['password'],
                            "address_line1" => $centre['address_line1'],
                            "address_line2" => $centre['address_line2'],
                            "locality" => $centre['locality'],
                            "landmark" => $centre['landmark'],
                            "country_id" => $centre['country_id'],
                            "state_name" => $centre['state_name'],
                            "state_id" => $centre['state_id'],
                            "city_name" => $centre['city_name'],
                            "city_id" => $centre['city_id'],
                            "pincode" => $centre['pincode'],
                            "centre_lat" => $centre['centre_lat'],
                            "centre_lng" => $centre['centre_lng'],
                            "centre_timings" => $centre['centre_timings'],
                            "centre_facilities" => $centre['centre_facilities'],
                            "centre_images" => $centre['centre_images'],
                            "head_name" => $centre['head_name'],
                            "head_mobile" => $centre['head_mobile'],
                            "head_email" => $centre['head_email'],
                            "contract_documents" => $centre['contract_documents'],
                            "seo_title" => $centre['seo_title'],
                            "seo_description" => $centre['seo_description'],
                            "seo_keywords" => $centre['seo_keywords'],
                            "deleted_at" => $centre['deleted_at'],
                            "display_name" => $centre['display_name'],
                            "lead_flow" => $centre['lead_flow'],
                            "about_us" => $centre['about_us'],
                            "rating" => $centre['rating'],
                            "centre_type" => $centre['centre_type'],


                            "status" => 1,
                            'created_by' => auth()->user()->id,
                            'updated_by' => auth()->user()->id,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'created_at' => date('Y-m-d H:i:s'),

                        ];
                        $response = Centre::UpdateOrCreate(['id' => $insertArr['centre_name']], $insertArr);
                    }
                    return redirect('admin/centres/list')->with('success', 'centres details sync successfully.');
                }
                else{
                    return redirect('admin/centres/list')->with('error', 'Something went wrong.');
                }
            }
        }
            else{
                return redirect('admin/centres/list')->with('error', 'Something went wrong.');
            }
        }
        else{
            return redirect('admin/centres/list')->with('error', 'Something went wrong.');
        }
    }



}
