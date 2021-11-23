<?php

namespace Imdgr886\User;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Imdgr886\User\Commands\Installer;
use Imdgr886\User\Controllers\AuthController;

class UserServiceProvider extends ServiceProvider
{
    public function register()
    {
        config([
            'auth.defaults.guard' => 'api',
            'auth.guards.api' => [
                'driver' => 'jwt',
                'provider' => 'users',
            ],
        ]);
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Installer::class,
            ]);
        }

        $this->defineRoutes();
    }

    protected function defineRoutes()
    {
        if (app()->routesAreCached()) {
            return;
        }

        Route::middleware(['api'])->prefix('api')->group(function () {
            Route::post('/login', AuthController::class.'@login');
            Route::post('/login-with-email', AuthController::class.'@loginWithEmail');
            Route::get('/me', AuthController::class.'@me');
        });
    }
}
