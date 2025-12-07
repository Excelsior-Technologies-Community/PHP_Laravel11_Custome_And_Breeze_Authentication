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
