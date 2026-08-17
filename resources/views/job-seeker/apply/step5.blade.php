<x-job-seeker-layout title="Apply — Documents">

    <div style="max-width: 720px;" class="mx-auto">
        <a href="{{ route('job-seeker.jobs.apply.step', [$jobPosting, 4]) }}" class="text-decoration-none small mb-3 d-inline-block">← Back</a>

        @include('job-seeker.apply._stepper')

        @if(session('success'))
            <div class="alert alert-success small">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger small">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card stat-card p-4">
            <h2 class="h5 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">Documents</h2>
            <p class="text-secondary small mb-4">PDF, JPG or PNG, up to 10MB each. Items marked * are required.</p>

            {{-- CV --}}
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div class="small">
                    <strong>CV / Resume *</strong>
                    @if(!empty($data['documents']['cv_document_id']))
                        <span class="text-success ms-2"><i class="fa-solid fa-circle-check"></i> Uploaded</span>
                    @endif
                </div>
                @if(empty($data['documents']['cv_document_id']))
                    <span class="text-warning small">Upload on Step 1 to auto-fill your details, or go back now</span>
                @endif
            </div>

            {{-- Passport --}}
            @php $slots = [
                'passport' => 'Passport',
                'certificates' => 'Academic Certificates',
                'license' => 'Professional License',
                'photo' => 'Passport Photo *',
            ]; @endphp

            @foreach($slots as $slot => $label)
                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                    <div class="small">
                        <strong>{{ $label }}</strong>
                        @if(!empty($data['documents'][$slot.'_document_id']))
                            <span class="text-success ms-2"><i class="fa-solid fa-circle-check"></i> Uploaded</span>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('job-seeker.jobs.apply.upload', [$jobPosting, $slot]) }}" enctype="multipart/form-data" class="d-flex gap-2">
                        @csrf
                        <input type="file" name="file" class="form-control form-control-sm" style="max-width: 200px;" accept=".pdf,.jpg,.jpeg,.png" required>
                        <button type="submit" class="btn btn-sm btn-outline-primary text-nowrap">
                            {{ !empty($data['documents'][$slot.'_document_id']) ? 'Replace' : 'Upload' }}
                        </button>
                    </form>
                </div>
            @endforeach

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('job-seeker.jobs.apply.step', [$jobPosting, 4]) }}" class="btn btn-light">← Back</a>
                <a href="{{ route('job-seeker.jobs.apply.step', [$jobPosting, 6]) }}" class="btn btn-primary">Next: Additional Information →</a>
            </div>
        </div>
    </div>

</x-job-seeker-layout>
