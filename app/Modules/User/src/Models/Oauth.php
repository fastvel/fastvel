<?php

namespace Imdgr886\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Oauth extends Model
{
    CONST WECHAT_MP = 'wechat_mp'; // 公众号
    CONST WECHAT_MINI = 'wechat_mini'; // 小程序

    protected $table = 'user_oauth';


    protected $fillable = [
        'openid', 'platform', 'access_token', 'refresh_token', 'expires_at', 'unionid', 'raw_data'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
