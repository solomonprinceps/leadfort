<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;
    protected $table = "payments";
    protected $fillable = [
        "status",
        "amount",
        "customer_id",
        "insurance_id",
        "reference",
    ];
}
