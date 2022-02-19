<?php

namespace Imdgr886\Order\Traits;

use Imdgr886\Order\Models\Order;

trait HasOrder {
    public function orders()
    {
        return $this->morphMany(Order::class, 'owner');
    }
}