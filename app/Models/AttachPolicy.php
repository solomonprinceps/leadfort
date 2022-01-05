<?php

namespace App\Models;

use App\Models\InsuranceCompany;
use App\Models\Policy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttachPolicy extends Model
{
    use HasFactory;
    protected $table = "attach_policies";
    protected $fillable = [
        "policy_id",
        "company_id",
        "rate",
        "policy_document",
        "description",
    ];

    public function company() {
        return $this->hasOne(InsuranceCompany::class, "company_id", "company_id");
    }

    public function policy() {
        return $this->hasOne(Policy::class, "policy_id", "policy_id");
    }
}
