<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Imdgr886\Order\OrderItemInterface;

class OrderItem extends Model
{

    const ORDER_TYPE_NEW = 'new';
    const ORDER_TYPE_RENEW = 'renew';

    public $timestamps = false;
    public $incrementing = false;

    public $fillable = [
        'price', 'qty', 'name', 'total', 'product_type', 'product_id', 'order_type'
    ];

    public function product(): MorphTo
    {
        return $this->morphTo();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

}
