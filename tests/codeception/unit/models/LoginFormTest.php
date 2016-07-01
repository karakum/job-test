<?php

namespace tests\codeception\unit\models;

use Yii;
use yii\codeception\TestCase;
use app\models\LoginForm;
use Codeception\Specify;

class LoginFormTest extends TestCase
{
    use Specify;

    protected function tearDown()
    {
        Yii::$app->user->logout();
        parent::tearDown();
    }

    public function testLoginNoUser()
    {
        $model = new LoginForm([
            'username' => 'not_existing_username',
        ]);

        $this->specify('user should be able to auto register and login, when there is no identity', function () use ($model) {
            expect('model should register and login user', $model->login())->true();
            expect('user should be logged in', Yii::$app->user->isGuest)->false();
        });
    }

    public function testLoginCorrect()
    {
        $model = new LoginForm([
            'username' => 'demo',
        ]);

        $this->specify('user should be able to login with correct credentials', function () use ($model) {
            expect('model should login user', $model->login())->true();
            expect('error message should not be set', $model->errors)->hasntKey('password');
            expect('user should be logged in', Yii::$app->user->isGuest)->false();
        });
    }

}
