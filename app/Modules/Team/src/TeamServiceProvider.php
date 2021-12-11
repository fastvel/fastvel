<?php

namespace Imdgr886\Team;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Imdgr886\Team\Listeners\CreatePersonalTeam;
use Imdgr886\User\Commands\Installer;
use Imdgr886\User\Models\User;

class TeamServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
        // 用户注册后，要生成团队
        Event::listen(Registered::class, CreatePersonalTeam::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Installer::class,
            ]);
        }

        //$this->defineRoutes();

        $this->publishes([__DIR__.'/../database/migrations' => database_path('migrations')], ['team', 'laravel-assets']);
    }
}
