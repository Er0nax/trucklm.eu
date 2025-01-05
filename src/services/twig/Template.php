<?php

namespace src\services\twig;

use src\App;
use src\helpers\BaseHelper;
use src\helpers\FileHelper;
use src\helpers\ResultHelper;
use src\services\Router;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

/**
 * @author Tim Zapfe
 * @date 28.11.2024
 */
class Template
{
    /**
     * @var Environment
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    public static Environment $twig;

    /**
     * @var FilesystemLoader
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    public static FilesystemLoader $loader;

    /**
     * Constructor.
     * @param string $type
     */
    public function __construct()
    {
        // get the template path
        $templatePath = FileHelper::get(App::getAlias('templatePath'));

        // new loader
        self::$loader = new FilesystemLoader($templatePath);

        // new twig
        self::$twig = new Environment(self::$loader, [
            'cache' => App::getConfig()->cacheTemplates ? FileHelper::get(App::getAlias('templateCachePath', 'cache')) : false,
            'debug' => App::getConfig()->debug,
        ]);

        // add extensions
        self::$twig->addExtension(new DebugExtension());
        self::$twig->addExtension(new Extension());

        // add globals
        self::$twig->addGlobal('session', $_SESSION);

        self::$twig->addGlobal('currentSite', [
            'url' => BaseHelper::getUrl()
        ]);

        self::$twig->addGlobal('config', [
            'useBootstrap' => App::getConfig()->useBootstrap,
            'useSwapper'   => App::getConfig()->useSwapper,
            'swapper'      => App::getConfig()->swapperSettings,
        ]);
    }

    /**
     * Returns the html for a file.
     * @return string
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    public function getHtml(string $file, array $_context = []): string
    {
        $context = array_merge([
            'entry' => App::getPage(),
            'slug'  => Router::$slug
        ], $_context);

        // get the final slug
        $slug = $context['slug'];

        // todo: hash can be done here with the slug...

        return self::$twig->render($this->getFileToRender($file), $context);
    }

    /**
     * ###########################
     * #### PRIVATE FUNCTIONS ####
     * ###########################
     */

    /**
     * Returns the file which should be rendered.
     * @param string $file
     * @return string
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    private function getFileToRender(string $file): string
    {
        // get the current page...
        $page = App::getPage();
        $fileExistsAsPage = FileHelper::exist('/templates/pages/' . $file . '.twig');
        $fileExistsAsContent = FileHelper::exist('/templates/content/' . $file . '/entry.twig');

        // current page is api?
        if ($page['name'] === 'api' && App::getConfig()->useApi) {

            // does the $file exist?
            if ($fileExistsAsContent) {

                // return the single file directly
                return '/content/' . $file . '/entry.twig';
            }

            // return the error as content
            return '/_error/content/block.twig';
        }

        // current page is homepage?
        if ($page['name'] === 'home') {

            // return the error as content
            return '/pages/home.twig';
        }

        // does the file exist as a page in pages?
        if ($fileExistsAsPage) {

            // return the page directly
            return '/pages/' . $file . '.twig';
        }

        // does the file exist as content??
        if ($fileExistsAsContent) {

            return '/pages/page.twig';
        }

        // it does not exist at all so show error/home
        if (App::getConfig()->useHomepageAsError) {

            // return the homepage
            return '/pages/home.twig';
        }

        // return the error page
        return '/_error/entry.twig';
    }
}