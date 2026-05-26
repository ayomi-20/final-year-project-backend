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

        // Update user role to provider
        $user->update(['role' => 'provider']);

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
            'cover_photo' => 'nullable|image|max:2048',
        ]);

        $provider = $request->user()->provider;

        if (!$provider) {
            return response()->json(['message' => 'Provider profile not found.'], 404);
        }

        // Upload documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $index => $file) {
                $path = $file->store('provider-documents', 'public');
                ProviderDocument::create([
                    'provider_id' => $provider->id,
                    'type'        => 'national_id',
                    'file_path'   => $path,
                ]);
            }
        }

        // Upload logo
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('provider-logos', 'public');
            $provider->update(['logo' => $path]);
        }

        // Upload cover photo
        if ($request->hasFile('cover_photo')) {
            $path = $request->file('cover_photo')->store('provider-covers', 'public');
            $provider->update(['cover_photo' => $path]);
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