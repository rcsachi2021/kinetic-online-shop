<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AdminLoginController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(),['email' => 'required|email', 'password' => 'required']);
        if($validator->passes())
        {
            if(auth()->guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember')))
            {
                $admin = auth()->guard('admin')->user();
                
                if($admin ->role === 1){
                    
                    return redirect()->route('admin.dashboard');
                }else{
                    auth()->guard('admin')->logout();
                    return redirect()->route('admin.login')->with('error', 'You are not authorized to access this account');
                }
                
            }else{
                return redirect()->back()->with('error', 'Invalid Email/Password');
            }
        }else{
            return redirect()->back()->withErrors($validator->errors())->withInput($request->only('email','password'));
        }
    }
}
