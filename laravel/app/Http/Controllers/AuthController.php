<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerification;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'register']]);
    }

    public function register(Request $request)
    {
        $user = User::create([
            "uuid" => (string) Str::uuid(),
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "authority" => "user"
        ]);

        return response()->json(['message' => 'User Created Successfully']);
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'message' => '誤り'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Logged Out Successfully']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function admin()
    {
        $authority = auth()->user()->authority;
        if ($authority === 'admin') {
            return response()->json(compact('authority'), 200);
        } elseif ($authority === 'manager') {
            return response()->json(compact('authority'), 200);
        } else {
            return response()->json([
                'message' => 'No Content'
            ], 204);
        }
    }

    public function sendEmailVerification()
    {
        $user = auth()->user();
        User::where('id', $user->id)->update([
            'salt_for_email' => Str::random(10),
            'salt_expiration' => Carbon::now()->addHour()
        ]);
        $user = User::find($user->id); //user情報が変わったので再取得
        Mail::to($user->email)->send(new EmailVerification($user));
        return response()->json(null, 200);
    }

    public function verifyEmail(Request $request)
    {
        $user = auth()->user();
        if ($user->email_verified_at) {
            return response()->json(['message' => 'already verified'], 203);
        }

        if (Carbon::now() > $user->salt_expiration) {
            return response()->json(['message' => 'salt expired'], 204);
        }
        $check = Hash::check($user->email.$user->salt_for_email, base64_decode($request->hashedEmail));
        if ($check) {
            User::where('id', $user->id)->update([
                'email_verified_at' => Carbon::now(),
                'salt_for_email' => null,
                'salt_expiration' => null
            ]);
            $user = User::find($user->id);
            return response()->json(compact('user'), 200);
        } else {
            return response()->json(null, 401);
        }
    }
}
