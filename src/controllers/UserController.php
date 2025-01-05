<?php
/**
 * @author Tim Zapfe
 * @copyright Tim Zapfe
 * @date 24.12.2024
 */

namespace src\controllers;

use src\App;
use src\components\Entry;
use src\helpers\ResultHelper;
use src\helpers\UserHelper;

class UserController
{
    /**
     * Checks if a steam64ID exists in cookies and if yes then login the user and save in session if valid token.
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 24.12.2024
     */
    public function autoLogin(): void
    {
        // check if steam64ID exists in cookies
        if (!empty($_COOKIE['steam64ID'])) {
            $steam64ID = $_COOKIE['steam64ID'];

            $user = UserHelper::getUserBySteam64ID($steam64ID);

            if (!empty($user)) {
                $_SESSION['user'] = $user;
            }
        }
    }

    /**
     * Trys to log in the user with given username and password.
     * @param string $username
     * @param string $password
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 24.12.2024
     */
    public function login(string $username, string $password): void
    {
        $entry = UserHelper::getUserQuery();

        // return error if username does not exist
        if (!UserHelper::checkIfUsernameExists($username)) {
            ResultHelper::render([
                'message' => App::t('Username {username} not found.', 'login', [
                    'username' => $username
                ])
            ], 404);
        }

        // get user's password (new entry query for extra security)
        $passwordEntry = new Entry();
        $passwordRow = $passwordEntry->columns(['users' => ['password']])->tables('users')->where(['users' => [['username', $username], ['active', true]]])->one();

        // return error if "$passwordRow" not found or password is not correct.
        if (empty($passwordRow) || !password_verify($password, $passwordRow['password'])) {
            ResultHelper::render([
                'message' => 'Your entered password is not correct!'
            ], 403, [
                'translate' => true
            ]);
        }

        // fetch the user
        $user = $entry->one();

        // save in session
        $_SESSION['user'] = $user;
        $_SESSION['steam64ID'] = $user['steam64ID'];

        // and cookies
        setcookie('steam64ID', $user['steam64ID'], time() + (86400 * 30), "/");

        // return the user
        ResultHelper::render([
            'message' => App::t('Welcome back, {username}', 'login', [
                'username' => $username
            ]),
            'user'    => $user
        ]);
    }

    /**
     * Try to register a new user with given username/password
     * @param string $username
     * @param string $password
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 24.12.2024
     */
    public function register(string $username, string $password): void
    {
        // return error if username does not exist
        if (UserHelper::checkIfUsernameExists($username)) {
            ResultHelper::render([
                'message' => App::t('Username {username} already exists.', 'register', [
                    'username' => $username
                ])
            ], 400);
        }

        // new entry to inset new rows.
        $entry = new Entry();

        // create avatar
        $avatarID = $entry->insert('images', [
            'src' => 'default.png'
        ], false);

        // avatar success?
        if (!is_numeric($avatarID)) {
            ResultHelper::render([
                'message' => 'There was en error while creating your avatar.'
            ], 500, [
                'translate' => true
            ]);
        }

        // create user
        $userID = $entry->insert('users', [
            'username'  => $username,
            'password'  => password_hash($password, PASSWORD_DEFAULT),
            'steam64ID' => UserHelper::generateUserToken(),
            'snowflake' => UserHelper::generateUserSnowflake($username),
            'avatar_id' => $avatarID,
        ], true);

        // user success?
        if (!is_numeric($userID)) {
            ResultHelper::render([
                'message' => 'There was an error while creating your profile.'
            ], 500, [
                'translate' => true
            ]);
        }

        // create user settings
        $userSettingsID = $entry->insert('user_settings', [
            'user_id' => $userID
        ]);

        // userSettings success?
        if (!is_numeric($userSettingsID)) {
            ResultHelper::render([
                'message' => 'There was an error while creating the settings for your profile.'
            ], 500, [
                'translate' => true
            ]);
        }

        // create user tachograph
        $tachoID = $entry->insert('tachographs', [
            'user_id' => $userID,
        ]);

        // tacho success?
        if (!is_numeric($tachoID)) {
            ResultHelper::render([
                'message' => 'There was an error while creating the tachograph for your profile.'
            ], 500, [
                'translate' => true
            ]);
        }

        // get the user
        $user = UserHelper::getUserQuery()->where(['users' => [['username', $username]]])->one();

        // save in session
        $_SESSION['user'] = $user;
        $_SESSION['steam64ID'] = $user['steam64ID'];

        // and in session
        setcookie('steam64ID', $user['steam64ID'], time() + (86400 * 30), "/");

        // return as success
        ResultHelper::render([
            'message' => App::t('Nice to meet you, {username}', 'register', [
                'username' => $username
            ]),
            'user'    => $user
        ]);
    }
}