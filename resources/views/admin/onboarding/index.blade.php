<x-admin-layout title="Onboarding">

    <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">Onboarding</h2>
    <p class="text-secondary mb-4">Create an account and go straight into their workspace to fill in the rest — profile, education, work experience, documents — exactly like someone who applied through the website themselves.</p>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card stat-card p-4 h-100 d-flex flex-column">
                <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;">
                    <i class="fa-solid fa-graduation-cap fs-4"></i>
                </div>
                <h3 class="h5 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Onboard a Student</h3>
                <p class="text-secondary flex-grow-1">
                    Create the account, then add their Personal Information, Academic details, Passport, Applications, and Documents from their Student Workspace.
                </p>
                @can('create', \App\Models\User::class)
                    <a href="{{ route('admin.students.create') }}" class="btn btn-primary">Start Student Onboarding →</a>
                @else
                    <button class="btn btn-secondary" disabled>You don't have permission to create Students</button>
                @endcan
            </div>
        </div>

        <div class="col-md-6">
            <div class="card stat-card p-4 h-100 d-flex flex-column">
                <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;">
                    <i class="fa-solid fa-briefcase fs-4"></i>
                </div>
                <h3 class="h5 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">Onboard a Job Seeker</h3>
                <p class="text-secondary flex-grow-1">
                    Create the account, then add their Personal Information, Education, Work Experience, Skills, Passport, and Documents (CV, ID, etc.) from their Job Seeker Workspace.
                </p>
                @can('createJobSeeker', \App\Models\User::class)
                    <a href="{{ route('admin.job-seekers.create') }}" class="btn btn-primary">Start Job Seeker Onboarding →</a>
                @else
                    <button class="btn btn-secondary" disabled>You don't have permission to create Job Seekers</button>
                @endcan
            </div>
        </div>
    </div>

    <div class="card stat-card p-4 mt-4">
        <h3 class="h6 fw-semibold mb-2" style="font-family:'Poppins',sans-serif;">What happens after account creation</h3>
        <ol class="small mb-0 ps-3">
            <li class="mb-1">A temporary password is generated and shown once — share it with the person securely.</li>
            <li class="mb-1">You land directly in their Workspace's <strong>Personal Information</strong> tab — fill in what they've told you (profile, passport, education, work experience for Job Seekers).</li>
            <li class="mb-1">Use the <strong>Documents</strong> tab to upload their CV, ID, certificates, or anything else you have on hand — the same as if they'd uploaded it themselves.</li>
            <li class="mb-0">Once their profile and documents are in place, create their first Application from the <strong>Applications</strong> tab to get their journey moving.</li>
        </ol>
    </div>

</x-admin-layout>
