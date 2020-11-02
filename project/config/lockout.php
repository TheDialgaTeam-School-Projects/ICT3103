<?php

return [
    'global' => [
        'session' => [
            'failed_count' => '_failed_count',
            'reset_timestamp' => '_reset_timestamp',
        ],
        'max_attempt' => 5,
        'lockout_duration' => 5,
    ],

    'user_account' => [
        'max_attempt' => 5,
        'lockout_duration' => 5,
    ],

    'bank_profile_otp' => [
        'max_attempt' => 5,
        'lockout_duration' => 5,
    ],
];
