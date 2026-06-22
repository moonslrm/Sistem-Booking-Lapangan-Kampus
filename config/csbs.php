<?php

return [
    'app_name' => env('APP_NAME', 'CSBS'),

    'booking' => [
        'slot_lock_minutes' => env('CSBS_SLOT_LOCK_MINUTES', 15),
        'cancellation_deadline_hours' => env('CSBS_CANCELLATION_DEADLINE_HOURS', 24),
        'payment_timeout_minutes' => env('CSBS_PAYMENT_TIMEOUT_MINUTES', 15),
    ],

    'refund' => [
        'enabled' => env('CSBS_REFUND_ENABLED', true),
        'percentage' => env('CSBS_REFUND_PERCENTAGE', 100),
        'policy_text' => env(
            'CSBS_REFUND_POLICY_TEXT',
            'Refund follows the configured cancellation deadline and percentage rules.'
        ),
    ],

    'payments' => [
        'midtrans' => [
            'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
            'server_key' => env('MIDTRANS_SERVER_KEY'),
            'client_key' => env('MIDTRANS_CLIENT_KEY'),
        ],
    ],

    'firebase' => [
        'credentials' => env('FIREBASE_CREDENTIALS'),
    ],
];