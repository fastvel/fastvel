<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Order\Models;

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OrderRefund
 * @package Imdgr886\Order\Models
 * 退款模型
 * 只有退款成功、审核拒绝、取消 3 个状态为最终状态，不可再操作
 */
class OrderRefund extends Model
{

    use DefaultDatetimeFormat;

    //use SoftDeletes;

    const PENDING = 'pending';
    const APPROVED = 'approved';
    const REFUNDING = 'refunding';
    const REFUNDED = 'refunded';
    const CANCELED = 'canceled';
    const REFUSED = 'refused';
    const FAILED = 'failed';


    static $statusLabels = [
        self::PENDING => '待审核',
        self::APPROVED => '审核通过',
        self::REFUNDING => '退款中',
        self::REFUNDED => '退款成功',
        self::CANCELED => '已取消',
        self::REFUSED => '审核不通过',
        self::FAILED => '退款失败',
    ];

    /**
     * 可以退款的状态
     * 审核通过、退款中、退款失败的可以执行退款
     * @var string[]
     */
    static $canRefundStstus = [
        self::APPROVED,
        self::REFUNDING,
        self::FAILED
    ];

    /**
     * 可以取消的状态
     * 只有执行失败的能取消
     * @var string[]
     */
    static $canCancelStatus = [
        self::FAILED
    ];

    protected $casts = [
        'stop_order_items' => 'array'
    ];

    public static function boot()
    {
        parent::boot();

    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(OrderTransaction::class);
    }

    /**
     * 判断当前状态是否可以退款
     */
    public function canRefund()
    {
         return in_array($this->status, self::$canRefundStstus);
    }

    /**
     * 判断当前状态是否可以取消
     */
    public function canCancel()
    {
        return in_array($this->status, self::$canCancelStatus);
    }

    // 触发 order 状态更新
    public function fireOrderStatusUpdate()
    {
        // 退款成功，更新订单的状态
        if ($this->status == OrderRefund::REFUNDED) {
            // 如果此退款金额跟订单金额一致，那么就是全额退款
            if ($this->refund_amount >= $this->order->paid_amount) {
                $this->order->status = Order::REFUNDED;
                $this->order->save();
            } else {
                // 已成功退款的金额
                $refundAmount = $this->order->refunds()->where('status', OrderRefund::REFUNDED)->sum('refund_amount');
                $this->order->status = $refundAmount >= $this->order->paid_amount ? Order::REFUNDED : Order::PARTIAL_REFUNDED;
                $this->order->save();
            }
        } else if ($this->status == OrderRefund::CANCELED || $this->status == OrderRefund::REFUSED) {

            // 审核失败或者取消后，看看还有没有退款
            if ($this->order->canRefundAmount() >= $this->order->paid_amount && $this->order->is_paid) {
                $this->order->status = Order::PAID;
                $this->order->save();
            }
        }
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Administrator::class, 'approved_by');
    }

    public function applyBy()
    {
        return $this->morphTo();
    }
}
