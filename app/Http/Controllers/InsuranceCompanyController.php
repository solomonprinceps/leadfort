<?php

namespace App\Http\Controllers;

use App\Models\InsuranceCompany;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class InsuranceCompanyController extends Controller
{
   public function createInsurance(Request $request) {
        $request->validate([
            "company_name" => "required|string",
            "transaction_pin" => "required|string",
        ]);
        $id = Auth::id();
        $checkname = InsuranceCompany::where("company_name", $request->company_name)->first();
        if ($checkname != null) {
            return response([
                "message" => "Company Name already exist.",
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
        $insurance = InsuranceCompany::create([
            "company_name" => $request->company_name,
            "company_id" => 'IC'.date('YmdHis').rand(10000, 99999),
            "adminId" => $admin->adminId
        ]);
        return response([
            "message" => "Insurance Company Created Successfully.",
            "status" => "success",
            "insurance" => $insurance
        ], 200);

   }

   public function editcompany(Request $request) {
        $request->validate([
            "company_name" => "required|string",
            "company_id" => "required|string",
            "transaction_pin" => "required|string",
        ]);
        $id = Auth::id();
        $checkname = InsuranceCompany::where("company_name", $request->company_name)->where("company_id", "!=", $request->company_id)->first();
        if ($checkname) {
            return response([
                "message" => "Company Name already exist.",
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
        // $insurance = InsuranceCompany::create([
        //     "company_name" => $request->company_name,
        //     "company_id" => 'IC'.date('YmdHis').rand(10000, 99999),
        //     "adminId" => $admin->adminId
        // ]);
        $insurance = InsuranceCompany::where("company_id", $request->company_id)->first();
        $insurance->update([
            "company_name" => $request->company_name
        ]);
        return response([
            "message" => "Insurance Company Updated Successfully.",
            "status" => "success",
            "insurance" => $insurance
        ], 200);

   }

   public function deletecompany(Request $request) {
        $request->validate([
            "company_id" => "required|string",
            "transaction_pin" => "required|string",
        ]);
        $id = Auth::id();
        $checkname = InsuranceCompany::where("company_name", $request->company_name)->where("company_id", "!=", $request->company_id)->first();
        if ($checkname) {
            return response([
                "message" => "Company Name already exist.",
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
        // $insurance = InsuranceCompany::create([
        //     "company_name" => $request->company_name,
        //     "company_id" => 'IC'.date('YmdHis').rand(10000, 99999),
        //     "adminId" => $admin->adminId
        // ]);
        $insurance = InsuranceCompany::where("company_id", $request->company_id)->first();
        if ($insurance == null) {
            return response([
                "message" => "Insurance Company Does'nt Exist.",
                "status" => "error"
            ], 400);
        }
        $insurance->delete();
        return response([
            "message" => "Insurance Company Deleted Successfully.",
            "status" => "success",
            "insurance" => $insurance
        ], 200);

    }

   public function getcompany($insurance) {
    //    return $insurance;
       $singleinsurance = InsuranceCompany::where("company_id", $insurance)->first();
       if ($singleinsurance == null) {
           return response([
               "message" => "Insurance Company Not Available.",
               "status" => "error"
           ], 400);
       }
       return response([
           "message" => "Fetch Successfully",
           "status" => "success",
           "insurance" => $singleinsurance
       ], 200);
   }

    public function listcompany(Request $request) {
        $request->validate([
            "page_number" => "required|integer"
        ]);

        $insurances = InsuranceCompany::paginate($request->page_number);
        if ($insurances->isEmpty()) {
            return response([
                "message" => "No Insurance company available",
                "status" => "error",
                "insurances" => null
            ], 200);
        }
        return response([
            "message" => "Insurance company available",
            "status" => "success",
            "insurances" => $insurances
        ], 200);
    }
}
