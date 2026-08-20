@props(['url', 'title'])

@php
    $encodedUrl = urlencode($url);
    $encodedTitle = urlencode($title);
@endphp

<div class="share-menu" data-share-menu>
    <button type="button" class="btn btn-ghost btn-sm btn-block" data-share-toggle>
        <i class="fa-solid fa-share-nodes"></i> Share
    </button>
    <div class="share-menu-panel">
        <a href="https://wa.me/?text={{ $encodedTitle }}%20{{ $encodedUrl }}" target="_blank" rel="noopener" class="share-menu-item share-whatsapp">
            <i class="fa-brands fa-whatsapp"></i> WhatsApp
        </a>
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}" target="_blank" rel="noopener" class="share-menu-item share-facebook">
            <i class="fa-brands fa-facebook-f"></i> Facebook
        </a>
        <a href="https://twitter.com/intent/tweet?url={{ $encodedUrl }}&text={{ $encodedTitle }}" target="_blank" rel="noopener" class="share-menu-item share-x">
            <i class="fa-brands fa-x-twitter"></i> X (Twitter)
        </a>
        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedUrl }}" target="_blank" rel="noopener" class="share-menu-item share-linkedin">
            <i class="fa-brands fa-linkedin-in"></i> LinkedIn
        </a>
        <a href="mailto:?subject={{ $encodedTitle }}&body={{ $encodedUrl }}" class="share-menu-item share-email">
            <i class="fa-solid fa-envelope"></i> Email
        </a>
        <button type="button" class="share-menu-item share-copy" data-share-copy data-share-url="{{ $url }}">
            <i class="fa-solid fa-link"></i> Copy Link
        </button>
        <div class="share-menu-copied"><i class="fa-solid fa-circle-check"></i> Link copied!</div>
    </div>
</div>
