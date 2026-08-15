<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class PortalNotificationController extends Controller
{
    public function open(Request $request, string $notification): RedirectResponse
    {
        $actor = $request->user();
        /** @var DatabaseNotification $record */
        $record = $actor->notifications()->findOrFail($notification);
        $record->markAsRead();

        $url = $record->data['url'] ?? null;

        if (! is_string($url) || $url === '') {
            return redirect()->route($actor->portalKey().'.dashboard');
        }

        return redirect()->to($url);
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}
