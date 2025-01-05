<?php

namespace src\components;


define('NOW', date('Y-m-d H:i:s'));

/**
 * @author Tim Zapfe
 * @date 15.11.2024
 */
class Config
{
    /**
     * All available languages.
     * @var array|string[]
     * @author Tim Zapfe
     * @date 15.11.2024
     */
    public array $languages = [
        'en' => 'English',
    ];

    /**
     * Boolean, whether something can be debugged or not.
     * @var bool
     * @author Tim Zapfe
     * @date 15.11.2024
     */
    public bool $debug = false;

    /**
     * Boolean, whether not registered routes are redirected to the error or homepage.
     * @var bool
     * @author Tim Zapfe
     * @date 15.11.2024
     */
    public bool $useHomepageAsError = false;

    /**
     * Has all important tables which are needed by the app.
     * Its always:
     * [<name>, <type>, <set>, <boolean null/not null>, <boolean if autoincrement>, <default>, <default ON UPDATE>]
     * @var array|array[]
     * @author Tim Zapfe
     * @date 15.11.2024
     */
    public array $tables = [
        // all translations
        'translations'  => [
            'columns'  => [
                ['id', 'INT', 11, false, true, null, null],
                ['category', 'VARCHAR', 100, false, false, 'site', null],
                ['value', 'VARCHAR', 2000, false, false, null, null],
                ['en', 'VARCHAR', 2000, true, false, null, null],
                ['de', 'VARCHAR', 2000, true, false, null, null],
                ['active', 'ENUM', "'true', 'false'", false, false, 'true', null],
                ['updated_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP'],
                ['created_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', null],
            ],
            'options'  => [
                'primary_key' => 'id'
            ],
            'defaults' => [
                [1, 'page', 'Home', 'Home', 'Home', 'true', NOW, NOW],
                [2, 'page', 'Error', 'Error', 'Fehler', 'true', NOW, NOW],
                [3, 'page', 'Welcome home.', 'Welcome home.', 'Willkommen auf der Startseite.', 'true', NOW, NOW],
                [4, 'page', 'Seems like something was not right.', 'Seems like something was not right.', 'Sieht aus als ob irgendwas nicht richtig funktioniert.', 'true', NOW, NOW],
                [5, 'page', 'Profile', 'Profile', 'Profil', 'true', NOW, NOW],
                [6, 'page', 'Login', 'Login', 'Anmelden', 'true', NOW, NOW],
                [7, 'page', 'Login with an existing account.', 'Login with an existing account.', 'Melde dich mit einem bestehnden Account an.', 'true', NOW, NOW],
                [8, 'page', 'Register', 'Register', 'Registrieren', 'true', NOW, NOW],
                [9, 'page', 'Register a new account.', 'Register a new account.', 'Registriere dich mit deinen Daten.', 'true', NOW, NOW],
                [10, 'page', 'Logout', 'Logout', 'Abmelden', 'true', NOW, NOW],
                [11, 'page', 'Logout of your account.', 'Logout of your account.', 'Melde dich aus deinem Account ab.', 'true', NOW, NOW],
                [12, 'page', 'This is the start of the API. It is still work in progress.', 'This is the start of the API. It is still work in progress.', 'Dies ist der Anfang der API. Sie befindet sich noch in der Entwicklung.', 'true', NOW, NOW],
                [13, 'page', 'There was an error while loading the page! Could not find "{page}" in the database.', 'There was an error while loading the page! Could not find "{page}" in the database.', 'Es ist ein Fehler beim Laden der Seite aufgetreten! Die Seite "{page}" konnte nicht in der Datenbank gefunden werden.', 'true', NOW, NOW],
                [14, 'page', 'This is a profile.', 'This is a profile.', 'Dies ist ein Profil.', 'true', NOW, NOW],
                [15, 'page', 'Looking for some data?', 'Looking for some data?', 'Suchst du nach Informationen?', 'true', NOW, NOW],
                [16, 'page', 'Policy', 'Policy', 'Datenschutz', 'true', NOW, NOW],
                [17, 'page', 'Legal information.', 'Legal information.', 'Rechtliche Informationen.', 'true', NOW, NOW],
            ]
        ],
        'pages'         => [
            'columns'  => [
                ['id', 'INT', 11, false, true, null, null],
                ['index', 'INT', 11, false, false, 0, null],
                ['show_always', 'ENUM', "'true','false'", false, false, 'false', null],
                ['hide_in_header', 'ENUM', "'true','false'", false, false, 'false', null],
                ['hide_in_footer', 'ENUM', "'true','false'", false, false, 'false', null],
                ['must_be_logged_in', 'ENUM', "'true','false', 'both'", false, false, 'both', null],
                ['is_raw_page', 'ENUM', "'true','false'", false, false, 'false', null],
                ['show_preloader', 'ENUM', "'true','false'", false, false, 'false', null],
                ['category', 'ENUM', "'normal','info'", false, false, 'normal', null],
                ['color', 'VARCHAR', 7, false, false, '#000000', null],
                ['icon', 'VARCHAR', 100, false, false, 'circle', null],
                ['name', 'VARCHAR', 100, false, false, null, null],
                ['title', 'VARCHAR', 100, true, false, null, null],
                ['headline', 'VARCHAR', 255, true, false, null, null],
                ['subline', 'VARCHAR', 1000, true, false, null, null],
                ['active', 'ENUM', "'true','false'", false, false, 'true', null],
                ['updated_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP'],
                ['created_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', null],
            ],
            'options'  => [
                'primary_key' => 'id'
            ],
            'defaults' => [
                [1, 1, 'true', 'false', 'false', 'both', 'false', 'false', 'normal', '#000000', 'circle', 'home', 'Home', 'Welcome home.', null, 'true', NOW, NOW],
                [2, 20, 'false', 'false', 'false', 'false', 'false', 'false', 'normal', '#000000', 'door-open', 'login', 'Login', 'Login with an existing account.', null, 'true', NOW, NOW],
                [3, 21, 'false', 'false', 'false', 'false', 'false', 'false', 'normal', '#000000', 'door-open', 'register', 'Register', 'Register a new account.', null, 'true', NOW, NOW],
                [4, 22, 'false', 'false', 'false', 'true', 'false', 'false', 'normal', '#000000', 'door-closed', 'logout', 'Logout', 'Logout of your account.', null, 'true', NOW, NOW],
                [5, 30, 'false', 'false', 'false', 'both', 'false', 'false', 'normal', '#000000', 'user', 'profile', 'Profile', 'This is a profile.', null, 'true', NOW, NOW],
                [6, 80, 'false', 'true', 'false', 'both', 'false', 'false', 'info', '#000000', 'info', 'policy', 'Policy', 'Legal information.', null, 'true', NOW, NOW],
                [7, 99, 'false', 'true', 'false', 'both', 'true', 'false', 'info', '#000000', 'api', 'api', 'Api', 'Looking for some data?', null, 'true', NOW, NOW],
                [8, 100, 'false', 'true', 'true', 'both', 'false', 'false', 'normal', '#000000', 'warning', 'error', 'Error', 'Seems like something was not right.', null, 'true', NOW, NOW],
            ]
        ],
        'api_tokens'    => [
            'columns' => [
                ['id', 'INT', 11, false, true, null, null],
                ['ip', 'INT', 11, false, false, null, null],
                ['token', 'VARCHAR', 255, false, false, null, null],
                ['uses', 'INT', 11, false, false, 0, null],
                ['active', 'ENUM', "'true','false'", false, false, 'true', null],
                ['updated_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP'],
                ['created_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', null],
            ],
            'options' => [
                'primary_key' => 'id'
            ]
        ],
        'api_whitelist' => [
            'columns' => [
                ['id', 'INT', 11, false, true, null, null],
                ['ip', 'INT', 11, false, false, null, null],
                ['active', 'ENUM', "'true','false'", false, false, 'true', null],
                ['updated_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP'],
                ['created_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', null],
            ],
            'options' => [
                'primary_key' => 'id'
            ]
        ],
        'users'         => [
            'columns' => [
                ['id', 'INT', 11, false, true, null, null],
                ['username', 'VARCHAR', 100, false, false, null, null],
                ['snowflake', 'VARCHAR', 100, false, false, null, null],
                ['steam64ID', 'VARCHAR', 20, false, false, null, null],
                ['password', 'VARCHAR', 255, false, false, null, null],
                ['avatar_id', 'INT', 11, false, false, null, null],
                ['role_id', 'INT', 11, false, false, 1, null],
                ['description', 'VARCHAR', 1000, false, false, 'We know nothing yet.', null],
                ['deleted', 'ENUM', "'true','false'", false, false, 'false', null],
                ['active', 'ENUM', "'true','false'", false, false, 'true', null],
                ['updated_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP'],
                ['created_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', null],
            ],
            'options' => [
                'primary_key' => 'id'
            ]
        ],
        'user_settings' => [
            'columns' => [
                ['id', 'INT', 11, false, true, null, null],
                ['user_id', 'INT', 11, false, false, null, null],
                ['language', 'VARCHAR', 2, false, false, 'en', null],
                ['darkmode', 'ENUM', "'true','false'", false, false, 'true', null],
                ['active', 'ENUM', "'true','false'", false, false, 'true', null],
                ['updated_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP'],
                ['created_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', null],
            ],
            'options' => [
                'primary_key' => 'id'
            ]
        ],
        'images'        => [
            'columns' => [
                ['id', 'INT', 11, false, true, null, null],
                ['src', 'VARCHAR', 255, false, false, null, null],
                ['active', 'ENUM', "'true','false'", false, false, 'true', null],
                ['updated_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP'],
                ['created_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', null],
            ],
            'options' => [
                'primary_key' => 'id'
            ]
        ],
        'roles'         => [
            'columns'  => [
                ['id', 'INT', 11, false, true, null, null],
                ['name', 'VARCHAR', 50, false, false, null, null],
                ['color', 'VARCHAR', 7, false, false, '#ffffff', null],
                ['active', 'ENUM', "'true','false'", false, false, 'true', null],
                ['updated_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP'],
                ['created_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', null],
            ],
            'options'  => [
                'primary_key' => 'id'
            ],
            'defaults' => [
                [1, 'User', '#ffffff', 'true', NOW, NOW]
            ]
        ],
        'info'          => [
            'columns'  => [
                ['id', 'INT', 11, false, true, null, null],
                ['version', 'VARCHAR', 5, false, false, '1.0.0', null],
                ['active', 'ENUM', "'true','false'", false, false, 'true', null],
                ['updated_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP'],
                ['created_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', null],
            ],
            'options'  => [
                'primary_key' => 'id'
            ],
            'defaults' => [
                [1, '1.0.0', 'true', NOW, NOW]
            ]
        ],
        'routes'        => [
            'columns'  => [
                ['id', 'INT', 11, false, true, null, null],
                ['route', 'VARCHAR', 255, false, false, null, null],
                ['page', 'VARCHAR', 255, false, false, null, null],
                ['active', 'ENUM', "'true', 'false'", false, false, 'true', null],
                ['updated_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP'],
                ['created_at', 'DATETIME', null, false, false, 'CURRENT_TIMESTAMP', null],
            ],
            'options'  => [
                'primary_key' => 'id'
            ],
            'defaults' => [
                [1, '/', 'home', 'true', NOW, NOW],
                [2, '/login', 'login', 'true', NOW, NOW],
                [3, '/register', 'register', 'true', NOW, NOW],
                [4, '/logout', 'logout', 'true', NOW, NOW],
                [5, '/error', 'error', 'true', NOW, NOW],
                [6, '/error/{type}', 'error', 'true', NOW, NOW],
                [7, '/profile', 'profile', 'true', NOW, NOW],
                [8, '/profile/{id}', 'profile', 'true', NOW, NOW],
                [9, '/profile/{id}/{action}', 'profile', 'true', NOW, NOW],
                [10, '/api', 'api', 'true', NOW, NOW],
            ]
        ],
    ];

