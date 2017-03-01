<?php

$config = [
    'homeUrl'=>'/',
    'components' => [
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'httpClient' => [
                'transport' => 'yii\httpclient\CurlTransport',
            ],
            'clients' => [
                'google' => [
                    'class' => 'yii\authclient\clients\Google',
                    'clientId'     => '', //oauth_google_clientId,
                    'clientSecret' => '', //oauth_google_clientSecret,
                ],
            ],
        ],
        'request' => [
//            'baseUrl'=>'/',
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
        'urlManager' => [
//            'baseUrl'=>'/',
        ]
    ],
];


if (YII_DEBUG) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
         'allowedIPs' => ['*'],
    ];
}
if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
         'allowedIPs' => ['*'],
    ];
}

return $config;
