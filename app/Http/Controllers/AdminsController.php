<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminsController extends Controller
{
    public function createAdmin(Request $request) {
        $id = Auth::id();
        $admin = Admin::where("id", $id)->first();
        if ($admin == null) {
            return response([
                "message" => "Admin does'nt exist.",
                "status" => "error"
            ], 400);
        }
        if ($admin->admin_type == "slave") {
            return response([
                "message" => "Admin does'nt exist.",
                "status" => "error"
            ], 400);
        }
        $request->validate([
            "firstname" => "required|string",
            "lastname" => "required|string",
            "email" => "required|email|unique:admins",
            "phone_number" => "required|string|unique:admins,phone_number",
            "password" => "required|string|confirmed"
        ]);
        $admins = Admin::create([
            "firstname" => $request->firstname,
            "lastname" => $request->lastname,
            "email" => $request->email,
            "admin_type" => "master",
            "adminId" => "ADMINID".date('YmdHis').rand(10000, 99999),
            "phone_number" => $request->phone_number,
            "password" => bcrypt($request->password)
        ]);
        return response([
            "message" => "Admin Created Successfully",
            "status" => "success",
            "admin" => $admins
        ], 200);
    }
}
