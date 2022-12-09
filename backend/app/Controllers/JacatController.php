<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\RequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Class AopBaseController
 *
 * AopBaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers with Aop fetures on!
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class JacatController extends BaseController
{
    
    protected $styles = [];
    protected $headFooter = ['head' => ['row' => [], 'auto' => []], 'footer' => ['row' => [], 'auto' => []]];

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['inflector', 'custom_string'];

    // Config values from config/jacat.php
    protected $mConfig = [];
	protected $mBaseUrl = [];
	protected $mSiteName = '';
	protected $mMetaData = [];
	protected $mScripts = [];
	protected $mStylesheets = [];

    // Values to be obtained automatically from router

	protected $mCtrler = 'home';		// current controller
	protected $mAction = 'index';		// controller function being called
	protected $mMethod = 'GET';			// HTTP request method

   // Values and objects to be overrided or accessible from child controllers
	protected $mPageTitlePrefix = '';
	protected $mPageTitle = '';
	protected $mInjectAfterPageTitle = '';
	protected $mInjectBeforeFooter = '';
	protected $mBodyClass = '';
	protected $mNavbarClass = '';
	protected $mNavmenuBg = '';
	protected $mSideClass = '';
	protected $mAsideClass = '';
	protected $mFooterClass = '';
	protected $mMenu = [];
	protected $mBreadcrumb = [];

    // Data to pass into views
	protected $mViewData = [];

	// Login user
	protected $mPageAuth = [];
	protected $mUser = NULL;
	protected $mUserGroups = [];
	protected $mUserMainGroup;


    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
        //Check DB
        $this->checkDb();
		$router = \CodeIgniter\Config\Services::router(); 
		// router info
		$this->mCtrler = $router->controllerName();
		$this->mAction = $router->methodName();
		$this->mMethod = $this->request->getServer('REQUEST_METHOD');
        $this->_setup();
		// initial setup
	}

    /**
     * Add Styles to head 
     *
     * @param [type] $value type 
     * @param string $type
     * @return void
     */
    protected function addStyles($value)
    {
        $uniq = md5($value);
        $this->styles[$uniq] = $value;
    }

    /**
     * Adds Header and Footer stuff!
     *
     * @param string $value
     * @param string $type
     * @param string $position
     * @return void
     */
    protected function addHeadFooter(string $value, string $type = "auto", string $position = "footer")
    {
        $uniq = md5($value);
        switch ($type) {
            case 'row':
                $this->headFooter[$position]['row'][$uniq] = $value;
                break;
            case 'auto':
                $this->headFooter[$position]['auto'][$uniq] = $value;
                break;
        }
    }

    /**
     * Check db is an optional feature that check and populate db
     *
     * @return object
     */
    protected function checkDb(): object
    {
        $migrate = null;
        $status = (object) ['message' => '', 'error' => false, 'debug' => ''];
        try {
            $migrate = \Config\Services::migrations();
        } catch (\Throwable $e) {
            $status->message = "Db Connection Error";
            $status->error = true;
            $status->debug = $e;
        }
        if ($migrate !== null) {
            try {
                $migrate->latest();
            } catch (\Throwable $e) {
                $status->message = "Db Update Error";
                $status->error = true;
                $status->debug = $e;
            }
        }
        return $status;
    }

    protected function renderJson(
        array $responseBody,
        int $code = ResponseInterface::HTTP_OK
    )
    {
        return $this
            ->response
                ->setStatusCode($code)
            ->setJSON($responseBody);
    }

    /**
     * Just a common function aopRender and aopModularize
     *
     * @param string $module
     * @param string $returnPath
     * @param array $arguments
     * @return object
     */
    private function aopPreRender(string $module, string $returnPath, array $arguments = []): object
    {
        $config = new \Config\Aop();
        $du = $config->develCi;
        $file = FCPATH . "amodules/$module/index.html";
        $moduleData = "";
        $args = "";
        if (!empty($arguments)) {
            foreach ($arguments as $k => $v) {
                $v = (!(is_string($v) || is_numeric($v))) ? htmlspecialchars(json_encode($v)) : $v;

                $args .= is_numeric($v) ? ' data-' . $k . '=' . $v : ' data-' . $k . '="' . $v . '"';
            }
        }
        if (substr(base_url(), 0, strlen($du)) == $du) {
            $du = $config->develAn;
            $file = $du . '/index.html';
            $moduleData = preg_replace('/<base.*?>/m', '<base href="' . $du . '"' . $args . '/>', file_get_contents($file));
        } else {
            $du = base_url($returnPath) . '/';
            $moduleData = preg_replace('/<base.*?>/m', '<base href="' . $du . '"' . $args . '/>', file_get_contents($file));
        }
        $seg = str_replace(base_url($returnPath), '', base_url($this->request->getPath()));
        $file = 'amodules/' . $module . $seg;
        //echo $file;
        //die();
        if (is_file(FCPATH . $file)) {
            return (object) ['type' => 'redirect', 'file' => $file];
        } else {
            return (object) ['type' => 'module', 'data' => $moduleData];
        }
    }

    /**
     * aopRender renders an angular module in its entirety, with no additional CSS or JS injections.
     *
     * @param string $module Name of module
     * @param string $returnPath Controller path or routing, for example: api/users
     * @param array  $arguments Array of arguments to pass to Angular. 
     *               All arguments can be accessed from within the Angular app using 
     *               document.getElementsByTagName("base")[0].dataset["key"] or document.getElementsByTagName("base")[0].dataset?.key
     * 
     * @return string | \CodeIgniter\HTTP\RedirectResponse
     */
    protected function aopRender(string $module, string $returnPath, array $arguments = []): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $pre = $this->aopPreRender($module, $returnPath, $arguments);
        if ($pre->type == 'redirect') {
            return redirect()->to(base_url($pre->file));
        } else {
            return $pre->data;
        }
    }

     /**
      * A White-list for given origins 
      *
      * @param mixed $allowed_domains list of origins you want to white-list
      * @param mixed $forceAll allow from ALL, DO NOT USE IN PRODUCTION!
      * @return void
      */
    protected function allowCors($allowed_domains=[], $forceAll=false)
    {
        if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
            $origin = $_SERVER['HTTP_ORIGIN'];
        } else if (array_key_exists('HTTP_REFERER', $_SERVER)) {
            $origin = $_SERVER['HTTP_REFERER'];
        } else {
            $origin = $_SERVER['REMOTE_ADDR'];
        }

        if ($forceAll || in_array($origin, $allowed_domains)) {
            header('Access-Control-Allow-Origin: ' . $origin);
        }

        header("Access-Control-Allow-Headers: Origin, X-API-KEY, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Headers, Authorization, observe, enctype, Content-Length, X-Csrf-Token");
        header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, PATCH, OPTIONS");
        header("Access-Control-Allow-Credentials: true");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            header("HTTP/1.1 200 OK");
            die();
        }

    }

    /**
     * Make Angualer page integrable inside a Codeigniter page!
     *
     * @param string $module Name of module
     * @param string $returnPath Controller path or routing, for example: api/users
     * @param array  $arguments Array of arguments to pass to Angular. 
     *               All arguments can be accessed from within the Angular app using 
     *               document.getElementsByTagName("base")[0]. getAttribute("name")
     * @return void
     */
    protected function aopModularize(string $module, string $returnPath, array $arguments = []): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $pre = $this->aopPreRender($module, $returnPath, $arguments);
        if ($pre->type == 'redirect') {
            return redirect()->to(base_url($pre->file));
        } else {
            $file = $pre->data;
            $matches = [];
            preg_match('/<body.*?>(.*?)<\/body>/s', $file, $matches);
            // Print the entire match result
            $body = $matches[1];
            $matches = [];
            preg_match('/<head.*?>(.*?)<\/head>/s', $file, $matches);
            // Print the entire match result
            $head = $matches[1];
            // Destructure Angular header
            $doc = new \DOMDocument();
            $doc->loadHTML('<html><head>' . $head . '</head></html>');
            foreach ($doc->getElementsByTagName('base') as $node) {
                $val = $doc->saveXML($node);
                $this->addHeadFooter($val, 'row', 'head');
            }
            foreach ($doc->getElementsByTagName('link') as $node) {
                $val = $doc->saveXML($node);
                if (strpos($val, 'rel="icon"') == false)
                    echo $val . PHP_EOL;
                $this->addHeadFooter($val, 'row', 'head');
            }
            foreach ($doc->getElementsByTagName('script') as $node) {
                $val = $doc->saveXML($node);
                $this->addHeadFooter($val, 'row', 'head');
            }
            foreach ($doc->getElementsByTagName('noscript') as $node) {
                $val = $doc->saveXML($node);
                $this->addHeadFooter($val, 'row', 'head');
            }
            foreach ($doc->getElementsByTagName('style') as $node) {
                $val = $doc->saveXML($node);
                $this->addHeadFooter($val, 'row', 'head');
            }

            // Return Body content
            return $body;
        }
    }

    // Add breadcrumb entry
	// (Link will be disabled when it is the last entry, or URL set as '#')
	protected function pushBreadcrumb($name, $url = '#', $append = TRUE)
	{
		$entry = ['name' => $name, 'url' => $url];

		if ($append)
			$this->mBreadcrumb[] = $entry;
		else
			array_unshift($this->mBreadcrumb, $entry);
	}

    private function _setup()
	{
		
		$config = new \Config\Jacat();
		// load default values
		$this->mBaseUrl = base_url() . '/';
		$this->mSiteName = $config->siteName;
		$this->mPageTitlePrefix = $config->pageTitlePrefix;
        $this->mPageTitle = $config->pageTitle;
		$this->mBodyClass = $config->bodyClass;
		$this->mNavbarClass = $config->navbarClass;
        $this->mNavmenuBg = $config->navmenuBg;
        $this->mSideClass = $config->sideClass;
		$this->mAsideClass = $config->asideClass;
		$this->mFooterClass = $config->footerClass;
		$this->mMenu = $config->menu;
		$this->mMetaData = $config->metaData;
		$this->mScripts =$config->scripts;
		$this->mStylesheets = $config->styleSheets;
		$this->mPageAuth = $config->pageAuth;


		// restrict pages
        /* Will be changed with 
		$uri = ($this->mAction == 'index') ? $this->mCtrler : $this->mCtrler . '/' . $this->mAction;
		if (!empty($this->mPageAuth[$uri]) && !$this->ion_auth->in_group($this->mPageAuth[$uri])) {
			$page_404 = $this->router->routes['404_override'];
			$redirect_url = empty($this->mModule) ? $page_404 : $this->mModule . '/' . $page_404;
			redirect($redirect_url);
		}
        */

		// get user data if logged in

		$this->mConfig = $config;
	}

    /**
     * Summary of render
     * @param mixed $view_file
     * @param mixed $layout
     * @return string
     */
	protected function render($view_file, $layout = 'default'): string
	{
		// automatically generate page title
		if (empty($this->mPageTitle)) {
			if ($this->mAction == 'index')
				$this->mPageTitle = humanize($this->mCtrler);
			else
				$this->mPageTitle = humanize($this->mAction);
		}

		$this->mViewData['ctrler'] = $this->mCtrler;
		$this->mViewData['action'] = $this->mAction;

		$this->mViewData['site_name'] = $this->mSiteName;
		$this->mViewData['page_title'] = $this->mPageTitlePrefix . $this->mPageTitle;
		// Usefull to add your custom html
		$this->mViewData['inject_after_page_title'] = $this->mInjectAfterPageTitle;
		$this->mViewData['inject_before_footer'] = $this->mInjectBeforeFooter;

		$this->mViewData['current_uri'] = uri_string();
		$this->mViewData['meta_data'] = $this->mMetaData;
		$this->mViewData['scripts'] = $this->mScripts;
		$this->mViewData['stylesheets'] = $this->mStylesheets;
		$this->mViewData['page_auth'] = $this->mPageAuth;
        $this->mViewData['jacat_version'] = $this->mConfig->jacatVersion;
		$this->mViewData['base_url'] = base_url();
		$this->mViewData['menu'] = $this->mMenu;
		$this->mViewData['user'] = $this->mUser;
        $this->mViewData['ga_id'] = $this->mConfig->gaId;
		$this->mViewData['body_class'] = $this->mBodyClass;
		$this->mViewData['navbar_class'] = $this->mNavbarClass;
		$this->mViewData['navmenu_bg'] = $this->mNavmenuBg;
		$this->mViewData['side_class'] = $this->mSideClass;
		$this->mViewData['aside_class'] = $this->mAsideClass;
		$this->mViewData['footer_class'] = $this->mFooterClass;
		// automatically push current page to last record of breadcrumb
		$this->pushBreadcrumb($this->mPageTitle);
		$this->mViewData['breadcrumb'] = $this->mBreadcrumb;

		// multilingual

		// debug tools - CodeIgniter profiler
		//$debug_config = $this->mConfig['debug'];
		//if (ENVIRONMENT === 'development' && !empty($debug_config)) {
			//$this->response->enable_profiler($debug_config['profiler']);
		//}
		
		$this->mViewData['inner_view'] = $view_file;
		$collect = view('_base/head', $this->mViewData);
		$collect .= view('_layouts/' . $layout, $this->mViewData);
		$collect .= view('_base/foot', $this->mViewData);
        return $collect;
	}

}