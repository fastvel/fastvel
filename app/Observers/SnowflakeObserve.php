<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;

/**
 * 未模型生成主键 ID，而不是自增
 */
class SnowflakeObserve
{
    public function creating(Model $model)
    {
        $model->id = app('snowflake')->id();
    }
}
