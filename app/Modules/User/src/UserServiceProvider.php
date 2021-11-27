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
    }

    protected function defineRoutes()
    {
        if (app()->routesAreCached()) {
            return;
        }

        Route::middleware(['api'])->prefix('api')->group(function () {
            Route::post('/user', Controllers\RegisterController::class. '@create');
            Route::post('/login', Controllers\AuthController::class.'@login');
            Route::post('/login-with-email', Controllers\AuthController::class.'@loginWithEmail');
            Route::get('/me', Controllers\AuthController::class.'@me');
        });

        Route::middleware('guest')->group(function () {
            Route::get('/verify-email/{id}/{hash}', [Controllers\VerifyEmailController::class, 'verifyEmail'])
                //->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

//            Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, 'verifyEmail'])
//                ->middleware(['auth', 'signed', 'throttle:6,1'])
//                ->name('login');
        });

    }
}
