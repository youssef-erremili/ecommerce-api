<?php

namespace App\Providers;

use App\Enums\AccountType;
use App\Models\User;
use App\Observers\UserObserver;
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
        User::observe(UserObserver::class);

        Gate::define('admin-access', function (User $user){
            return $user->account_type === AccountType::ADMIN->value;
        });
    }
}
