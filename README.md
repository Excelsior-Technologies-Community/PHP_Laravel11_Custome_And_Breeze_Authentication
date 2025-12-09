# ✅ PHP_Laravel12_Custome_Authentication_Breeze_Authentication


---

## ⭐ Introduction

This tutorial demonstrates how to implement **Authentication in Laravel 11** with **two separate roles**:

### 🔹 Admin Authentication

* Implemented using **Laravel Breeze**
* Uses the default `users` table

### 🔹 Customer Authentication

* Custom Login & Registration (no Breeze)
* Uses a **separate `customers` table**
* Each customer includes:

  * name
  * email
  * password
  * status
  * created_by
  * updated_by
  * soft deletes

This tutorial is:

✅ Beginner-friendly
✅ Step-by-step
✅ Interview-focused
✅ Cleanly structured with real-world Laravel practices

---

## ⭐ Step 1: Install Laravel 11

```bash
composer create-project laravel/laravel:^11.0 PHP_Laravel12_Custome_Authentication_Breeze_Authentication
cd PHP_Laravel12_Custome_Authentication_Breeze_Authentication
```

---

## ⭐ Step 2: Configure Database

Open **.env** file and update:

```env
DB_DATABASE=laravel11_auth
DB_USERNAME=root
DB_PASSWORD=
```

---

## ⭐ Step 3: Create Customers Table Migration

**File:** `database/migrations/xxxx_create_customers_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This method is executed when we run: php artisan migrate
     */
    public function up(): void
    {
        // Create the 'customers' table
        Schema::create('customers', function (Blueprint $table) {

            $table->id(); 
            // Primary key (Auto-increment ID)

            $table->string('name'); 
            // Customer full name

            $table->string('email')->unique(); 
            // Customer email (used for login, must be unique)

            $table->string('password'); 
            // Stores hashed password

            $table->enum('status', ['active', 'inactive'])->default('active'); 
            // Account status: active or inactive

            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->cascadeOnDelete();
            // Admin user who created this customer
            // If admin is deleted → customer record will also be deleted

            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            // Admin user who last updated this customer
            // If admin is deleted → set updated_by to NULL

            $table->timestamps(); 
            // created_at & updated_at timestamps

            $table->softDeletes(); 
            // deleted_at column for soft delete functionality
        });
    }

    /**
     * Reverse the migrations.
     * This method runs when: php artisan migrate:rollback
     */
    public function down(): void
    {
        // Drop the 'customers' table if it exists
        Schema::dropIfExists('customers');
    }
};

```

## ⭐ Step 4: Default Users Table (Admin)

Already provided by Laravel. No modification required.
```

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

```

## ⭐ Step 5: Run Migration

```bash
php artisan migrate
```

---

## ⭐ Step 6: Create Customer Model

**File:** `app/Models/Customer.php`

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Customer Model
 * ----------------
 * This model represents the `customers` table.
 * It is used for CUSTOM CUSTOMER AUTHENTICATION
 * and works separately from the default User model (admins).
 */
class Customer extends Authenticatable
{
    // Enables notification support & soft delete feature
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * -----------------------------------------
     * These fields can be filled using:
     * Customer::create([...])
     */
    protected $fillable = [
        'name',        // Customer full name
        'email',       // Customer email (used for login)
        'password',    // Encrypted password
        'status',      // active / inactive
        'created_by',  // Admin ID who created customer
        'updated_by'   // Admin ID who last updated customer
    ];

    /**
     * Attributes hidden from arrays & JSON responses
     * ----------------------------------------------
     * Password will never be exposed accidentally
     */
    protected $hidden = [
        'password'
    ];

    /**
     * Date attributes used for soft deletes
     * -------------------------------------
     * deleted_at column is handled automatically
     */
    protected $dates = [
        'deleted_at'
    ];
}

```

---

## ⭐ Step 7: Configure Authentication Guards

**File:** `config/auth.php`

Add customer guard and provider:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'customer' => [
        'driver' => 'session',
        'provider' => 'customers',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
    'customers' => [
        'driver' => 'eloquent',
        'model' => App\Models\Customer::class,
    ],
],
```

---

## ⭐ Step 8: Create Customer Auth Controller

```bash
php artisan make:controller Customer/AuthController
```

**File:** `app/Http/Controllers/Customer/AuthController.php`

```php
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

```

---

## ⭐ Step 9: Customer Routes

**File:** `routes/web.php`

```php
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

```

---

## ⭐ Step 10: Customer Views

All Tailwind-based views:

```
resources/views/customer/auth/
├── register.blade.php
├── login.blade.php
└── dashboard.blade.php
```

🔷 View 1: Customer Register

resources/views/customer/auth/register.blade.php

