<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'name','email','password','status','created_by','updated_by'
    ];

    protected $hidden = ['password'];
    protected $dates = ['deleted_at'];
}
