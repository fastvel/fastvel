<?php

namespace Imdgr886\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Imdgr886\Sms\Facades\Sms;

class ConfirmableMobileController extends Controller
{
    public function sendVerifyCode(Request $request)
    {
        if (!auth('api')->user()->mobile) {
            return response()->json([
                'success' => false,
                'message' => '您还未绑定手机号'
            ]);
        }
        $success = Sms::sendVerify(auth('api')->user()->mobile, $request->get('scenes'));
        return response()->json([
            'success' => $success,
            'message' => $success ? '验证码发送成功': '验证码发送失败'
        ]);
    }

    public function handle(Request $request)
    {
        $data = $request->only('verify_code');
        $data['mobile'] = auth('api')->user()->mobile;
        $validator = Validator::make($data, [
            'verify_code' => ['required', 'verify_code']
        ]);
        $validator->validate();
        // 10分钟有效
        Cache::put(
            'mobile.confirmation',
            ['time' => time(), 'mobile' => $data['mobile']],
            now()->addMinutes(10)
        );
        return response()->json([
            'success' => true,
            'message' => '手机号验证通过'
        ]);
    }
}
