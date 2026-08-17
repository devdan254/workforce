<x-admin-layout title="Create Job Posting">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Create Job Posting</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.job-postings.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.job-postings._form', ['posting' => null, 'categories' => $categories])
    </form>

</x-admin-layout>
