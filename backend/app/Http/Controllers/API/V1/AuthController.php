<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\API\V1\LoginRequest;
use App\Http\Controllers\API\V1\Controller;
use App\Http\Resources\API\V1\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthController extends Controller
{

    // Temporary code for development purposes
    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function login(LoginRequest $request)
    {
        // dd($request->only('email', 'password'));
        // có thể dùng cách này
        // if (!auth()->attempt($request->only('email', 'password'))) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Invalid login details'
        //     ], 401);
        // }

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => [
                    'status' => '401',
                    'title' => 'Unauthorized',
                    'detail' => 'Invalid login details'
                ]
            ], 401);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        $user->setAttribute('token', $token);

        return new UserResource($user);
    }
}
