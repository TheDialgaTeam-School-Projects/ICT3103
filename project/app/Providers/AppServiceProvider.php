<?php

namespace App\Providers;

use Authy\AuthyApi;
use Carbon\Carbon;
use Godruoyi\Snowflake\Snowflake;
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
        $this->app->bind(AuthyApi::class, function ($app) {
            return new AuthyApi($app['config']->get('authy.production_key'));
        });

        $this->app->bind(Snowflake::class, function () {
            return (new Snowflake())->setStartTimeStamp(Carbon::createFromDate(2020, 10, 1)->getTimestamp() * 1000);
        });
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
