<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        \App\Listeners\LogAuthenticationEvents::class,
    ];

    public function boot(): void
    {
        //
    }
}
