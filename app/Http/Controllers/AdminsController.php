<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class AdminsController extends Controller
{
    public function createAdmin(Request $request) {
        $id = Auth::id();
        $admin = Admin::where("id", $id)->first();
        // return $admin;
        if ($admin == null) {
            return response([
                "message" => "Admin does'nt exist.",
                "status" => "error"
            ], 400);
        }
        if ($admin->admin_type == "slave") {
            return response([
                "message" => "You can't create an admin account.",
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

    public function getData() {
        $id = Auth::id();
        $admin = Admin::where("id", $id)->first();
        // return $admin
        if ($admin == null) {
            return response([
                "message" => "Admin does'nt exist.",
                "status" => "error"
            ], 400);
        }
        return response([
            "message" => "Admin data fetched.",
            "status" => "error",
            "admin" => $admin
        ], 200);
    }

    public function addOremove(Request $request) {
        $id = Auth::id();
        $request->validate([
            "transaction_pin" => "required|string"
        ]);
        $admin = Admin::where("id", $id)->first();
        if ($admin == null) {
            return response([
                "message" => "Admin does'nt exist.",
                "status" => "error"
            ], 400);
        }
        $admin->update([
            "transaction_pin" => $request->transaction_pin,
        ]);
        $admin->save();
        return response([
            "message" => "Admin transaction pin added successfully.",
            "status" => "success",
            "admin" => $admin
        ], 200);

    }

    public function editprofile(Request $request) {
        $id = Auth::id();
        $request->validate([
            "firstname" => "required|string",
            "lastname" => "required|string",
            "email" => "required|email",
            "phone_number" => "required|string"
        ]);
        
        $admin = Admin::where("id", $id)->first();
        // return $admin
        if ($admin == null) {
            return response([
                "message" => "Admin does'nt exist.",
                "status" => "error"
            ], 400);
        }
        $checkemail = Admin::where("email",$request->email)->where("id", "!=", $id)->first();
        if ($checkemail) {
            return response([
                "message" => "Email belongs to another admin.",
                "status" => "error"
            ], 400);
        }

        $checkphone = Admin::where("email",$request->phone_number)->where("id", "!=", $id)->first();
        if ($checkphone) {
            return response([
                "message" => "Phone Number belongs to another admin.",
                "status" => "error"
            ], 400);
        }
        $admin->update([
            "firstname" => $request->firstname,
            "lastname" => $request->lastname,
            "email" => $request->email,
            "phone_number" => $request->phone_number,
        ]);
        $admin->save();
        return response([
            "message" => "Admin profile edited successfully.",
            "status" => "success",
            "admin" => $admin
        ], 200);

    }

    public function uploadImage(Request $request) {
        $request->validate([
            "image" => "required|string"
        ]);
        $id = Auth::id();
        $admin = Admin::where("id", $id)->first();
        // return $admin
        if ($admin == null) {
            return response([
                "message" => "Admin does'nt exist.",
                "status" => "error"
            ], 400);
        }
        $admin->update([
            "image" => $request->image
        ]);
        $admin->save();

        return response([
            "message" => "Admin Image Uploaded successfully.",
            "status" => "success",
            "admin" => $admin
        ], 200);

    }
    public function logout() {
        $id = Auth::id();
        $customer = Admin::where("id", $id)->first();
        if ($customer == null) {
            return response([
                "message" => "Customer does'nt exist.",
                "status" => "error"
            ], 400);
        }
        Auth::user()->tokens()->delete();
        return response([
            "status" => "success",
            "message" => "Admin logout successful.",
        ], 200);
    }
    public function login(Request $request) {
        $request->validate([
            "email" => "required|email",
            "password" => "required|string"
        ]);
        $customer = Admin::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response([
                "message" => "The provided credentials are incorrect.",
                "status" => "error"
            ], 400);
        }
        return response([
            'admin' => $customer,
            "status" => "success",
            "message" => "Login Successful.", 
            'token' => $customer->createToken('webapp', ['role:admin'])->plainTextToken
        ]);
    }
}
