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
