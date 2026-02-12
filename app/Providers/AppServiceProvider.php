<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Import your models
use App\Models\Category;
use App\Models\User;
use App\Models\ReturnModel;
use App\Models\Borrowing;               
use App\Models\Tool;
use App\Models\Role;

// Import your observers
use App\Observers\CategoryObserver;
use App\Observers\BorrowingObserver;
use App\Observers\ReturnObserver;
use App\Observers\UserObserver;
use App\Observers\ToolObserver;
use App\Observers\RoleObserver;

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
        // Register observers
        Role::observe(RoleObserver::class);
        User::observe(UserObserver::class);
        Category::observe(CategoryObserver::class);
        Tool::observe(ToolObserver::class);
        Borrowing::observe(BorrowingObserver::class);
        ReturnModel::observe(ReturnObserver::class);
    }
}
