<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Log Viewer Title
    |--------------------------------------------------------------------------
    |
    | The title that will be displayed at the top of the Log Viewer page.
    |
    */

    'title' => 'Eidos Log Viewer',

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | The following array contains the configuration for the routes used by
    | the Log Viewer. You can customize the path, middleware, and other
    | options for each route.
    |
    */

    'routes' => [
        'web' => [
            'prefix' => 'log-viewer',
            'middleware' => [
                'web',
                // Adicionando o middleware para proteger a rota.
                // Apenas usuários com o e-mail de admin poderão acessar.
                'is_admin',
            ],
        ],

        'api' => [
            'prefix' => 'log-viewer/api',
            'middleware' => [
                'api',
                // Adicionando o middleware para proteger a API.
                'is_admin',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Back to app URL
    |--------------------------------------------------------------------------
    |
    | The URL to which the "Back to app" link will point.
    |
    */

    'back_to_app_url' => config('app.url'),

    /*
    |--------------------------------------------------------------------------
    | Response theme
    |--------------------------------------------------------------------------
    |
    | The theme of the Log Viewer page.
    | Can be 'light' or 'dark'.
    |
    */

    'theme' => 'dark',

    /*
    |--------------------------------------------------------------------------
    | Include file path
    |--------------------------------------------------------------------------
    |
    | Whether to include the file path in the log entries.
    |
    */

    'include_file_path' => true,

    /*
    |--------------------------------------------------------------------------
    | Larger stack trace
    |--------------------------------------------------------------------------
    |
    | Whether to display a larger stack trace in the log entries.
    |
    */

    'larger_stack_trace' => true,

    /*
    |--------------------------------------------------------------------------
    | Shorter stack trace
    |--------------------------------------------------------------------------
    |
    | List of vendor paths to be removed from the stack trace.
    |
    */

    'shorter_stack_trace' => [
        '/vendor/laravel/framework',
        '/vendor/livewire/livewire',
        '/vendor/livewire/volt',
        '/vendor/spatie/laravel-ray',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache driver
    |--------------------------------------------------------------------------
    |
    | The cache driver to be used by the Log Viewer.
    | Null will use the default cache driver.
    |
    */

    'cache_driver' => null,

    /*
    |--------------------------------------------------------------------------
    | Chunk size
    |--------------------------------------------------------------------------
    |
    | The number of bytes to read from the log file at a time.
    |
    */

    'chunk_size' => 65536, // 64KB

    /*
    |--------------------------------------------------------------------------
    | Maximum file size
    |--------------------------------------------------------------------------
    |
    | The maximum file size in megabytes that the Log Viewer will open.
    |
    */

    'max_file_size' => 512, // 512MB

];
