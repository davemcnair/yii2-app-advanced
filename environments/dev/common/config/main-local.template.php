<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
        ],
//        'db_legacy' => [
//            'class' => 'yii\db\Connection',
//            'dsn' => 'mysql:host=localhost;dbname=-legacy',
//            'username' => 'root',
//            'password' => 'root',
//            'charset' => 'utf8',
//            'tablePrefix'=>'xxx_',
//        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
