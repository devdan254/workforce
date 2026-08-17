<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

/**
 * Notifications are Laravel's own polymorphic notifiable — fully generic
 * already. Reused directly by Job Seeker routes; only the view name and
 * fallback redirect are role-aware.
 */
class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()->paginate(15);

        $view = $request->user()->isJobSeeker() ? 'job-seeker.notifications.index' : 'student.notifications.index';

        return view($view, ['notifications' => $notifications]);
    }

    /**
     * Clicking a notification marks it read AND takes you to whatever it's
     * about — the `link` field baked into each notification's data() by the
     * Notification classes built earlier (StatusChangedNotification etc.).
     */
    public function read(Request $request, DatabaseNotification $notification): RedirectResponse
    {
        abort_unless(
            $notification->notifiable_id === $request->user()->id
                && $notification->notifiable_type === $request->user()->getMorphClass(),
            403
        );

        $notification->markAsRead();

        $fallback = $request->user()->isJobSeeker() ? route('job-seeker.notifications.index') : route('student.notifications.index');

        return redirect($notification->data['link'] ?? $fallback);
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}
