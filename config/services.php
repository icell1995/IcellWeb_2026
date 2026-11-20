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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'irsms' => [
        'verify_token_url' => env('IRSMS_VERIFY_TOKEN_URL', 'https://irsms.korlantas.polri.go.id/verify-token'),
        'sso_login_url' => env('IRSMS_SSO_LOGIN_URL', 'https://irsms.korlantas.polri.go.id/sso-login'),
        'total_laka_url' => env('IRSMS_TOTAL_LAKA_URL', 'https://irsms.korlantas.polri.go.id/irsmsapi/api/getTotalLaka'),
        'total_laka_key' => env('IRSMS_TOTAL_LAKA_KEY', 'Hy6d3K1d93LOHRfbeE0KKly1YK9t4YdGsbNDEvyxAYI=icell'),
        'jatanlin_list_url' => env('IRSMS_JATANLIN_LIST_URL', 'https://irsms.korlantas.polri.go.id/irsmsapi/api/getDataJatanlin'),
        'jatanlin_list_key' => env('IRSMS_JATANLIN_LIST_KEY', '9xEVPyQE26KTpo5h6DxwW34m2TkUGnqD243b1i4BNs3C7yg3dL4Ynql3ehRsfZbsQubzRR51BTudsPeMFRtkLBGiennZ0TojmmK9=getDataJatanlin'),
        'jatanlin_detail_url' => env('IRSMS_JATANLIN_DETAIL_URL', 'https://irsms.korlantas.polri.go.id/irsmsapi/api/getDetailJatanlin'),
        'jatanlin_detail_key' => env('IRSMS_JATANLIN_DETAIL_KEY', '3b1i4BNs3C7y1BTudsPeMFRtkLBGiennZ0Tojg3dL4Ynql3ehRsfZbsQubzRR5mmK99xEVPyQE26KTpo5h6DxwW34m2TkUGnqD24=getDetailJatanlin'),
        'dashboard_url' => env('IRSMS_DASHBOARD_URL', 'https://irsms.korlantas.polri.go.id/irsmsapi/api/icell/dashboard'),
        'dashboard_key' => env('IRSMS_DASHBOARD_KEY', '9xEVPyQE26KTpo5h6DxwW34m2TkUGnqD243b1i4BNs3C7yg3dL4Ynql3ehRsfZbsQubzRR51BTudsPeMFRtkLBGiennZ0TojmmK9=icell-dashboard'),
        'total_accident_url' => env('IRSMS_TOTAL_ACCIDENT_URL', 'https://irsms.korlantas.polri.go.id/irsmsapi/api/get_total_accident'),
        'view_key' => env('IRSMS_VIEW_KEY', '09s08e23TBJ1hEXwAMSIH00eBI1F5BODfeLVlHMHnIZrNsDmtS=getdataviewICELL'),
        'selra_key' => env('IRSMS_SELRA_KEY', 'Hy6d3K1d93LOHRfbeE0KKly1YK9t4YdGsbNDEvyxAYI=icell'),
    ],

    'icell_api_tokens' => [
        'emp' => env('ICELL_API_TOKEN_EMP', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiSUNFTEwtRU1QIiwicHJvdmlkZXIiOiJJQ0VMTC1BUEkifQ.EhjiING3v9rX54P3afd29H4TRzV0LlI9t3AHyjiWGKg'),
        'tar' => env('ICELL_API_TOKEN_TAR', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiSUNFTEwtVEFSIiwicHJvdmlkZXIiOiJJQ0VMTC1BUEkifQ.2A2TDuDtWXyNLHiUB4LTPpBC6L5nIlerFI07pEnuQeI'),
        'irsms' => env('ICELL_API_TOKEN_IRSMS', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiSUNFTEwtSVJTTVMiLCJwcm92aWRlciI6IklDRUxMLUFQSSJ9.BEy5KZ6CQqAZgRI6nnwEW-u80WB4zKcO_hJBuABPqWE'),
        'divtik' => env('ICELL_API_TOKEN_DIVTIK', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiSUNFTEwtIiwicHJvdmlkZXIiOiJJQ0VMTC1BUEkifQ.4v3w3pXrX1sY4mJtX5sT8eGf6b0g9yZ1zQ9v62H3Fs=divtik'),
        'sp2hp' => env('ICELL_API_TOKEN_SP2HP', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiSUNFTEwtU1AySFAiLCJwcm92aWRlciI6IklDRUxMLUFQSSJ9.7kZ9mN2pQ8vWxJ4sL3rY6tH5uA1bC0gD9fE2_sp2hp'),
        'dashboard' => env('ICELL_DASHBOARD_API_TOKEN'),
    ],

    'pusiknas' => [
        'api_username' => env('PUSIKNAS_API_USERNAME'),
        'api_password' => env('PUSIKNAS_API_PASSWORD'),
    ],

    'esignature' => [
        'host' => env('ESIGNATURE_API_HOST'),
        'username' => env('ESIGNATURE_API_USERNAME'),
        'password' => env('ESIGNATURE_API_PASSWORD'),
        'token' => env('ESIGNATURE_API_TOKEN'),
    ],

    'whatsapp' => [
        'api_url' => env('WHATSAPP_BOT_API_URL'),
        'app_key' => env('WHATSAPP_BOT_APP_KEY'),
        'auth_key' => env('WHATSAPP_BOT_AUTH_KEY'),
        'templates' => [
            'otp_code' => env('WHATSAPP_BOT_TEMPLATE_ID_OTP_CODE'),
            'doc_ready_to_signed' => env('WHATSAPP_BOT_TEMPLATE_ID_DOC_READY_TO_SIGNED'),
            'doc_validated' => env('WHATSAPP_BOT_TEMPLATE_ID_DOC_VALIDATED'),
            'doc_validate_rejected' => env('WHATSAPP_BOT_TEMPLATE_ID_DOC_VALIDATE_REJECTED'),
        ],
    ],

    'puskarda' => [
        'api_url' => env('PUSKARDA_API_URL', 'https://10.34.17.4/puskarda-main/proses'),
    ],

    'emp' => [
        'api_token' => env('EMP_API_TOKEN', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiSUNFTEwtRU1QIiwicHJvdmlkZXIiOiJJQ0VMTC1BUEkifQ.EhjiING3v9rX54P3afd29H4TRzV0LlI9t3AHyjiWGKg'),
    ],

];
