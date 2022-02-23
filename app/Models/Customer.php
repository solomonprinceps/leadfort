<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Claim;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable , HasApiTokens;
    protected $table = "customers";
    protected $fillable = [
        "firstname",
        "lastname",
        "google_token",
        "google_id",
        "email",
        "image",
        "authId",
        "remember_token",
        "phone_number",
        "password"
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // /**
    //  * The attributes that should be cast.
    //  *
    //  * @var array<string, string>
    //  */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function providers() {
        return $this->hasMany(Provider::class,'user_id','id');
    }

    public function claim() {
        return $this->hasMany(Claim::class, "customer_id", "authId");
    }
}
