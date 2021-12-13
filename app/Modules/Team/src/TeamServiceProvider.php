<?php

namespace Imdgr886\Team;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Imdgr886\Team\Command\InstallCommand;
use Imdgr886\Team\Http\Controllers\InvitationController;
use Imdgr886\Team\Http\Controllers\TeamController;
use Imdgr886\Team\Listeners\CreatePersonalTeam;
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
        $this->defineRoutes();
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            InstallCommand::class,
        ]);
        $this->publishes([__DIR__.'/../database/migrations' => database_path('migrations')], ['team', 'laravel-assets']);
    }

    protected function defineRoutes()
    {
        // api
        Route::middleware(['api'])->prefix('api')->group(function () {
            Route::put('/team/{team}/invite-token', InvitationController::class . '@resetLink');
            Route::get('/team/current-team', TeamController::class . '@currentTeam');
            Route::get('/teams', TeamController::class . '@allTeams');
            Route::post('/team', TeamController::class . '@create');
        });
    }
}
