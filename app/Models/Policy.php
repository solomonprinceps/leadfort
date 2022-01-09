<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AttachPolicy;
class Policy extends Model
{
    use HasFactory;
    protected $table = "policies";
    protected $fillable = ['policy_name','policy_id'];

    public function attachpolicy() {
        return $this->hasMany(AttachPolicy::class, "policy_id", "policy_id");
    }
}
