<?php

namespace Imdgr886\Sms;

use Illuminate\Support\ServiceProvider;
use Overtrue\EasySms\EasySms;

class SmsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('easysms', function () {
            return new EasySms(config('modules.sms'));
        });
    }

    public function boot()
    {
        $this->defineRoutes();
        if (!$this->app->runningInConsole()) {
            return;
        }

        // publish
        $this->publishes([__DIR__. '/../config/sms.php' => config_path('modules/sms.php')], 'sms');
    }

    protected function defineRoutes()
    {

    }
}
