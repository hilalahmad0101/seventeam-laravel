<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('admin.auth.login');
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                'exists:users,email'
            ],
            'password' => [
                'required',
                'string'
            ]
        ]);

        $is_admin=User::whereEmailAndRole($request->email,1)->first();
        if(!$is_admin){
            return redirect()->back()->with('error','Invalid email and password');
        }

        $admin = Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ]);
        if ($admin) {
            return redirect()->intended('/dashboard')->with('success', 'Login Successfully');
        } else {
            return redirect()->back()->with('error', 'Invalid Email and password');
        }
    }
}
