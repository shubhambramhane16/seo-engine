<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserApprovalHierarchy;
use DB;
use Validator;
use Image;
use File;
use Auth;
use Session;
use Redirect;
use Illuminate\Support\Str;

class UserController extends Controller
{
  private function isSuperAdmin($user)
  {
    if (!$user || !$user->role) {
      return false;
    }

    $roleTitle = strtolower(trim($user->role->role));
    return $roleTitle === 'super admin' || $roleTitle === 'superadmin' || Str::contains($roleTitle, 'super');
  }

  private function saveApprovalHierarchy($userId, Request $request)
  {
    $managerId = $request->reporting_manager_id ?: null;
    $adminId = $request->admin_approver_id ?: null;

    if ($managerId && $managerId == $userId) {
      return 'User cannot be their own reporting manager.';
    }

    if ($adminId && $adminId == $userId) {
      return 'User cannot be their own final approver.';
    }

    if ($managerId && $adminId && $managerId == $adminId) {
      return 'Reporting manager and final approver must be different users.';
    }

    UserApprovalHierarchy::updateOrCreate([
      'user_id' => $userId,
    ], [
      'manager_id' => $managerId,
      'admin_id' => $adminId,
      'updated_by' => auth()->user()->id,
      'created_by' => auth()->user()->id,
    ]);

    return null;
  }

  /**
   * Load admin login page
   * @method index
   * @param  null
   *
   */
  public function index()
  {
    try {
      $page_title = 'User Management';
      $page_description = '';
      $breadcrumbs = [
        [
          'title' => 'User Management',
          'url' => '',
        ]
      ];
      $status = request('status');
      if ($status == '0') {
        $status = '2';
      }
      $currentUser = auth()->user()->load('role');
      $isSuperAdmin = $this->isSuperAdmin($currentUser);

      $users = User::with(['role', 'approvalHierarchy.manager', 'approvalHierarchy.admin'])->when($status, function ($users) use ($status) {
        if ($status != '-1') {
          $status = conditionalStatus($status);
          $users->where('status', '=', $status);
        }
      })->orderBy('id', 'desc')->get();
      return view('admin.pages.users.list', compact('page_title', 'page_description', 'breadcrumbs',  'users', 'isSuperAdmin'));
    } catch (\Exception $e) {
      dd($e);
      return redirect()->back()->with('error', $e->getMessage());
    }



    // return view('admin.pages..list', compact('page_title', 'page_description', 'breadcrumbs'));
  }

