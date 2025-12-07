<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Here is where you can register web routes for your application.
| These routes are loaded by the RouteServiceProvider.
| They all receive the "web" middleware group.
*/

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\AuthController;

/*
|--------------------------------------------------------------------------
| Default Welcome Page Route
|--------------------------------------------------------------------------
| Loads Laravel's default welcome page
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Default Dashboard Route (Admin/User)
|--------------------------------------------------------------------------
| Protected by "auth" and "verified" middleware
| Only logged-in and verified users can access this page
*/
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile Routes (Default Laravel Authentication)
|--------------------------------------------------------------------------
| These routes are used for the default users table
| Access is limited to authenticated users only
*/
Route::middleware('auth')->group(function () {

    // Show profile edit page
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    // Update profile data
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    // Delete user account
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Customer Authentication Routes
|--------------------------------------------------------------------------
| Separate authentication system for customers
| Uses custom customers table and customer guard
*/
Route::prefix('customer')->name('customer.')->group(function(){

    /*
    |--------------------------------------------------------------
    | Customer Registration Routes
    |--------------------------------------------------------------
    */

    // Show customer registration form
    Route::get('register', [AuthController::class, 'showRegisterForm'])
        ->name('register');

    // Handle customer registration form submission
    Route::post('register', [AuthController::class, 'register']);


    /*
    |--------------------------------------------------------------
    | Customer Login Routes
    |--------------------------------------------------------------
    */

    // Show customer login form
    Route::get('login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    // Handle customer login request
    Route::post('login', [AuthController::class, 'login']);


    /*
    |--------------------------------------------------------------
    | Customer Dashboard Route
    |--------------------------------------------------------------
    | Protected using "auth:customer" middleware
    | Only logged-in customers can access this route
    */

    Route::get('dashboard', [AuthController::class, 'dashboard'])
        ->middleware('auth:customer')
        ->name('dashboard');


    /*
    |--------------------------------------------------------------
    | Customer Logout Route
    |--------------------------------------------------------------
    */

    // Logout customer and redirect to login page
    Route::get('logout', [AuthController::class, 'logout'])
        ->name('logout');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Laravel Breeze / Jetstream)
|--------------------------------------------------------------------------
| Handles login, register, password reset for default users
*/
require __DIR__.'/auth.php';
