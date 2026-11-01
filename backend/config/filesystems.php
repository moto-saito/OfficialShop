<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        // 商品・ニュース等のアップロード画像用ディスク。
        // MEDIA_DISK_DRIVER=s3 で S3 互換ストレージ（Supabase Storage / Cloudflare R2 等）に保存する。
        // 本番（Render 無料プラン）はディスクが永続化されないため s3 を使う。
        'public' => env('MEDIA_DISK_DRIVER', 'local') === 's3'
            ? [
                'driver' => 's3',
                'key' => env('MEDIA_S3_KEY'),
                'secret' => env('MEDIA_S3_SECRET'),
                'region' => env('MEDIA_S3_REGION', 'auto'),
                'bucket' => env('MEDIA_S3_BUCKET'),
                // 公開 URL のベース（例: https://xxx.supabase.co/storage/v1/object/public/<bucket>）
                'url' => env('MEDIA_S3_URL'),
                'endpoint' => env('MEDIA_S3_ENDPOINT'),
                'use_path_style_endpoint' => env('MEDIA_S3_PATH_STYLE', true),
                'visibility' => 'public',
                'throw' => false,
                'report' => false,
            ]
            : [
                'driver' => 'local',
                'root' => storage_path('app/public'),
                'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
                'visibility' => 'public',
                'throw' => false,
                'report' => false,
            ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
