<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Blade;

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

    public function boot()
    {
        Activity::saving(function (Activity $activity) {
            if (!Auth::check()) {
                // Guest (tidak login)
                $activity->causer_type = null;
                $activity->causer_id = null;
                $activity->properties = $activity->properties->merge([
                    'causer_name' => 'Guest'
                ]);
            } else {
                // User login (admin)
                $activity->properties = $activity->properties->merge([
                    'causer_name' => Auth::user()->name
                ]);
            }
        });
        
        Blade::if('userType', function ($type) {
            return auth()->check() && auth()->user()->type === $type;
        });
    }
}
