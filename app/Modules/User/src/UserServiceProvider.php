<?php

namespace Imdgr886\User;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Imdgr886\User\Commands\Installer;
use Imdgr886\User\Http\Controllers;

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

        $this->publishes([__DIR__.'/../migrations' => database_path('migrations')], ['user', 'laravel-asset']);
    }

    protected function defineRoutes()
    {
        if (app()->routesAreCached()) {
            return;
        }

        Route::middleware(['api'])->prefix('api')->group(function () {
            Route::post('/register', Controllers\RegisterController::class. '@create');
            Route::post('/login-via-mobile', Controllers\AuthController::class.'@viaMobile');
            Route::post('/login-via-email', Controllers\AuthController::class.'@viaEmail');
            Route::get('/me', Controllers\AuthController::class.'@me');
        });

        Route::middleware('guest')->group(function () {
            Route::get('/verify-email/{id}/{hash}', [Controllers\VerifyEmailController::class, 'verifyEmail'])
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');
        });
    }
}
