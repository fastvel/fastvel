<?php

namespace Imdgr886\Wechat\Http\MessageHandlers;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;

class TextHandler implements EventHandlerInterface
{
    public function handle($payload = null)
    {
        return $payload['Content'];
    }
}
