<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // nuxt側のadminページに万が一入られた場合でも、managerの作成はadminしかできないようにする。
        $authority = auth()->user()->authority;
        if ($authority === 'admin') {
            User::create([
                "uuid" => (string) Str::uuid(),
                "name" => $request->name,
                "email" => $request->email,
                'email_verified_at' => date('Y-m-d H:i:s'),
                "password" => Hash::make($request->password),
                "authority" => "manager"
            ]);
            return response()->json(['message' => 'Manager Created Successfully']);
        } else {
            return response()->json([
                'message' => '不正なアクセスを検知しました'
            ], 403);
        }
    }


    public function show($uuid)
    {
        $user = User::with([
            'reservations.restaurant:uuid,name',
            'reservations.evaluation',
            'favorites.restaurant:uuid,name,area,genre,img_path',
            ])->where('uuid', $uuid)->first();
        return response()->json(compact('user'), 200);
    }
}
