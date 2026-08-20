<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Notification Email
    |--------------------------------------------------------------------------
    |
    | Every public-facing form on the marketing site (Job Application, Study
    | Abroad Application, Hire/Worker Request, Contact) sends a notification
    | here via App\Mail\AdminNotificationMail. Centralized in one config key
    | rather than hardcoded in four separate controllers, so changing the
    | recipient is a one-line .env change, not a code change.
    |
    */

    'admin_email' => env('ADMIN_NOTIFICATION_EMAIL', 'admin@alturaworkforce.com'),

    /*
    |--------------------------------------------------------------------------
    | General Inquiry Email
    |--------------------------------------------------------------------------
    |
    | Contact page + the Study Abroad consultation-request form both send
    | here — genuine inquiries with no account and no application behind
    | them, so unlike Job/Study applications, nothing is persisted to the
    | database. The email itself IS the record.
    |
    */

    'info_email' => env('INFO_NOTIFICATION_EMAIL', 'info@alturaworkforce.com'),

];
