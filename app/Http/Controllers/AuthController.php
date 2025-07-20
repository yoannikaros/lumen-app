<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'nama' => 'required|string',
            'telepon' => 'nullable|string',
            'alamat' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P'
        ]);

        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'nama' => $request->nama,
                'telepon' => $request->telepon,
                'alamat' => $request->alamat,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin ?? 'L',
                'status' => 'aktif'
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'register',
                'table_name' => 'users',
                'record_id' => $user->id,
                'details' => 'User registered: ' . $user->username,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil didaftarkan',
                'data' => [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'nama' => $user->nama
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendaftarkan user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('username', $request->username)
                   ->where('status', 'aktif')
                   ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        // Update last login
        $user->update(['last_login' => Carbon::now()]);

        // Generate JWT token
        $payload = [
            'iss' => 'sb-farm-api',
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + (env('JWT_TTL', 60) * 60)
        ];

        $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        // Log activity
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'table_name' => 'users',
            'record_id' => $user->id,
            'details' => 'User logged in: ' . $user->username,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => env('JWT_TTL', 60) * 60,
                'user' => [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'nama' => $user->nama,
                    'roles' => $user->roles->pluck('nama')
                ]
            ]
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->auth;
        $user->load('roles.permissions');

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'nama' => $user->nama,
                'telepon' => $user->telepon,
                'alamat' => $user->alamat,
                'tanggal_lahir' => $user->tanggal_lahir,
                'jenis_kelamin' => $user->jenis_kelamin,
                'status' => $user->status,
                'last_login' => $user->last_login,
                'roles' => $user->roles->map(function($role) {
                    return [
                        'id' => $role->id,
                        'nama' => $role->nama,
                        'permissions' => $role->permissions->pluck('nama')
                    ];
                })
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->auth;

        // Log activity
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'logout',
            'table_name' => 'users',
            'record_id' => $user->id,
            'details' => 'User logged out: ' . $user->username,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }
}