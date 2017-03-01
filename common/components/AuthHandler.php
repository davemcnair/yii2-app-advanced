<?php
namespace common\components;

use common\models\Auth;
use common\models\User;
use Yii;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;

/**
 * AuthHandler handles successful authentication via Yii auth component
 */
class AuthHandler
{
    /**
     * @var ClientInterface
     */
    private $client;

    const REMEMBER_ME=2592000;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function handle()
    {
        $attributes = $this->client->getUserAttributes();
        $emails = ArrayHelper::getColumn(ArrayHelper::getValue($attributes, 'emails'),'value');
        $email=$emails[0];
        $id = ArrayHelper::getValue($attributes, 'id');
//        $nickname = ArrayHelper::getValue($attributes, 'login');

        /* @var Auth $auth */
        $auth = Auth::find()->where([
            'source' => $this->client->getId(),
            'source_id' => $id,
        ])->one();

        if (Yii::$app->user->isGuest) {
            if ($auth) { // login
                Yii::$app->user->login($auth->user, self::REMEMBER_ME);
            } else { // link if 1st time
                if ($user=User::find()->where(['email' => $email])->one()) {
                    $this->linkAuth($user, $id);
                } else {
                    Yii::$app->getSession()->setFlash('error', [
                        Yii::t('app', 'Unable to link user: {errors}', [
                            'client' => $this->client->getTitle(),
                            'errors' => json_encode('email not registered'),
                        ]),
                    ]);
                }
            }
        }
    }

    private function linkAuth($user, $id){
        $auth = new Auth([
            'user_id' => $user->id,
            'source' => $this->client->getId(),
            'source_id' => (string)$id,
        ]);
        if ($auth->save()) {
            Yii::$app->getSession()->setFlash('success', [
                Yii::t('app', 'Linked {client} account.', [
                    'client' => $this->client->getTitle()
                ]),
            ]);
            Yii::$app->user->login($auth->user, self::REMEMBER_ME);
        } else {
            Yii::$app->getSession()->setFlash('error', [
                Yii::t('app', 'Unable to link {client} account: {errors}', [
                    'client' => $this->client->getTitle(),
                    'errors' => json_encode($auth->getErrors()),
                ]),
            ]);
        }
    }

    /**
     * @param User $user
     */
    private function updateUserInfo(User $user)
    {
        $attributes = $this->client->getUserAttributes();
        $github = ArrayHelper::getValue($attributes, 'login');
        if ($user->github === null && $github) {
            $user->github = $github;
            $user->save();
        }
    }
}

