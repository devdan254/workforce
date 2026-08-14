<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()->paginate(15);

        return view('student.notifications.index', ['notifications' => $notifications]);
    }

    /**
     * Clicking a notification marks it read AND takes you to whatever it's
     * about — the `link` field baked into each notification's data() by the
     * Notification classes we built earlier (StatusChangedNotification etc.).
     */
public function read(Request $request, DatabaseNotification $notification): RedirectResponse
{
    abort_unless(
        $notification->notifiable_id === $request->user()->id
            && $notification->notifiable_type === $request->user()->getMorphClass(),
        403
    );

    $notification->markAsRead();

    $link = $notification->data['link'] ?? null;

    if ($link) {
        return redirect($link);
    }

    return redirect()->route('student.notifications.index');
}

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}
