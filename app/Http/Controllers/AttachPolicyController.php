<?php

namespace App\Http\Controllers;

use App\Models\AttachPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\InsuranceCompany;
use App\Models\Policy;

class AttachPolicyController extends Controller
{
    public function create_attachemnt(Request $request) {
        $request->validate([
            'image' => 'required|mimes:png,jpg,jpeg,pdf|max:2048',
            "policy_id" => "required|string",
            "company_id" => "required|string",
            "rate" => "required|string",
            "amount" => "required|string",
            "description" => "required|string",
            "transaction_pin" => "required|string",
        ]);
        $attachmentcheck = AttachPolicy::where("company_id", $request->company_id)->where("policy_id", $request->policy_id)->first();
        if ($attachmentcheck != null) {
            return response([
                "message" => "The Policy is already attached.",
                "status" => "error"
            ],400);
        
        }
        $insurance_company = InsuranceCompany::where("company_id", $request->company_id)->first();
        if ($insurance_company == null) {
            return response([
                "message" => "Insurance company doesn't exist.",
                "status" => "error"
            ],400);
        }
        $policy = Policy::where("policy_id",$request->policy_id)->first(); 

        if ($policy == null) {
            return response([
                "message" => "Policy doesn't exist.",
                "status" => "error"
            ],400);
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
        // return $request->image;
        if($file = $request->file('image')) {
            $filename = 'policydocument-'. rand(10000,99999) . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('policydocument'), $filename);
            $documentlink = asset('policydocument/'.$filename);
            
            $newattachment = AttachPolicy::create([
                "policy_id" => $request->policy_id,
                "company_id" => $request->company_id,
                "rate" => $request->rate,
                "amount" => $request->amount,
                "policy_document" => $documentlink,
                "description" => $request->description,
            ]);
            $newattachment->company;
            $newattachment->policy;  
            return response()->json([
                "status" => 'success',
                "customer" => $newattachment,
                "message" => "Policy Attached Successfully.",
            ], 200);
        }
    }


    public function edit_attachemnt(Request $request) {
        $request->validate([
            'image' => 'mimes:png,jpg,jpeg,pdf|max:2048',
            "rate" => "required|string",
            "attachment_id" => "required|string",
            "amount" => "required|string",
            "description" => "required|string",
            "transaction_pin" => "required|string",
        ]);
        $newattachment = AttachPolicy::where("id", $request->attachment_id)->first();
        if ($newattachment == null) {
            return response([
                "message" => "Attachment doesn't exist.",
                "status" => "error"
            ],400);
        }
        $attachmentcheck = AttachPolicy::where("company_id", $request->company_id)->where("policy_id", $request->policy_id)->first();
        if ($attachmentcheck != null) {
            return response([
                "message" => "The Policy is already attached.",
                "status" => "error"
            ],400);
        
        }
        // $insurance_company = InsuranceCompany::where("company_id", $request->company_id)->first();
        // if ($insurance_company == null) {
        //     return response([
        //         "message" => "Insurance company doesn't exist.",
        //         "status" => "error"
        //     ],400);
        // }

        // $policy = Policy::where("policy_id",$request->policy_id)->first(); 

        // if ($policy == null) {
        //     return response([
        //         "message" => "Policy doesn't exist.",
        //         "status" => "error"
        //     ],400);
        // }

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
        // return $request->image;
        if($file = $request->file('image')) {
            $filename = 'policydocument-'. rand(10000,99999) . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->move(public_path('policydocument'), $filename);
            $documentlink = asset('policydocument/'.$filename);

            $newattachment->update([
                "rate" => $request->rate,
                "amount" => $request->amount,
                "policy_document" => $documentlink,
                "description" => $request->description,
            ]);
            $newattachment->save();
            $newattachment->company;
            $newattachment->policy;  
            return response()->json([
                "status" => 'success',
                "customer" => $newattachment,
                "message" => "Policy Edited Successfully.",
            ], 200);
        } else {
            $newattachment->update([
                "rate" => $request->rate,
                "amount" => $request->amount,
                "description" => $request->description,
            ]);
            $newattachment->save();
            $newattachment->company;
            $newattachment->policy;  
            return response()->json([
                "status" => 'success',
                "customer" => $newattachment,
                "message" => "Policy Edited Successfully.",
            ], 200);
        }
    }

    public function list_attachemnt(Request $request) {
        $request->validate([
            "page_number" => "required|string"
        ]);
        $listattch = AttachPolicy::paginate($request->page_number);
        if ($listattch->isEmpty()) {
            return response()->json([
                "message" => "No data available.",
                "error" => "error",
                "data" => null
            ], 400);
        }
        foreach ($listattch as $value) {
            $value->company;
            $value->policy;
        }
        return response()->json([
            "message" => "Data fetched",
            "error" => "error",
            "data" => $listattch
        ], 200);
    }
}
