<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;
    protected $table = "claims";
    protected $fillable = [
        "status",
        "claim_id",
        "customer_id",
        "insurance_id",
        "circumstance",
        "description",
        "images",
        "documents",
    ];

    public function insurance() {
        return $this->hasOne(Insurance::class, "insurance_id", "insurance_id");
    }
}
