<?php

namespace Imdgr886\User\Http\Controllers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
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
     * 账号密码登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function viaPassword(Request $request)
    {
        $credentials = request(['password']);
        $emailValidator = Validator::make($request->all(), [
            'account' => ['required', 'email']
        ]);
        $mobileValidator = Validator::make($request->all(), [
            'account' => ['phone:CN']
        ]);

        if (!$mobileValidator->fails()) {
            $credentials['mobile'] = $request->get('account');
        } elseif (!$emailValidator->fails()) {
            $credentials['email'] = $request->get('account');
        } else {
            throw ValidationException::withMessages(['account' => '请输入有效的邮箱或手机号']);
        }

        $request->validate([
            'account' => ['required'],
            'password' => ['required', Password::default()],
        ]);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json([
                    'message' => '账号或密码错误',
                    'errors' => [
                        'password' => '账号或密码错误'
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
