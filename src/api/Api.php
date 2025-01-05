<?php

namespace src\api;

use src\App;
use src\components\Entry;
use src\helpers\FileHelper;
use src\helpers\ResultHelper;
use src\helpers\UserHelper;

/**
 * @author Tim Zapfe
 * @date 19.11.2024
 */
class Api
{
    /**
     * @var array
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 21.12.2024
     */
    protected array $page;

    /**
     * @var array|mixed
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 21.12.2024
     */
    protected array $request;

    /**
     * @var Entry
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 21.12.2024
     */
    protected Entry $entry;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->page = App::getPage();
        $this->request = App::getRequest();
        $this->entry = new Entry();
    }

    /**
     * Run the api.
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 21.12.2024
     */
    public function run(): void
    {
        // get first key
        $controller = ucfirst(strtolower(App::getRequest([0, 'controller'], 'main')));

        // check if controller exists
        if (!FileHelper::exist(DS . 'src' . DS . 'api' . DS . 'controllers' . DS . $controller . '.php')) {
            ResultHelper::render([
                'message' => 'Could not find any valid controller.',
            ], 404, [
                'translate' => true
            ]);
        }

        // new class
        $class = 'src\\api\\controllers\\' . $controller;

        if (!class_exists($class)) {
            ResultHelper::render([
                'message' => 'Could not find any valid class in controller.',
            ], 404, [
                'translate' => true
            ]);
        }

        // create method name
        $action = App::getRequest([1, 'action']);

        // action given?
        if (empty($action)) {
            ResultHelper::render([
                'message' => 'Invalid action.',
            ], 404, [
                'translate' => true
            ]);
        }

        // create instance
        $instance = new $class();
        $methods = get_class_methods($instance);
        $action = 'action' . $this->convertToPascalCase($action);

        // check if action exist
        if (!in_array($action, $methods)) {
            ResultHelper::render([
                'message' => 'Either your method does not exist or is private.',
            ], 404, [
                'translate' => true
            ]);
        }

        // class function
        $result = $instance->$action();

        // the action may already exit's at some point, but if not, we have to return the result.
        ResultHelper::render($result);
    }

    /**
     * Converts a string with - to pascal case.
     * @param string $input
     * @return string
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 24.12.2024
     */
    private function convertToPascalCase(string $input): string
    {
        // explode by -
        $parts = explode('-', $input);

        // convert to pascal case
        $parts = array_map(function ($part) {
            return ucfirst(strtolower($part));
        }, $parts);

        // add all together
        return implode('', $parts);
    }

    /**
     * #################################
     * ###### PROTECTED FUNCTIONS ######
     * #################################
     */

    /**
     * Requires a logged-in user and returns it.
     * @return array
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 21.12.2024
     */
    protected function requireUser(): array
    {
        // check if steam64ID is given
        $steam64ID = App::getRequest(['steam64ID']);

        if (empty($steam64ID)) {
            ResultHelper::render([
                'message' => 'Could not find a valid steam64ID.',
            ], 403);
        }

        // check if token exists in users
        $user = UserHelper::getUserBySteam64ID($steam64ID);

        if (empty($user)) {
            ResultHelper::render([
                'message' => 'Could not find a valid user.',
            ], 403);
        }

        // set user to session
        $_SESSION['user'] = $user;

        // set token to cookies
        setcookie('steam64ID', $steam64ID, time() + (86400 * 30), "/");

        return $user;
    }

    /**
     * Requires the page token.
     * @return string
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 21.12.2024
     */
    protected function requireToken(): string
    {
        // get token
        $token = App::getRequest('token');

        // token empty or not the same?
        if (empty($token) || $token !== App::getToken()) {

            // display error
            ResultHelper::render([
                'message' => 'Invalid token provided. Please reload the page.'
            ], 403, [
                'translate' => true
            ]);
        }

        return $token;
    }
}