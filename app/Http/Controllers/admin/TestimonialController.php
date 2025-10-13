<?php

namespace App\Http\Controllers\admin;

use App\Models\City;
use App\Models\Testimonial;
use App\Models\Locality;
use App\Models\CityDetails;
use App\Models\TestimonialImport;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Exports\Excel\TestimonialExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Centre;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class TestimonialController extends Controller
{
    public function index()
    {
        $page_title = 'Testimonial';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Testimonial Management',
                'url' => '',
            ]
        ];
        $status = request('status');
        if ($status == '0') {
            $status = '2';
        }
        $testimonials = Testimonial::when($status, function ($testimonials) use ($status) {
            if ($status != '-1') {
                $status = conditionalStatus($status);
                $testimonials->where('status', '=', $status);
            }
        })->orderBy('id', 'DESC')->get();
        return view('admin.pages.testimonial.list', compact('page_title', 'page_description', 'breadcrumbs', 'testimonials'));
    }

    public function add(Request $request)
    {
        try {

            if ($request->isMethod('post')) {
                // return $request->all();
                $validator = Validator::make($request->all(), [
                    'title' => 'required',
                    'content' => 'required',
                    'city_id' => 'required',
                    'locality_id' => 'required',
                    'centre_id' => 'required',
                ], [
                    'title.required' => 'Testimonial title is required.',
                    'content.required' => 'Testimonial page is required.',
                    'city_id.required' => 'Testimonial form is required.',
                    'locality_id.required' => 'Testimonial form is required.',
                    'centre_id.required' => 'Testimonial form is required.',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                }
                DB::beginTransaction();
                $input = $request->except(['_token']);

                    $customArray = [
                        'slug' => Str::slug($request->input('title')),
                        'status' => 1,
                        'updated_by' => auth()->user()->id,
                    ];

                    $insertArray = array_merge($input, $customArray);
                if ($insertArray) {
                    $response = Testimonial::Create($insertArray);
                    DB::commit();
                    return redirect('admin/testimonials/list')->with('success', 'Testimonial details updated successfully.');
                } else {
                    return redirect()->back()->withErrors(['Testimonial details not update.'])->withInput($request->all());
                }
            }
            $cities=[];
            $localities=[];
            $centeres=[];
            $testimonials=[];
            $cityId=request('city_id');
            $pageSettings = $this->pageSetting('add');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];
            $cities= City::where('status',1)->get();
            if($cityId){
                $localities= Locality::where('status',1)->where('city_id',$cityId)->get();
                $centeres= Centre::where('status',1)->where('city_id',$cityId)->get();
            }

            return view('admin.pages.testimonial.add', compact('page_title', 'page_description', 'breadcrumbs', 'cities','localities','centeres'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('admin/create-offer')->with('error', $e->getMessage())->withInput($request->all());
        }
    }
    public function edit(Request $request, $testimonialId)
    {
        try {
            if ($testimonialId) {
                if ($request->isMethod('post')) {
                    $validator = Validator::make($request->all(), [
                        'title' => 'required',
                        'content' => 'required',
                        'city_id' => 'required',
                        'locality_id' => 'required',
                        'centre_id' => 'required',
                    ], [
                        'title.required' => 'Testimonial title is required.',
                        'content.required' => 'Testimonial page is required.',
                        'city_id.required' => 'Testimonial form is required.',
                        'locality_id.required' => 'Testimonial form is required.',
                        'centre_id.required' => 'Testimonial form is required.',
                    ]);
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput($request->all());
                    }

                    DB::beginTransaction();

                    $input = $request->except(['_token']);

                    $customArray = [
                        'name' => $request->input('name'),
                        'status' => 1,
                        'updated_by' => auth()->user()->id,
                    ];

                    $updateArr = array_merge($input, $customArray);

                    // Commented out the 'return $request->all();' line as it would exit the function before reaching the update code

                    $response = Testimonial::updateOrCreate(['id' => $testimonialId], $updateArr);
                    DB::commit();
                    return redirect('admin/testimonials/list')->with('success', 'Testimonial details updated successfully.');
                }

                $cities=[];
                $localities=[];
                $centeres=[];
                $testimonials=[];


                $pageSettings = $this->pageSetting('edit');
                $page_title =  $pageSettings['page_title'];
                $page_description = $pageSettings['page_description'];
                $breadcrumbs = $pageSettings['breadcrumbs'];


                $cities= City::where('status',1)->get();
                $localities= Locality::where('status',1)->get();
                $centeres= Centre::where('status',1)->get();
                $testimonials = Testimonial::where('status', 1)->get();
                $testimonialDetail = Testimonial::where('id', $testimonialId)->first();

                if ($testimonialDetail) {

                    return view('admin.pages.testimonial.edit', compact('page_title', 'page_description', 'breadcrumbs', 'testimonialDetail','cities','localities','centeres'));
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

    function localityList(Request $request, $cityId, $testimonialId)
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

            $testimonialId = request('testimonial_id') ?? $testimonialId;
            $status = request('status');
            if ($status == '0') {
                $status = '2';
            }

            if ($cityId) {
                $localities = Locality::with(['city', 'testimonial'])->when($testimonialId, function ($cities) use ($testimonialId) {
                    if (!empty($testimonialId)) {
                        $cities->where('testimonial_id', '=', $testimonialId);
                    }
                })->when($status, function ($cities) use ($status) {
                    if ($status != '-1') {
                        $status = conditionalStatus($status);
                        $cities->where('status', '=', $status);
                    }
                })->where('city_id', '=', $cityId)->orderBy('name', 'asc')->get();


                return view('admin.pages.locality.list', compact('page_title', 'page_description', 'breadcrumbs', 'localities', 'cityId', 'testimonialId'));
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


                        DB::table('testimonials_import')->truncate();
                        foreach ($rawData as $key => $row) {

                            // note that these fields are completely different for you as your database fields and excel fields so replace them with your own database fields
                            if ($key > 0) {

                                //  dd($row);
                                $insertArr[] = [
                                    'name' => strtoupper($row[0]),
                                    'slug' => Str::slug($row[0]),
                                    'status' => 1,
                                    'created_by' => auth()->user()->id,
                                    'updated_by' => auth()->user()->id,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'created_at' => date('Y-m-d H:i:s'),
                                ];
                            }
                        }
                        // dd($insertArr);
                        $inds = TestimonialImport::insert($insertArr);
                        // dd($insertArr);

                        // DB::testimonialment("UPDATE seo_testimonials_import SET slug = REPLACE(slug, '-', ' ') where slug is NULL");

                        DB::testimonialment("SET sql_mode = ''");


                        $results =   DB::select("SELECT name,COUNT(name) as duplicates FROM seo_testimonials_import group by name having count(*) >= 2");

                        if (count($results) > 0) {
                            $i = 1;
                            $html = '<h3>Followings are duplicate testimonial name</h3>';
                            foreach ($results as $key => $list) {
                                $html .=  $i++ . ') ' . $list->name . ' - ' . $list->name . ' - ' . $list->duplicates . '<br>';
                            }
                            return redirect()->back()->withErrors([$html]);
                        }
                        // dd($results);

                        DB::testimonialment("UPDATE seo_testimonials as t JOIN seo_testimonials_import itt ON itt.name = t.name SET t.name = itt.name , t.status = itt.status , t.slug = itt.slug,t.created_by = itt.created_by , t.updated_by = itt.updated_by , t.created_at = itt.created_at , t.updated_at = itt.updated_at
                        WHERE t.name = itt.name");

                        DB::testimonialment("INSERT INTO seo_testimonials (name,status,slug,created_by,updated_by,created_at,updated_at) SELECT name,status,slug,created_by,updated_by,created_at,updated_at FROM seo_testimonials_import WHERE name NOT IN (SELECT name FROM seo_testimonials) AND deleted_at is NULL");

                        DB::commit();
                        return redirect('admin/testimonials/list')->with('success', 'Testimonial details updated successfully.');
                    }
                }
            }

            $pageSettings = $this->pageSetting('import');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];

            return view('admin.pages.testimonial.import', compact('page_title', 'page_description', 'breadcrumbs'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }


    public function updateStatus($testimonialId, $status)
    {
        try {
            if ($testimonialId) {

                DB::beginTransaction();
                $status = ($status == '1') ? $status = '0': $status = '1';

                $updateArr = [
                    'status' => $status,
                ];

                $response = Testimonial::updateOrCreate(['id' => $testimonialId], $updateArr);
                // dd($response);
                DB::commit();
                // dd($response);
                return redirect('admin/testimonials/list')->with('success', 'testimonial status updated successfully.');
            } else {
                return redirect()->back()->with('error', 'testimonial details not found.');
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('admin/create-offer')->with('error', $e->getMessage());
        }
    }

    public function pageSetting($action, $dataArray = [])
    {
        if ($action == 'edit') {
            $data['page_title'] = 'Testimonial';
            $data['page_description'] = 'Edit Testimonial';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Testimonial Management',
                    'url' => url('admin/testimonials/list'),
                ],
                [
                    'title' => 'Edit Testimonial',
                    'url' => '',
                ],
            ];
            return $data;
        }

        if ($action == 'add') {
            $data['page_title'] = 'Testimonial';
            $data['page_description'] = 'Add a New Testimonial';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Testimonial Management',
                    'url' => url('admin/testimonials/list'),
                ],
                [
                    'title' => 'Add Testimonial',
                    'url' => '',
                ],
            ];
            return $data;
        }

        if ($action == 'import') {
            $data['page_title'] = 'Testimonial';
            $data['page_description'] = 'Import Testimonial';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Testimonial Management',
                    'url' => url('admin/testimonials/list'),
                ],
                [
                    'title' => 'Import Testimonial',
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
            return Excel::download(new TestimonialExport, 'Testimonials.xlsx');
        else
            return redirect()->back()->withErrors(['Export type not defined.']);
    }




    function TestimonialAPi()
    {

        $settings = DB::table('settings')->first();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $settings->base_url2 . '/getAllTestimonials',
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
        $data = $this->TestimonialAPi();
        // dd($data);
        if ($data) {
            $data = json_decode($data, true);

            if ($data != null) {

                if ($data['Success'] == true) {
                    // dd($data['Result']);
                    // dd($data['Result'] && $data['Result']['Testimonials']);
                    if ($data['Result'] && $data['Result']['Testimonials']) {
                        $testimonials = $data['Result']['Testimonials'];
                        // dd($testimonials);


                        foreach ($testimonials as $testimonial) {

                            DB::beginTransaction();
                            $insertArr = [


                                'name' => $testimonial['name'],
                                'slug' => Str::slug($testimonial['name']),
                                'country_id' => $testimonial['country_id'],
                                "status" => 1,
                                'created_by' => auth()->user()->id,
                                'updated_by' => auth()->user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'created_at' => date('Y-m-d H:i:s'),

                            ];
                            // dump($insertArr);
                            $response = Testimonial::UpdateOrCreate(['name' => $testimonial['name']], $insertArr);
                            // dump($response);
                            // dd($response);
                            DB::commit();
                            // db.refresh();
                        }

                        return redirect('admin/testimonials/list')->with('success', 'Testimonial details sync successfully.');
                    } else {
                        return redirect('admin/testimonials/list')->with('error', 'Something went wrong.');
                    }
                }
            } else {
                return redirect('admin/testimonials/list')->with('error', 'Something went wrong.');
            }
        }
    }
}
