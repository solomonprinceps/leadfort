<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;

class AdminsController extends Controller
{
    public function createAdmin(Request $request) {
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
