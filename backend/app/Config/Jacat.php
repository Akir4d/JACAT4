<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Jacat extends BaseConfig
{
    /**
     * Version of Jacat
     * @var string
     */
    public $jacatVersion = '1.2.1';

    /**
     * Default sitename, 
     * this apply only if dbconfiguration is not found
     * @var string
     */
    public $siteName = 'JACAT';

    /**
     * Default PageTitle
     * @var mixed
     */
    public $pageTitle = '';

    /**
     * Default PageTitle Prefix
     * @var mixed
     */
    public $pageTitlePrefix = '';

    /**
     * Author Metadata
     * @var array
     */
    public $metaData = [
        'author' => '',
        'description' => '',
        'keywords' => ''
    ];

    /**
     * Default Scripts to load
     * @var array
     */
    public $scripts = [
        'head' => [],
        'foot' => [
            '/assets/admin-lte/plugins/jquery/jquery.min.js',
            '/assets/admin-lte/plugins/bootstrap/dist/js/bootstrap.bundle.min.js',
            '/assets/admin-lte/js/adminlte.min.js'
        ]
    ];

    /**
     * Default Styles Sheets to load
     * @var array
     */
    public $styleSheets = [
        'screen' => [
            '/assets/admin-lte/css/adminlte.min.css',
            '/assets/admin-lte/plugins/fontawesome-free/css/all.min.css',
            '/assets/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css',
            '/assets/admin-lte/ion-icons/css/ionicons.min.css'
        ]
    ];
    // Default CSS class for <body> tag
    public $bodyClass = 'sidebar-mini hold-transition layout-top-nav';
    public $navbarClass = 'navbar navbar-expand navbar-dark';

    public $footerClass = "";

    public $navmenuBg = 'bg-dark';

    public $sideClass = '';
    public $asideClass = '';
    public $footerStyle = 'bg-dark';

    // Google Analytics User ID
    public $gaId = '';

    // Menu items
    public $menu = [
        'home' => [
            'name' => 'Home',
            'url' => '',
        ],
    ];

    // Login page
    public $loginUrl = '';

    // Restricted pages
    public $pageAuth = [];

    // Email config
    public $email = [
        'from_email' => '',
        'from_name' => '',
        'subject_prefix' => ''
    ];

    public $debug = [
        'view_data' => FALSE,
        'profiler' => FALSE
    ];

}