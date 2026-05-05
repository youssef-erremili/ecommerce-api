<?php

namespace App\Providers;

use App\Enums\AccountType;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Observers\CategoryObserver;
use App\Observers\ProductObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Authenticatable::class, function ($app) {
            return Auth::user();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Product::observe(ProductObserver::class);
        Category::observe(CategoryObserver::class);

        Gate::define('admin-access', function (User $user) {
            return $user->account_type === AccountType::ADMIN;
        });

        Gate::define('vendor-access', function (User $user) {
            return $user->account_type === AccountType::VENDOR;
        });

        Gate::define('admin-or-vendor', function (User $user) {
            return $user->account_type === AccountType::ADMIN || $user->account_type === AccountType::VENDOR;
        });
    }
}
