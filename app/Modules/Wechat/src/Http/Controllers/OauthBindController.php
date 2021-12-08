<?php

namespace Imdgr886\Wechat\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Imdgr886\User\Models\Oauth;
use Imdgr886\User\Models\User;

class OauthBindController extends Controller
{
    public function bind(Request $request)
    {
        $request->validate([
            'mobile' => ['required', 'phone:CN'],
            'verify_code' => ['required', 'verify_code'],
            'openid' => ['required', 'string']
        ]);
        $wechatUser = app('wechat.official_account')->user->get($request->get('openid'));
        // 是否有账号
        $user = User::query()->firstOrCreate([
            'mobile' => $request->get('mobile')
        ], [
            'name' => $wechatUser['nickname'],
            'avatar' =>$wechatUser['headimgurl'],
        ]);
        // 如果是新注册的用户，触发注册事件
        if ($user->wasRecentlyCreated) {
            event(new Registered($user));
        } else {
//            $user->update([
//                'name' => $wechatUser['nickname'],
//                'avatar' =>$wechatUser['headimgurl'],
//            ]);
        }

        // 绑定关系
        $user->oauth()->saveMany([
            new Oauth([
                'type' => $request->get('oauth_type'),
                'openid' => $request->get('openid'),
                'unionid' => $request->get('unionid'),
                'platform' => Oauth::WECHAT_MP,
                'raw_data' => $wechatUser
            ])
        ]);

        $token = auth('api')->login($user);

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'user' => $user,
        ]);
    }
}
