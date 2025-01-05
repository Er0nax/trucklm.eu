<?php

use src\helpers\BaseHelper;

return [
    // global settings which can be replaced by either dev or production
    '*'          => [
        'aliases'            => [
            'templateCachePath' => '/storage/cache',
            'templatePath'      => '/templates',
            'assetPath'         => BaseHelper::getUrl() . 'assets'
        ],
        'languages'          => [
            'en' => 'English',
            'de' => 'Deutsch',
        ],
        'assetImageFolders'  => [
            'general'    => 'general',
            'avatar'     => 'avatars',
            'tacho' => 'tachograph',
        ],
        'swapperSettings'    => [
            'attributeName' => 'data-page',
            'devServerPort' => '5173',
            'containerId'   => 'page-content',
            'devServer'     => 'http://localhost',
        ],
        'routeRewrites'      => [
            'index' => '/',
            'home'  => '/',
        ],
        'useHomepageAsError' => false,
        'useLanguagesInUrl'  => true,
        'useTranslation'     => true,
        'useBootstrap'       => false,
        'useSwapper'         => true,
        'useApi'             => true,
    ],
    // overwrites * when ENVIRONMENT is dev
    'dev'        => [
        'cacheTemplates' => false,
        'cacheAssets'    => false,
        'useViteDev'     => false,
        'debug'          => true,
    ],
    // overwrites * when ENVIRONMENT is production
    'production' => [
        'cacheTemplates' => true,
        'cacheAssets'    => true,
        'useViteDev'     => false,
        'debug'          => false,
    ]
];