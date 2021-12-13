<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Sms\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Imdgr886\Sms\Facades\Sms;

class VerifyController extends Controller
{
    public function __construct()
    {
        $middleware = config('modules.sms.middleware', []);
        if ($middleware) {
            $this->middleware($middleware);
        }
    }

    public function send(Request $request)
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
    }

    protected function sendViaMobile()
    {
        $mobile = request()->get('mobile');

        if (!Sms::checkInterval($mobile)) {
            return response()->json([
                'success' => false,
                'message' => "发送验证码太频繁，请稍候再试。",
            ]);
        }

        if (Sms::sendVerify($mobile, '')) {
            return response()->json([
                'success' => true,
                'sent_at' => time(),
                'message' => '发送成功'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => '发送失败'
            ]);
        }
    }

    protected function sendViaEmail()
    {

    }
}
