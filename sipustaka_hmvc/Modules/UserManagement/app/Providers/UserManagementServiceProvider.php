<?php

namespace Modules\UserManagement\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class UserManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'usermanagement');

        Route::middleware('web')->group(__DIR__.'/../../routes/web.php');
    }
}
