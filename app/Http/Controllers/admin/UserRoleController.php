<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Auth;
use Session;
use Redirect;

class UserRoleController extends Controller
{
    /**
     * Load admin login page
     * @method index
     * @param  null
     *
     */
    public function index()
    {
        $page_title = 'User Role Management';
        $page_description = '';
        $breadcrumbs = [
            [
                'title' => 'User Role Management',
                'url' => '',
            ]
        ];
        return view('admin.pages.userrole.list', compact('page_title', 'page_description', 'breadcrumbs'));
    }

    /**
     * Load admin add user
     * @method add user
     * @param null
     */
    public function add()
    {
        $page_title = 'User Role Management';
        $page_description = 'Add User Role';
        $breadcrumbs = [
            [
                'title' => 'User Role Management',
                'url' => url('admin/userrole/list'),
            ],
            [
                'title' => 'Add User Role',
                'url' => '',
            ],
        ];
        return view('admin.pages.userrole.add', compact('page_title', 'page_description', 'breadcrumbs'));
    }
    public function edit()
    {
        $page_title = 'User Role Management';
        $page_description = 'Edit User Role';
        $breadcrumbs = [
            [
                'title' => 'User Role Management',
                'url' => url('admin/userrole/list'),
            ],
            [
                'title' => 'Edit User Role',
                'url' => '',
            ],
        ];

        return view('admin.pages.userrole.edit', compact('page_title', 'page_description', 'breadcrumbs'));
    }
    public function permission()
    {
        $page_title = 'User Role Management';
        $page_description = 'Role Permissions';
        $breadcrumbs = [
            [
                'title' => 'User Role Management',
                'url' => url('admin/userrole/list'),
            ],
            [
                'title' => 'Role Permissions',
                'url' => '',
            ],
        ];

        return view('admin.pages.userrole.permission', compact('page_title', 'page_description', 'breadcrumbs'));
    }
}
