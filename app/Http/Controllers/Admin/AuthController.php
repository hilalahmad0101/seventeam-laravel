<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        $admin = Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ]);

        if ($admin) {
            return redirect()->intended('admin.dashboard')->with('success', 'Login Successfully');
        } else {
            return redirect()->back()->with('success', 'Invalid Email and password');
        }
    }
}
