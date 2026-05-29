<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Groq API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Groq API used by the AI Assistant feature.
    | Get your free API key at https://console.groq.com
    |
    */

    'api_key' => env('GROQ_API_KEY'),

    'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),

    'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),

];
