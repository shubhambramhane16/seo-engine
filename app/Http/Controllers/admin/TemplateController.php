<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Validator;

use App\Models\Template;

use Maatwebsite\Excel\Facades\Excel;

class TemplateController extends Controller
{
    public function index()
    {
        try {
            $page_title = 'Template';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Template Management',
                    'url' => '',
                ]
            ];
            $status = request('status');
            if ($status == '0') {
                $status = '2';
            }

            $templates = Template::when($status, function ($query, $status) {
                return $query->where('status', $status);
            })

                ->orderBy('id', 'desc')->get();
            return view('admin.pages.template.list', compact('page_title', 'page_description', 'breadcrumbs', 'templates'));
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
                    'template_name' => 'required',
                    'file' => 'required',
                    'description' => 'required',
                ], [
                    'description.required' => 'description is required.',
                    'file.required' => 'File is required.',
                    'template_name.required' => 'Template name is required.',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                }

                DB::beginTransaction();

                $s3UploadRes = null;
                if ($request->hasFile('file')) {
                    $s3UploadRes = uploadFileAwsBucketSdk($request);
                }

                $insertArr = [
                    'description' => $request->description,
                    'template_name' => $request->template_name,
                    'template_image' => $s3UploadRes,
                    'status' => 1,
                ];
                // dd($insertArr);


                $response = Template::create($insertArr);
                DB::commit();
                return redirect('admin/templates/list')->with('success', 'Template added successfully.');
            }



            $pageSettings = $this->pageSetting('add');

            $page_title = $pageSettings['page_title'];
            $page_description = $pageSettings['page_description'];
            $breadcrumbs = $pageSettings['breadcrumbs'];


            return view('admin.pages.template.add', compact('page_title', 'page_description', 'breadcrumbs', ));
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('admin/create-offer')->with('error', $e->getMessage())->withInput($request->all());
        }
    }


    public function edit(Request $request, $templateId)
    {
        try {
            if ($templateId) {
                $templateDetail = Template::where('id', $templateId)->first();
                if ($templateDetail) {
                    if ($request->isMethod('post')) {
                        $validator = Validator::make($request->all(), [
                            'description' => 'required',
                            'file' => 'required',
                            'template_name' => 'required',
                        ], [
                            'description.required' => 'description is required.',
                            'file.required' => 'File is required.',
                            'template_name.required' => 'Template name is required.',
                        ]);
                        if ($validator->fails()) {
                            return redirect()->back()->withErrors($validator)->withInput($request->all());
                        }

                        DB::beginTransaction();

                        $s3UploadRes = null;
                        if ($request->hasFile('file')) {
                            if ($request->hasFile('file')) {
                                $s3UploadRes = uploadFileAwsBucketSdk($request);
                            }
                        }

                        $updateArr = [
                            'template_name' => $request->template_name,
                            'template_image' => $s3UploadRes,
                            'description' => $request->description,
                            'status' => 1,
                        ];

                        $response = Template::where('id', $templateId)->update($updateArr);
                        DB::commit();
                        return redirect('admin/templates/list')->with('success', 'Template updated successfully.');
                    }
                    $pageSettings = $this->pageSetting('edit');
                    $page_title = $pageSettings['page_title'];
                    $page_description = $pageSettings['page_description'];
                    $breadcrumbs = $pageSettings['breadcrumbs'];
                    return view('admin.pages.template.edit', compact('page_title', 'page_description', 'breadcrumbs', 'templateDetail'));
                } else {


                    return redirect()->back()->with('error', 'Template not found.');
                }
            } else {
                return redirect()->back()->with('error', 'Template not found.');
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('admin/create-offer')->with('error', $e->getMessage())->withInput($request->all());
        }

    }


    public function updateStatus($templateId, $status)
    {
        try {
            if ($templateId) {

                DB::beginTransaction();
                $status = ($status == 1) ? $status = 0 : $status = 1;
                $updateArr = [
                    'status' => $status,
                ];
                $response = Template::where('id', $templateId)->update($updateArr);
                DB::commit();
                return redirect('admin/templates/list')->with('success', 'Template status updated successfully.');
            } else {
                return redirect()->back()->with('error', 'Template not found.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage())->withInput($request->all());
        }
    }



    public function pageSetting($action, $dataArray = [])
    {
        if ($action == 'edit') {
            $data['page_title'] = 'Template';
            $data['page_description'] = 'Edit Template';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Template Management',
                    'url' => url('admin/templates/list'),
                ],
                [
                    'title' => 'Edit Template',
                    'url' => '',
                ],
            ];
            return $data;
        }

        if ($action == 'add') {
            $data['page_title'] = 'Template';
            $data['page_description'] = 'Add a New Template';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Template Management',
                    'url' => url('admin/templates/list'),
                ],
                [
                    'title' => 'Add Template',
                    'url' => '',
                ],
            ];
            return $data;
        }
    }

}
