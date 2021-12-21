<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip', 'proxy_port', 'proxy_type', 'proxy_user', 'proxy_pass', 'provider', 'team_id'
    ];

    protected $hidden = ['proxy_pass', 'remote_pass'];

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    /**
     * 返回设备提供商实例
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    public function getProvider()
    {
        $className = '\\App\\DeviceProvider\\Providers\\' . Str::camel($this->provider);
        return app($className);
    }
}
