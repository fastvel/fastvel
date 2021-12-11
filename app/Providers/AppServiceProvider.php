<?php

namespace App\Providers;

use App\Observers\SnowflakeObserve;
use Illuminate\Support\ServiceProvider;
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
//        if ($this->app->isLocal()) {
//            $this->app->register(TelescopeServiceProvider::class);
//        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(SnowflakeObserve::class);
        // Team::observe(SnowflakeObserve::class);
        // Device::observe(SnowflakeObserve::class);
    }
}
