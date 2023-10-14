<?php

namespace App\Providers;

use App\Repository\ClientOrderRepo;
use App\Interfaces\CrudRepoInterface;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\ClientOrderController;

class CrudRepoProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->when(ClientOrderController::class)
        ->needs(CrudRepoInterface::class)
        ->give(function() {
            return new ClientOrderRepo();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
