<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
class CustomersController extends Controller
{
    public function reset(Request $request) {
        $request->validate([
            "token" => "required|string",
            "password" => "required|string|confirmed",
        ]);
        $selectToken = DB::select("SELECT * FROM `password_resets` WHERE `token` = '$request->token' ORDER BY `password_resets`.`created_at` DESC");
        // $passReset =  $selectToken[0]->created_at;
        if ($selectToken == null) {
            return response([
                "message" => "Password Token is invalid.",
                "status" => "error"
            ],400);
        }
        $passReset =  $selectToken[0]->created_at;
        $now = time();
        $difftime = $now - strtotime($selectToken[0]->created_at);
        if ($difftime > 120) {
            return response([
                "message" => "Token Expired.",
                "status" => "error"
            ],400);
        }

        $customer = Customer::where("email", $selectToken[0]->email)->first();
        if ($customer == null) {
            return response([
                "message" => "Customer doesn't exist.",
                "status" => "error"
            ],400);
        }
        $customer->update([
            "password" => bcrypt($request->password),
            "remember_token" => null
        ]);
        $customer->save();
        return response([
            "message" => "Password reset successfully.",
            "error" => "success"
        ], 200);

    }

    public function uploadImage(Request $request) {
        $id = Auth::id();
        $request->validate([
            "image" => "required|string"
        ]);
        $customer = Customer::where("id", $id)->first();
        if ($customer == null) {
            return response([
                "message" => "Customer doesn't exist.",
                "status" => "error"
            ],400);
        }
        $customer->update([
            "image" => $request->image
        ]);
        $customer->save();
        return response([
            "message" => "Customer profile image added.",
            "status" => "error",
            "customer" => $customer
        ], 200);
    }

    public function sendResetLinkEmail(Request $request) {
        $request->validate([
            "email" => "required|email",
        ]);
        $selectToken = DB::select("SELECT * FROM `password_resets` WHERE `email` = '$request->email' ORDER BY `password_resets`.`created_at` DESC");
        $selectToken[0]->created_at;
        $now = time();
        $difftime = $now - strtotime($selectToken[0]->created_at);
        if ($difftime < 120) {
            return response([
                "message" => "Reset Password link already sent try again in 3 minutes.",
                "status" => "error"
            ],400);
        }
        $customer = Customer::where("email", $request->email)->first();
        if ($customer == null) {
            return response([
                "message" => "Customer Email doesn't exist on our system.",
                "status" => "error"
            ],400);
        }
        $token = bcrypt(rand(100000,999999));
        $customer->update([
            "remember_token" => $token,
        ]);
        $customer->save();
        $passwordReset = DB::select("INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES ('$request->email', '".$token."', '".NOW()."');");
        $selectToken = DB::select("SELECT * FROM `password_resets` WHERE `email` = '$request->email' ORDER BY `password_resets`.`created_at` DESC");
        // return $selectToken[0];
        Mail::to($request->email)->send(new ForgotPassword($token));
        return response([
            "message" => "Reset email link has been sent to your email successfully.",
            "error" => "success"
        ], 200);
    }


