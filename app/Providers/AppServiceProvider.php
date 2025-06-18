<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::define('is-gestor', function (User $user) {
            return $user->perfil === 'Gestor';
        });

        Gate::define('is-funcionario', function (User $user) {
            return $user->perfil === 'Funcionario';
        });
    }
}
