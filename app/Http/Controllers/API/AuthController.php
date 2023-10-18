<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'forgotPassword', 'resetPassword']]);
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'string|required|email',
                'password' => 'string|required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $credentials = $request->only('email', 'password');

            $token = Auth::attempt($credentials);

            if (!$token) {
                return response()->json([
                    "status" => false,
                    "message" => "Invalid Credentials"
                ], 401);
            }

            return response()->json([
                "status" => true,
                "message" => "Login Success",
                "data" => [
                    'access_token' => $token,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "status" => false,
                "message" => 'Login Failed'
            ], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|min:3',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed:password_confirmation',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'uniq_id' => generateUuid(),
            ]);
            DB::commit();

            return response()->json([
                "status" => true,
                'message' => 'User created successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                "status" => false,
                "message" => 'Registration failed'
            ], 500);
        }
    }

    public function me()
    {
        try {
            $user = Auth::user();

            return response()->json([
                "status" => true,
                'message' => 'Success',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "status" => false,
                "message" => 'Failed'
            ], 500);
        }
    }

    public function logout()
    {
        try {
            Auth::logout();
            return response()->json([
                "status" => true,
                'message' => 'Successfully logged out',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "status" => false,
                "message" => 'Failed'
            ], 500);
        }
    }

    public function refresh()
    {
        try {
            $token = Auth::refresh();
            return response()->json([
                "status" => true,
                "message" => "Refresh Token Success",
                "data" => [
                    'access_token' => $token,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "status" => false,
                "message" => 'Refresh Token Failed'
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $token = generateRandomToken();

            DB::beginTransaction();
            $user->update([
                'reset_password_token' => $token,
            ]);
            DB::commit();

            $subject = "Reset Password Request";
            $content = [
                'url' => env('FRONTEND_URL') . '/reset-password?token=' . $token,
            ];

            Mail::to($user->email)->send(new SendEmail($subject, 'emails.reset-password', $content));

            return response()->json([
                'status' => true,
                'message' => 'Email sent successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Email sent failed',
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|string|min:6|confirmed:password_confirmation',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $token = $request->header('x-reset-password-token');

            $user = User::where('reset_password_token', $token)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ], 404);
            }

            DB::beginTransaction();
            $user->update([
                'password' => Hash::make($request->password),
                'reset_password_token' => null,
            ]);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Password reset successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Password reset failed',
            ], 500);
        }
    }
}
