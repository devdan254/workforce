<x-admin-layout title="Create Resource">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Create Resource</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.resources.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.resources._form', ['resource' => null])
    </form>

</x-admin-layout>
