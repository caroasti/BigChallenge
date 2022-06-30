<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $arguments = $request->validated();
        $user = User::where('email', $arguments['email'])->first();
        if (!$user || !Hash::check($arguments['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'User logged succesfully',
            'id' => $user->id,
            'name' =>$user->name,
            'email'=>$user->email,
            'token' => $user->createToken('token')->plainTextToken,
        ]);
    }
}
