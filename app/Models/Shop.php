<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id', 'name', 'platform', 'account', 'password', 'device_id'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
