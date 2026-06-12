<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\ProviderDocument;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    // Step 1: Register provider personal + business basics
    public function register(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string',
            'business_type' => 'required|string',
            'district'      => 'required|string',
            'address'       => 'required|string',
            'description'   => 'nullable|string',
        ]);

        $user = $request->user();

        // Check if provider profile already exists
        if ($user->provider) {
            return response()->json(['message' => 'Provider profile already exists.'], 422);
        }

        $provider = Provider::create([
            'user_id'       => $user->id,
            'business_name' => $request->business_name,
            'business_type' => $request->business_type,
            'district'      => $request->district,
            'address'       => $request->address,
            'description'   => $request->description,
            'status'        => 'pending',
        ]);

        return response()->json([
            'message'  => 'Provider profile created.',
            'provider' => $provider,
        ], 201);
    }

    // Step 2: Upload documents
    public function uploadDocuments(Request $request)
{
    $request->validate([
        'documents'   => 'nullable|array',
        'documents.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
        'logo'        => 'nullable|image|max:2048',
    ]);

    $provider = $request->user()->provider;

    if (!$provider) {
        return response()->json(['message' => 'Provider profile not found.'], 404);
    }

    // Upload documents and save to both provider_documents and providers table
    if ($request->hasFile('documents')) {
        $files = $request->file('documents');
        foreach ($files as $index => $file) {
            $path = $file->store('provider-documents', 'public');
            $type = $index === 0 ? 'national_id' : 'trading_license';
            ProviderDocument::create([
                'provider_id' => $provider->id,
                'type'        => $type,
                'file_path'   => $path,
            ]);
            // Also save path directly on provider record
            $provider->update([$type => $path]);
        }
    }

    // Upload logo
    if ($request->hasFile('logo')) {
        $path = $request->file('logo')->store('provider-logos', 'public');
        $provider->update(['logo' => $path]);
    }

    return response()->json(['message' => 'Documents uploaded successfully.']);
}

    // Step 3: Submit for review
    public function submit(Request $request)
    {
        $provider = $request->user()->provider;

        if (!$provider) {
            return response()->json(['message' => 'Provider profile not found.'], 404);
        }

        $provider->update(['status' => 'pending']);

        return response()->json(['message' => 'Application submitted for review.']);
    }


    // Update application
    public function update(Request $request)
{
    $provider = $request->user()->provider;

    if (!$provider) {
        return response()->json(['message' => 'Provider profile not found.'], 404);
    }

    if ($provider->status === 'approved') {
        return response()->json(['message' => 'Approved provider profile cannot be edited.'], 403);
    }

    $request->validate([
        'business_name' => 'sometimes|string',
        'business_type' => 'sometimes|string',
        'district'      => 'sometimes|string',
        'address'       => 'sometimes|string',
        'description'   => 'nullable|string',
    ]);

    $provider->update([
        'business_name' => $request->business_name ?? $provider->business_name,
        'business_type' => $request->business_type ?? $provider->business_type,
        'district'      => $request->district ?? $provider->district,
        'address'       => $request->address ?? $provider->address,
        'description'   => $request->description ?? $provider->description,
        'status'        => 'pending',
        'rejection_reason' => null,
    ]);

    return response()->json(['message' => 'Application updated and resubmitted.', 'provider' => $provider]);
}

    // Get own provider profile
    public function profile(Request $request)
    {
        $provider = $request->user()->provider;

        if (!$provider) {
            return response()->json(['message' => 'Provider profile not found.'], 404);
        }

        return response()->json($provider->load('documents', 'services'));
    }
}