<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TempUser; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email:rfc,dns|unique:users|unique:temp_users,email',
            'contact'    => 'required|digits:10|unique:users|unique:temp_users,contact',
            'password'   => 'required|min:6'
        ]);

        // Generate a unique 60-character token
        $token = Str::random(60);

        // Save temp user
        $tempUser = TempUser::create([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            'contact'           => $request->contact,
            'password'          => Hash::make($request->password),
            'verification_token'=> $token,
            'token_created_at'  => now()
        ]);

        // Verification link
        $verificationUrl = "twendeapp://verify-email?token={$token}";

        // Send email with clickable link
        Mail::send('emails.verify', [
            'name' => $tempUser->first_name,
            'verificationUrl' => $verificationUrl
        ], function ($message) use ($tempUser) {
            $message->to($tempUser->email)
                    ->subject('Please verify your email');
        });

        // Auto verify ONLY in local environment (for Postman testing)
        if (
            app()->environment('local') &&
            $request->has('auto_verify') &&
            $request->auto_verify
        ) {
            $user = User::create([
                'first_name' => $tempUser->first_name,
                'last_name'  => $tempUser->last_name,
                'email'      => $tempUser->email,
                'contact'    => $tempUser->contact,
                'password'   => $tempUser->password
            ]);

            $tempUser->delete();

            return response()->json([
                'message' => 'Registration successful! You can now log in.',
                'user'    => $user
            ]);
        }

        return response()->json([
            'message' => 'Registration in progress! Please check your email to verify your account.'
        ], 201);
    }

    // VERIFY EMAIL
    public function verifyEmail(Request $request)
{
    $request->validate([
        'token' => 'required|string'
    ]);

    $tempUser = TempUser::where('verification_token', $request->token)->first();

    if (!$tempUser) {
        return response()->json([
            'message' => 'Invalid or expired verification link.'
        ], 404);
    }

    if ($tempUser->token_created_at->addMinutes(10)->isPast()) {
        $tempUser->delete();
        return response()->json([
            'message' => 'Verification link expired. Please register again.'
        ], 410);
    }

    $user = User::create([
        'first_name' => $tempUser->first_name,
        'last_name'  => $tempUser->last_name,
        'email'      => $tempUser->email,
        'contact'    => $tempUser->contact,
        'password'   => $tempUser->password
    ]);

    $tempUser->delete();

    return response()->json([
        'message' => 'Email verified! You can now log in.',
        'user'    => $user
    ]);
}




    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();

        $expiresAt = Carbon::now()->addDays(30); // 30 days from now

        $token = $user->createToken(
            'flutter-token',
            ['*'],           // abilities, '*' = full access
            $expiresAt       // token expiration
        )->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}