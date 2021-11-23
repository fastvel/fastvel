<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace Imdgr886\Order\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    use DefaultDatetimeFormat;
    const UPDATED_AT = null;

    protected $fillable = [
        'order_id', 'order_status', 'comment'
    ];
}
