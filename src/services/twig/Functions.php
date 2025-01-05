<?php

namespace src\services\twig;

use src\App;
use src\helpers\BaseHelper;
use src\helpers\FileHelper;
use src\services\Router;
use src\services\twig\web\SiteModule;

/**
 * @author Tim Zapfe
 * @date 19.11.2024
 */
class Functions
{
    /**
     * Returns the image source of an image.
     * @param string $src
     * @param string $type
     * @param array $config
     * @return string
     * @author Tim Zapfe
     * @date 19.11.2024
     */
    public function getImageSource(string $src, string $type, array $config): string
    {
        $path = FileHelper::getImage($src, $type, $config);

        // add timestamp if no cache allowed
        if (!App::getConfig()->cacheAssets) {
            $path = BaseHelper::getUrlWithTimestamp($path);
        }

        return $path;
    }

    /**
     * Returns the alias.
     * @param string $alias
     * @return mixed|string|null
     * @author Tim Zapfe
     * @date 20.11.2024
     */
    public function getAlias(string $alias): mixed
    {
        return App::getAlias($alias);
    }

    /**
     * Returns the full url of an asset which is inside the assets' folder.
     * @param string $src
     * @return string
     * @author Tim Zapfe
     * @date 20.11.2024
     */
    public function getAsset(string $src): string
    {
        $path = App::getAlias('assetPath') . BaseHelper::ensureWithCharacter($src, '/');

        // add timestamp if no cache is allowed
        if (!App::getConfig()->cacheAssets) {
            $path = BaseHelper::getUrlWithTimestamp($path);
        }

        return $path;
    }

    /**
     * Echos the script with the given site entry
     * @return void
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 20.11.2024
     */
    public function includeSiteModule(): void
    {
        SiteModule::includeSiteModule();
        SiteModule::includeDevServer();
    }

    /**
     * Returns the page title.
     * @return string
     * @author Tim Zapfe
     * @copyright Tim Zapfe
     * @date 20.11.2024
     */
    public function getPageTitle(): string
    {
        return App::getPage()['title'] . ' | ' . $_ENV['SITE_NAME'];
    }

    /**
     * Returns array of pages for specific type.
     * @param string $type
     * @return array
     * @author Tim Zapfe
     * @date 29.11.2024
     */
    public function getPages(string $type = null, string $category = null): array
    {
        $pages = [];

        foreach (Router::$pages as $page) {

            // specify category given?
            if (!empty($category) && $category !== $page['category']) {
                continue;
            }

            // show always?
            if ($page['show_always']) {
                // add
                $pages[] = $page;
                continue;
            }

            // must be logged in, but is not logged in?
            if ($page['must_be_logged_in'] === true && !BaseHelper::isLoggedIn()) {
                continue;
            }

            // must not be logged in, but is logged in?
            if ($page['must_be_logged_in'] === false && BaseHelper::isLoggedIn()) {
                continue;
            }

            // is header?
            if ($type === 'header') {

                // hide in header?
                if ($page['hide_in_header']) {
                    continue;
                }

                // add
                $pages[] = $page;
                continue;
            }

            // is footer?
            if ($type === 'footer') {

                // hide in footer?
                if ($page['hide_in_footer']) {
                    continue;
                }

                // add
                $pages[] = $page;
                continue;
            }

            // add page
            $pages[] = $page;
        }

        return $pages;
    }

    /**
     * Returns an env variable.
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     * @author Tim Zapfe
     * @date 29.11.2024
     */
    public function getEnv(string $key, mixed $default = null): mixed
    {
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        return $default;
    }
}