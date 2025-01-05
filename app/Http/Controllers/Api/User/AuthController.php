<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Mail\UserMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users,email',
                'name' => 'required|string|min:1|max:20',
                'password' => 'required|string|confirmed|min:6|max:20'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->all(),
                ]);
            }

            $code = rand(10000, 99999);
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'code' => $code,
                'password' => Hash::make($request->password)
            ]);

            $details = [
                'subject' => 'Email Verification',
                'content' => 'Your email verification code is. ' . $code,
            ];
            \Mail::to($request->email)->send(new UserMail($details));

            return response()->json([
                'success' => true,
                'message' => 'Check mail for email confirmation'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }


    public function emailVerification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'code' => 'required|integer|exists:users,code|min:5',
            ]);


            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->all(),
                ]);
            }

            $user = User::whereCodeAndEmail($request->code, $request->email)->first();

            if (time() > strtotime($user->updated_at) + 60) {
                $user->code = 0;
                $user->save();
                return response()->json([
                    'success' => false,
                    'message' => 'code has been expired , click on resend button',
                ]);
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Selected code',
                ]);
            }



            $user->email_verified_at = now();
            $user->is_verified = 1;
            $user->save();

            return response()->json([
                'success' => false,
                'message' => 'Email verified successfully, now you can login',
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function login(Request $request)
    {
        try {

            $key = 'login-attempts:' . $request->ip(); // You can also use user email if you prefer

            $maxAttempts = 5; // Maximum allowed attempts
            $decaySeconds = 60; // Block time in seconds after exceeding attempts

            // Check if the user has exceeded the limit
            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $seconds = RateLimiter::availableIn($key);
                return response()->json([
                    'message' => "Too many login attempts. Please try again in {$seconds} seconds.",
                    'remaining_attempts' => 0,
                ], 429);
            }

            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->all(),
                ]);
            }

            $user = User::whereEmail($request->email)->first();

            RateLimiter::hit($key, $decaySeconds);

            // Calculate remaining attempts
            $remainingAttempts = $maxAttempts - RateLimiter::attempts($key);

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email or password', // Use "or" for clarity
                    'remaining_attempts' => $remainingAttempts,

                ]);
            }

            if ($user->is_verified == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is not verified', // Use "or" for clarity
                ]);
            }

            
            if ($user->status == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is banned', // Use "or" for clarity
                ]);
            }


            $user->tokens()->delete();
            $token = $user->createToken('Api Token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login Successfully',
                'token' => $token,
                'user' => $user
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function forgetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->all(),
                ]);
            }

            $user = User::whereEmail($request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ]);
            }

            $code = rand(10000, 99999);
            $user->code = $code;
            $user->save();

            $details = [
                'subject' => 'Forget password',
                'content' => 'Your forget password verification code is. ' . $code,
            ];
            \Mail::to($request->email)->send(new UserMail($details));

            return response()->json([
                'success' => true,
                'message' => 'Email verification send successfully',
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }


    public function forgetPasswordVerification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'code' => 'required|integer|exists:users,code|min:5',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->all(),
                ]);
            }

            $user = User::whereCodeAndEmail($request->code, $request->email)->first();

            if (time() > strtotime($user->updated_at) + 60) {
                $user->code = 0;
                $user->save();
                return response()->json([
                    'success' => false,
                    'message' => 'code has been expired , click on resend button',
                ]);
            }
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Selected code',
                ]);
            }

            $token = $user->createToken('Api Token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully, reset your password',
                'token' => $token
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|string|confirmed|min:6|max:20'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->all(),
                ]);
            }

            $user = User::find(auth()->id());
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ]);
            }

            $user->password = Hash::make($request->password);
            $user->save();



            return response()->json([
                'success' => true,
                'message' => 'Password change successfully',
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }


    public function resendEmail(Request $request)
    {
        try {

            $key = 'resend-email:' . $request->ip(); // Or $request->user()->id for logged-in users

            // Check if the user is allowed to resend
            if (RateLimiter::tooManyAttempts($key, 3)) {
                return response()->json([
                    'message' => 'Too many attempts. Please try after 24hr.'
                ], 429);
            }

            // Record an attempt
            RateLimiter::hit($key, 1440 * 60); // 1440 minutes = 24 hours

            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->all(),
                ]);
            }

            $user = User::whereEmail($request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ]);
            }
            $code = rand(10000, 99999);
            $user->code = $code;
            $user->save();

            $details = [
                'subject' => 'Email Verification',
                'content' => 'Your email verification code is. ' . $code,
            ];
            \Mail::to($request->email)->send(new UserMail($details));

            return response()->json([
                'success' => true,
                'message' => 'Email verification send successfully',
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }
}
