<?php

return [

    'back' => 'Back',
    'back_to_login' => 'Back to Login',
    'next' => 'Next',
    'verify' => 'Verify',

    'invalid_session' => 'Session is invalid or expired.',

    'lockout' => [
        'global' => '{1} Too many request made. Please try again in :count second.|[2,*] Too many request made. Please try again in :count seconds.',
        'otp' => '{1} Too many request made. Please try again in :count second.|[2,*] Too many request made. Please try again in :count seconds.',
        'user' => '{1} Too many request made. Please try again in :count second.|[2,*] Too many request made. Please try again in :count seconds.',
    ],

    'otp' => [
        'request_timeout' => '{1} Please wait for :count second before you can request another two factor authentication token.|[2,*] Please wait for :count seconds before you can request another two factor authentication token.',
        'token_mismatch' => 'Invalid two factor authentication token.',
        'service_error' => 'Two factor authentication service error. Please contact administrator for help.',
    ],

];
