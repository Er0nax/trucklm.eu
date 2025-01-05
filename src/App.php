<?php

namespace src;

use Random\RandomException;
use src\components\Config;
use src\controllers\UserController;
use src\helpers\BaseHelper;
use src\helpers\FileHelper;
use src\services\Database;
use src\services\Router;
use src\services\Translation;

/**
 * @author Tim Zapfe
 * @date 07.11.2024
 */
class App
{
    private static Database $database;
    private static Translation $translation;
    private static Config $config;

    /**
     * Constructor and start of the app.
     * @throws RandomException
     */
    public function __construct()
    {
        // set config
        $this->loadConfig();
        $this->loadLanguage();

        // init services
        self::$database = new Database();
        self::$translation = new Translation();

        // try to log in the user
        $User = new UserController();
        $User->autoLogin();
    }

    /**
     * Load the config and set it to app config instance.
     * @return void
     * @author Tim Zapfe
     * @date 15.11.2024
     */
    private function loadConfig(): void
    {
        $environment = $_ENV['ENVIRONMENT'] ?? 'production';

        $Config = new Config();
        $config = FileHelper::getFileInclude('/config/general.php');

        // config loaded?
        if (is_array($config)) {
            // load global config first
            if (!empty($config['*'])) {
                foreach ($config['*'] as $key => $value) {
                    if (isset($Config->$key)) {
                        if (!is_object($Config->$key)) {
                            // set value
                            $Config->$key = $value;
                        }
                    }
                }
            }

            // replace values by environment
            if (!empty($config[$environment])) {
                foreach ($config[$environment] as $key => $value) {
                    if (isset($Config->$key)) {
                        if (!is_object($Config->$key)) {
                            $Config->$key = $value;
                        }
                    }
                }
            }
        }

        // set config object
        self::$config = $Config;
    }

    /**
     * Loads a language and sets it to the session.
     * @return void
     * @author Tim Zapfe
     * @date 19.11.2024
     */
    private function loadLanguage(): void
    {
        // given by session?
        if (!empty($_SESSION['language'])) {
            // value language?
            if (in_array($_SESSION['language'], array_keys(self::getConfig()->languages))) {
                // set in cookie
                BaseHelper::setCookie('language', $_SESSION['language'], 30);
                return;
            }
        }

        // given by cookie?
        if (!empty($_COOKIE['language'])) {
            if (in_array($_COOKIE['language'], array_keys(self::getConfig()->languages))) {
                // set in session and cookie (renew)
                $_SESSION['language'] = $_COOKIE['language'];
                BaseHelper::setCookie('language', $_COOKIE['language'], 30);
                return;
            }
        }

        // given by env?
        if (!empty($_ENV['LANGUAGE'])) {
            if (in_array($_ENV['LANGUAGE'], array_keys(self::getConfig()->languages))) {
                // set in session and cookie
                $_SESSION['language'] = $_ENV['LANGUAGE'];
                BaseHelper::setCookie('language', $_ENV['LANGUAGE'], 30);
                return;
            }
        }

        // set default english
        $_SESSION['language'] = 'en';
    }

    /**
     * #############################
     * ## PUBLIC STATIC FUNCTIONS ##
     * #############################
     */

    /**
     * Returns the Config
     * @return Config
     * @author Tim Zapfe
     * @date 15.11.2024
     */
    public static function getConfig(): Config
    {
        return self::$config;
    }

    /**
     * Returns the Database
     * @return Database
     * @author Tim Zapfe
     * @date 15.11.2024
     */
    public static function getDatabase(): Database
    {
        return self::$database;
    }

    /**
     * Returns an alias or the default value (null by default)
     * @param string $alias
     * @param string|null $default
     * @return mixed|string|null
     * @author Tim Zapfe
     * @date 15.11.2024
     */
    public static function getAlias(string $alias, string $default = null): mixed
    {
        return self::$config->aliases[$alias] ?? $default;
    }

    /**
     * Returns a translated string.
     * @param string|null $value
     * @param string $category
     * @param array $variables
     * @return string|null
     * @author Tim Zapfe
     * @date 15.11.2024
     */
    public static function t(?string $value, string $category = 'site', array $variables = []): ?string
    {
        return self::$translation::t($value, $category, $variables);
    }

    /**
     * Sets the language iso to the session's language.
     * @param string $iso
     * @return void
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    public static function setLanguage(string $iso): void
    {
        $_SESSION['language'] = $iso;
    }

    /**
     * Returns the value for a key (string|array of keys) from the url or post/get/cookie
     * @param string|array $key
     * @param mixed|null $default
     * @return mixed
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    public static function getRequest(string|array|int $key = null, mixed $default = null): mixed
    {
        return Router::getParamOrPath($key, $default);
    }

    /**
     * Returns the current page.
     * @return array
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    public static function getPage(string $name = null): array
    {
        if (empty($name)) {
            return Router::$page;
        }

        foreach (Router::$pages as $page) {
            if ($page['name'] === $name) {
                return $page;
            }
        }

        return [];
    }

    /**
     * Sets an error in the session.
     * @param int $type
     * @param string $message
     * @return void
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    public static function setError(int $type, string $message): void
    {
        // set in session.
        $_SESSION['error'] = [
            'type'    => $type,
            'message' => $message
        ];
    }

    /**
     * Returns the token.
     * @return string
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 21.12.2024
     */
    public static function getToken(): string
    {
        // token already generated?
        if (!empty($_SESSION['token'])) {
            return $_SESSION['token'];
        }

        // generate token
        $_SESSION['token'] = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 20);

        // return it
        return $_SESSION['token'];
    }
}