<?php

namespace Imdgr886\Wechat\Http\MessageHandlers\Events;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Imdgr886\User\Models\Oauth;
use Imdgr886\User\Models\User;
use Imdgr886\Wechat\Events\ScanLoginEvent;

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
        if (Str::startsWith($eventKey, 'scan.login:')) {
            $scene = str_replace('scan.login:', '', $eventKey);
            $userinfo = app('wechat.official_account')->user->get($data['FromUserName']);
            $unionid = @$userinfo['unionid'];
            if ($unionid && ($oauth = Oauth::where(['unionid' => $unionid])->first() )) {
                event(new ScanLoginEvent($oauth->user, $scene));
                return 'unionid';
            }

            $oauth = Oauth::query()->where(['openid' => $data['FromUserName'], 'platform' => Oauth::WECHAT_MP])->first();

            if (!$oauth) {
                $user = User::create([
                    'name' => $userinfo['nickname'],
                    'avatar' =>$userinfo['headimgurl'],
                ]);
                $user->oauth()->save(new Oauth([
                    'user_id' => $user->id,
                    'openid' => $data['FromUserName'],
                    'platform' => Oauth::WECHAT_MP,
                    'unionid' => $unionid ?: null,
                ]));
            } else {
                $user = $oauth->user;
            }

            event(new ScanLoginEvent($user, $scene));
            return '扫码登录成功';
        }
        return $eventKey;

    }


}
