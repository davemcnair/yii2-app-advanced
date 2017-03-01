<?php
namespace common\components;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\AdminUser;
use yii\web\BadRequestHttpException;
use yii\helpers\StringHelper;
use yii\web\Response;
use yii\base\Model;

class AbstractController extends Controller
{
    const BACKGROUND=true;
    const INLINE=false;

    public $jsFile;

    public function init() {
        parent::init();

        $path='@app/views/' . $this->id . '/ajax.js';
        if (file_exists(\Yii::getAlias($path))){
            $this->jsFile = $path;

            // Publish and register the required JS file
            Yii::$app->assetManager->publish($this->jsFile);
            $this->getView()->registerJsFile(
                Yii::$app->assetManager->getPublishedUrl($this->jsFile),
                ['yii\web\YiiAsset'] // depends
            );
        }
    }


    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@','admin'],
                        'matchCallback' => function ($rule, $action) {
                            $role=AdminUser::roleFor(Yii::$app->user->identity->email);
                            return in_array($role,$rule->roles);
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index'  => ['get'],
                    'view'   => ['get'],
                    'create' => ['get', 'post'],
                    'update' => ['post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $user=Yii::$app->user->identity;
        if (StringHelper::startsWith($action->id,'ajax-'))  {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (Yii::$app->request->isAjax === false){
                throw new BadRequestHttpException();
            }
        }
        if (StringHelper::startsWith($action->id,'dev-'))  {
            if ($user && !$user->group->is('dev')){
                throw new BadRequestHttpException();
            }
        }
        return parent::beforeAction($action);
    }

    public function success($verb='done', $action=''){
        return $this->stringifyAction($action).' '.$verb;
    }

    public function error($message='', $action=''){
        if ($message instanceof Model){
            $message=$this->stringifyErrors($message);
        }
        return $this->stringifyAction($action).' error'.($message?', '.$message:'');
    }

    protected function stringifyAction($action=''){
        $ajaxless=StringHelper::startsWith($this->action->id, 'ajax')?substr($this->action->id,5):$this->action->id;
        return ucfirst($this->id).' '.($action?$action:$ajaxless);
    }

    public function catchException($ex, $pkModel, $formModel=null){
        $message=$this->action->id.' failed, model pk: '.$pkModel->primaryKey.' '.$ex->getMessage();
        Yii::error($message, 'application');
        $pkAttr=$pkModel->primaryKey()[0];
        if ($formModel){
            $formModel->addError($pkAttr, $message);
        } else {
            $pkModel->addError($pkAttr, $message);
        }
    }

    public function stringifyErrors($model){
        $msg='';
        foreach($model->errors as $errors){
            foreach($errors as $err){
             $msg.=$err.'<br>';
            }
        }
        return $msg;
    }

    public function ajaxErrorResponse($model){
        $response=['status'=>'error','error'=>$this->stringifyErrors($model)];
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }
}
