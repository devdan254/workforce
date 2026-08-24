<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

/**
 * Notifications are Laravel's own polymorphic notifiable — fully generic
 * already. Reused directly by Job Seeker and Employer routes; only the
 * view name and fallback redirect are role-aware.
 */
class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->user()->notifications();

        // Optional — every existing caller (Student/Job Seeker/Employer/
        // Admin's own sidebar link) omits this and sees everything exactly
        // as before. Only the dashboard's new Unread/Read breakdown links
        // pass it, to land on a genuinely filtered view rather than the
        // full list.
        if ($request->filled('read')) {
            $request->boolean('read') ? $query->whereNotNull('read_at') : $query->whereNull('read_at');
        }

        $notifications = $query->paginate(15)->withQueryString();

        $view = $this->viewFor($request->user());

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

        $fallback = route($this->routeNameFor($request->user()).'notifications.index');

        return redirect($notification->data['link'] ?? $fallback);
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    private function viewFor($user): string
    {
        return $this->routeNameFor($user).'notifications.index';
    }

    /**
     * Not a real route name — reused as a dotted prefix for both the view
     * name (job-seeker.notifications.index) and the route() helper
     * (route('job-seeker.notifications.index')), since both happen to
     * share the same "prefix.notifications.index" shape.
     */
    private function routeNameFor($user): string
    {
        return match (true) {
            $user->isStaff() => 'admin.',
            $user->isJobSeeker() => 'job-seeker.',
            $user->isEmployer() => 'employer.',
            default => 'student.',
        };
    }
}