```


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <!-- Responsive layout for mobile, tablet and desktop -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Page Title -->
    <title>Customer Register</title>

    <!-- Tailwind CSS CDN for modern styling -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<!-- Full-screen center alignment -->
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <!-- Main Registration Card -->
    <div class="bg-white shadow-lg rounded-lg w-full max-w-md p-8">

        <!-- Page Heading -->
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">
            Customer Register
        </h2>

        <!-- ===============================
             SUCCESS MESSAGE
             Displayed after successful registration
        ================================== -->
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- ===============================
             VALIDATION ERROR MESSAGES
             Displayed when form input fails validation
        ================================== -->
        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- ===============================
             CUSTOMER REGISTRATION FORM
        ================================== -->
        <form action="{{ route('customer.register') }}" method="POST" class="space-y-5">

            <!-- CSRF Token: Required for form security -->
            @csrf

            <!-- Customer Name Field -->
            <div>
                <label class="block text-gray-700 mb-1" for="name">
                    Name
                </label>

                <input
                    type="text"
                    name="name"
                    id="name"
                    placeholder="Enter your name"
                    class="w-full border border-gray-300 rounded px-4 py-2
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                >
            </div>

            <!-- Customer Email Field -->
            <div>
                <label class="block text-gray-700 mb-1" for="email">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    id="email"
                    placeholder="Enter your email"
                    class="w-full border border-gray-300 rounded px-4 py-2
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                >
            </div>

            <!-- Password Field -->
            <div>
                <label class="block text-gray-700 mb-1" for="password">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Enter your password"
                    class="w-full border border-gray-300 rounded px-4 py-2
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                >
            </div>

            <!-- Confirm Password Field -->
            <div>
                <label class="block text-gray-700 mb-1" for="password_confirmation">
                    Confirm Password
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    placeholder="Confirm your password"
                    class="w-full border border-gray-300 rounded px-4 py-2
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                >
            </div>

            <!-- Register Button -->
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold
                       py-2 px-4 rounded transition duration-200"
            >
                Register
            </button>
        </form>

        <!-- Redirect to Login Page -->
        <div class="text-center mt-5">
            <p class="text-gray-600">
                Already have an account?
                <a href="{{ route('customer.login') }}"
                   class="text-blue-600 hover:underline">
                   Login here
                </a>
            </p>
        </div>

    </div>
</body>
</html>


```
🔷 View 2: Customer Login

resources/views/customer/auth/login.blade.php
```

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Page Title -->
    <title>Customer Login</title>

    <!-- TailwindCSS CDN for modern UI styling -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<!-- Background + Center layout -->
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <!-- Login Container Card -->
    <div class="bg-white shadow-lg rounded-lg w-full max-w-md p-8">

        <!-- Page Heading -->
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">
            Customer Login
        </h2>

        <!-- ===============================
             DISPLAY VALIDATION ERRORS
        ================================== -->
        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <!-- Display first validation error -->
                {{ $errors->first() }}
            </div>
        @endif

        <!-- ===============================
             DISPLAY SUCCESS MESSAGE
             (e.g., after registration)
        ================================== -->
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif


        <!-- ===============================
             LOGIN FORM
        ================================== -->
        <form action="{{ route('customer.login') }}" method="POST" class="space-y-5">

            <!-- CSRF Token for Form Security -->
            @csrf

            <!-- Email Input Field -->
            <div>
                <label class="block text-gray-700 mb-1" for="email">Email</label>

                <!-- Customer email input -->
                <input type="email" name="email" id="email" placeholder="Enter your email"
                    class="w-full border border-gray-300 rounded px-4 py-2
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <!-- Password Input Field -->
            <div>
                <label class="block text-gray-700 mb-1" for="password">Password</label>

                <!-- Customer password input -->
                <input type="password" name="password" id="password" placeholder="Enter your password"
                    class="w-full border border-gray-300 rounded px-4 py-2
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <!-- Login Button -->
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold
                       py-2 px-4 rounded transition duration-200">
                Login
            </button>
        </form>


        <!-- Redirect to Registration Page -->
        <div class="text-center mt-5">
            <p class="text-gray-600">
                Don't have an account?
                <a href="{{ route('customer.register') }}" class="text-blue-600 hover:underline">
                    Register here
                </a>
            </p>
        </div>

    </div>

</body>
</html>

```
🔷 View 3: Customer Dashboard

resources/views/customer/auth/dashboard.blade.php
```

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <!-- Ensure proper scaling on all devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Page Title -->
    <title>Customer Dashboard</title>

    <!-- TailwindCSS CDN for modern UI styling -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<!-- Full height + light gray background + column layout -->
<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- ===============================
         TOP NAVBAR
    ================================== -->
    <nav class="bg-white shadow px-6 py-4 flex justify-between items-center">

        <!-- Dashboard Title -->
        <h1 class="text-xl font-bold text-gray-800">Customer Dashboard</h1>

        <!-- Logout Button -->
        <a href="{{ route('customer.logout') }}"
           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition duration-200">
            Logout
        </a>
    </nav>


    <!-- ===============================
         MAIN DASHBOARD CONTENT
    ================================== -->
    <div class="flex-grow flex items-center justify-center">

        <!-- Dashboard Card -->
        <div class="bg-white shadow-lg rounded-lg w-full max-w-lg p-8 text-center">

            <!-- Logged-in Customer Name -->
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Welcome, {{ Auth::guard('customer')->user()->name }}!
            </h2>

            <!-- Simple dashboard description -->
            <p class="text-gray-600 mb-6">
                You are successfully logged in to your customer account.
            </p>

        </div>
    </div>


    <!-- ===============================
         FOOTER SECTION
    ================================== -->
    <footer class="bg-white shadow py-4 text-center text-gray-500">
        &copy; {{ date('Y') }} Your Company. All rights reserved.
    </footer>

</body>
</html>

```
## ⭐ Step 11: Install Breeze for Admin Authentication

```bash
composer require laravel/breeze
php artisan breeze:install
npm install
npm run build
php artisan migrate
```

Admin Routes:

* `/login`
* `/register`

Uses **users** table.

---

## 📁 Final Project Structure

```
laravel11-authentication
├── app/Models/Customer.php
├── app/Http/Controllers/Customer/AuthController.php
├── routes/web.php
├── resources/views/customer/auth
│   ├── register.blade.php
│   ├── login.blade.php
│   └── dashboard.blade.php
├── resources/views/auth   // Breeze Admin
├── database/migrations
│   ├── create_users_table.php
│   └── create_customers_table.php
└── .env
```

---

🎉 **Your Laravel 11 Admin + Customer Authentication tutorial is complete and production-ready!**
