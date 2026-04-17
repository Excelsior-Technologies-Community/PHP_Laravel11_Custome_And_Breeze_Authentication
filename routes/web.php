<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('customer')->name('customer.')->group(function(){

    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);

    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:customer')->group(function () {
        Route::get('dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
        
        // Profile Routes for Customer
        Route::get('profile', [AuthController::class, 'editProfile'])->name('profile.edit');
        Route::post('profile', [AuthController::class, 'updateProfile'])->name('profile.update');
        
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    });
});

require __DIR__.'/auth.php';