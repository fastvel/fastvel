<?php

namespace Imdgr886\Wechat\Http\MessageHandlers;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use Imdgr886\Wechat\Http\MessageHandlers\Events\Scan;

class EventHandler implements EventHandlerInterface
{

    public function handle($payload = null)
    {
        switch (strtolower($payload['Event'])) {
            case 'subscribe':
                if (!empty($payload['EventKey'])) {
                    return (new Scan())->handle($payload);
                }
                return $this->subscribe();
            case 'unsubscribe':
                return $this->unsubscribe();
            case 'scan':
                return (new Scan())->handle($payload);
            default:
                return "你瞅啥";
        }
    }

    public function subscribe()
    {

    }

    /**
     * 取消关注
     * @return void
     */
    protected function unsubscribe()
    {

    }
}
