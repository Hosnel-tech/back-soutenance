<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Td;
use App\Policies\TdPolicy;
use App\Models\Epreuve;
use App\Policies\EpreuvePolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    Gate::policy(Td::class, TdPolicy::class);
    Gate::policy(Epreuve::class, EpreuvePolicy::class);
    }
}
