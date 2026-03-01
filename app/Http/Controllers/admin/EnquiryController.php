<?php

namespace App\Http\Controllers\admin;

use App\Models\City;
use App\Models\Enquiry;
use App\Models\Locality;
use App\Models\CityDetails;
use App\Models\EnquiryImport;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Exports\Excel\EnquiryExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $page_title       = 'Enquiry Management';
            $page_description = 'List of all enquiries';
            $breadcrumbs      = [
                ['title' => 'Enquiry List', 'url' => ''],
            ];

            if ($request->ajax() || $request->isMethod('post')) {
                $status = $request->input('status');

                $query = Enquiry::query()
                    ->select([
                        'id',
                        'name',
                        'number',
                        'city',
                        'locality',
                        'item_reference',
                        'form',
                        'query',
                        'created_at',
                        'status'
                    ]);

                // Status filter
                if ($status !== null && $status != '-1') {
                    $query->where('status', $status);
                }

                // Date range filter
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $query->whereDate('created_at', '>=', $request->start_date)
                        ->whereDate('created_at', '<=', $request->end_date);
                }

                // Global search
                if ($request->filled('search.value')) {
                    $search = $request->input('search.value');
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('number', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%")
                            ->orWhere('locality', 'like', "%{$search}%")
                            ->orWhere('item_reference', 'like', "%{$search}%")
                            ->orWhere('form', 'like', "%{$search}%")
                            ->orWhere('query', 'like', "%{$search}%");
                    });
                }

                $recordsFiltered = $query->count();

                // Ordering
                $orderColumnIndex = $request->input('order.0.column', 0);
                $orderDir         = $request->input('order.0.dir', 'desc');

                $columns = [
                    0  => 'id',
                    1  => 'name',
                    2  => 'number',
                    3  => 'city',
                    4  => 'locality',
                    5  => 'item_reference',
                    6  => 'form',
                    7  => 'query',
                    8  => 'created_at',
                    9  => 'status',
                ];

                $orderColumn = $columns[$orderColumnIndex] ?? 'id';
                $query->orderBy($orderColumn, $orderDir);

                // Pagination
                $start  = (int) $request->input('start', 0);
                $length = (int) $request->input('length', 20);
                if ($length <= 0 || $length > 200) $length = 20;

                $enquiries = $query->skip($start)->take($length)->get();

                $data = [];
                $counter = $start + 1;

                foreach ($enquiries as $enquiry) {
                    $statusClass = $enquiry->status == 1 ? 'success' : 'danger';
                    $statusText  = $enquiry->status == 1 ? 'Active' : 'InActive';

                    $statusHtml = '<a href="javascript:void(0)" data-url="' . url("admin/enquiry/update-status/{$enquiry->id}/{$enquiry->status}") . '" onclick="changeStatus(this)">
                    <span class="label label-lg font-weight-bold label-light-' . $statusClass . ' label-inline">' . $statusText . '</span>
                </a>';

                    $actionHtml = '<a href="' . url("/admin/enquiry/edit/{$enquiry->id}") . '" class="btn btn-sm btn-clean btn-icon" title="Edit" data-toggle="tooltip">
                    <i class="la la-edit"></i>
                </a>';

                    $data[] = [
                        'counter'        => $counter++,
                        'name'           => $enquiry->name ?? '-',
                        'number'         => $enquiry->number ?? '-',
                        'city'           => $enquiry->city ?? '-',
                        'locality'       => $enquiry->locality ?? '-',
                        'item_reference' => $enquiry->item_reference ?? '-',
                        'form'           => $enquiry->form ?? '-',
                        'query'          => $enquiry->query ?? '-',
                        'created_at'     => $enquiry->created_at ? $enquiry->created_at->format('d-m-Y H:i:s') : '-',
                        'status'         => $statusHtml,
                        'action'         => $actionHtml,
                    ];
                }

                return response()->json([
                    'draw'            => (int) $request->input('draw'),
                    'recordsTotal'    => Enquiry::count(),
                    'recordsFiltered' => $recordsFiltered,
                    'data'            => $data,
                ]);
            }

            return view('admin.pages.enquiry.list', compact('page_title', 'page_description', 'breadcrumbs'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->isMethod('post')) {
                return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function add(Request $request)
    {
        try {

            if ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'number' => 'required',
                    'page' => 'required',
                    'form' => 'required',
                ], [
                    'name.required' => 'Enquiry name is required.',
                    'number.required' => 'Enquiry number is required.',
                    'page.required' => 'Enquiry page is required.',
                    'form.required' => 'Enquiry form is required.'
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

                    $insertArray = array_merge($input, $customArray);
                if ($insertArray) {
                    $response = Enquiry::Create($insertArray);
                    DB::commit();
                    return redirect('admin/enquiry/list')->with('success', 'Enquiry details updated successfully.');
                } else {
                    return redirect()->back()->withErrors(['Enquiry details not update.'])->withInput($request->all());
                }
            }


            $pageSettings = $this->pageSetting('add');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];

            $enquiries = Enquiry::where('status', 1)->get();
            return view('admin.pages.enquiry.add', compact('page_title', 'page_description', 'breadcrumbs', 'enquiries'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('admin/create-offer')->with('error', $e->getMessage())->withInput($request->all());
        }
    }
    public function edit(Request $request, $enquiryId)
    {
        try {
            if ($enquiryId) {
                if ($request->isMethod('post')) {
                    $validator = Validator::make($request->all(), [
                        'name' => 'required',
                        'number' => 'required',
                        'page' => 'required',
                        'form' => 'required',
                    ], [
                        'name.required' => 'Enquiry name is required.',
                        'number.required' => 'Enquiry number is required.',
                        'page.required' => 'Enquiry page is required.',
                        'form.required' => 'Enquiry form is required.'
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

                    $response = Enquiry::updateOrCreate(['id' => $enquiryId], $updateArr);
                    DB::commit();
                    return redirect('admin/enquiry/list')->with('success', 'Enquiry details updated successfully.');
                }

                $pageSettings = $this->pageSetting('edit');



                $page_title =  $pageSettings['page_title'];
                $page_description = $pageSettings['page_description'];
                $breadcrumbs = $pageSettings['breadcrumbs'];

                $enquiryDetail = Enquiry::where('id', $enquiryId)->first();

                if ($enquiryDetail) {

                    return view('admin.pages.enquiry.edit', compact('page_title', 'page_description', 'breadcrumbs', 'enquiryDetail'));
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

    function localityList(Request $request, $cityId, $enquiryId)
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

            $enquiryId = request('enquiry_id') ?? $enquiryId;
            $status = request('status');
            if ($status == '0') {
                $status = '2';
            }

            if ($cityId) {
                $localities = Locality::with(['city', 'enquiry'])->when($enquiryId, function ($cities) use ($enquiryId) {
                    if (!empty($enquiryId)) {
                        $cities->where('enquiry_id', '=', $enquiryId);
                    }
                })->when($status, function ($cities) use ($status) {
                    if ($status != '-1') {
                        $status = conditionalStatus($status);
                        $cities->where('status', '=', $status);
                    }
                })->where('city_id', '=', $cityId)->orderBy('name', 'asc')->get();


                return view('admin.pages.locality.list', compact('page_title', 'page_description', 'breadcrumbs', 'localities', 'cityId', 'enquiryId'));
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


                        DB::table('enquiries_import')->truncate();
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
                        $inds = EnquiryImport::insert($insertArr);
                        // dd($insertArr);

                        // DB::enquiryment("UPDATE seo_enquiries_import SET slug = REPLACE(slug, '-', ' ') where slug is NULL");

                        DB::enquiryment("SET sql_mode = ''");


                        $results =   DB::select("SELECT name,COUNT(name) as duplicates FROM seo_enquiries_import group by name having count(*) >= 2");

                        if (count($results) > 0) {
                            $i = 1;
                            $html = '<h3>Followings are duplicate enquiry name</h3>';
                            foreach ($results as $key => $list) {
                                $html .=  $i++ . ') ' . $list->name . ' - ' . $list->name . ' - ' . $list->duplicates . '<br>';
                            }
                            return redirect()->back()->withErrors([$html]);
                        }
                        // dd($results);

                        DB::enquiryment("UPDATE seo_enquiries as t JOIN seo_enquiries_import itt ON itt.name = t.name SET t.name = itt.name , t.status = itt.status , t.slug = itt.slug,t.created_by = itt.created_by , t.updated_by = itt.updated_by , t.created_at = itt.created_at , t.updated_at = itt.updated_at
                        WHERE t.name = itt.name");

                        DB::enquiryment("INSERT INTO seo_enquiries (name,status,slug,created_by,updated_by,created_at,updated_at) SELECT name,status,slug,created_by,updated_by,created_at,updated_at FROM seo_enquiries_import WHERE name NOT IN (SELECT name FROM seo_enquiries) AND deleted_at is NULL");

                        DB::commit();
                        return redirect('admin/enquiry/list')->with('success', 'Enquiry details updated successfully.');
                    }
                }
            }

            $pageSettings = $this->pageSetting('import');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];

            return view('admin.pages.enquiry.import', compact('page_title', 'page_description', 'breadcrumbs'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }


    public function updateStatus($enquiryId, $status)
    {
        try {
            if ($enquiryId) {

                DB::beginTransaction();
                $status = ($status == '1') ? $status = '0': $status = '1';

                $updateArr = [
                    'status' => $status,
                ];

                $response = Enquiry::updateOrCreate(['id' => $enquiryId], $updateArr);
                // dd($response);
                DB::commit();
                // dd($response);
                return redirect('admin/enquiry/list')->with('success', 'enquiry status updated successfully.');
            } else {
                return redirect()->back()->with('error', 'enquiry details not found.');
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
            $data['page_title'] = 'Enquiry';
            $data['page_description'] = 'Edit Enquiry';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Enquiry Management',
                    'url' => url('admin/enquiry/list'),
                ],
                [
                    'title' => 'Edit Enquiry',
                    'url' => '',
                ],
            ];
            return $data;
        }

        if ($action == 'add') {
            $data['page_title'] = 'Enquiry';
            $data['page_description'] = 'Add a New Enquiry';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Enquiry Management',
                    'url' => url('admin/enquiry/list'),
                ],
                [
                    'title' => 'Add Enquiry',
                    'url' => '',
                ],
            ];
            return $data;
        }

        if ($action == 'import') {
            $data['page_title'] = 'Enquiry';
            $data['page_description'] = 'Import Enquiry';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Enquiry Management',
                    'url' => url('admin/enquiry/list'),
                ],
                [
                    'title' => 'Import Enquiry',
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
            return Excel::download(new EnquiryExport, 'Enquiries.xlsx');
        else
            return redirect()->back()->withErrors(['Export type not defined.']);
    }




    function EnquiryAPi()
    {

        $settings = DB::table('settings')->first();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $settings->base_url2 . '/getAllEnquiries',
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
        $data = $this->EnquiryAPi();
        // dd($data);
        if ($data) {
            $data = json_decode($data, true);

            if ($data != null) {

                if ($data['Success'] == true) {
                    // dd($data['Result']);
                    // dd($data['Result'] && $data['Result']['Enquiries']);
                    if ($data['Result'] && $data['Result']['Enquiries']) {
                        $enquiries = $data['Result']['Enquiries'];
                        // dd($enquiries);


                        foreach ($enquiries as $enquiry) {

                            DB::beginTransaction();
                            $insertArr = [


                                'name' => $enquiry['name'],
                                'slug' => Str::slug($enquiry['name']),
                                'country_id' => $enquiry['country_id'],
                                "status" => 1,
                                'created_by' => auth()->user()->id,
                                'updated_by' => auth()->user()->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'created_at' => date('Y-m-d H:i:s'),

                            ];
                            // dump($insertArr);
                            $response = Enquiry::UpdateOrCreate(['name' => $enquiry['name']], $insertArr);
                            // dump($response);
                            // dd($response);
                            DB::commit();
                            // db.refresh();
                        }

                        return redirect('admin/enquiry/list')->with('success', 'Enquiry details sync successfully.');
                    } else {
                        return redirect('admin/enquiry/list')->with('error', 'Something went wrong.');
                    }
                }
            } else {
                return redirect('admin/enquiry/list')->with('error', 'Something went wrong.');
            }
        }
    }
}
