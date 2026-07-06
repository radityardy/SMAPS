<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $role = $request->input('role', 'patient');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $role,
        ]);

        if ($role === 'doctor') {
            $specialization = $request->input('specialization', 'Umum');
            $prefix = $request->input('queue_prefix');
            
            if (!$prefix) {
                $words = explode(' ', preg_replace('/[^a-zA-Z\s]/', '', $request->name));
                $prefix = '';
                foreach ($words as $w) {
                    if (!empty($w)) {
                        $prefix .= strtoupper($w[0]);
                    }
                }
                $prefix = substr(trim($prefix), 0, 3);
                if (empty($prefix)) {
                    $prefix = 'D';
                }
                $originalPrefix = $prefix;
                $counter = 1;
                while (\App\Models\Doctor::where('queue_prefix', $prefix)->exists()) {
                    $prefix = $originalPrefix . $counter;
                    $counter++;
                }
            }

            \App\Models\Doctor::create([
                'user_id' => $user->id,
                'specialization' => $specialization,
                'queue_prefix' => $prefix,
                'is_active' => true,
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => new UserResource($user),
            'token' => $token,
            'access_token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($user),
            'token' => $token,
            'access_token' => $token,
        ]);
    }

    public function me(): JsonResponse
    {
        return response()->json([
            'user' => new UserResource(auth()->user()),
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
