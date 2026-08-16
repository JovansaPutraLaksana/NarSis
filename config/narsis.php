<?php

return [
    'upload_max_kb' => (int) env('NARSIS_UPLOAD_MAX_KB', 4096),

    'storage' => [
        'driver' => env('NARSIS_STORAGE_DRIVER', 'local'),
        'supabase_url' => env('SUPABASE_URL'),
        'supabase_service_key' => env('SUPABASE_SERVICE_ROLE_KEY'),
        'supabase_bucket' => env('SUPABASE_STORAGE_BUCKET', 'narsis-private'),
    ],
];
