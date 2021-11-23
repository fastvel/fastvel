<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Order\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderTransaction extends Model
{
    use DefaultDatetimeFormat;


    const GATEWAY_ALI = 'alipay';
    const GATEWAY_WECHAT = 'wechat';
    const GATEWAY_PAY_LATER = 'pay_later';
    const GATEWAY_TRANSFER = 'transfer';
    const GATEWAY_ISSUED_FIRST = 'issued_first';

    const GATEWAY_ENUM = [
        self::GATEWAY_ALI => '支付宝',
        self::GATEWAY_WECHAT => '微信支付',
        self::GATEWAY_PAY_LATER => '先用后付',
        self::GATEWAY_TRANSFER => '转账',
        self::GATEWAY_ISSUED_FIRST => '先下号后付款'
    ];

    public $incrementing = false;

    protected $guarded = [];
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(OrderRefund::class);
    }
}
