<?php
/**
 * @author Tim Zapfe
 * @copyright Tim Zapfe
 * @date 23.12.2024
 */

namespace src\api\controllers;

use src\api\Api;
use src\App;
use src\controllers\UserController;
use src\helpers\ResultHelper;
use src\helpers\UserHelper;

class Users extends Api
{

    /**
     * Return all users.
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 24.12.2024
     */
    public function actionAll(): void
    {
        // get all users
        $users = UserHelper::getUserQuery()->all();

        ResultHelper::render($users);
    }

    /**
     * Logic to log in a user by their username/password.
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 24.12.2024
     */
    public function actionLogin(): void
    {
        // get username and password
        $username = App::getRequest([2, 'username']);
        $password = App::getRequest([3, 'password']);

        // username found?
        if (empty($username)) {
            ResultHelper::render([
                'message' => 'Could not find a valid username.'
            ], 404, [
                'translate' => true
            ]);
        }

        // password found?
        if (empty($password)) {
            ResultHelper::render([
                'message' => 'Could not find a valid password.'
            ], 404, [
                'translate' => true
            ]);
        }

        $User = new UserController();
        $User->login($username, $password);
    }

    /**
     * Logic to register a new user by their username/password/password_repeat
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 24.12.2024
     */
    public function actionRegister(): void
    {
        // get username and password
        $username = App::getRequest([2, 'username']);
        $password = App::getRequest([3, 'password']);
        $passwordRepeat = App::getRequest([4, 'password_repeat']);

        // username found?
        if (empty($username)) {
            ResultHelper::render([
                'message' => 'Could not find a valid username.'
            ], 404, [
                'translate' => true
            ]);
        }

        // password found?
        if (empty($password)) {
            ResultHelper::render([
                'message' => 'Could not find a valid password.'
            ], 404, [
                'translate' => true
            ]);
        }

        // password found?
        if (empty($passwordRepeat)) {
            ResultHelper::render([
                'message' => 'Could not find a valid repeated password.'
            ], 404, [
                'translate' => true
            ]);
        }

        if ($password !== $passwordRepeat) {
            ResultHelper::render([
                'message' => 'The passwords did not match.'
            ], 400, [
                'translate' => true
            ]);
        }

        $User = new UserController();
        $User->register($username, $password);
    }

    /**
     * Returns information for a username/password when registering.
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 24.12.2024
     */
    public function actionRegisterLiveCheck(): void
    {
        // get username
        $username = App::getRequest([2, 'username']);
        $password = App::getRequest([3, 'password']);

        ResultHelper::render([
            'message'   => '/',
            'snowflake' => UserHelper::generateUserSnowflake($username)
        ]);
    }
}