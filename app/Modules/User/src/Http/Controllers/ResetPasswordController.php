<?php

namespace Imdgr886\User\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Passwords\PasswordResetServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Imdgr886\User\Models\User;

class ResetPasswordController extends Controller
{
    public function resetViaMobile(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed'],
            'mobile' => ['required', 'phone:CN']
        ]);
        $user = User::where(['mobile' => $request->get('mobile')])->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '手机号未注册'
            ]);
        }
        $user->password = Hash::make($request->get('password'));
        $user->save();
        return response()->json([
            'success' => true,
            'message' => '密码重置成功'
        ]);
    }
    /**
     * 发送密码重置链接
     */
//    public function sendLink(Request $request)
//    {
//        $request->validate([
//            'email' => ['required', 'email'],
//        ]);
//
//        // We will send the password reset link to this user. Once we have attempted
//        // to send the link, we will examine the response then see the message we
//        // need to show to the user. Finally, we'll send out a proper response.
//        $status = Password::sendResetLink(
//            $request->only('email')
//        );
//
//        return response()->json([
//            'success' => $status == Password::RESET_LINK_SENT,
//            'message' => __($status)
//        ]);
//
//    }
//
//    public function create(Request $request)
//    {
//        return view('auth.reset-password', ['request' => $request]);
//    }
//
//    public function store(Request $request)
//    {
//        $request->validate([
//            'token' => ['required'],
//            'email' => ['required', 'email'],
//            'password' => ['required', 'confirmed'],
//        ]);
//
//        // Here we will attempt to reset the user's password. If it is successful we
//        // will update the password on an actual user model and persist it to the
//        // database. Otherwise we will parse the error and return the response.
//        $status = Password::reset(
//            $request->only('email', 'password', 'password_confirmation', 'token'),
//            function ($user) use ($request) {
//                $user->forceFill([
//                    'password' => Hash::make($request->password),
//                    'remember_token' => Str::random(60),
//                ])->save();
//
//                event(new PasswordReset($user));
//            }
//        );
//
//        // If the password was successfully reset, we will redirect the user back to
//        // the application's home authenticated view. If there is an error we can
//        // redirect them back to where they came from with their error message.
//        return $status == Password::PASSWORD_RESET
//            ? redirect()->route('login')->with('status', __($status))
//            : back()->withInput($request->only('email'))
//                ->withErrors(['email' => __($status)]);
//    }


}
