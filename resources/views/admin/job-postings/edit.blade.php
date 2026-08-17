<x-admin-layout title="Edit Job Posting">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Edit Job Posting</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.job-postings.update', $posting) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @include('admin.job-postings._form', ['posting' => $posting, 'categories' => $categories])
    </form>

</x-admin-layout>
