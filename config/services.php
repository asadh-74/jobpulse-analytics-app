<?php

// Add this array entry to your project's existing config/services.php
// (a fresh Laravel install already ships that file with other providers in it).

return [

    // ...keep the existing entries Laravel generated (postmark, resend, slack...)

    'remotive' => [
        'url' => env('REMOTIVE_API_URL', 'https://remotive.com/api/remote-jobs'),
    ],

];
