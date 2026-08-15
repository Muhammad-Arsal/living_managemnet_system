<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Council;
use App\Models\Staff;
use App\View\Composers\NotificationDropdownComposer;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::addNamespace('frontend', resource_path('frontend'));
        View::addNamespace('backend', resource_path('backend'));

        Relation::morphMap([
            'admin' => Admin::class,
            'staff' => Staff::class,
            'council' => Council::class,
        ]);

        View::composer([
            'backend::admin.layouts.partials.navbar',
            'backend::staff.layouts.partials.navbar',
            'backend::council.layouts.partials.navbar',
        ], NotificationDropdownComposer::class);
    }
}
