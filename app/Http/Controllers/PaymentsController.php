<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

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
}
