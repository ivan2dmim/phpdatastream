<?php
// define routes for FastRoute
return [
    [
        'GET',
        '/',
        [
            'PHPDatastream\Controllers\Homepage',
            'show'
        ]
    ],
    [
        [
            'GET',
            'POST'
        ],
        '/result[.php]',
        [
            'PHPDatastream\Controllers\Result',
            'result'
        ]
    ]
];