<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Kafka Brokers
    |--------------------------------------------------------------------------
    | List of Kafka bootstrap brokers separated by comma.
    */
    'brokers' => env('KAFKA_BROKERS', 'localhost:9092'),

    /*
    |--------------------------------------------------------------------------
    | SASL Authentication
    |--------------------------------------------------------------------------
    */
    'sasl' => [
        'username' => env('KAFKA_SASL_USERNAME', ''),
        'password' => env('KAFKA_SASL_PASSWORD', ''),
        'mechanisms' => env('KAFKA_SASL_MECHANISMS', 'PLAIN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Protocol
    |--------------------------------------------------------------------------
    | Supported: SASL_PLAINTEXT, SASL_SSL, PLAINTEXT, SSL
    */
    'securityProtocol' => env('KAFKA_SECURITY_PROTOCOL', 'SASL_PLAINTEXT'),

    /*
    |--------------------------------------------------------------------------
    | Default Topics
    |--------------------------------------------------------------------------
    */
    'topics' => [
        'sppti_kirim' => env('KAFKA_TOPIC_SPPTI_KIRIM', 'icell-sppti-kirim-dev'),
    ],
];
