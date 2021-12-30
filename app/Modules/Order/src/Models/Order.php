<?php

/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Order\Models;

use App\Admin\Traits\RoleTrait;
use App\Models\BillsOrder;
use App\Models\OrderOperationLogs;
use App\Models\PayAfterIssuedBill;
use App\Models\UserCoupon;
use App\Models\PayAfterIssued;
use App\Models\PayAfterIssuedOrder;
use App\Models\PlansCombos;
use Imdgr886\User\Models\User;
use Carbon\Carbon;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use DefaultDatetimeFormat, SoftDeletes;
    //use SoftDeletes;

    const PENDING = 'pending';
    const PAID = 'paid';
    const CANCELED = 'canceled';
    const APPLY_REFUND = 'apply_refund';
    const REFUNDED = 'refunded';
    const PARTIAL_REFUNDED = 'partial_refunded';
    const WAIT_APPROVE = 'wait_approve';

    static $statusLabels = [
        self::PENDING => '待支付',
        self::PAID => '已支付',
        self::CANCELED => '已取消',
        self::REFUNDED => '全额退款',
        self::PARTIAL_REFUNDED => '部分退款',
        self::APPLY_REFUND => '申请退款中',
        self::WAIT_APPROVE => '付款待审核',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'id' => 'string', // js 处理不了 bigint
    ];

    protected $fillable = [
        'user_id', 'subject', 'items_total', 'coupon_id',
        'discount_amount', 'order_amount', 'ip', 'is_renew'
    ];

    public $incrementing = false;



    public $appends = ['status_label', 'order_type'];

    protected $hidden = ['pivot'];

    public function getStatusLabelAttribute()
    {
        return @self::$statusLabels[$this->status];
    }

    public function history(): HasMany
    {
        return $this->hasMany(OrderHistory::class);
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(OrderTransaction::class, 'order_id', 'id')->whereNotNull('paid_at')->orderByDesc('id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(OrderTransaction::class);
    }

    public function addHistory($comment)
    {
        /*$history = new OrderHistory(['order_status' => $this->status, 'comment' => $comment, 'order_id' => $this->id]);
        $history->save();*/
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(OrderRefund::class);
    }

    public function coupon()
    {
        return $this->hasOne(UserCoupon::class, 'used_by_order', 'id');
    }

    public function bill()
    {
        return $this->hasOne(BillsOrder::class, 'order_id', 'id');
    }

    public function payAfterIssuedOrder()
    {
        return $this->hasOne(PayAfterIssuedOrder::class, 'order_id', 'id');
    }

    public function payAfterIssuedBill()
    {
        return $this->hasOne(PayAfterIssuedBill::class, 'order_id', 'id');
    }

    /**
     * 已退款金额
     * @return float
     */
    public function getRefundAmountAttribute()
    {
        $refundAmount = $this->refunds()
            ->whereNotIn('status', [
                OrderRefund::REFUSED,
                OrderRefund::CANCELED,
            ])
            ->sum('refund_amount');

        return max(0, $refundAmount);
    }

    public function canRefundAmount()
    {
        return max(0, $this->paid_amount - $this->refundAmount);
    }

    public function scopeUser($query)
    {
        return $query->where('user_id', auth()->id());
    }


    public function paid($paidAmount = 0)
    {
        $this->paid_amount = $paidAmount;
        $this->paid_at = Carbon::now()->toDateTimeString();
        $this->status = self::PAID;
        return $this;
    }

    public function getOrderTypeAttribute()
    {
        $type = '新购订单';

        if ($this->items()->exists()) {
            switch ($this->items()->first()->order_type) {
                case OrderItem::ORDER_TYPE_NEW:
                    $type = '新购';
                    break;
                case OrderItem::ORDER_TYPE_RENEW:
                    $type = '续费';
                    break;
                default:
                    $type = '其他';
                    break;
            }
        }
        return $type;
    }

    public function manual_discount()
    {
        return $this->hasOne(OrderOperationLogs::class)->where('operation_type', OrderOperationLogs::ORDER_MANUAL_DISCOUNT_TYPE);
    }

    public function getAllDiscountedAmountTextAttribute()
    {
        $couponDiscount = $this->discount_amount ?? 0;
        $manualDiscount = 0;
        $operationLog = OrderOperationLogs::query()->where('order_id', $this->id)
            ->where('operation_type', OrderOperationLogs::ORDER_MANUAL_DISCOUNT_TYPE)
            ->first();
        if ($operationLog) {
            $manualDiscount = $operationLog->amount_info['after']['discount_amount'] - $operationLog->amount_info['before']['discount_amount'];
        }
        $text = '';
        if ($couponDiscount > 0 || $manualDiscount > 0) {
            $couponText = '';
            if ($couponDiscount > 0) {
                $couponText .= '- ' . $couponDiscount;
            }
            if ($manualDiscount > 0) {
                $couponText .= '- ' . $manualDiscount;
            }
            $text .= "<br><span class='text-muted'>($this->items_total $couponText)</span>";
        }
        // ($this->discount_amount > 0 ? "<br/><span class='text-muted'>({$this->items_total} - {$this->discount_amount})</span>" : '');
        return $text;
    }
}