    /**
     * Containing all important aliases
     * @var array
     * @author Tim Zapfe
     * @date 15.11.2024
     */
    public array $aliases = [];

    /**
     * Default page entry config.
     * @var array
     * @author Tim Zapfe
     * @date 18.11.2024
     */
    public array $defaultPageConfig = [
        'id'                => null,
        'index'             => null,
        'show_always'       => false,
        'hide_in_header'    => false,
        'hide_in_footer'    => false,
        'must_be_logged_in' => 'both',
        'is_raw_page'       => false,
        'show_preloader'    => false,
        'category'          => 'normal',
        'color'             => '#ffffff',
        'icon'              => 'circle',
        'name'              => null,
        'title'             => null,
        'headline'          => null,
        'subline'           => null,
        'active'            => true,
        'updated_at'        => NOW,
        'created_at'        => NOW,
    ];

    /**
     * Returns array of folders with their name
     * @var array|string[]
     * @author Tim Zapfe
     * @date 19.11.2024
     */
    public array $assetImageFolders = [
        'general' => 'general',
    ];

    /**
     * Boolean whether the app should also use the api or not.
     * @var bool
     * @author Tim Zapfe
     * @date 19.11.2024
     */
    public bool $useApi = true;

    /**
     * Boolean whether languages can be used in  the url or not.
     * @var bool
     * @author Tim Zapfe
     * @date 19.11.2024
     */
    public bool $useLanguagesInUrl = true;

