<?php

namespace Imdgr886\Wechat\Http\MessageHandlers\Events;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Imdgr886\User\Models\Oauth;
use Imdgr886\User\Models\User;
use Imdgr886\Wechat\Events\ScanLoginEvent;
use Imdgr886\Wechat\Events\UnauthBindEvent;

/**
 * 微信扫码事件处理
 */
class Scan
{

    public function handle($data)
    {
        // 二维码场景 KEY
        $eventKey = str_replace('qrscene_', '', $data['EventKey']);
        // 是否是扫码新关注的用户
        // $newSubscribe = $data['Event'] == 'subscribe';

        // 扫码登录
        if (Str::startsWith($eventKey, 'login:')) {
            $scene = str_replace('login:', '', $eventKey);
            $userinfo = app('wechat.official_account')->user->get($data['FromUserName']);
            $unionid = @$userinfo['unionid'];
            if ($unionid) {
                $oauth = Oauth::where(['unionid' => $unionid])->first();
            } else {
                $oauth = Oauth::query()->where(['openid' => $data['FromUserName'], 'platform' => Oauth::WECHAT_MP])->first();
            }

            if ($oauth) {
                $user = $oauth->user;
                event(new ScanLoginEvent($user, $scene));
                return '扫码登录成功';
            } else {
                // 还没有绑定账号，需要绑定
                event(new UnauthBindEvent($data['FromUserName'], $scene));
                return "扫码成功，请绑定账号";
            }

        }
        return null;

    }


}
