<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('customer.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:customers,email',
            'password'=>'required|min:6|confirmed'
        ]);

        $customer = Customer::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'status'=>'active',
            'created_by'=>null
        ]);

        return redirect()->route('customer.login')->with('success','Registration successful!');
    }

    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email','password');

        if(Auth::guard('customer')->attempt($credentials)){
            return redirect()->route('customer.dashboard');
        }

        return back()->withErrors(['email'=>'Invalid credentials']);
    }

    public function dashboard()
    {
        return view('customer.auth.dashboard');
    }

    public function logout()
    {
        Auth::guard('customer')->logout();
        return redirect()->route('customer.login');
    }
}
