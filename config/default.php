<?php return [
    'debug' => true,

    'session' => [
        'driver' => Vendimia\Session\Driver\Php::class,
        'options' => [
            'cookie_secure' => true,
            'cookie_httponly' => true,
            'cookie_samesite' => 'strict',
        ],
    ],

    'database' => [
        'connector' => Vendimia\Database\Driver\Sqlite\Connector::class,
        'options' => [
            'filename' => Vendimia\PROJECT_PATH . '/database.sqlite',
        ],
    ],

    'middleware' => [
        Vendimia\Middleware\Routing::class,
    ],
];