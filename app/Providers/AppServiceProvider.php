<?php

namespace App\Providers;

use App\Models\Device;
use App\Observers\SnowflakeObserve;
use App\Observers\TeamObserve;
use Illuminate\Support\ServiceProvider;
use Imdgr886\Order\Models\Order;
use Imdgr886\Team\Models\Team;
use Imdgr886\User\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Snowflake 观察者，设置主键 ID
        User::observe(SnowflakeObserve::class);
        Team::observe(SnowflakeObserve::class);
        Order::observe(SnowflakeObserve::class);
        Device::observe(SnowflakeObserve::class);

        // team 观察者，设置 team_id
        Order::observe(TeamObserve::class);
        
    }
}
