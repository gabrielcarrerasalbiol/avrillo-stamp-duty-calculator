<?php

return [
    'standard' => [
        'bands' => [
            ['up_to_previous' => 0, 'up_to' => 125000, 'rate_bps' => 0],
            ['up_to_previous' => 125000, 'up_to' => 250000, 'rate_bps' => 200],
            ['up_to_previous' => 250000, 'up_to' => 925000, 'rate_bps' => 500],
            ['up_to_previous' => 925000, 'up_to' => 1500000, 'rate_bps' => 1000],
            ['up_to_previous' => 1500000, 'up_to' => null, 'rate_bps' => 1200],
        ],
    ],
    'first_time_buyer' => [
        'max_price' => 500000,
        'bands' => [
            ['up_to_previous' => 0, 'up_to' => 300000, 'rate_bps' => 0],
            ['up_to_previous' => 300000, 'up_to' => 500000, 'rate_bps' => 500],
        ],
    ],
    'additional_property' => [
        'minimum_price' => 40000,
        'rate_bps' => 500,
    ],
];