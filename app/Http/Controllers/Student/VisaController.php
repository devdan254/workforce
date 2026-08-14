<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudyApplication;
use App\Services\ApplicationStatusService;
use Illuminate\View\View;

class VisaController extends Controller
{
    public function show(StudyApplication $application): View
    {
        // If there's no visa yet, we're really asking "can you see this application at all" —
        // reuse StudyApplicationPolicy since a VisaApplication doesn't exist to check ownership against.
        $this->authorize('view', $application);

        $visa = $application->visaApplication;

        $nextStatuses = $visa
            ? app(ApplicationStatusService::class)->allowedNextStatuses($visa)
            : collect();

        return view('student.visa.show', [
            'application' => $application,
            'visa' => $visa,
            'nextStatuses' => $nextStatuses,
        ]);
    }
}
