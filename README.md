# ✅ laravel11-authentication

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
composer create-project laravel/laravel:^11.0 laravel11-authentication
cd laravel11-authentication
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
     * Create the customers table.
     * This table is used only for customer login (not admin).
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();                     // Primary key
            $table->string('name');           // Customer name
            $table->string('email')->unique();// Login email
            $table->string('password');       // Hashed password

            $table->enum('status', ['active','inactive'])->default('active');

            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
```

---

```
## ⭐ Step 4: Default Users Table (Admin)

Already provided by Laravel. No modification required.

---

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

class Customer extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'status', 'created_by', 'updated_by'
    ];

    protected $hidden = ['password'];
    protected $dates = ['deleted_at'];
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

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('customer.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|min:6|confirmed'
        ]);

        Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'active',
            'created_by' => null
        ]);

        return redirect()->route('customer.login')->with('success','Registration successful');
    }

    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    public function login(Request $request)
    {
        if (Auth::guard('customer')->attempt($request->only('email','password'))) {
            return redirect()->route('customer.dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
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
```

---

## ⭐ Step 9: Customer Routes

**File:** `routes/web.php`

```php
use App\Http\Controllers\Customer\AuthController;

Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('register', [AuthController::class,'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class,'register']);

    Route::get('login', [AuthController::class,'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class,'login']);

    Route::get('dashboard', [AuthController::class,'dashboard'])->middleware('auth:customer')->name('dashboard');

    Route::get('logout', [AuthController::class,'logout'])->name('logout');
});
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Page Title -->
    <title>Customer Register</title>

    <!-- TailwindCSS CDN for modern UI styling -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<!-- Background + Center Layout -->
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <!-- Main Card Container -->
    <div class="bg-white shadow-lg rounded-lg w-full max-w-md p-8">

        <!-- Page Heading -->
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">
            Customer Register
        </h2>

        <!-- SUCCESS MESSAGE (After registration success) -->
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- VALIDATION ERRORS (If form inputs are invalid) -->
        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <!-- ===========================
             REGISTRATION FORM
        ===============================-->
        <form action="{{ route('customer.register') }}" method="POST" class="space-y-5">

            <!-- CSRF Token for Form Security -->
            @csrf

            <!-- Name Input Field -->
            <div>
                <label class="block text-gray-700 mb-1" for="name">Name</label>

                <!-- User name input box -->
                <input type="text" name="name" id="name" placeholder="Enter your name"
                    class="w-full border border-gray-300 rounded px-4 py-2
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <!-- Email Input Field -->
            <div>
                <label class="block text-gray-700 mb-1" for="email">Email</label>

                <!-- User email input box -->
                <input type="email" name="email" id="email" placeholder="Enter your email"
                    class="w-full border border-gray-300 rounded px-4 py-2
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <!-- Password Input Field -->
            <div>
                <label class="block text-gray-700 mb-1" for="password">Password</label>

                <!-- User password input box -->
                <input type="password" name="password" id="password" placeholder="Enter your password"
                    class="w-full border border-gray-300 rounded px-4 py-2
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <!-- Confirm Password Field -->
            <div>
                <label class="block text-gray-700 mb-1" for="password_confirmation">Confirm Password</label>

                <!-- Re-enter password for validation -->
                <input type="password" name="password_confirmation" id="password_confirmation"
                    placeholder="Confirm your password"
                    class="w-full border border-gray-300 rounded px-4 py-2
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4
                       rounded transition duration-200">
                Register
            </button>
        </form>


        <!-- Link to Login Page -->
        <div class="text-center mt-5">
            <p class="text-gray-600">
                Already have an account? 
                <a href="{{ route('customer.login') }}" class="text-blue-600 hover:underline">
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
