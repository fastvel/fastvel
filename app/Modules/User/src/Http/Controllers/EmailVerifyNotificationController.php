<?php

namespace Imdgr886\User\Http\Controllers;

use Illuminate\Routing\Controller;

class EmailVerifyNotificationController extends Controller
{
    public function send()
    {
        if (auth('api')->user()->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => '邮箱已经验证过'
            ]);
        }
        if (!auth('api')->user()->email) {
            return response()->json([
                'success' => false,
                'message' => '您还未设置邮箱'
            ]);
        }
        auth('api')->user()->sendEmailVerificationNotification();
        return response()->json([
            'success' => true,
            'message' => '验证邮件已发送到邮箱'
        ]);
    }
}