    /**
     * Boolean, whether strings will be translated or not.
     * @var bool
     * @author Tim Zapfe
     * @date 19.11.2024
     */
    public bool $useTranslation = true;

    /**
     * Boolean whether bootstrap should be used or not.
     * @var bool
     * @author Tim Zapfe
     * @date 20.11.2024
     */
    public bool $useBootstrap = true;

    /**
     * Boolean whether twig should use its cache function or not.
     * @var bool
     * @author Tim Zapfe
     * @date 20.11.2024
     */
    public bool $cacheTemplates = true;

    /**
     * Boolean whether assets should be cached in the browser or not. If false, a random string is set behind every asset link.
     * @var bool
     * @author Tim Zapfe
     * @date 20.11.2024
     */
    public bool $cacheAssets = true;

    /**
     * Boolean whether the project should use vite or not.
     * @var bool
     * @author Tim Zapfe
     * @date 20.11.2024
     */
    public bool $useViteDev = true;

    /**
     * Boolean whether the swapper should be used or not.
     * @var bool
     * @author Tim Zapfe
     * @date 21.11.2024
     */
    public bool $useSwapper = true;

    /**
     * Containing all information for the swapper.
     * @var array|string[]
     * @author Tim Zapfe
     * @date 21.11.2024
     */
    public array $swapperSettings = [
        'attributeName' => 'data-page',
        'containerId'   => 'page-content',
        'devServer'     => 'http://localhost',
        'devServerPort' => '5173',
    ];

    /**
     * Array of route rewrites (key redirects to value)
     * @var array|string[]
     * @author Tim Zapfe
     * @date 28.11.2024
     */
    public array $routeRewrites = [
        '/' => 'home'
    ];
}