<?php
use kartik\datecontrol\Module;
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'modules' => [
        'datecontrol' =>  [
            'class' => '\kartik\datecontrol\Module',
            'displayTimezone'=>'Europe/London',
            'displaySettings' => [
                Module::FORMAT_DATE => 'dd/MM/yyyy',
                Module::FORMAT_TIME => 'hh:mm a',
                Module::FORMAT_DATETIME => 'dd/MM/yyyy hh:mm a',
            ],
            // format settings for saving each date attribute (PHP format example)
            'saveSettings' => [
                Module::FORMAT_DATE => 'php:U', // saves as unix timestamp
                Module::FORMAT_TIME => 'php:U',
                Module::FORMAT_DATETIME => 'php:U',
            ]
        ]
    ],
    'components' => [
        'formatter' => [
            'locale'=>'en-GB',
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'php:j M Y',
            'datetimeFormat' => 'php:d/m/Y H:i:s',
            'timeFormat' => 'php:H:i:s',
            'currencyCode' => 'GBP'
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ]
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager' => [
           'class' => 'yii\rbac\DbManager', // or use PhpManager
        ],
        'user' => [
            'class' => 'mdm\admin\models\User', // before rbac migration
//            'class' => 'yii\web\User', // after rbac migration
            'identityClass' => 'mdm\admin\models\User',
        ],
        'as access' => [
           'class' => 'mdm\admin\components\AccessControl',
            'allowActions' => [
                'site/*',
            ],
        ],
    ],
];
