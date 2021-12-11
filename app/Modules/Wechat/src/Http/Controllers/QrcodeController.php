<?php

namespace Imdgr886\Wechat\Http\Controllers;

use Illuminate\Routing\Controller;
use Imdgr886\Snowflake\Facades\Snowflake;

class QrcodeController extends Controller
{
    /**
     * 扫描二维码关注公众号并登录
     * @return void
     */
    public function login()
    {
        $app = app('wechat.official_account');
        $key = Snowflake::id();
        // 24小时有效的二维码
        $result = $app->qrcode->temporary("login:{$key}", 24 * 3600);
        $result['success'] = true;
        $result['key'] = $key;
        $result['qr_url'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='. $result['ticket'];
        return response()->json($result);
    }

    /**
     * 扫码绑定微信
     * @return \Illuminate\Http\JsonResponse
     */
    public function bind()
    {
        $app = app('wechat.official_account');
        $key = Snowflake::id();
        // 24小时有效的二维码
        $result = $app->qrcode->temporary("bind:{$key}", 24 * 3600);
        $result['success'] = true;
        $result['key'] = $key;
        $result['qr_url'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='. $result['ticket'];
        return response()->json($result);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function teamInvitation()
    {
        $app = app('wechat.official_account');
        $key = Snowflake::id();
        // 24小时有效的二维码
        $result = $app->qrcode->temporary("team_invitation:{$key}", 24 * 3600);
        $result['success'] = true;
        $result['qr_url'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='. $result['ticket'];
        return response()->json($result);
    }
}
