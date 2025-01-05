<?php

namespace src\services;

use src\api\Api;
use src\App;
use src\components\Entry;
use src\helpers\BaseHelper;
use src\helpers\FileHelper;
use src\helpers\ResultHelper;
use src\services\twig\Template;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Router Service
 * @author Tim Zapfe
 * @date 26.11.2024
 */
class Router
{
    public static string $route = '/';
    public static string $slug = '/';
    public static array $routes = [];
    public static array $params = [];
    public static array $paths = [];
    public static array $pages = [];
    public static array $page = [];

    /**
     * ######################
     * ## PUBLIC FUNCTIONS ##
     * ######################
     */

    /**
     * Constructor
     */
    public function __construct()
    {
        // set all information
        $this->setParams();
        $this->setPaths();
        $this->setRoutes();
        $this->setPages();
    }

    /**
     * Run the router.
     * @return void
     * @author Tim Zapfe
     * @date 26.11.2024
     */
    public function run(): void
    {
        $this->rewriteRoute();

        // Loop through all routes
        foreach (self::$routes as $route => $page) {

            // exists as content or file?
            if (!FileHelper::existsAsPageOrContentInTemplates($page)) {
                continue;
            }

            // Convert route keys with placeholders into regex patterns
            $pattern = preg_replace('#\{[^/]+\}#', '[^/]+', $route);

            // Add start (^) and end ($) delimiters to ensure full match
            $pattern = '#^' . $pattern . '$#';

            // Check if the current route matches the pattern
            if (preg_match($pattern, self::$route)) {

                // Check if the page exists in self::$pages
                if ($this->setPage($page)) {

                    // update route
                    self::$route = $route;
                    break;
                }
            }
        }

        // rename keys for paths
        $this->modifyPathsWithRoute();

        // api given? if yes => we will end the code here anyway
        $this->handleApiEnvironment();

        // If no route matched, handle fallback
        if (empty(self::$page)) {
            // use homepage as error page?
            if (App::getConfig()->useHomepageAsError) {
                // set homepage
                $this->setPage('home', true);
            } else {
                // set error page
                $this->setPage('error', true);
            }
        }

        // at this point in code, we have a page as the entry point and its no api call.

        // new template
        $template = new Template();
        try {
            echo $template->getHtml(self::$page['name'], [
                'params' => App::getRequest(),
            ]);
        } catch (LoaderError $e) {
            echo 'Could not load template. LoaderError: ' . $e->getMessage();
        } catch (RuntimeError $e) {
            echo 'Could not load template. RuntimeError: ' . $e->getMessage();
        } catch (SyntaxError $e) {
            echo 'Could not load template. SyntaxError: ' . $e->getMessage();
        }
    }

    /**
     * Returns the value for a given key inside paths and params.
     * If keys both exist in paths and params, it will return paths (form url) value first.
     * If not set, it will return the default value (default is null).
     * @param int|string|array|null $key Can be an array of possible keys. The first matching key that exists will be returned.
     * @param mixed|null $default Which is null by default.
     * @return mixed|null
     * @author Tim Zapfe
     * @date 26.11.2024
     */
    public static function getParamOrPath(int|string|array $key = null, mixed $default = null): mixed
    {
        // $key null?
        if ($key === null) {

            // simply return all paths and params
            return array_merge(self::$params, self::$paths);
        }

        // is $key an array of possible params?
        if (is_array($key)) {

            // loop through keys
            foreach ($key as $k) {

                // does key exist in paths?
                if (isset(self::$paths[$k])) {

                    // return path
                    return self::$paths[$k];
                }

                // does key exist in params?
                if (isset(self::$params[$k])) {

                    // return params
                    return self::$params[$k];
                }

                // does exist in custom header?
                if (isset($_SERVER['HTTP_' . $k])) {

                    // return the value
                    return $_SERVER['HTTP_' . $k];
                }

                // does exist in session?
                if (isset($_SESSION[$k])) {
                    return $_SESSION[$k];
                }

                // in file_get_contents('php://input')
                $content = file_get_contents('php://input');

                if (is_string($content)) {
                    $json = json_decode($content, true);

                    if (isset($json[$k])) {
                        return $json[$k];
                    }
                }
            }

            return $default;
        }

        // does key exist in paths?
        if (isset(self::$paths[$key])) {

            // return path
            return self::$paths[$key];
        }

        // does key exists in params?
        if (isset(self::$params[$key])) {

            // return the value
            return self::$params[$key];
        }

        // does exist in custom header?
        if (isset($_SERVER['HTTP_' . $key])) {

            // return the value
            return $_SERVER['HTTP_' . $key];
        }

        // does exist in session?
        if (isset($_SESSION[$key])) {

            // return the value
            return $_SESSION[$key];
        }

        // in file_get_contents('php://input')
        $content = file_get_contents('php://input');

        if (is_string($content)) {
            $json = json_decode($content, true);

            if (isset($json[$key])) {
                return $json[$key];
            }
        }

        return $default;
    }

    /**
     * #######################
     * ## PRIVATE FUNCTIONS ##
     * #######################
     */

