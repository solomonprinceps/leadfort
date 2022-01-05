<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class PolicyController extends Controller
{
    public function createpolicy(Request $request) {
       
        $request->validate([
            "policy_name" => "required|string",
            "transaction_pin" => "required|string"
        ]);

        $checkname = Policy::where("policy_name", $request->policy_name)->first();
        if ($checkname != null) {
            return response([
                "message" => "Policy name already exist.",
                "status" => "error"
            ], 400);
        } 


        $id = Auth::id();
        $admin = Admin::where("id", $id)->first();
        if ($admin == null) {
            return response([
                "message" => "Admin does'nt exist.",
                "status" => "error"
            ], 400);
        }
        if ($admin->transaction_pin != $request->transaction_pin) {
            return response([
                "message" => "Admin transaction pin not correct.",
                "status" => "error"
            ], 400);
        }

        $policy = Policy::create([
            "policy_name" => $request->policy_name,
            "policy_id" => 'PL'.date('YmdHis').rand(10000, 99999),
        ]);

        return response([
            "status" => "success",
            "policy" => $policy,
            "message" => "Policy Created Successfully"
        ], 200);
    }

    public function editpolicy(Request $request) {
        $request->validate([
            "policy_name" => "required|string",
            "policy_id" => "required|string",
            "transaction_pin" => "required|string",
        ]);
        $id = Auth::id();
        $checkname = Policy::where("policy_name", $request->policy_name)->where("policy_id", "!=", $request->policy_id)->first();
        if ($checkname) {
            return response([
                "message" => "Policy Name already exist.",
                "status" => "error"
            ], 400);
        } 
        $admin = Admin::where("id", $id)->first();
        if ($admin == null) {
            return response([
                "message" => "Admin does'nt exist.",
                "status" => "error"
            ], 400);
        }
        if ($admin->transaction_pin != $request->transaction_pin) {
            return response([
                "message" => "Admin transaction pin not correct.",
                "status" => "error"
            ], 400);
        }

        $policy = Policy::where("policy_id", $request->policy_id)->first();
        $policy->update([
            "policy_name" => $request->policy_name
        ]);
        return response([
            "message" => "Policy Updated Successfully.",
            "status" => "success",
            "policy" => $policy
        ], 200);

    }

    public function listPolicies(Request $request) {
        $request->validate([
            "page_number" => "required|integer"
        ]);

        $policy = Policy::paginate($request->page_number);
        if ($policy->isEmpty()) {
            return response([
                "message" => "No Insurance policy available",
                "status" => "error",
                "policy" => null
            ], 200);
        }
        return response([
            "message" => "Insurance policy available",
            "status" => "success",
            "policy" => $policy
        ], 200);
    }

}
