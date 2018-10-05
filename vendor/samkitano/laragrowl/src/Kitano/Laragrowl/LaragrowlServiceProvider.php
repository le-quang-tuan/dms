<?php

namespace Kitano\Laragrowl;

use Illuminate\Support\ServiceProvider;
use Kitano\Laragrowl\LaravelSessionStore as LSession;
use Kitano\Laragrowl\SessionStore as SSession;

class LaragrowlServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            SSession::class,
            LSession::class
        );

        $this->app->singleton('laragrowl', function () {
            return $this->app->make('\Kitano\Laragrowl\LaragrowlNotifier');
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../../views', 'laragrowl');

        $this->publishes([
            __DIR__ . '/../../views'  => resource_path('views/vendor/laragrowl'),
            __DIR__ . '/../../assets' => public_path('vendor/laragrowl'),
        ]);
    }
}