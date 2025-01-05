<?php
/**
 * @author Tim Zapfe
 * @copyright Tim Zapfe
 * @date 20.11.2024
 */

namespace src\services\twig\web;

use src\App;
use src\helpers\BaseHelper;
use src\services\Router;

/**
 * SiteModule class
 * @author Tim Zapfe
 * @date 21.11.2024
 */
class SiteModule
{
    /**
     * Updates the config.
     * @return void
     * @author Tim Zapfe
     * @date 25.11.2024
     */
    public static function includeDevServer(): void
    {
        $port = App::getConfig()->swapperSettings['devServerPort'];
        $url = App::getConfig()->swapperSettings['devServer'];
        $full = $url . ':' . $port . '/';

        if (!App::getConfig()->useViteDev) {
            // Get the webpack
            $path = App::getAlias('assetPath') . BaseHelper::ensureWithCharacter('/webpack/site.umd.cjs', '/');

            if (!App::getConfig()->cacheAssets) {
                $path = BaseHelper::getUrlWithTimestamp($path);
            }

            // Include the webpack JavaScript
            echo '<script type="application/javascript" src="' . $path . '"></script>';

        } else {
            // Include dev server scripts
            echo '<script type="module" src="' . $full . '@vite/client"></script>';
            echo '<script type="module" src="' . $full . 'src/main.ts"></script>';
            echo '<script type="module" src="' . $full . '@vite-plugin-checker-runtime-entry"></script>';
        }

    }

    /**
     * Includes the window.Site content for the js.
     * @return void
     * @author Tim Zapfe
     * @date 25.11.2024
     */
    public static function includeSiteModule(): void
    {
        echo '<script>window.Site = ' . json_encode(self::getContent()) . '</script>';
    }

    /**
     * Returns the content for the javascript.
     * @return array
     * @author Tim Zapfe
     * @date 21.11.2024
     */
    public static function getContent(): array
    {
        return [
            'baseUrl'     => BaseHelper::getUrl(),
            'title'       => $_ENV['SITE_NAME'],
            'language'    => $_SESSION['language'],
            'environment' => $_ENV['ENVIRONMENT'],
            'loggedIn'    => BaseHelper::isLoggedIn(),
            'token'       => App::getToken(),
            'useSwapper'  => App::getConfig()->useSwapper,
            'swapper'     => App::getConfig()->swapperSettings,
            'entry'       => App::getPage(),
            'routes'      => Router::$routes
        ];
    }
}