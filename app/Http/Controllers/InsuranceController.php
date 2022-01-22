<?php

namespace App\Http\Controllers;

use App\Models\AttachPolicy;
use App\Models\Customer;
use App\Models\Insurance;
use App\Models\Policy;
use Illuminate\Http\Request;
use App\Models\Payments;
use Illuminate\Support\Facades\Auth;

class InsuranceController extends Controller
{
    public function getAttachment($id) {
        $auth = Auth::id();
        $customer = Customer::where("id", $auth)->first();
        if ($customer == null) {
            return response([
                "message" => "Customer does'nt exist.",
                "status" => "error"
            ], 400);
        }

        $insurnace = Insurance::where("id", $id)->where("customer_id", $customer->authId)->first();
        if ($insurnace == null) {
            return response([
                "message" => "Insurance doesn't exist.",
                "status" => "error"
            ], 400);
        }
        $availablepolicys = Policy::where("policy_id", $insurnace->policy_id)->first();
        $attacted = $availablepolicys->attachpolicy;
        foreach ($attacted as $attact) {
            $attact->company;
        }
        $availablepolicys->attachpolicy = $attacted;
        return response([
            "message" => "Insurance doesn't exist.",
            "status" => "success",
            "insurance" => $insurnace,
            "policy" => $availablepolicys,
        ], 200);
    }

    public function createStepone(Request $request) {
        $id = Auth::id();
        $request->validate([
            "policy_id" => "required|string",
            // "attach_policies_id" => "required|string",
            "value_of_assets" => "required|string",
            "state" => "required|string",
            "lga" => "required|string",
            "description" => "required|string"
        ]);
        $customer = Customer::where("id", $id)->first();
        if ($customer == null) {
            return response([
                "message" => "Customer does'nt exist.",
                "status" => "error"
            ], 400);
        }
        $policy = Policy::where("policy_id", $request->policy_id)->first();
        if ($policy == null) {
            return response([
                "message" => "Policy does'nt exist.",
                "status" => "error",
                "policy" => null
            ],400);
        }
        // $attachment = AttachPolicy::where("id", $request->attach_policies_id)->first();
        // if ($attachment == null) {
        //     return response([
        //         "message" => "Attachment of policy does'nt exist.",
        //         "status" => "error",
        //         "policy" => null
        //     ],400);
        // }
        $insurnace_id = "INS".date('YmdHis').rand(10000, 99999);
        $newInsurance = Insurance::create([
            "policy_id" => $request->policy_id,
            // "attach_policies_id" => $request->attach_policies_id,
            "value_of_assets" => $request->value_of_assets,
            "insurance_id" => $insurnace_id,
            "customer_id" => $customer->authId,
            "state" => $request->state,
            "lga" => $request->lga,
            "description" => $request->description
        ]);
        return response([
            "message" => "Insurance attached successfully.",
            "status" => "success",
            "insurance" => $newInsurance
        ],200);
    }

    public function createSteptwo(Request $request) {
        $request->validate([
            "attach_policies_id" => "required|string",
            "insurance_id" => "required|string"
        ]);
        $id = Auth::id();
        $customer = Customer::where("id", $id)->first();
        if ($customer == null) {
            return response([
                "message" => "Customer does'nt exist.",
                "status" => "error"
            ], 400);
        }

        $insurance = Insurance::where("insurance_id", $request->insurance_id)->first();
        if ($insurance == null) {
            return response([
                "message" => "Insurance does'nt exist.",
                "status" => "error",
                "insurance" =>  null
            ], 400);
        }
        if ($insurance->status != 0) {
            return response([
                "message" => "Insurance is already awaiting payments.",
                "status" => "error",
                "insurance" =>  null
            ], 400);
        }
        $attachments = AttachPolicy::where("id", $request->attach_policies_id)->where("policy_id", $insurance->policy_id)->first();
        if ($insurance == null) {
            return response([
                "message" => "Insurance Company does'nt exist.",
                "status" => "error",
                "insurance" =>  null
            ], 400);
        }
        $insurance->update([
            "attach_policies_id" => $request->attach_policies_id,
            "status" => '1'
        ]);
        $insurance->save();
        $ref = "REF".date('YmdHis').rand(10000, 99999).rand(10000, 99999);
        $payments = Payments::create([
            "amount" => $attachments->amount,
            "customer_id" => $customer->authId,
            "insurance_id" => $insurance->insurance_id,
            "reference" => $ref
        ]);
        return response([
            "message" => "Insurance awaitng payments.",
            "status" => "success",
            "insurance" =>  $insurance,
            "payments" => $payments,
            "reference" => $ref
        ], 200);
    }
}
