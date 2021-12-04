<?php

namespace Imdgr886\User\Http\Controllers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\UnauthorizedException;
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

        return $this->respondWithToken($token, $user);
    }

    /**
     * 邮箱登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function viaEmail(Request $request)
    {
        $credentials = request(['email', 'password']);
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json([
                    'message' => '邮箱或密码错误',
                    'errors' => [
                        'password' => '邮箱或密码错误'
                    ]
                ]);
        }

        return $this->respondWithToken($token, auth('api')->user());
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
        try {
            return $this->respondWithToken(auth('api')->refresh(), auth('api')->user());
        } catch (\Exception $e) {
            throw new AuthenticationException();
        }

    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'user' => $user
        ]);
    }

}
