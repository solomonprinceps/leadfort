<?php

namespace App\Models;

use App\Models\InsuranceCompany;
use App\Models\Policy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Insurance;

class AttachPolicy extends Model
{
    use HasFactory;
    protected $table = "attach_policies";
    protected $fillable = [
        "policy_id",
        "company_id",
        "rate",
        "amount",
        "policy_document",
        "description",
    ];

    public function company() {
        return $this->hasOne(InsuranceCompany::class, "company_id", "company_id");
    }

    public function policy() {
        return $this->belongsTo(Policy::class, "policy_id", "policy_id");
    }

    public function attachpolicy() {
        return $this->belongsTo(Insurance::class, "attach_policies_id", "attach_policies_id");
    }
}
