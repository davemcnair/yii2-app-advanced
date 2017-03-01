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
class FunctionalTester extends \Codeception\Actor
{
    use _generated\FunctionalTesterActions;

    public function loginAs($role){
        switch ($role){
            case 'devAdmin';
                $this->login(
                    'dev',
                     \Yii::$app->params['devPassword']
                );
}
    }

    public function login($name, $password)
    {
        $I = $this;
        $I->amOnPage('/user/login');
        $I->fillField('Username', $name);
        $I->fillField('Password', $password);
        $I->click('login-button');
        $I->wait(1);
//        $I->see('Logout ('.$name.')');
//        $I->dontSeeLink('Login');
    }

}
