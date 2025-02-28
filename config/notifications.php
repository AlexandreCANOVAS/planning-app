<?php

return [
    'channels' => [
        'mail' => [
            'queue' => 'notifications',
        ],
        'database' => [
            'queue' => 'default',
        ],
    ],
]; 