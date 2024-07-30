<?php
return [
    'disks' => [
        'gcs' => [
            'driver' => 'gcs',
            'project_id' => env('GOOGLE_CLOUD_PROJECT_ID', 'your-project-id'),
            'key_file' => [
                'type' => 'service_account',
                'private_key_id' => env('GOOGLE_CLOUD_PRIVATE_KEY_ID'),
                'private_key' => env('GOOGLE_CLOUD_PRIVATE_KEY'),
                'client_email' => env('GOOGLE_CLOUD_CLIENT_EMAIL'),
                'client_id' => env('GOOGLE_CLOUD_CLIENT_ID'),
                'auth_uri' => env('GOOGLE_CLOUD_AUTH_URI'),
                'token_uri' => env('GOOGLE_CLOUD_TOKEN_URI'),
                'auth_provider_x509_cert_url' => env('GOOGLE_CLOUD_AUTH_PROVIDER_CERT_URL'),
                'client_x509_cert_url' => env('GOOGLE_CLOUD_CLIENT_CERT_URL'),
            ],
            'bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET', 'your-bucket'),
        ]
    ]
];
