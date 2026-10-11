<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supabase Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration values for connecting to Supabase API and Storage.
    |
    */

    'url' => env('SUPABASE_URL', ''),

    'key' => env('SUPABASE_KEY', env('SUPABASE_SERVICE_ROLE_KEY', env('SUPABASE_ANON_KEY', ''))),

    'bucket' => env('SUPABASE_STORAGE_BUCKET', 'games'),
];
