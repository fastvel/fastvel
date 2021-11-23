<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Order\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;

class OrderTransactionNotify extends Model
{
    use DefaultDatetimeFormat;
    protected $fillable = [
        'transaction_id', 'event', 'result', 'data'
    ];

    protected $casts = [
        'data' => 'json'
    ];
    const UPDATED_AT = null;
}
