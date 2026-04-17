<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SessionController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [SessionController::class, 'index'])->name('dashboard');
    Route::delete('/sessions/{id}', [SessionController::class, 'destroy'])->name('sessions.destroy');
    Route::post('/sessions/logout-all', [SessionController::class, 'logoutOtherDevices'])->name('sessions.logout_all');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('customers/{customer}/toggle', [CustomerController::class, 'toggleStatus'])->name('customers.toggle');
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
});

Route::prefix('customer')->name('customer.')->group(function(){

    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);

    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:customer')->group(function () {
        Route::get('dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
        
        Route::get('profile', [AuthController::class, 'editProfile'])->name('profile.edit');
        Route::post('profile', [AuthController::class, 'updateProfile'])->name('profile.update');
        
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    });
});

require __DIR__.'/auth.php';