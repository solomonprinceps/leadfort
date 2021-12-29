<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;
    protected $table = "providers";
    protected $fillable = ['provider','provider_id','authId','avatar'];
    protected $hidden = ['created_at','updated_at'];
}
