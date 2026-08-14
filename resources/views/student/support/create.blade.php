<x-student-layout title="Raise a Ticket">

    <h2 class="h4 fw-semibold mb-4" style="font-family:'Poppins',sans-serif;color:#082159;">Raise a Support Ticket</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card stat-card p-4" style="max-width: 640px;">
        <form method="POST" action="{{ route('student.support.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Subject</label>
                <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Category</label>
                    <select name="category" class="form-select" required>
                        @foreach(['Applications', 'Documents', 'Payments', 'Visa', 'Travel', 'Other'] as $category)
                            <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Priority</label>
                    <select name="priority" class="form-select" required>
                        <option value="low" @selected(old('priority') === 'low')>Low</option>
                        <option value="medium" @selected(old('priority', 'medium') === 'medium')>Medium</option>
                        <option value="high" @selected(old('priority') === 'high')>High</option>
                        <option value="urgent" @selected(old('priority') === 'urgent')>Urgent</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Message</label>
                <textarea name="message" class="form-control" rows="5" required placeholder="Describe your question or issue...">{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Submit Ticket</button>
            <a href="{{ route('student.support.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>

</x-student-layout>
