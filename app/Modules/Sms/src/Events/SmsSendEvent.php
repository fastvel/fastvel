<?php

namespace Imdgr886\Sms\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SmsSendEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $scenes;
    protected $mobile;
    protected $gateway;
    protected $content;
    protected $success;

    public function __construct(string $scenes, string $mobile, string $gateway, string $content, bool $success)
    {
        $this->scenes = $scenes;
        $this->mobile = $mobile;
        $this->gateway = $gateway;
        $this->content = $content;
        $this->success = $success;
    }
}