    public function getData(){
        $id = Auth::id();
        $customer = Customer::where("id", $id)->first();
        if ($customer == null) {
            return response([
                "message" => "Customer does'nt exist.",
                "status" => "error"
            ], 400);
        }
        $user = Auth::user();
        return response([
            "customer" => $customer,
            "status" => "success",
            "message" => "Customer Fetcehd Successfully."
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
        
        $admin = Customer::where("id", $id)->first();
        // return $admin
        if ($admin == null) {
            return response([
                "message" => "Admin does'nt exist.",
                "status" => "error"
            ], 400);
        }
        $checkemail = Customer::where("email",$request->email)->where("id", "!=", $id)->first();
        if ($checkemail) {
            return response([
                "message" => "Email belongs to another customer.",
                "status" => "error"
            ], 400);
        }

        $checkphone = Customer::where("email",$request->phone_number)->where("id", "!=", $id)->first();
        if ($checkphone) {
            return response([
                "message" => "Phone Number belongs to another customer.",
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
            "message" => "Customer profile edited successfully.",
            "status" => "success",
            "admin" => $admin
        ], 200);

    }


    public function logout() {
        $id = Auth::id();
        $customer = Customer::where("id", $id)->first();
        if ($customer == null) {
            return response([
                "message" => "Customer does'nt exist.",
                "status" => "error"
            ], 400);
        }
        Auth::user()->tokens()->delete();
        return response([
            "status" => "success",
            "message" => "Customer logout successful.",
        ], 200);
    }


    public function login(Request $request) {
        
        $request->validate([
            "email" => "required|email",
            "password" => "required|string"
        ]);
        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response([
                "message" => "The provided credentials are incorrect.",
                "status" => "error"
            ], 400);
        }
        return response([
            'customer' => $customer,
            "status" => "success",
            "message" => "Login Successful.", 
            'token' => $customer->createToken('webapp', ['role:customer'])->plainTextToken
        ]);

    }


    public function redirectToProvider()
    {

        $uerUrl = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        return response([
            "message" => "Google Auth Url",
            "status" => "success",
            "url" => $uerUrl
        ], 200);
    }


    public function handleProviderCallback()
    {
        
        try {
            $user = Socialite::driver('google')->stateless()->user();
            Log::info($user->user["email"]);
            $customer = Customer::where("email", $user->user["email"])->first();
            if ($customer == null) {
                $newcustomer = Customer::create([
                    "firstname" => $user->user["given_name"],
                    "lastname" => $user->user["family_name"],
                    "email" => $user->user["email"],
                    "google_token" => $user->token,
                    "google_token" => $user->id,
                    "authId" => 'AUTHID'.date('YmdHis').rand(100, 999),
                    "image" => $user->user["picture"]
                ]);
                return response([
                    'customer' => $newcustomer,
                    "status" => "success",
                    "message" => "Registration Successful.", 
                    'token' => $newcustomer->createToken('webapp', ['role:customer'])->plainTextToken
                ], 200);
            } else {
                
                $customer->update([
                    "google_token" => $user->token,
                    "google_token" => $user->id,
                ]);
                $customer->save();
                return response([
                    'customer' => $customer,
                    "status" => "success",
                    "message" => "Login Successful.", 
                    'token' => $customer->createToken('webapp', ['role:customer'])->plainTextToken
                ], 200);
            }
            // return $user;
        } catch (ClientException $exception) {
            return response([
                'message' => 'Invalid credentials provided.',
                "status" => "error"
            ], 422);
        }
        // return 123423;
        // return Socialite::customer('google')->stateless()->user();
        // $user = Socialite::with('google')->stateless()->user();
        // Log::info(json_encode($user));
        
        // return $user;

        // $checkcustomer = Customer::where("email", $user->getEmail())->first();
        // if ($checkcustomer != null) {
        //     $userCreated = Customer::firstOrCreate(
        //         [
        //             'email' => $user->getEmail()
        //         ],
        //         [
        //             'email_verified_at' => now(),
        //             'firstname' => $user->getName(),
        //             "authId" => 'AUTHID'.date('YmdHis').rand(100, 999),
        //             // 'status' => true,
        //         ]
        //     );
        // }
        
        // $userCreated->providers()->updateOrCreate(
        //     [
        //         'provider' => "google",
        //         'provider_id' => $user->getId(),
        //     ],
        //     [
        //         'avatar' => $user->getAvatar()
        //     ]
        // );
        // return response([
        //     'customer' => $userCreated,
        //     "status" => "success",
        //     "message" => "Login Successful.", 
        //     'token' => $userCreated->createToken('webapp', ['role:customer'])->plainTextToken
        // ]);
    }

    
    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['facebook', 'github', 'google'])) {
            return response()->json(['error' => 'Please login using facebook, github or google'], 422);
        }
    }

    public function register(Request $request) {
        $request->validate([
            "firstname" => "required|string",
            "lastname" => "required|string",
            "email" => "required|email|unique:customers",
            "phone_number" => "required|string|unique:customers,phone_number",
            "password" => "required|string|confirmed"
        ]);
        $customer = Customer::create([
            "firstname" => $request->firstname,
            "lastname" => $request->lastname,
            "email" => $request->email,
            "authId" => 'AUTHID'.date('YmdHis').rand(100, 999),
            "phone_number" => $request->phone_number,
            "password" => bcrypt($request->password)
        ]);
        return response([
            "message" => "Customer Created Successfully.",
            "status" => "success",
            "customer" => $customer
        ], 200);
    }

    
}
