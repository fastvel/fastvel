<?php

namespace Imdgr886\User\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Imdgr886\User\Models\User;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'string', 'min:6'],
            'terms' => ['required', 'accepted']
        ]);

        $user = User::create([
            'name' => $request->get('name'),
            'email' =>$request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        event(new Registered($user));

        return response()->json([
            'status' => 'success',
            'access_token' => auth('api')->login($user),
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
