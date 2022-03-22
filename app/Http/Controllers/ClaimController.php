<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Insurance;
use App\Models\Customer;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ClaimController extends Controller
{
    public function createclaim(Request $request) {
        $id = Auth::id();
        $request->validate([
            "insurance_id" => "required|string",
            "circumstance" => "required|string",
            "description" => "required|string",
            "images" => 'required|string',
            "documents" => 'required|string',
        ]);
        $insurance = Insurance::where("insurance_id", $request->insurance_id)->first();
        if ($insurance == null) {
            return response([
                "message" => "Insurance not available",
                "status" => "error"
            ], 200);
        }
        // if ($insurance->status != 2) {
        //     return response([
        //         "message" => "You can't make claim for unpaid policy",
        //         "status" => "error"
        //     ], 200);
        // }
        $customer = Customer::where("id", $id)->first();
        if ($customer == null) {
            return response([
                "message" => "Customer does'nt exist.",
                "status" => "error"
            ], 400);
        }
        $uploadImages = [];
        $uploadDocuments = [];
        $images = json_decode($request->images);
        $documents = json_decode($request->documents);
        if ($images != null) {
            foreach ($images as $value) {
                array_push($uploadImages, $value);
            }
        }
        
        if ($documents != null) {
            foreach ($documents as $value) {
                array_push($uploadDocuments, $value);
            }
        }
        
        
        $claim = "CLM".date('YmdHis').rand(10000, 99999).rand(10000, 99999).rand(10000, 99999);
        Claim::create([
            "claim_id" => $claim,
            "customer_id" => $customer->authId,
            "insurance_id" => $request->insurance_id,
            "circumstance" => $request->circumstance,
            "description" => $request->description,
            "images" => json_encode($uploadImages),
            "documents" => json_encode($uploadDocuments)
        ]);
        $clm = Claim::where("claim_id", $claim)->first();
        $clm->insurance;
        $clm->images = json_decode($clm->images);
        $clm->documents = json_decode($clm->documents);
        return response([
            "message" => "Claim Created",
            "status" => "success",
            "claim" => $clm
        ],200);
    }

    public function getclaim($claimid) {
        $id = Auth::id();
        $customer = Customer::where("id", $id)->first();
        if ($customer == null) {
            return response([
                "message" => "Customer does'nt exist.",
                "status" => "error"
            ], 400);
        }
        $insurances = claim::where("customer_id", $customer->authId)->where("claim_id", $claimid)->first();
        if ($insurances == null) {
            return response([
                "message" => "Claim does'nt exist.",
                "status" => "error"
            ], 400);
        }
        $insurances->insurance->policy->attachpolicy;
        $insurances->images = json_decode($insurances->images);
        $insurances->documents = json_decode($insurances->documents);
        return response([
            "status" => "success",
            "message" => "Insurance Fetched Successfully.",
            "insurance" => $insurances
        ], 200);
    }

    public function claim_changestatus(Request $request) {
        $request->validate([
            "claim_id" => "required|string",
            "status" => "required|integer"
        ]);
        $id = Auth::id();
        $customer = Admin::where("id", $id)->first();
        if ($customer == null) {
            return response([
                "message" => "Admin does'nt exist.",
                "status" => "error"
            ], 400);
        }
        if ($request->status == '1' && $request->status == '-1') {
            return response([
                "message" => "wrong status.",
                "status" => "error"
            ], 400);
        }
        $claim = Claim::where("claim_id", $request->claim_id)->first();
        if ($claim == null) {
            return response([
                "message" => "Claim already exist.",
                "status" => "error"
            ], 400);
        }
        if ($claim->status == $request->status) {
            return response([
                "message" => "Claim status already changed.",
                "status" => "error"
            ], 400);
        }
        $claim->update([
            "status" => $request->status
        ]);
        $claim->save();
        return response([
            "message" => "Claim status change successfully.",
            "status" => "success"
        ], 200);

    }

    public function list_claim(Request $request) {
        $request->validate([
            "page_number" => "required|integer",
            "status" => "required|string",
            "search_text" => "nullable|string"
        ]);
        $id = Auth::id();
        $customer = Customer::where("id", $id)->first();
        if ($customer == null) {
            return response([
                "message" => "Customer does'nt exist.",
                "status" => "error"
            ], 400);
        }
        $payments = Claim::where("customer_id", $customer->authId)->where("status", $request->status)->orderBy('id', 'DESC')->paginate($request->page_number);
        foreach($payments as $payment){
            $payment->insurance->policy->attachpolicy;
            $payment->images = json_decode($payment->images);
            $payment->documents = json_decode($payment->documents);
            $pol = $payment->insurance->policy->policy_name;
            $attach = $payment->insurance->policy->attachpolicy[0]->company;
            $payment->policyname = $pol;
            $payment->insurer = $attach->company_name;
        }
        return response([
            "status" => "success",
            "message" => "Claims Fetched Successfully.",
            "insurance" => $payments
        ], 200);
        if ($request->search_text != null) {
            $payments = Claim::where("customer_id", $customer->authId)->where("claim_id", $request->search_text)->where("status", $request->status)->orderBy('id', 'DESC')->paginate($request->page_number);
            foreach($payments as $payment){
                $payment->insurance->policy->attachpolicy;
                $payment->images = json_decode($payment->images);
                $payment->documents = json_decode($payment->documents);
            }
            return response([
                "status" => "success",
                "message" => "Claims Fetched Successfully.",
                "insurance" => $payments
            ], 200);
        }
    }

    public function adminsingle_claim($claimid) {
        $id = Auth::id();
        $admin = Admin::where("id", $id)->first();
        if ($admin == null) {
            return response([
                "message" => "Admin does'nt exist.",
                "status" => "error"
            ], 400);
        }
        $claims = Claim::where("claim_id", $claimid)->first();
        if ($claims == null) {
            return response([
                "message" => "Claim does'nt exist.",
                "status" => "error"
            ], 400);
        }
        $claims->insurance->policy->attachpolicy;
        $claims->images = json_decode($claims->images);
        $claims->documents = json_decode($claims->documents);
        $claims->customer;
        
        return response([
            "message" => "Claim fetched successfully.",
            "claims" => $claims,
            "status" => "success"
        ], 200);
    }

    public function adminlist_claim(Request $request) {
        $request->validate([
            "page_number" => "required|integer",
            "id" => "nullable|string",
            "searchText" => "nullable|string"
        ]);
        $id = Auth::id();
        $admin = Admin::where("id", $id)->first();
        if ($admin == null) {
            return response([
                "message" => "Admin does'nt exist.",
                "status" => "error"
            ], 400);
        }
        if ($request->id == null) {
            if ($request->searchText != null) {
                $claims = Claim::where("claim_id", $request->searchText)->paginate($request->page_number);
                foreach($claims as $claim){
                    $claim->insurance->policy->attachpolicy;
                    $claim->images = json_decode($claim->images);
                    $claim->documents = json_decode($claim->documents);
                    $claim->customer;
                }
                return response([
                    "message" => "Claims fetched successfully.",
                    "claims" => $claims,
                    "status" => "success"
                ], 400);
            }
            $claims = Claim::paginate($request->page_number);
            foreach($claims as $claim){
                $claim->insurance->policy->attachpolicy;
                $claim->images = json_decode($claim->images);
                $claim->documents = json_decode($claim->documents);
                $claim->customer;
            }
            return response([
                "message" => "Claims fetched successfully.",
                "claims" => $claims,
                "status" => "success"
            ], 400);
        }

        if ($request->id == 0) {
            if ($request->searchText != null) {
                $claims = Claim::where("status", $request->id)->where("claim_id", $request->searchText)->paginate($request->page_number);
                foreach($claims as $claim){
                    $claim->insurance->policy->attachpolicy;
                    $claim->images = json_decode($claim->images);
                    $claim->documents = json_decode($claim->documents);
                    $claim->customer;
                }
                return response([
                    "message" => "Claims fetched successfully.",
                    "claims" => $claims,
                    "status" => "success"
                ], 400);
            }
            $claims = Claim::where("status", $request->id)->paginate($request->page_number);
            foreach($claims as $claim){
                $claim->insurance->policy->attachpolicy;
                $claim->images = json_decode($claim->images);
                $claim->documents = json_decode($claim->documents);
                $claim->customer;
            }
            return response([
                "message" => "Claims fetched successfully.",
                "claims" => $claims,
                "status" => "success"
            ], 400);
        }

        if ($request->id == 1) { // claimed
            if ($request->searchText != null) {
                $claims = Claim::where("status", $request->id)->where("claim_id", $request->searchText)->paginate($request->page_number);
                foreach($claims as $claim){
                    $claim->insurance->policy->attachpolicy;
                    $claim->images = json_decode($claim->images);
                    $claim->documents = json_decode($claim->documents);
                    $claim->customer;
                }
                return response([
                    "message" => "Claims fetched successfully.",
                    "claims" => $claims,
                    "status" => "success"
                ], 400);
            }
            $claims = Claim::where("status", $request->id)->paginate($request->page_number);
            foreach($claims as $claim){
                $claim->insurance->policy->attachpolicy;
                $claim->images = json_decode($claim->images);
                $claim->documents = json_decode($claim->documents);
                $claim->customer;
            }
            return response([
                "message" => "Claims fetched successfully.",
                "claims" => $claims,
                "status" => "success"
            ], 400);
        }
    }
}
