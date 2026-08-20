<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\AdminNotificationMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * Deliberately NOT backed by a database model — per explicit instruction,
 * these are genuine "send an email, nothing stored" forms. No migration,
 * no Admin-side inbox to view these later; the email arriving at
 * info@alturaworkforce.com IS the record. This is different from every
 * other public form built so far (Job/Study Application, Hire) — those all
 * create real, persisted rows an Admin can review; these two don't.
 *
 * Both methods return JSON (success or 422 validation errors) rather than
 * redirecting — main.js's ajax-form handler expects this, so the original
 * inline "form disappears, success message appears" design actually works,
 * instead of the page reloading.
 */
class InquiryController extends Controller
{
    public function storeConsultation(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'destination' => ['nullable', 'string', 'max:100'],
            'course' => ['nullable', 'string', 'max:150'],
        ]);

        Mail::to(config('notifications.info_email'))->send(new AdminNotificationMail(
            heading: 'Study Abroad Consultation Request',
            lines: array_filter([
                'Name' => $data['name'],
                'Email' => $data['email'],
                'Phone' => $data['phone'],
                'Preferred Destination' => $data['destination'] ?? null,
                'Intended Course' => $data['course'] ?? null,
            ]),
        ));

        return response()->json(['message' => 'Request received.']);
    }

    public function storeContact(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        Mail::to(config('notifications.info_email'))->send(new AdminNotificationMail(
            heading: 'New Contact Form Message',
            lines: array_filter([
                'Name' => $data['name'],
                'Email' => $data['email'],
                'Phone' => $data['phone'] ?? null,
                'Subject' => $data['subject'] ?? null,
                'Message' => $data['message'],
            ]),
        ));

        return response()->json(['message' => 'Message sent.']);
    }
}
