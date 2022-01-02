<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable , HasApiTokens;


    protected $table = "admins";
    protected $fillable = [
        "firstname",
        "lastname",
        "adminId",
        "transaction_pin",
        "email",
        "image",
        "admin_type",
        "phone_number",
        "password"
    ];

    protected $hidden = [
        'password',
    ];

    // /**
    //  * The attributes that should be cast.
    //  *
    //  * @var array<string, string>
    //  */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

}
