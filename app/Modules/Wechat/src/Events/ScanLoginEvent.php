<?php

namespace Imdgr886\Wechat\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Imdgr886\User\Models\User;

class ScanLoginEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $key;

    /**
     * @param User $user 用户
     * @param $key string 扫码的 EventKey 场景值
     */
    public function __construct(User $user, $key)
    {
        $this->user = $user;
        $this->key = $key;
    }

    public function broadcastWith()
    {
        return [
            'access_token' => auth('api')->login($this->user),
            'user' => $this->user,
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
