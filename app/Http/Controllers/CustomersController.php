<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CustomersController extends Controller
{
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
                "message" => "he provided credentials are incorrect.",
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
        // $validated = $this->validateProvider($provider);
        // if (!is_null($validated)) {
        //     return $validated;
        // }

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

    /**
     * @param $provider
     * @return JsonResponse
     */
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
            "email" => "required|email|unique:customers,email",
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
