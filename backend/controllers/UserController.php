<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use mdm\admin\controllers\UserController as BaseUserController;
use common\components\AuthHandler;

class UserController extends BaseUserController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
//                            'signup',
                            'auth',
                            'reset-password',
                            'login',
                            'request-password-reset'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
//                    [
//                        'actions' => [
//                            'index',
//                            'view',
//                            'delete',
//                            'activate'
//                        ],
//                        'allow' => true,
//                        'roles' => ['Admin'],
//                    ],
                    [
                        'actions' => [
                            'logout',
                            'change-password',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'logout' => ['post'],
                    'activate' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function onAuthSuccess($client)
    {
        (new AuthHandler($client))->handle();
    }

}