<?php

namespace Imdgr886\Wechat\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Imdgr886\User\Models\Oauth;
use Imdgr886\User\Models\User;

/**
 * 扫码登录
 */
class UnauthBindEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $openid;

    public $key;

    /**
     * @param $openId string 用户 的 opendi
     * @param $key string 扫码的 EventKey 场景值
     */
    public function __construct($openid, $key)
    {
        $this->openid = $openid;
        $this->key = $key;
    }

    public function broadcastWith()
    {
        return [
            'openid' => $this->openid,
            'oauth_type' => Oauth::WECHAT_MP,
        ];
    }

    public function broadcastOn()
    {
        return new Channel('mp_qrcode.scan.login.' . $this->key);
    }

    public function broadcastAs()
    {
        return 'scanned';
    }
}
