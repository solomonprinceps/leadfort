<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Customer;
use Faker\Provider\ar_EG\Payment;

class PaymentsController extends Controller
{
    public function paymentlist(Request $request) {
        $request->validate([ 
            "page_number" => "required|string",
            "status" => "nullable|string"
        ]);
        $id = Auth::id();
        $customer = Admin::where("id", $id)->first();
        if ($customer == null) {
            return response([
                "message" => "Admin does'nt exist.",
                "status" => "error"
            ], 400);
        }
        if($request->status == null) {
            $payments = Payments::orderBy('id', 'DESC')->paginate($request->page_number);
            return response([
                "message" => "Payments fetched successfully.",
                "status" => "success",
                "payments" => $payments
            ], 200);
        }
        $payments = Payments::where("status",$request->status)->orderBy('id', 'DESC')->paginate($request->page_number);
        return response([
            "message" => "Payments fetched successfully.",
            "status" => "success",
            "payments" => $payments
        ], 200);
    }


    public function singlepayment($payment) {
        // paymentlistuser
        $id = Auth::id();
        $customer = Customer::where("id", $id)->first();
        if ($customer == null) {
            return response([
                "message" => "Customer does'nt exist.",
                "status" => "error"
            ], 400);
        }
        $payment = Payments::where("reference", $payment)->where("customer_id", $customer->authId)->first();
        if ($payment != null) {
            return response([
                "message" => "Payment fetched successfully.",
                "status" => "success",
                "payment" => $payment
            ], 200);
        }
        return response([
            "message" => "Payment doesn't exist .",
            "status" => "error",
            "payment" => $payment
        ], 400);
    }


    public function paymentlistuser(Request $request) {
        $request->validate([ 
            "page_number" => "required|string",
            "status" => "nullable|string"
        ]);
        $id = Auth::id();
        $customer = Customer::where("id", $id)->first();
        if ($customer == null) {
            return response([
                "message" => "Customer does'nt exist.",
                "status" => "error"
            ], 400);
        }
        if($request->status == null) {
            $payments = Payments::where("customer_id", $customer->authId)->orderBy('id', 'DESC')->paginate($request->page_number);
            foreach ($payments as $value) {
                $pol = $value->insurance->policy->policy_name;
                $attach = $value->insurance->policy->attachpolicy[0]->company;
                $value->policyname = $pol;
                $value->insurer = $attach->company_name;
            }
            return response([
                "message" => "Payments fetched successfully.",
                "status" => "success",
                "payments" => $payments
            ], 200);
        }
        $payments = Payments::where("customer_id", $customer->authId)->where("status",$request->status)->orderBy('id', 'DESC')->paginate($request->page_number);
        foreach ($payments as $value) {
            $pol = $value->insurance->policy->policy_name;
            $attach = $value->insurance->policy->attachpolicy[0]->company;
            $value->policyname = $pol;
            $value->insurer = $attach->company_name;
        }
        return response([
            "message" => "Payments fetched successfully.",
            "status" => "success",
            "payments" => $payments
        ], 200);
    }
}
