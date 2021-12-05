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
            //'auth.defaults.guard' => 'api',
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

        // api
        Route::middleware(['api'])->prefix('api')->group(function () {
            // 注册
            Route::post('/register-via-email', Controllers\RegisterController::class. '@viaEmail');
            Route::post('/register-via-mobile', Controllers\RegisterController::class. '@viaMobile');
            // 登录
            Route::post('/login-via-mobile', Controllers\AuthController::class.'@viaMobile');
            Route::post('/login-via-email', Controllers\AuthController::class.'@viaEmail');

            // 忘记密码
            Route::post('verify-mobile', Controllers\VerifyMobileController::class . '@verifyMobile');
            // Route::post('/password-reset-link', Controllers\ResetPasswordController::class . '@sendLink');
            // 通过手机号重置密码
            Route::post('/reset-password-via-mobile', Controllers\ResetPasswordController::class . '@resetViaMobile');

            Route::post('/token/refresh', Controllers\AuthController::class.'@refresh');

            Route::group(['middleware' => 'auth:api'], function () {
                Route::get('/me', Controllers\ProfileController::class.'@me');
                // 发送邮箱验证邮件
                Route::post('/email-verify-notification', Controllers\EmailVerifyNotificationController::class . '@send');

                // 验证手机号
                Route::post('/mobile-verify-code', Controllers\ConfirmableMobileController::class . '@sendVerifyCode');
                Route::post('/mobile-confirmation', Controllers\ConfirmableMobileController::class . '@handle');

                // 修改面膜
                Route::post('/reset-password', Controllers\ResetPasswordController::class. '@handle');
            });

        });

        // web
        Route::middleware('guest')->group(function () {

            // 邮箱验证
            Route::get('/verify-email/{id}/{hash}', [Controllers\VerifyEmailController::class, 'verifyEmail'])
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

            // Route::get('/reset-password/{token}', Controllers\ResetPasswordController::class. '@create')->name('password.reset');
            // Route::post('/reset-password', Controllers\ResetPasswordController::class. '@store')->name('auth.reset-password');
        });
    }
}
