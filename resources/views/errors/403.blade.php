@php
    $code = '403';
    $heading = "Access Forbidden";
    $message = "You don't have permission to view this page. If you believe this is a mistake, please contact your administrator.";
    $icon = '<svg fill="none" viewBox="0 0 24 24" stroke="#082159" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>';
@endphp
@include('errors._chrome')
