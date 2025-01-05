<?php

namespace src\api\controllers;

use Exception;
use JetBrains\PhpStorm\NoReturn;
use src\api\Api;
use src\App;
use src\helpers\BaseHelper;
use src\helpers\ResultHelper;
use src\services\Router;
use src\services\twig\Template;

/**
 * @author Tim Zapfe
 * @date 16.12.2024
 */
class Swapper extends Api
{
    /**
     * Returns the html
     */
    public function actionGet(): void
    {
        $this->requireToken();

        // default values
        $defaultPage = 'home';
        $entry = App::getPage($defaultPage);

        // create slug parts from given page
        $requestSlug = BaseHelper::ensureWithCharacter(App::getRequest(['slug'], $defaultPage), '/', '/');
        $slugParts = array_values(array_filter(explode('/', $requestSlug)));

        // extract page and params
        $page = strtolower($slugParts[0] ?? $defaultPage);
        $params = $this->parseRoute($requestSlug, array_slice($slugParts, 1));

        // rewrite routes
        $routeRewrites = App::getConfig()->routeRewrites;
        $page = $routeRewrites[$page] ?? $page;

        // is homepage?
        if ($page === '/') {
            $page = $defaultPage;
        }

        // valid page?
        $validPages = array_column(Router::$pages, null, 'name');
        if (isset($validPages[$page])) {
            $entry = $validPages[$page];
        }

        // get the template
        $template = new Template();
        try {
            $html = $template->getHtml($page, [
                'entry'  => $entry,
                'params' => $params,
                'slug'   => $requestSlug,
            ]);
        } catch (Exception $e) {
            ResultHelper::render([
                'message' => $e->getMessage(),
            ], 500);
        }

        ResultHelper::render([
            'message' => 'HTML rendered.',
            'content' => [
                'entry' => $entry,
                'html'  => $html
            ],
        ]);
    }

    /**
     * Returns updates params
     * @param string $url
     * @param array $params
     * @return array|null
     * @author Tim Zapfe
     * @date 17.12.2024
     */
    private function parseRoute(string $url, array $params): ?array
    {
        $url = rtrim($url, '/');

        foreach (Router::$routes as $route => $value) {
            $routePattern = preg_replace('/{\w+}/', '(?P<$0>[^/]+)', $route);
            $routePattern = str_replace(['{', '}'], '', $routePattern);
            $routePattern = '~^' . rtrim($routePattern, '/') . '$~';

            if (preg_match($routePattern, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (!is_int($key)) {
                        $params[$key] = $match;
                    }
                }

                return array_filter($params, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);
            }
        }

        return [];
    }
}
