<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationDropdownComposer
{
    public function compose(View $view): void
    {
        $portal = $this->portalFromView($view->name());
        $guard = $portal !== null ? config('portals.'.$portal.'.guard') : null;
        $actor = is_string($guard) ? Auth::guard($guard)->user() : null;

        $view->with([
            'portalKey' => $portal,
            'portalNotifications' => $actor?->notifications()->latest()->limit(8)->get() ?? collect(),
            'portalUnreadCount' => $actor?->unreadNotifications()->count() ?? 0,
        ]);
    }

    private function portalFromView(string $viewName): ?string
    {
        if (str_contains($viewName, 'admin.layouts')) {
            return 'admin';
        }

        if (str_contains($viewName, 'staff.layouts')) {
            return 'staff';
        }

        if (str_contains($viewName, 'council.layouts')) {
            return 'council';
        }

        return null;
    }
}
