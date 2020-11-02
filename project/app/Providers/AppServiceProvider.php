<?php

namespace App\Providers;

use App\Services\AuthyService;
use Authy\AuthyApi;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register('\\Barryvdh\\LaravelIdeHelper\\IdeHelperServiceProvider');
        }

        $this->app->bind(AuthyApi::class, function ($app) {
            return new AuthyApi($app['config']->get('authy.production_key'));
        });

        $this->app->bind(AuthyService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
