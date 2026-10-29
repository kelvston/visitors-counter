<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'chatbot' => [
        'python_binary' => env(
            'CHATBOT_PYTHON_BINARY',
            base_path(PHP_OS_FAMILY === 'Windows' ? '.venv/Scripts/python.exe' : '.venv/bin/python')
        ),
        'model_path' => env('CHATBOT_NLP_MODEL_PATH', storage_path('app/chatbot-nlp-model')),
        'training_data_path' => storage_path('app/chatbot-training.json'),
    ],

];
