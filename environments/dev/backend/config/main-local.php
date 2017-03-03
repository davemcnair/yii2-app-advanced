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
        'generators' => [
            'giiant-model'=>[
                'class'     => 'schmunk42\giiant\generators\model\Generator',
                'templates' => [
                    'mymodel' => '@app/gii/model/default',
                ]
            ],
            'crud' => [
                'class' => 'backend\gii\crud\Generator',
                'templates' => [
                    'ekoCrud' => '@backend/gii/crud/default',
                ]
            ],
            'model' => [
                'class' => 'backend\gii\model\Generator',
                'templates' => [
                    'ekoModel' => '@backend/gii/model/default',
                ]
            ],
        ],
    ];
}

return $config;
