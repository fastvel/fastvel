Install
---

`composer require imdgr886/laravel-order`  

`php artisan order:install`

routes
---

```php
Route::group(['middleware' => ['web']], function () {
    Route::get('/alipay/order/{order}', 'Imdgr886\Order\Controllers\PayController@alipay');
    Route::get('/wechat/order/{order}', 'Imdgr886\Order\Controllers\PayController@wechat');
    Route::post('/payment/notify/{$gateway}', 'Imdgr886\Order\Http\Controllers\NotifyController@notify');
});

```
/alipay/order/{order}?method=[web/scan/app/wap]

**Notify**  
将通知地址加入到中间件 csrfVerify 的排除列表中
