<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Services\ApplicationStatusService;
use Illuminate\View\View;

class VisaController extends Controller
{
    public function show(JobApplication $application): View
    {
        $this->authorize('view', $application);

        $visa = $application->visaApplication;

        $nextStatuses = $visa
            ? app(ApplicationStatusService::class)->allowedNextStatuses($visa)
            : collect();

        return view('job-seeker.visa.show', [
            'application' => $application,
            'visa' => $visa,
            'nextStatuses' => $nextStatuses,
        ]);
    }
}
