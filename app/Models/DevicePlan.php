<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Imdgr886\Order\OrderItemInterface;

class DevicePlan extends Model implements OrderItemInterface
{
    public static $providerNames = [
            'qcloud' => '腾讯云',
            'aws' => '亚马逊',
            'self-host' => '自有设备',
            'aliyun' => '阿里云',
            'google' => '谷歌云',
            'azure' => '微软云',
        ];

    public function getProviderNameAttribute()
    {
        return self::$providerNames[$this->provider] ?: $this->provider;
    }

    /**
     * @return BelongsTo
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function getName(array $options = [])
    {
        $duration = Arr::get($options, 'duration', 1);
        return $this->provider_name . " ({$duration}个月)";
    }

    public function getPrice(array $options = [])
    {
        $duration = Arr::get($options, 'duration', 1);
        $this->getAttributes();
        if ($duration >= 12) {
            // 12个月85折
            return round($this->price * $duration * 0.85, 2);
        } elseif ($duration >= 6) {
            // 6个月9折
            return round($this->price * $duration * 0.9, 2);
        }
        return round($this->price * $duration, 2);
    }

    public function getProductAmount($qty = 1, array $options = [])
    {
        return round($this->getPrice($options) * $qty, 2);
    }

    public function getPrimaryKeyValue()
    {
        return $this->id;
    }
}
