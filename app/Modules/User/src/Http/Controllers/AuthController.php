<?php

namespace Imdgr886\User\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Imdgr886\User\Models\User;

class AuthController extends Controller
{
    /**
     * 手机号登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function viaMobile(Request $request)
    {
        $request->validate([
            'mobile' => ['required', 'phone:CN'],
            'verify_code' => ['required', 'verify_code'],
        ]);
        // 手机号一键注册登录
        $user = User::query()->firstOrCreate([
            'mobile' => $request->get('mobile')
        ], [
            'name' => $request->get('mobile'),
        ]);
        // 如果是新注册的用户，触发注册事件
        if ($user->wasRecentlyCreated) {
            event(new Registered($user));
        }

        $token = auth('api')->login($user);

        return $this->respondWithToken($token);
    }

    /**
     * 邮箱登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function viaEmail(Request $request)
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json([
                    'message' => '邮箱或密码错误',
                    'errors' => [
                        'password' => '邮箱或密码错误'
                    ]
                ]);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

}