    /**
     * sets all params from get, post and files.
     * @return void
     * @author Tim Zapfe
     * @date 26.11.2024
     */
    private function setParams(): void
    {
        $params = [];

        // add gets
        foreach ($_GET as $key => $value) {
            $params[$key] = $value;
        }

        // add params
        foreach ($_POST as $key => $value) {
            $params[$key] = $value;
        }

        // add files
        foreach ($_FILES as $key => $value) {
            $params['files'][$key] = $value;
        }

        self::$params = $params;
    }

    /**
     * Sets all paths to static property.
     * @return void
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    private function setPaths(): void
    {
        $url = $this->getRoute();
        self::$slug = $url;

        // make array
        $paths = $this->modifyPathsLanguage(array_values(array_filter(explode('/', $url))));

        // save paths
        self::$paths = $paths;
    }

    /**
     * Returns the current route.
     * @return string
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    private function getRoute(): string
    {
        $baseUrl = BaseHelper::getUrl();
        $fullUrl = BaseHelper::getUrl(true);

        // remove baseUrl from fullUrl
        $url = str_replace($baseUrl, '', $fullUrl);

        // does get params exist?
        if (str_contains($url, '?')) {

            // remove everything behind ?
            $url = substr($url, 0, strpos($url, '?'));
        }

        // return as well...
        return BaseHelper::ensureWithCharacter($url, '/', '/');
    }

    /**
     * Sets all routes to static property.
     * @return void
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    private function setRoutes(): void
    {
        // get all routes from db
        $entry = new Entry();

        $routes = $entry->columns(['routes' => ['*']])->tables(['routes'])->where(['routes' => [['active', true]]])->all();

        // loop through pages
        foreach ($routes as $route) {

            // add slash at the start and end...
            $routePath = BaseHelper::ensureWithCharacter($route['route'], '/', '/');

            // add page to routes
            self::$routes[$routePath] = $route['page'];
        }
    }

    /**
     * Sets all routes to static property.
     * @return void
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    private function setPages(): void
    {
        // get all routes from db
        $entry = new Entry();

        $pages = $entry->columns(['pages' => ['*']])->tables(['pages'])->where(['pages' => [['active', true]]])->all();

        // loop through pages
        foreach ($pages as $page) {

            // translate headline and subline
            $page['headline'] = App::t($page['headline'], 'page');
            $page['subline'] = App::t($page['subline'], 'page');

            // add page to pages
            self::$pages[$page['name']] = array_merge(App::getConfig()->defaultPageConfig, $page);
        }
    }

    /**
     * Returns all paths with language removed if exists.
     * @param array $paths
     * @return array
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    private function modifyPathsLanguage(array $paths): array
    {

        // get available languages
        $languages = App::getConfig()->languages;

        // set current route
        self::$route = $this->getRoute();

        if (App::getConfig()->useLanguagesInUrl) {
            // loop through paths
            foreach ($paths as $key => $path) {

                // contains language?
                if (in_array($path, array_keys($languages))) {

                    // set language
                    App::setLanguage($path);

                    // unset key from paths
                    unset($paths[$key]);

                    // remove iso code from route
                    self::$route = str_replace('/' . $path, '', self::$route);
                }
            }
        }

        // return re-indexed paths
        return array_values($paths);
    }

    /**
     * Sets the page to static property and returns boolean on success.
     * @param string $page
     * @param bool $throw
     * @return bool
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    private function setPage(string $page, bool $throw = false): bool
    {
        if (!empty(self::$pages[$page])) {
            self::$page = self::$pages[$page];

            return true;
        }

        if ($throw) {
            ResultHelper::render([
                'message' => App::t('There was an error while loading the page! Could not find "{page}" in the database.', 'app', [
                    'page' => $page
                ])
            ], 404);
        }

        return false;
    }

    /**
     * Renames the path keys by given route key names.
     * @return void
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    private function modifyPathsWithRoute(): void
    {
        self::$paths = [];

        // Remove leading and trailing slashes from the pattern
        $pattern = trim(self::$route, '/');

        // Split the pattern into its parts using "/"
        $keys = explode('/', $pattern);

        // first param is always the page (0, 1, 2, so 0 is the first)
        if (empty($keys[0])) {
            // display homepage as error?
            if (App::getConfig()->useHomepageAsError) {
                self::$paths['page'] = 'home';
            } else {
                self::$paths['page'] = 'error';
            }
        } else {
            self::$paths['page'] = $keys[0];
        }

        // unset first 3 params
        unset($keys[0]);

        // add every other as param
        foreach ($keys as $key) {
            self::$paths[] = $key;
        }
    }

    /**
     * Creates a new api environment if page is "api".
     * @return void
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    private function handleApiEnvironment(): void
    {
        // is page api?
        if (!empty(self::$paths['page']) && self::$paths['page'] === 'api' && App::getConfig()->useApi) {

            $this->setPage('api', true);

            // new api
            $api = new Api();
            $api->run();

            exit;
        }
    }

    /**
     * Rewrites the current route if matching route in settings.
     * @return void
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    private function rewriteRoute(): void
    {
        // loop through all rewrites
        foreach (App::getConfig()->routeRewrites as $from => $to) {

            // exists as key (from) in rewrites?
            if (BaseHelper::ensureWithCharacter($from, '/', '/') === self::$route) {

                // should be homepage?
                if ($to === '/') {
                    // set as homepage
                    self::$route = $to;
                    continue;
                }

                // rewrite current route
                self::$route = BaseHelper::ensureWithCharacter($to, '/', '/');
            }
        }
    }
}