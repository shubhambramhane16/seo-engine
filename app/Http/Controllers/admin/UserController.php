<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;
use DB;
use Validator;
use Image;
use File;
use Auth;
use Session;
use Redirect;

class UserController extends Controller
{
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
      $users = User::with(['role'])->when($status, function ($users) use ($status) {
        if ($status != '-1') {
          $status = conditionalStatus($status);
          $users->where('status', '=', $status);
        }
      })->orderBy('id', 'desc')->get();
      return view('admin.pages.users.list', compact('page_title', 'page_description', 'breadcrumbs',  'users'));
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
        DB::commit();
        return redirect('admin/users/list')->with('success', 'User details added successfully.');
      }

      $pageSettings = $this->pageSetting('add');

      $page_title =  $pageSettings['page_title'];
      $page_description = $pageSettings['page_description'];
      $breadcrumbs = $pageSettings['breadcrumbs'];

      return view('admin.pages.users.add', compact('page_title', 'page_description', 'breadcrumbs'));
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
        DB::commit();
        return redirect('admin/users/list')->with('success', 'User details added successfully.');
      }

      $pageSettings = $this->pageSetting('edit');

      $page_title =  $pageSettings['page_title'];
      $page_description = $pageSettings['page_description'];
      $breadcrumbs = $pageSettings['breadcrumbs'];

      $details = User::where('id', $id)->first();

      return view('admin.pages.users.edit', compact('page_title', 'page_description', 'breadcrumbs', 'details'));
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
