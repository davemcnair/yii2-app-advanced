<?php
namespace backend\tests;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    public function loginAs($role){
        switch ($role){
            case 'devAdmin';
                $this->login(
                   'dev',
                   \Yii::$app->params['devPassword']
                );
        }
    }

    public function login($username, $password)
    {
        $I = $this;
         $I->amOnPage('/user/login');
        // if snapshot exists - skipping login
        // doesnt owrk
        try {
            if ($I->loadSessionSnapshot('login')) {
                return;
            }
        } catch(UnableToSetCookieException $e){
            // doesnt work on phantomjs as of 22/5/17
            return;
        }
        // logging in
        $I->amOnPage('user/login');
        $I->submitForm('#login-form', [
            'Login[username]' => $username,
            'Login[password]' => $password
        ]);
        $I->wait(1);
        $I->see($username, '.navbar-nav');
        // saving snapshot
        $I->saveSessionSnapshot('login');
    }
}
