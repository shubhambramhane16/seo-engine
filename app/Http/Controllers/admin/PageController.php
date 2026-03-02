<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Rules;
use App\Models\Pages;
use App\Models\City;
use App\Models\PagesHistory;
use App\Models\PageApprovalRequest;
use App\Models\PageApprovalRequestLog;
use App\Models\UserApprovalHierarchy;
use App\Models\Locality;
use App\Models\Category;
use App\Models\PathologyTest as Items;
use App\Models\User;
use DB;
use Validator;
use Image;
use File;
use App\Exports\Excel\CategoryExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class PageController extends Controller
{
    private function editableFields()
    {
        return [
            'page_url',
            'page_name',
            'slug',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'og_meta_title',
            'og_meta_description',
            'og_meta_image_url',
            'twitter_card_title',
            'twitter_card_description',
            'schema_markup',
            'header_content',
            'center_content',
            'footer_content',
            'page_script',
        ];
    }

    private function isSuperAdmin($user)
    {
        if (!$user || !$user->role) {
            return false;
        }

        $roleTitle = strtolower(trim($user->role->role));
        return $roleTitle === 'super admin' || $roleTitle === 'superadmin' || Str::contains($roleTitle, 'super');
    }

    private function approvalBadge($status)
    {
        if ($status === 'pending_manager' || $status === 'pending_admin') {
            return '<span class="label label-lg font-weight-bold label-light-warning label-inline">Pending Approval</span>';
        }
        if ($status === 'approved') {
            return '<span class="label label-lg font-weight-bold label-light-success label-inline">Approved</span>';
        }
        if ($status === 'rejected') {
            return '<span class="label label-lg font-weight-bold label-light-danger label-inline">Rejected</span>';
        }
        return '<span class="label label-lg font-weight-bold label-secondary label-inline">Draft</span>';
    }

    public function index(Request $request)
    {
        try {
            $page_title = 'Page Management';
            $page_description = '';
            $breadcrumbs = [
                [
                    'title' => 'Page Management',
                    'url' => '',
                ],
            ];

            // Handle AJAX request for DataTables
            if ($request->ajax()) {
                $status = $request->get('status');
                if ($status == '0') {
                    $status = '2';
                }

                $query = Pages::with(['rule', 'latestApprovalRequest'])->when($status && $status != '-1', function ($q, $status) {
                    return $q->where('status', $status);
                });

                // Get filtered count
                $filteredCount = $query->count();

                // Apply search
                if ($request->has('search') && !empty($request->get('search')['value'])) {
                    $search = $request->get('search')['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('page_name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%")
                            ->orWhere('seo_title', 'like', "%{$search}%");
                    });
                }

                // Get total count after search
                $totalFiltered = $query->count();

                // Apply ordering
                $order = $request->get('order', []);
                if (!empty($order) && isset($order[0])) {
                    $orderColumnIndex = $order[0]['column'] ?? 0;
                    $orderDirection = $order[0]['dir'] ?? 'desc';
                } else {
                    $orderColumnIndex = 0;
                    $orderDirection = 'desc';
                }

                $columns = ['id', 'page_name', 'slug', 'seo_title', 'status'];

                if (isset($columns[$orderColumnIndex])) {
                    $query->orderBy($columns[$orderColumnIndex], $orderDirection);
                } else {
                    $query->orderBy('id', 'desc');
                }

                // Apply pagination
                $start = $request->get('start', 0);
                $length = $request->get('length', 10);
                $pages = $query->skip($start)->take($length)->get();

                // Format data for DataTables
                $data = [];
                $counter = $start + 1;
                foreach ($pages as $page) {
                    $statusLabel = $page->status == 1
                        ? '<span class="label label-lg font-weight-bold label-light-success label-inline">Active</span>'
                        : '<span class="label label-lg font-weight-bold label-light-danger label-inline">InActive</span>';

                    $statusLink = '<a href="javascript:void(0)" data-url="' . url('admin/page/update-status/' . $page->id . '/' . $page->status) . '" onclick="changeStatus(this)">' . $statusLabel . '</a>';

                    $actions = '<a href="' . url('/admin/page/edit/' . $page->id) . '" class="btn btn-sm btn-clean btn-icon" title="Edit details" data-toggle="tooltip">
                        <i class="la la-edit"></i>
                    </a>
                    <a href="' . url('/admin/page/delete/' . $page->id) . '" class="btn btn-sm btn-clean d-none btn-icon" title="Delete" data-toggle="tooltip">
                        <i class="la la-trash"></i>
                    </a>';

                    $approvalStatus = optional($page->latestApprovalRequest)->status;

                    $data[] = [
                        'counter' => $counter++,
                        'page_name' => $page->page_name ?? '',
                        'slug' => $page->slug ?? '',
                        'meta_title' => $page->seo_title ?? '',
                        'approval_status' => $this->approvalBadge($approvalStatus),
                        'status' => $statusLink,
                        'action' => $actions,
                    ];
                }

                return response()->json([
                    'draw' => intval($request->get('draw')),
                    'recordsTotal' => Pages::count(),
                    'recordsFiltered' => $totalFiltered,
                    'data' => $data,
                ]);
            }

            // Regular page load
            return view('admin.pages.page.list', compact('page_title', 'page_description', 'breadcrumbs'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Handles the addition of new pages based on a specific rule.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function add(Request $request)
    {
       if ($request->isMethod('post')) {
            // Validation update - city_id bhi required kar do agar mandatory hai
            $validator = Validator::make($request->all(), [
                'rule_id' => 'required|exists:rules,id',
                'city_id' => 'required|exists:cities,id',  // Add this
                'number_of_combination' => 'required|integer|min:1',
            ], [
                'rule_id.required' => 'Rule is required.',
                'city_id.required' => 'Please select a city.',
                'number_of_combination.required' => 'Number of combination is required.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            DB::beginTransaction();

            try {
                // History record
                PagesHistory::updateOrCreate([
                    'rule_id' => $request->rule_id
                ], [
                    'no_of_pages' => $request->number_of_combination,
                    'created_by' => auth()->user()->id,
                ]);

                $rule = Rules::findOrFail($request->rule_id);

                $properties = $rule->properties ? json_decode($rule->properties, true) : [];

                $models = [];
                foreach ($properties as $property) {
                    $models[] = match ($property) {
                        'city-name' => City::class,
                        'locality-name' => Locality::class,
                        'category-name' => Category::class,
                        'item-name' => Items::class,
                        default => null,
                    };
                }
                $models = array_filter($models);

                $data = [];
                $selectedCityId = $request->city_id;

                foreach ($models as $model) {
                    $query = $model::where('status', 1);

                    // Important: City aur Locality ko selected city se filter karo
                    if ($model == City::class) {
                        $query->where('id', $selectedCityId); // Sirf selected city
                    } elseif ($model == Locality::class) {
                        $query->where('city_id', $selectedCityId); // Us city ki localities
                    }
                    // Category aur Items usually city-independent hote hain, to unko waise hi rakho

                    if ($model == Category::class) {
                        $items = $query->where('parent_id', '!=', 0)
                                    ->pluck('category_name')
                                    ->map(fn($name) => strtolower(str_replace(' ', '-', $name)))
                                    ->toArray();
                    } else {
                        $items = $query->pluck('slug')
                                    ->filter()
                                    ->toArray();
                    }

                    // Agar koi data nahi mila to empty array, warna combinations zero ho jayenge
                    $data[] = $items ?: [];
                }

                // Agar koi bhi array empty hai aur rule mein wo property hai, to warning ya limit check
                $expectedCombinations = !empty($data) ? array_product(array_map('count', $data)) : 0;

                if ($expectedCombinations > $request->number_of_combination) {
                    // Optional: Limit combinations if user asked for less
                    // Ya phir error de sakte ho
                }

                // Cartesian product
                $combinations = !empty($data) ? $this->cartesianProduct($data) : [];

                // Limit to user-requested number
                $combinations = array_slice($combinations, 0, $request->number_of_combination);

                $pagesToUpsert = [];
                foreach ($combinations as $combination) {
                    $slugPath = implode('/', $combination);
                    $fullSlug = $rule->prefix . '/' . $slugPath;

                    $pagesToUpsert[] = [
                        'page_name' => ucwords(str_replace('-', ' ', $slugPath)),
                        'rule_id' => $request->rule_id,
                        'slug' => $fullSlug,
                        'page_url' => env('FRONTENT_URI') . $fullSlug,
                        'created_by' => auth()->user()->id,
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($pagesToUpsert)) {
                    // Pages::upsert();
                    // return $pagesToUpsert;
                    Pages::upsert(
                         $pagesToUpsert,           // The data to insert/update
                        ['slug'],                 // The unique column(s) to check for duplicates
                        ['updated_at']
                    );
                }

                DB::commit();
                 return redirect('admin/page/list')->with('success', 'Pages generated successfully.');


            } catch (\Exception $e) {
                DB::rollback();
                \Log::error('Page Generation Failed: ' . $e->getMessage());
                return redirect()->back()->withErrors(['error' => 'Failed to generate pages: ' . $e->getMessage()]);
            }
         }
            // Display the "Add Page" form
            $pageSettings = $this->pageSetting('add');
            $rules = Rules::orderBy('id', 'desc')->where('status', 1)->get();
            $pagesHistory = PagesHistory::orderBy('id', 'desc')->get();

            return view('admin.pages.page.add', [
                'page_title' => $pageSettings['page_title'],
                'page_description' => $pageSettings['page_description'],
                'breadcrumbs' => $pageSettings['breadcrumbs'],
                'rules' => $rules,
                'pagesHistory' => $pagesHistory,
            ]);

    }

    /**
     * Calculates the cartesian product of a set of arrays.
     * Helper function to generate all combinations.
     *
     * @param array $arrays
     * @return array
     */
    private function cartesianProduct(array $arrays)
    {
        if (!$arrays) {
            return [];
        }
        $result = [[]];
        foreach ($arrays as $key => $values) {
            $append = [];
            foreach ($result as $product) {
                foreach ($values as $value) {
                    $product[$key] = $value;
                    $append[] = $product;
                }
            }
            $result = $append;
        }
        // Flatten the resulting multi-dimensional array into simple arrays of values
        return array_map(fn($item) => array_values($item), $result);
    }


    public function edit(Request $request, $id)
    {
        try {
            if ($id) {
                if ($request->isMethod('post')) {
                    $validator = Validator::make($request->all(), [
                        'page_url' => '',
                        'page_name' => '',
                        'slug' => '',
                        'seo_title' => '',
                        'seo_description' => '',
                        'seo_keywords' => '',
                        'og_meta_title' => '',
                        'og_meta_description' => '',
                        'og_meta_image_url' => '',
                        'twitter_card_title' => '',
                        'twitter_card_description' => '',
                        'schema_markup' => '',
                        'header_content' => '',
                        'center_content' => '',
                        'footer_content' => '',
                        'page_script' => '',
                    ], [
                        'page_url.required' => 'Page Url is required.',
                        'page_name.required' => 'Reference Name is required.',
                        'slug.required' => 'Slug is required.',
                        'seo_title.required' => 'Title is required.',
                        'seo_description.required' => 'Meta Description is required.',
                        'seo_keywords.required' => 'Meta Keywords is required.',
                        'og_meta_title.required' => 'OG Meta Title is required.',
                        'og_meta_description.required' => 'OG Meta Description is required.',
                        'og_meta_image_url.required' => 'OG Meta Image Url is required.',
                        'twitter_card_title.required' => 'Twitter card Title is required.',
                        'twitter_card_description.required' => 'Twitter card Description is required.',
                        'schema_markup.required' => 'Schema Markup is required.',
                        'header_content.required' => 'Header Content is required.',
                        'center_content.required' => 'Center Content is required.',
                        'footer_content.required' => 'Footer Content is required.',
                        'page_script.required' => 'Page Script is required.',
                    ]);
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
                    }
                    DB::beginTransaction();

                    $currentUser = auth()->user()->load('role');
                    $details = Pages::where('id', $id)->first();
                    if (!$details) {
                        DB::rollback();
                        return redirect()->back()->withErrors(['Page details not found.']);
                    }

                    $editableFields = $this->editableFields();
                    $newPayload = [];
                    foreach ($editableFields as $field) {
                        $newPayload[$field] = $request->{$field};
                    }

                    $oldPayload = [];
                    foreach ($editableFields as $field) {
                        $oldPayload[$field] = $details->{$field};
                    }

                    $hierarchy = UserApprovalHierarchy::where('user_id', $currentUser->id)->first();
                    $managerApproverId = null;
                    $adminApproverId = null;

                    if ($hierarchy) {
                        $managerApproverId = $hierarchy->manager_id;
                        $adminApproverId = $hierarchy->admin_id;
                    }

                    if (!$managerApproverId && !$adminApproverId) {
                        DB::rollback();
                        return redirect()->back()->withErrors(['Approval hierarchy is not configured for your user. Please contact Super Admin.'])->withInput($request->all());
                    }

                    $nextStatus = $managerApproverId ? 'pending_manager' : 'pending_admin';
                    $currentApproverId = $managerApproverId ?: $adminApproverId;

                    $existingPending = PageApprovalRequest::where('page_id', $id)
                        ->whereIn('status', ['pending_manager', 'pending_admin'])
                        ->orderBy('id', 'desc')
                        ->first();

                    if ($existingPending && $existingPending->requested_by != $currentUser->id && !$this->isSuperAdmin($currentUser)) {
                        DB::rollback();
                        return redirect()->back()->withErrors(['A pending approval request already exists for this page.'])->withInput($request->all());
                    }

                    if ($existingPending) {
                        $fromStatus = $existingPending->status;
                        $existingPending->update([
                            'requested_by' => $currentUser->id,
                            'manager_approver_id' => $managerApproverId,
                            'admin_approver_id' => $adminApproverId,
                            'current_approver_id' => $currentApproverId,
                            'old_payload' => $oldPayload,
                            'new_payload' => $newPayload,
                            'status' => $nextStatus,
                            'approver_comments' => null,
                            'approved_by' => null,
                            'rejected_by' => null,
                            'overridden_by' => null,
                            'reviewed_at' => null,
                            'published_at' => null,
                        ]);

                        PageApprovalRequestLog::create([
                            'request_id' => $existingPending->id,
                            'action_by' => $currentUser->id,
                            'action' => 'updated_request',
                            'from_status' => $fromStatus,
                            'to_status' => $nextStatus,
                            'comments' => 'Page update request refreshed by editor.',
                        ]);
                    } else {
                        $approvalRequest = PageApprovalRequest::create([
                            'page_id' => $details->id,
                            'requested_by' => $currentUser->id,
                            'manager_approver_id' => $managerApproverId,
                            'admin_approver_id' => $adminApproverId,
                            'current_approver_id' => $currentApproverId,
                            'old_payload' => $oldPayload,
                            'new_payload' => $newPayload,
                            'status' => $nextStatus,
                        ]);

                        PageApprovalRequestLog::create([
                            'request_id' => $approvalRequest->id,
                            'action_by' => $currentUser->id,
                            'action' => 'requested',
                            'from_status' => null,
                            'to_status' => $nextStatus,
                            'comments' => 'Page update submitted for approval.',
                        ]);
                    }

                    DB::commit();
                    return redirect('admin/page/list')->with('success', 'Page update saved as Pending Approval.');
                }
                $page_title = 'Page Management';
                $page_description = 'Edit Page';
                $details = Pages::with(['latestApprovalRequest'])->where('id', $id)->first();
                if ($details) {
                    $pageSettings = $this->pageSetting('edit', ['slug' => $details->slug]);

                    $page_title =  $pageSettings['page_title'];
                    $page_description = $pageSettings['page_description'];
                    $breadcrumbs = $pageSettings['breadcrumbs'];
                    return view('admin.pages.page.edit', compact('page_title', 'page_description', 'breadcrumbs', 'details'));
                } else {
                    return redirect()->back()->withErrors(['Category details not found.']);
                }
            } else {
                return redirect()->back()->withErrors(['Category id is missing.']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            if ($id) {
                DB::beginTransaction();
                $cat = Pages::find($id);
                if ($cat->delete()) {
                    DB::commit();
                    return redirect()->back()->with('success', 'Page deleted successfully.');
                } else {
                    return redirect()->back()->with('error', 'Failed to delete try again.');
                }
            } else {
                return redirect()->back()->with('error', 'Page details not found.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function updateStatus($pageId, $status)
    {
        try {
            if ($pageId) {
                // dd($pageId);
                DB::beginTransaction();
                $status = ($status == 1) ? $status = 0 : $status = 1;
                $updateArr = [
                    'status' => $status,
                ];
                $response = Pages::UpdateOrCreate(['id' => $pageId], $updateArr);
                // dd($response);
                DB::commit();
                return redirect('admin/page/list')->with('success', 'Page status updated successfully.');
            } else {
                return redirect()->back()->with('error', 'Page details not found.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage())->withInput($request->all());
        }
    }



    public function pageSetting($action, $dataArray = [])
    {
        if ($action == 'edit') {
            $data['page_title'] = 'Page';
            $data['page_description'] = 'Edit Page';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Page Management',
                    'url' => url('admin/page/list'),
                ]
            ];
            if (isset($dataArray['title']) && !empty($dataArray['title'])) {
                $data['breadcrumbs'][] =
                    [
                        'title' => $dataArray['title'],
                        'url' => '',

                    ];
            } else {
                $data['breadcrumbs'][] = [

                    'title' => 'Edit Page',
                    'url' => '',

                ];
            }
            return $data;
        }

        if ($action == 'add') {
            $data['page_title'] = 'Page';
            $data['page_description'] = 'Add a New Page';
            $data['breadcrumbs'] = [
                [
                    'title' => 'Page Management',
                    'url' => url('admin/page/list'),
                ],
                [
                    'title' => 'Add Page',
                    'url' => '',
                ],
            ];
            return $data;
        }
    }
}
