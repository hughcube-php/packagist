<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/7/7
 * Time: 19:39
 */

return [
    'bin' => env('SATIS_COMPOSER_DIR'),

    'runtime_dir' => env('SATIS_RUNTIME_DIR'),
    'composer_dir' => env('SATIS_COMPOSER_DIR'),
    'build_dir' => env('SATIS_BUILD_DIR'),

    'satis' => [
        'name' => 'hzcube/packagist',
        'homepage' => 'https://packagist.demo.net/composer',
        'config' => [
            'secure-http' => false,
            'http-basic' => [
                'codeup.aliyun.com' => [
                    'username' => env('ALIYUN_CODEUP_USERNAME'),
                    'password' => env('ALIYUN_CODEUP_PASSWORD')
                ]
            ]
        ],
        'archive' => [
            'directory' => 'dist',
            'format' => 'zip',
            'skip-dev' => true
        ],
        'require-dependencies' => false,
        'require-dev-dependencies' => false
    ]
];
