<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Imdgr886\Order\Events\BeforePlaceOrderEvent;
use Imdgr886\User\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Imdgr886\Order\Events\PlaceOrderEvent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(BeforePlaceOrderEvent::class, function ($event) {
            dump($event->order->getAttributes(), $event->items[0]->product);
        });
    }
}
