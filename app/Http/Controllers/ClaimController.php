<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Insurance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClaimController extends Controller
{
    public function createclaim(Request $request) {
        $request->validate([
            "insurance_id" => "required|string",
            "circumstance" => "required|string",
            "description" => "required|string",
            "images" => 'required',
            'images.*' => 'required|mimes:jpeg,jpg,png,gif|max:2048',
            "documents" => 'required',
            'documents.*' => 'mimes:jpeg,jpg,png,gif,csv,txt,pdf|max:2048',
        ]);
        $insurance = Insurance::where("insurance_id", $request->insurance_id)->first();
        if ($insurance == null) {
            return response([
                "message" => "Insurance not available",
                "status" => "error"
            ], 200);
        }
        $uploadImages = [];
        $uploadDocuments = [];
        if($request->hasfile('images')) {
            foreach($request->file('images') as $file)
            {
                $filename = 'claimImage-'. rand(10000,99999) . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->move(public_path('claimImage'), $filename);
                $documentlink = asset('claimImage/'.$filename); 
                $uploadImages[]=$documentlink;
            }
        }

        if($request->hasfile('documents')) {
            foreach($request->file('documents') as $file)
            {
                $filename = 'claimDocument-'. rand(10000,99999) . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->move(public_path('claimDocument'), $filename);
                $documentlink = asset('claimDocument/'.$filename); 
                $uploadDocuments[]=$documentlink;
            }
        }
        $claim = "CLM".date('YmdHis').rand(10000, 99999).rand(10000, 99999).rand(10000, 99999);
        Claim::create([
            "claim_id" => $claim,
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
}
