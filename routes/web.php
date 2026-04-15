<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\AuthController;

/*
|--------------------------------------------------------------------------
| Default Welcome Page Route
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Default Dashboard Route (Admin/User)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile Routes (Default Laravel Authentication)
|--------------------------------------------------------------------------
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
*/
Route::prefix('customer')->name('customer.')->group(function(){

    // Customer Registration
    Route::get('register', [AuthController::class, 'showRegisterForm'])
        ->name('register');
    Route::post('register', [AuthController::class, 'register']);

    // Customer Login
    Route::get('login', [AuthController::class, 'showLoginForm'])
        ->name('login');
    Route::post('login', [AuthController::class, 'login']);

    // Customer Dashboard (auth:customer)
    Route::get('dashboard', [AuthController::class, 'dashboard'])
        ->middleware('auth:customer')
        ->name('dashboard');

    // Customer Logout
    Route::get('logout', [AuthController::class, 'logout'])
        ->name('logout');
});

/*
|--------------------------------------------------------------------------
| Default Laravel Authentication Routes (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';