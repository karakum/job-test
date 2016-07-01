<?php

namespace tests\codeception\unit\models;

use app\models\Users;
use yii\codeception\TestCase;
use Codeception\Specify;

class UsersTest extends TestCase
{
    use Specify;

    protected function setUp()
    {
        parent::setUp();
        // uncomment the following to load fixtures for user table
        //$this->loadFixtures(['user']);
    }

    public function testCreateUser()
    {

        $user = new Users([
            'username' => 'newUser' . md5(time()),
        ]);
        $this->assertTrue($user->save());
        $user->refresh();
        $this->assertEquals('0.00', $user->balance);

    }

    public function testValidateEmptyUsername()
    {
        $model = new Users([
            'username' => '',
        ]);
        expect('model is not valid', $model->validate())->false();
        expect('username is incorrect', $model->errors)->hasKey('username');
    }

    public function testValidateTooLongUsername()
    {
        $model = new Users([
            'username' => 'too_long_username_________________________________.',
        ]);
        expect('model is not valid', $model->validate())->false();
        expect('username too long', $model->errors)->hasKey('username');
    }

}
