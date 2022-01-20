<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceCompany extends Model
{
    use HasFactory;
    protected $table = "insurance_companies";
    protected $fillable = [
        "company_name",
        "company_id",
        "adminId",
        "image",
        "customer_number"
    ];
}
