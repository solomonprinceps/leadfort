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
        $uploadImages = [];
        $uploadDocuments = [];
        $images = json_decode($request->images);
        $documents = json_decode($request->documents);
        foreach ($images as $value) {
            $uploadImages = $value;
        }
        foreach ($documents as $value) {
            $uploadDocuments = $value;
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
