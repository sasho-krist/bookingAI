<?php

return [
    'api_key' => env('OPENAI_API_KEY'),
    'organization' => env('OPENAI_ORGANIZATION'),
    'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
    'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
    'verify_ssl' => env('OPENAI_VERIFY_SSL', true),
    'ca_file' => env('OPENAI_CA_FILE'),
];
