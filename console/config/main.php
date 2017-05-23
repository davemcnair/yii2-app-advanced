<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
          ],
        'batch' => [
            'class' => 'schmunk42\giiant\commands\BatchController',
            'modelQueryNamespace' => '\\common\\models\\query',
            'useTranslatableBehavior'=>false,
            'enableI18N'=>false,
            'crudIndexGridClass'=>'kartik\grid\GridView',
    //        'crudControllerNamespace' => 'app\\modules\\crud\\controllers',
    //        'crudSearchModelNamespace' => 'app\\modules\\crud\\models\\search',
    //        'crudViewPath' => '@app/modules/crud/views',
            'crudPathPrefix' => '/',
    //        'crudTidyOutput' => true,
    //        'crudAccessFilter' => true,
    //        'crudProviders' => [
    //            'schmunk42\\giiant\\generators\\crud\\providers\\optsProvider',
    //        ],
    //        'tablePrefix' => 'eko_',
            /*'tables' => [
                'app_profile',
            ]*/
        ]
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => $params,
];
