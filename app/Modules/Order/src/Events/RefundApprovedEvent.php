<?php

namespace Imdgr886\Order\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Imdgr886\Order\Models\OrderRefund;

/**
 * Class RefundApprovedEvent
 * 退款审核通过事件
 * @package App\Events
 */
class RefundApprovedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $refund;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(OrderRefund $refund)
    {
        $this->refund = $refund;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
//    public function broadcastOn()
//    {
//        return new PrivateChannel('channel-name');
//    }
}
