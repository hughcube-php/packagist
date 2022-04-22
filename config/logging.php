<?php

use HughCube\Laravel\DingTalk\Log\Handler as DingTalkHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Application Log Mode
    |--------------------------------------------------------------------------
    |
    */

    'path' => env('LOG_PATH'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['error', 'alarm', 'app',],
            'ignore_exceptions' => false,
        ],
        'error' => [
            'driver' => 'daily',
            'path' => log_path('error.log'),
            'level' => 'warning',
            'days' => 0,
        ],

        'app' => [
            'driver' => 'daily',
            'path' => log_path('app.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 0,
        ],

        'alarm' => [
            'driver' => 'monolog',
            'level' => 'warning',
            'handler' => DingTalkHandler::class,
            'handler_with' => [
                'enabled' => true === env('DING_TALK_ROBOT_ENABLED', true),
                'robot' => null,
                'bubble' => false,
            ],
        ],

        'queue' => [
            'driver' => 'daily',
            'path' => log_path('queue.log'),
            'level' => env('LOG_LEVEL', 'info'),
            'days' => 0,
            'bubble' => false,
        ],

        'schedule' => [
            'driver' => 'daily',
            'path' => log_path('schedule.log'),
            'level' => env('LOG_LEVEL', 'info'),
            'days' => 0,
            'bubble' => false,
        ],

        'stdout' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stdout',
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],
    ],

];
