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

    const ORDER_TYPE_NEW = 0;
    const ORDER_TYPE_RENEW = 1;
    const ORDER_TYPE_OTHER = 2;

    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;
    protected $table = 'order_items';

    public $fillable = [
        'price', 'quantity', 'name', 'total', 'item_type', 'item_id', 'service_number', 'order_type', 'discounted_price'
    ];

    public function product(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'item_type', 'item_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

//    public function setProductAttribute(OrderItemInterface $product)
//    {
//        $this->item_type = get_class($product);
//        $this->item_id = $product->getPrimaryKeyValue();
//    }
}
