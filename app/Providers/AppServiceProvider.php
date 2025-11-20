<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Requisition;
use App\Models\User;
use App\Observers\RequisitionObserver;
use App\Observers\UserObserver;

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
        // Register model observers
        Requisition::observe(RequisitionObserver::class);
        User::observe(UserObserver::class);
    }
}
