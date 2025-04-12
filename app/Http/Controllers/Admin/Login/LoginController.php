<?php

namespace App\Http\Controllers\Admin\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = "/admin";

    public function __construct()
    {
        $this->middleware('guest:admins')->except('logout');
    }

    public function showLoginForm()
    {
        return view('admin.login.login');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);
        
        if (Auth::guard('admins')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
            return redirect()->intended('/admin/dashboard');
        }
        
        return back()->withInput($request->only('email', 'remember'));
    }

    public function logout()
    {
        Auth::guard("admins")->logout();
        Session::flush();
        Session::regenerate();
        return view('admin.login.login');
    }
}
