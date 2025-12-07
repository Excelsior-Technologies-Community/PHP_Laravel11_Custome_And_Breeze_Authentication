<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Customer Authentication Controller
 * -----------------------------------
 * This controller handles:
 *  - Customer Registration
 *  - Customer Login
 *  - Customer Dashboard access
 *  - Customer Logout
 *
 * It uses the custom "customer" guard
 * defined in config/auth.php
 */
class AuthController extends Controller
{
    /**
     * Show customer registration form
     * --------------------------------
     * Returns: resources/views/customer/auth/register.blade.php
     */
    public function showRegisterForm()
    {
        return view('customer.auth.register');
    }

    /**
     * Handle customer registration
     * -----------------------------
     * Steps:
     * 1. Validate request data
     * 2. Hash customer password
     * 3. Save customer in database
     * 4. Redirect to login page
     */
    public function register(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:customers,email',
            'password' => 'required|min:6|confirmed'
        ]);

        // Create customer record
        $customer = Customer::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password), // encrypt password
            'status'     => 'active',                       // default status
            'created_by' => null                            // no admin at signup
        ]);

        // Redirect to login page after successful registration
        return redirect()->route('customer.login')
                         ->with('success', 'Registration successful!');
    }

    /**
     * Show customer login form
     * ------------------------
     * Returns: resources/views/customer/auth/login.blade.php
     */
    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    /**
     * Handle customer login
     * ----------------------
     * Steps:
     * 1. Get email & password
     * 2. Attempt login using customer guard
     * 3. Redirect to dashboard if success
     * 4. Show error if credentials invalid
     */
    public function login(Request $request)
    {
        // Extract only email & password from request
        $credentials = $request->only('email', 'password');

        // Attempt authentication using customer guard
        if (Auth::guard('customer')->attempt($credentials)) {
            return redirect()->route('customer.dashboard');
        }

        // Login failed
        return back()->withErrors([
            'email' => 'Invalid credentials'
        ]);
    }

    /**
     * Customer Dashboard
     * -------------------
     * Accessible only after customer login
     */
    public function dashboard()
    {
        return view('customer.auth.dashboard');
    }

    /**
     * Customer Logout
     * ----------------
     * Logs out authenticated customer
     * and redirects to login page
     */
    public function logout()
    {
        Auth::guard('customer')->logout();

        return redirect()->route('customer.login');
    }
}
