<?php

namespace App\Providers;

use App\Repository\Eloquent\BankAccountRepositoryInterface;
use App\Repository\Eloquent\BankProfileOtpRepositoryInterface;
use App\Repository\Eloquent\BankProfileRepositoryInterface;
use App\Repository\Eloquent\UserAccountRepositoryInterface;
use App\Repository\Eloquent\UserSessionRepositoryInterface;
use App\Repository\UserRepository;
use App\Repository\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(BankProfileRepositoryInterface::class, UserRepository::class);
        $this->app->bind(BankProfileOtpRepositoryInterface::class, UserRepository::class);
        $this->app->bind(BankAccountRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserAccountRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserSessionRepositoryInterface::class, UserRepository::class);

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
