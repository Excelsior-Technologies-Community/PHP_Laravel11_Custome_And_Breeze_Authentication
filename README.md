# Laravel 11 Authentication

## ⭐ Project Overview

This project demonstrates how to implement **authentication in Laravel 11** for both **Admin** and **Customer** users:

- **Admin Authentication**
  - Implemented using **Laravel Breeze**
  - Uses default `users` table
  - Provides login and registration routes

- **Customer Authentication**
  - Custom login and registration system
  - Uses a separate `customers` table
  - Each customer has:
    - `name`, `email`, `password`
    - `status` (active/inactive)
    - `created_by`, `updated_by`
    - Soft deletes support

This tutorial is beginner-friendly and follows a **step-by-step structure** with proper explanations, routes, models, and controllers.

---

## 🔹 Step 1: Install Laravel 11

```bash
composer create-project laravel/laravel:^11.0 laravel11-authentication
cd laravel11-authentication
🔹 Step 2: Configure Database
Open .env file and update database credentials:

env
Copy code
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel11_auth
DB_USERNAME=root
DB_PASSWORD=

🔹 Step 3: Create Customers Table Migration
bash
Copy code
php artisan make:migration create_customers_table --create=customers
Add fields: name, email, password, status, created_by, updated_by, timestamps, softDeletes.

🔹 Step 4: Run Migrations
bash
Copy code
php artisan migrate
This will create both users and customers tables.

🔹 Step 5: Create Customer Model
bash
Copy code
php artisan make:model Customer
Extend Authenticatable

Include Notifiable and SoftDeletes

Define fillable, hidden, and dates properties

🔹 Step 6: Configure Auth Guards
Edit config/auth.php:

Add a new guard for customers:

php
Copy code
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
Add a provider for customers:

php
Copy code
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

🔹 Step 7: Create Customer Authentication Controller
bash
Copy code
php artisan make:controller Customer/AuthController
Handles:

Customer Registration

Customer Login

Dashboard Access

Logout

Uses the customer guard

🔹 Step 8: Add Customer Routes
Edit routes/web.php:

Prefix routes with /customer

Group routes for:

Registration (GET / POST)

Login (GET / POST)

Dashboard (protected with auth:customer)

Logout

🔹 Step 9: Create Views
Place Blade files under:

swift
Copy code
resources/views/customer/auth/
register.blade.php

login.blade.php

dashboard.blade.php

TailwindCSS used for modern UI styling

🔹 Step 10: Install Laravel Breeze for Admin Authentication
bash
Copy code
composer require laravel/breeze --dev
php artisan breeze:install
npm install
npm run build
php artisan migrate
Admin can login/register at /login and /register

Uses default users table

🔹 Step 11: Full File Structure
swift
Copy code
laravel11-authentication/
│
├── app/Models/Customer.php
├── app/Http/Controllers/Customer/AuthController.php
├── routes/web.php
├── resources/views/customer/auth/
│       ├── register.blade.php
│       ├── login.blade.php
│       └── dashboard.blade.php
├── resources/views/auth/        // Breeze admin views
├── database/migrations/
│       ├── 2014_…_create_users_table.php  // admin
│       └── 2025_…_create_customers_table.php  // customer
└── .env
🔹 Step 12: Useful Artisan Commands
bash
Copy code
# Run migrations
php artisan migrate

# Rollback last migration batch
php artisan migrate:rollback

# Create new controller
php artisan make:controller ControllerName

# Create new model
php artisan make:model ModelName

# Serve the project
php artisan serve
🎉 Conclusion
Admin Authentication via Laravel Breeze (users table)

Customer Authentication via custom system (customers table)

Soft delete and separate guards for customers

TailwindCSS-based modern UI for customer pages

This project provides a solid foundation for multi-auth systems in Laravel 11.
