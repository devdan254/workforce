@php
    $code = '404';
    $heading = "Page Not Found";
    $message = "The page you're looking for doesn't exist, may have been moved, or the link you followed might be broken.";
    $icon = '<svg fill="none" viewBox="0 0 24 24" stroke="#082159" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
@endphp
@include('errors._chrome')
