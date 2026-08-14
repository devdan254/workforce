<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudyApplication;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user();

        // "Where am I?" — we surface the student's most recently active application
        // as the headline card. A student CAN have multiple applications (spec is explicit
        // about this) — this is deliberately "primary application", not "the" application.
        $primaryApplication = $student->studyApplications()
            ->with(['currentStatus', 'university', 'course', 'visaApplication.currentStatus', 'invoices'])
            ->latest('submitted_at')
            ->first();

        $documentStats = [
            'uploaded' => $student->documents()->whereIn('status', ['uploaded', 'under_review', 'verified'])->count(),
            'pending' => $student->documents()->where('status', 'required')->count(),
        ];

        $financialSummary = $this->financialSummary($student);

        $notifications = $student->notifications()->latest()->take(5)->get();

        $upcomingAppointments = $student->appointments()
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->take(5)
            ->get();

        $outstandingActions = $this->buildOutstandingActions($student, $primaryApplication, $financialSummary);

        return view('student.dashboard', [
            'student' => $student,
            'primaryApplication' => $primaryApplication,
            'documentStats' => $documentStats,
            'financialSummary' => $financialSummary,
            'notifications' => $notifications,
            'upcomingAppointments' => $upcomingAppointments,
            'outstandingActions' => $outstandingActions,
        ]);
    }

    private function financialSummary($student): array
    {
        $invoices = $student->invoices;

        $total = (float) $invoices->sum('total');
        $paid = (float) $invoices->sum('amount_paid');

        return [
            'total' => $total,
            'paid' => $paid,
            'balance' => $total - $paid,
            'currency' => $invoices->first()->currency ?? 'KES',
        ];
    }

    /**
     * "Your Attention Is Required" — every item here is derived from real records,
     * never hardcoded. This is exactly the spec's "must be dynamically generated" rule.
     */
    private function buildOutstandingActions($student, ?StudyApplication $application, array $financialSummary): array
    {
        $actions = [];

        $requiredDocs = $student->documents()->where('status', 'required')->get();
        foreach ($requiredDocs as $doc) {
            $actions[] = [
                'label' => "Upload {$doc->name}",
                'link' => route('student.applications.show', $application?->id ?? 0),
            ];
        }

        $rejectedDocs = $student->documents()->where('status', 'rejected')->get();
        foreach ($rejectedDocs as $doc) {
            $actions[] = [
                'label' => "Replace rejected document: {$doc->name}",
                'link' => route('student.applications.show', $application?->id ?? 0),
            ];
        }

        if ($financialSummary['balance'] > 0) {
            $pendingInvoice = $student->invoices()->where('status', '!=', 'paid')->first();
            if ($pendingInvoice) {
                $actions[] = [
                    'label' => 'Pay outstanding invoice: '.$financialSummary['currency'].' '.number_format($financialSummary['balance'], 2),
                    'link' => route('student.invoices.show', $pendingInvoice->id),
                ];
            }
        }

        $unconfirmedAppointments = $student->appointments()->where('status', 'requested')->get();
        foreach ($unconfirmedAppointments as $appointment) {
            $actions[] = [
                'label' => "Confirm {$appointment->type} on ".$appointment->scheduled_at->format('d M'),
                'link' => '#',
            ];
        }

        return $actions;
    }
}
