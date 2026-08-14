<x-student-layout title="Resources">

    <h2 class="h4 fw-semibold mb-1" style="font-family:'Poppins',sans-serif;color:#082159;">Resources</h2>
    <p class="text-secondary mb-4">Guides, checklists and forms to help you through your study abroad journey.</p>

    @forelse($groupedResources as $category => $resources)
        <div class="mb-4">
            <h3 class="h6 fw-semibold text-secondary text-uppercase small mb-3" style="letter-spacing:.04em;">{{ $category }}</h3>
            <div class="row g-3">
                @foreach($resources as $resource)
                    <div class="col-md-6 col-lg-4">
                        <div class="card stat-card p-3 h-100 d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-2">
                                <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width:44px;height:44px;flex-shrink:0;">
                                    <i class="fa-solid fa-{{ match($resource->type) { 'guide' => 'book-open', 'faq' => 'circle-question', 'form' => 'file-signature', default => 'list-check' } }}"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $resource->title }}</div>
                                    <div class="small text-secondary text-capitalize">{{ $resource->type }}</div>
                                </div>
                            </div>
                            @if($resource->body)
                                <p class="small text-secondary flex-grow-1">{{ \Illuminate\Support\Str::limit($resource->body, 100) }}</p>
                            @endif
                            <div class="mt-auto pt-2">
                                @if($resource->file_path)
                                    <a href="{{ route('student.resources.download', $resource) }}" class="btn btn-sm btn-outline-primary w-100">
                                        <i class="fa-solid fa-download"></i> Download
                                    </a>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#resource-{{ $resource->id }}">
                                        Read More
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(!$resource->file_path)
                        <div class="modal fade" id="resource-{{ $resource->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" style="font-family:'Poppins',sans-serif;">{{ $resource->title }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">{{ $resource->body }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @empty
        <div class="card stat-card p-5 text-center text-secondary">No resources published yet.</div>
    @endforelse

</x-student-layout>