  /**
   * Load admin add user
   * @method add user
   * @param null
   */
  public function addUser(Request $request)
  {
    try {
      if ($request->isMethod('post')) {
        $validator = Validator::make($request->all(), [
          'name' => 'required',
          'email' => 'required',
          'mobile' => 'required',
          'role_id' => 'required',
          'password' => 'required',
        ], [
          'name.required' => 'User name is required.',
          'email.required' => 'Email is required.',
          'mobile.required' => 'Mobile no is required.',
          'role_id.required' => 'Select user role.',
          'password.required' => 'Password is required.',
        ]);
        if ($validator->fails()) {
          return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        DB::beginTransaction();


        $array = [
          'name' => $request->name,
          'email' => $request->email,
          'mobile' => $request->mobile,
          'role_id' => $request->role_id,
          'status' => 0,
          'created_by' => auth()->user()->id,
          'updated_by' => auth()->user()->id,
          'password' => bcrypt($request->password)

        ];
        $UserEmail = User::where('email', $array['email'])->exists();
        if ($UserEmail) {
          return redirect()->back()->withErrors(['User Email already exist.'])->withInput($request->all());
        }
        // dd(  $array );
        $response = User::UpdateOrCreate(['id' => null], $array);

        $isSuperAdmin = $this->isSuperAdmin(auth()->user()->load('role'));
        if ($isSuperAdmin) {
          $hierarchyError = $this->saveApprovalHierarchy($response->id, $request);
          if ($hierarchyError) {
            DB::rollback();
            return redirect()->back()->withErrors([$hierarchyError])->withInput($request->all());
          }
        }

        DB::commit();
        return redirect('admin/users/list')->with('success', 'User details added successfully.');
      }

      $pageSettings = $this->pageSetting('add');

      $page_title =  $pageSettings['page_title'];
      $page_description = $pageSettings['page_description'];
      $breadcrumbs = $pageSettings['breadcrumbs'];

      $isSuperAdmin = $this->isSuperAdmin(auth()->user()->load('role'));
      $approvers = User::where('status', 1)->orderBy('name', 'asc')->get();
      return view('admin.pages.users.add', compact('page_title', 'page_description', 'breadcrumbs', 'isSuperAdmin', 'approvers'));
    } catch (\Exception $e) {
      dd($e);
      DB::rollback();
      return redirect()->back()->withErrors($e->getMessage());
    }
  }
  public function editUser(Request $request, $id)
  {

    try {
      if ($request->isMethod('post')) {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
          'name' => 'required',
          'email' => 'required',
          'mobile' => 'required',
          'role_id' => 'required',
        ], [
          'name.required' => 'User name is required.',
          'email.required' => 'Email is required.',
          'mobile.required' => 'Mobile no is required.',
          'role_id.required' => 'Select user role.',
        ]);
        if ($validator->fails()) {
          return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        DB::beginTransaction();

        $array = [
          'name' => $request->name,
          'email' => $request->email,
          'mobile' => $request->mobile,
          'role_id' => $request->role_id

        ];
        $UserEmail = User::where('email', $array['email'])->where('id', '!=', $id)->exists();
        if ($UserEmail) {
          return redirect()->back()->withErrors(['User Email already exist.'])->withInput($request->all());
        }
        // dd(  $array );
        $response = User::UpdateOrCreate(['id' => $id], $array);

        $isSuperAdmin = $this->isSuperAdmin(auth()->user()->load('role'));
        if ($isSuperAdmin) {
          $hierarchyError = $this->saveApprovalHierarchy($response->id, $request);
          if ($hierarchyError) {
            DB::rollback();
            return redirect()->back()->withErrors([$hierarchyError])->withInput($request->all());
          }
        }

        DB::commit();
        return redirect('admin/users/list')->with('success', 'User details added successfully.');
      }

      $pageSettings = $this->pageSetting('edit');

      $page_title =  $pageSettings['page_title'];
      $page_description = $pageSettings['page_description'];
      $breadcrumbs = $pageSettings['breadcrumbs'];

      $details = User::with('approvalHierarchy')->where('id', $id)->first();
      $isSuperAdmin = $this->isSuperAdmin(auth()->user()->load('role'));
      $approvers = User::where('status', 1)->where('id', '!=', $id)->orderBy('name', 'asc')->get();

      return view('admin.pages.users.edit', compact('page_title', 'page_description', 'breadcrumbs', 'details', 'isSuperAdmin', 'approvers'));
    } catch (\Exception $e) {
      dd($e);
      DB::rollback();
      return redirect()->back()->withErrors($e->getMessage());
    }




    return view('admin.pages.users.edit', compact('page_title', 'page_description', 'breadcrumbs'));
  }





  public function delete($id)
  {
    try {
      if ($id) {
        DB::beginTransaction();
        $cat = User::find($id);
        if ($cat->delete()) {
          DB::commit();
          return redirect()->back()->with('success', 'User deleted successfully.');
        } else {
          return redirect()->back()->with('error', 'Failed to delete try again.');
        }
      } else {
        return redirect()->back()->with('error', 'User details not found.');
      }
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->with('error', $e->getMessage());
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
        $response = User::UpdateOrCreate(['id' => $id], $updateArr);
        DB::commit();
        return redirect('admin/users/list')->with('success', 'User status updated successfully.');
      } else {
        return redirect()->back()->with('error', 'User details not found.');
      }
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->with('error', $e->getMessage());
    }
  }




  public function pageSetting($action, $dataArray = [])
  {
    if ($action == 'edit') {
      $data['page_title'] = 'User Management';
      $data['page_description'] = 'Edit User';
      $data['breadcrumbs'] = [
        [
          'title' => 'User Management',
          'url' => url('admin/users/list'),
        ],
        [
          'title' => 'Edit User',
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
      $data['page_title'] = 'User Management';
      $data['page_description'] = 'Add a User';
      $data['breadcrumbs'] = [
        [
          'title' => 'User Management',
          'url' => url('admin/users/list'),
        ],
        [
          'title' => 'Add a User',
          'url' => '',
        ],
      ];
      return $data;
    }
  }
}
