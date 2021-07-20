<?php

return [
    'paymongo_public_key' => env('PAYMONGO_PUBLIC_KEY', null),
    'paymongo_secret_key' => env('PAYMONGO_SECRET_KEY', null),
    'gcash_redirect_success' => env('GCASH_SUCCESS_REDIRECT', null),
    'gcash__redirect_fail' => env('GCASH_FAIL_REDIRECT', null),
    'app_url' => env('APP_URL', null)
];
