<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Session;
use Redirect;
use Validator;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Load admin login page
     * @method index
     * @param  null
     *
     */
    public function index()
    {
        return view('admin.pages.auth.login');
    }

    /**
     * Admin login and their employee
     * @method login
     * @param null
     */
    public function login(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect("admin/")->withErrors($validator);
        }
        $userData = array(
            'email' => $req->get('email'),
            'password' => $req->get('password')
        ); 
      
        if (Auth::attempt($userData)) {


            $user = Auth::user();
            $id = $user->id;
            $email = $user->email;
            $name = $user->name;
            $role = $user->role_id;
            if ($user->status == 1) {
                $systemRoles = getSystemRoles($role)->toArray();
               
                if (count($systemRoles)) {
                    $systemRoles = json_decode($systemRoles[0]['permission'], 1);
                }
                Session::put('id', $id);
                Session::put('name', $name);
                Session::put('email', $email);
                Session::put('role', $role);
                Session::put('access_name', 'admin');
                Session::put('system_roles', $systemRoles);
                /**
                 * Update last login and last login IP
                 */
                $this->authenticated($req, $user);
                return redirect("admin/dashboard");
            } else {
                Auth::logout();

                Session::flash('status', "This user has been deactivated.");
                return redirect("admin?error=This user has been deactivated.")->withError(['error' => 'This user has been deactivated.']);
            }
        } else {

            Auth::logout();
            Session::flush();
            Session::flash('status', "Invalid Login");
            return redirect("admin/")->withErrors(['error' => 'Invalid Email and Password.']);
        }
    }

    public function logout()
    {

        Auth::logout();
        Session::flush();
        return Redirect::to('admin/');
    }
    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    function authenticated($request, $user)
    {
        $user->update([
            'last_login' => date('Y-m-d H:i:s'),
            'last_login_ip' => $request->getClientIp()
        ]);
    }
}
