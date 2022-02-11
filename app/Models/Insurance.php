<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AttachPolicy;
use App\Models\Claim;
class Insurance extends Model
{
    use HasFactory;
    protected $table = "insurances";
    protected $fillable = [
        "status",
        "policy_id",
        "attach_policies_id",
        "value_of_assets",
        "insurance_id",
        "customer_id",
        "state",
        "lga",
        "description",
    ];
    public function attachpolicy() {
        return $this->hasMany(AttachPolicy::class, "attach_policies_id", "id");
    }

    public function policy() {
        return $this->hasOne(Policy::class, "policy_id", "policy_id");
    }

    
    public function claim() {
        return $this->hasMany(Claim::class, "insurance_id", "insurance_id");
    }

    public function payments()
    {
        return $this->hasMany(Payments::class);
    }


}
