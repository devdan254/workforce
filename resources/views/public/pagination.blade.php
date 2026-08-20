@if ($paginator->hasPages())
<div class="pagination">
    @if ($paginator->onFirstPage())
        <button class="pagination-arrow" disabled><i class="fa-solid fa-arrow-left"></i> Prev</button>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="pagination-arrow"><i class="fa-solid fa-arrow-left"></i> Prev</a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="pagination-dots">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <button class="active">{{ $page }}</button>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="pagination-arrow">Next <i class="fa-solid fa-arrow-right"></i></a>
    @else
        <button class="pagination-arrow" disabled>Next <i class="fa-solid fa-arrow-right"></i></button>
    @endif
</div>
@endif
