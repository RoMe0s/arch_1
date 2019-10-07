<?php

namespace App\Providers;

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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(
            \Game\Domain\Repository\GameRepositoryInterface::class,
            \Game\Infrastructure\Repository\Eloquent\GameRepository::class
        );

        $this->app->bind(
            \Game\Domain\Repository\PlayerRepositoryInterface::class,
            \Game\Infrastructure\Repository\Eloquent\PlayerRepository::class
        );

        $this->app->bind(
            \Game\Domain\Repository\StepRepositoryInterface::class,
            \Game\Infrastructure\Repository\Eloquent\StepRepository::class
        );
    }
}
