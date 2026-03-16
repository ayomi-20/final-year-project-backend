<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
    // REGISTER — just save the user, no OTP yet
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email:rfc,dns|unique:users,email',
            'contact'    => 'required|digits:10|unique:users,contact',
            'password'   => 'required|min:6',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'contact'    => $request->contact,
            'password'   => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Registration successful! Please log in.',
        ], 201);
    }

    // LOGIN — validate credentials, then send OTP
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $user = Auth::user();

        // Generate 6-digit OTP and store it on the user
        $otp = strval(random_int(100000, 999999));

        $user->login_otp         = $otp;
        $user->login_otp_created_at = now();
        $user->save();

        // Send OTP email
        Mail::send('emails.verify', [
            'name' => $user->first_name,
            'otp'  => $otp,
        ], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Your login verification code');
        });

        return response()->json([
            'message' => 'Credentials verified. Please check your email for your login code.',
            'email'   => $user->email,
        ]);
    }

    // VERIFY OTP — confirms identity and returns token
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        if ($user->login_otp !== $request->otp) {
            return response()->json([
                'message' => 'Invalid OTP. Please try again.',
            ], 401);
        }

        if (Carbon::parse($user->login_otp_created_at)->addMinutes(10)->isPast()) {
            $user->login_otp            = null;
            $user->login_otp_created_at = null;
            $user->save();

            return response()->json([
                'message' => 'OTP expired. Please log in again.',
            ], 410);
        }

        // Clear OTP after successful use
        $user->login_otp            = null;
        $user->login_otp_created_at = null;
        $user->save();

        $token = $user->createToken(
            'flutter-token',
            ['*'],
            Carbon::now()->addDays(30)
        )->plainTextToken;

        return response()->json([
            'message' => 'Login successful!',
            'user'    => $user,
            'token'   => $token,
        ]);
    }

    // FORGOT PASSWORD — send OTP to email
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email'   => 'required|email',
            'contact' => 'required',
        ]);

        $user = User::where('email', $request->email)
                    ->where('contact', $request->contact)
                    ->first();

        if (!$user) {
            return response()->json([
                'message' => 'No account found with those details.',
            ], 404);
        }

        $otp = strval(random_int(100000, 999999));

        $user->login_otp            = $otp;
        $user->login_otp_created_at = now();
        $user->save();

        // ✅ uses emails.reset now
        Mail::send('emails.reset', [
            'name' => $user->first_name,
            'otp'  => $otp,
        ], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Your password reset code');
        });

        return response()->json([
            'message' => 'Reset code sent! Please check your email.',
        ]);
    }

// VERIFY RESET CODE — confirm email + contact + OTP
    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email'   => 'required|email',
            'contact' => 'required',
            'otp'     => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)
                    ->where('contact', $request->contact)
                    ->where('login_otp', $request->otp)
                    ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid code or details. Please try again.',
            ], 401);
        }

        if (Carbon::parse($user->login_otp_created_at)->addMinutes(10)->isPast()) {
            $user->login_otp            = null;
            $user->login_otp_created_at = null;
            $user->save();

            return response()->json([
                'message' => 'Code expired. Please request a new one.',
            ], 410);
        }

        return response()->json([
            'message' => 'Code verified! Please set your new password.',
        ]);
    }

    // RESET PASSWORD — save new password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'contact'  => 'required',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $request->email)
                    ->where('contact', $request->contact)
                    ->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        $user->password             = Hash::make($request->password);
        $user->login_otp            = null;
        $user->login_otp_created_at = null;
        $user->save();

        return response()->json([
            'message' => 'Password reset successful! You can now log in.',
        ]);
    }

        // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}