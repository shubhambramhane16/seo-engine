<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Rules;
use App\Models\Template;
use DB;
use Validator;

use Maatwebsite\Excel\Facades\Excel;
class RuleController extends Controller
{
    public function index()
    {
        $page_title = 'Rules';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'Rules Management',
                'url' => '',
            ]
        ];
        $templateId = request('template_id');
        $status = request('status');
        if ($status == '0') {
            $status = '2';
        }

        $rules = Rules::with(['template'])->when($templateId, function ($query, $templateId) {
            return $query->where('template_id', $templateId);
        })->when($status, function ($query, $status) {
            return $query->where('status', $status);
        })->orderBy('id', 'desc')->get();
        return view('admin.pages.rules.list', compact('page_title', 'page_description', 'breadcrumbs', 'rules'));
    }


    public function add(Request $request)
    {
        try {

            if ($request->isMethod('post')) {
                // dd($request->all());
                $validator = Validator::make($request->all(), [
                    // 'prefix' => 'required',
                    'rule_name' => 'required',
                    'items' => 'required',
                    'url_structure' => 'required | unique:rules,url_structure',
                    'description' => '',
                    'template_id' => 'required'

                ], [
                    'rule_name.required' => 'Rule Name is required.',
                    'prefix.required' => 'Rules prefix is required.',
                    'url_structure.required' => 'Rules url_structure is required.',
                    'url_structure.unique' => 'Rules url structure is already exists.',
                    'description.required' => 'Rules description is required.',
                    'template_id.required' => 'Rules template is required.',
                    'items.required' => 'Rules items is required.',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                }

                DB::beginTransaction();

                $insertArr = [
                    'rule_name' => $request->rule_name,
                    'prefix' => $request->prefix,
                    'url_structure' => $request->url_structure,
                    'properties' => json_encode($request->items),
                    'description' => $request->description,
                    'template_id' => $request->template_id,
                ];

                $response = Rules::UpdateOrCreate(['id' => $request->id], $insertArr);

                DB::commit();

                return redirect('admin/rules/list')->with('success', 'Rules added successfully.');

            }


            $pageSettings = $this->pageSetting('add');

            $page_title =  $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];
            $templates = Template::where('status', 1)->get();

            return view('admin.pages.rules.add', compact('page_title', 'page_description', 'breadcrumbs', 'templates'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('admin/create-offer')->with('error', $e->getMessage())->withInput($request->all());
        }
    }


    public function edit(Request $request, $ruleId)
    {
        try {
            if ($ruleId) {
                if ($request->isMethod('post')) {
                    // dd($request->all());
                    $validator = Validator::make($request->all(), [
                        'rule_name' => 'required',
                        // 'prefix' => 'required',
                        'items' => 'required',
                        'url_structure' => 'required | unique:rules,url_structure,' . $ruleId,
                        'description' => '',

                    ], [
                        'rule_name.required' => 'Rule Name is required.',
                        'prefix.required' => 'Rules prefix is required.',
                        'url_structure.required' => 'Rules url_structure is required.',
                        'description.required' => 'Rules description is required.',
                        'template_id.required' => 'Rules template is required.',
                        'items.required' => 'Rules items is required.',
                    ]);
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput($request->all());
                    }

                    DB::beginTransaction();


                    $insertArr = [
                        'rule_name' => $request->rule_name,
                        'prefix' => $request->prefix,
                        'url_structure' => $request->url_structure,
                        'properties' => json_encode($request->items),
                        'description' => $request->description,
                        'template_id' => $request->template_id,
                    ];

                    $response = Rules::UpdateOrCreate(['id' => $ruleId], $insertArr);

                    DB::commit();

                    return redirect('admin/rules/list')->with('success', 'Rules updated successfully.');
                }


                $pageSettings = $this->pageSetting('edit');
                $page_title =  $pageSettings['page_title'];
                $page_description = $pageSettings['page_description'];
                $breadcrumbs = $pageSettings['breadcrumbs'];
                $ruleDetail = Rules::where('id', $ruleId)->first();
                $templates = Template::where('status', 1)->get();
                if ($ruleDetail) {
                    return view('admin.pages.rules.edit', compact('page_title', 'page_description', 'breadcrumbs', 'ruleDetail' ,'templates'));
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


    public function updateStatus($ruleId, $status)
    {
        try {
            if ($ruleId) {

                DB::beginTransaction();
                $status = ($status == 1) ? $status = 0 : $status = 1;
                $updateArr = [
                    'status' => $status,
                ];
                $response = Rules::UpdateOrCreate(['id' => $ruleId], $updateArr);
                DB::commit();
                return redirect('admin/rules/list')->with('success', 'Rules status updated successfully.');
            } else {
                return redirect()->back()->with('error', 'Rules not found.');
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
            $data['page_title'] = 'Rules';
            $data['page_description'] = 'Edit Rules';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Rules Management',
                    'url' => url('admin/rules/list'),
                ],
                [
                    'title' => 'Edit Rules',
                    'url' => '',
                ],
            ];
            return $data;
        }

        if ($action == 'add') {
            $data['page_title'] = 'Rules';
            $data['page_description'] = 'Add a New Rules';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Rules Management',
                    'url' => url('admin/rules/list'),
                ],
                [
                    'title' => 'Add Rules',
                    'url' => '',
                ],
            ];
            return $data;
        }
    }


}
