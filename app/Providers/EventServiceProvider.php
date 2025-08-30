<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\ProductViewed;
use App\Listeners\ProcessProductImages;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ProductViewed::class => [
            ProcessProductImages::class,
        ],
    ];
}