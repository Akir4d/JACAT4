<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['inflector', 'custom_string'];

    // Values to be obtained automatically from router
	protected $mModule = '';			// module name (empty = Frontend Website)
	protected $mCtrler = 'home';		// current controller
	protected $mAction = 'index';		// controller function being called
	protected $mMethod = 'GET';			// HTTP request method

	// Config values from config/jacat.php
	protected $mConfig = array();
	protected $mBaseUrl = array();
	protected $mSiteName = '';
	protected $mMetaData = array();
	protected $mScripts = array();
	protected $mStylesheets = array();

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
	protected $mMenu = array();
	protected $mBreadcrumb = array();

	// Multilingual
	protected $mMultilingual = FALSE;
	protected $mLanguage = 'en';
	protected $mAvailableLanguages = array();

	// Data to pass into views
	protected $mViewData = array();

	// Login user
	protected $mPageAuth = array();
	protected $mUser = NULL;
	protected $mUserGroups = array();
	protected $mUserMainGroup;

	// Constructor
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
		$router = \CodeIgniter\Config\Services::router(); 
		// router info
		$this->mModule = ""; // da implementare
		$this->mCtrler = $router->controllerName();
		$this->mAction = $router->methodName();
		$this->mMethod = $this->request->getServer('REQUEST_METHOD');

		// initial setup
		$this->_setup();
	}

	public function get_idiom() {
		$config['language'] = $this->request->getLocale();
		if ($config['language'] === 'auto') {
		}
		return $config['language'];
	}

	// Setup values from file: config/jacat.php
	private function _setup()
	{
		
		$config = config('Jacat', false)->jacat;
		// load default values
		$this->mBaseUrl = empty($this->mModule) ? base_url() : base_url($this->mModule) . '/';
		
		$this->mSiteName = empty($config['site_name']) ? '' : $config['site_name'];
		$this->mPageTitlePrefix = empty($config['page_title_prefix']) ? '' : $config['page_title_prefix'];
		$this->mPageTitle = empty($config['page_title']) ? '' : $config['page_title'];
		$this->mBodyClass = empty($config['body_class']) ? '' : $config['body_class'];
		$this->mNavbarClass = empty($config['navbar_class']) ? '' : $config['navbar_class'];
		$this->mNavmenuBg = empty($config['navmenu_bg']) ? '' : $config['navmenu_bg'];
		$this->mSideClass = empty($config['side_class']) ? '' : $config['side_class'];
		$this->mAsideClass = empty($config['aside_class']) ? '' : $config['aside_class'];
		$this->mFooterClass = empty($config['footer_class']) ? '' : $config['footer_class'];
		$this->mMenu = empty($config['menu']) ? array() : $config['menu'];
		$this->mMetaData = empty($config['meta_data']) ? array() : $config['meta_data'];
		$this->mScripts = empty($config['scripts']) ? array() : $config['scripts'];
		$this->mStylesheets = empty($config['stylesheets']) ? array() : $config['stylesheets'];
		$this->mPageAuth = empty($config['page_auth']) ? array() : $config['page_auth'];


		// restrict pages
		$uri = ($this->mAction == 'index') ? $this->mCtrler : $this->mCtrler . '/' . $this->mAction;
		if (!empty($this->mPageAuth[$uri]) && !$this->ion_auth->in_group($this->mPageAuth[$uri])) {
			$page_404 = $this->router->routes['404_override'];
			$redirect_url = empty($this->mModule) ? $page_404 : $this->mModule . '/' . $page_404;
			redirect($redirect_url);
		}

		// push first entry to breadcrumb
		if ($this->mCtrler != 'home') {
			$page = $this->mMultilingual ? lang('home') : 'Home';
			$this->push_breadcrumb($page, '');
		}

		// get user data if logged in

		$this->mConfig = $config;
	}

	// Verify user login (regardless of user group)
	protected function verify_login($redirect_url = NULL)
	{
		if (!$this->ion_auth->logged_in()) {
			if ($redirect_url == NULL)
				$redirect_url = $this->mConfig['login_url'];

			redirect($redirect_url);
		}
	}

	// Verify user authentication
	// $group parameter can be name, ID, name array, ID array, or mixed array
	// Reference: http://benedmunds.com/ion_auth/#in_group
	protected function verify_auth($group = 'members', $redirect_url = NULL)
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->in_group($group)) {
			if ($redirect_url == NULL)
				$redirect_url = $this->mConfig['login_url'];

			redirect($redirect_url);
		}
	}

	// Add script files, either append or prepend to $this->mScripts array
	// ($files can be string or string array)
	protected function add_script($files, $append = TRUE, $position = 'foot')
	{
		$files = is_string($files) ? array($files) : $files;
		$position = ($position === 'head' || $position === 'foot') ? $position : 'foot';

		if ($append)
			$this->mScripts[$position] = array_merge($this->mScripts[$position], $files);
		else
			$this->mScripts[$position] = array_merge($files, $this->mScripts[$position]);
	}

	// Add stylesheet files, either append or prepend to $this->mStylesheets array
	// ($files can be string or string array)
	protected function add_stylesheet($files, $append = TRUE, $media = 'screen')
	{
		$files = is_string($files) ? array($files) : $files;

		if ($append)
			$this->mStylesheets[$media] = array_merge($this->mStylesheets[$media], $files);
		else
			$this->mStylesheets[$media] = array_merge($files, $this->mStylesheets[$media]);
	}

	protected function load_inject ($inject, $settings) {
		return $this->load->view('inject/'.$inject, $settings, true);
	}

	// Render template
	protected function render($view_file, $layout = 'default')
	{
		// automatically generate page title
		if (empty($this->mPageTitle)) {
			if ($this->mAction == 'index')
				$this->mPageTitle = humanize($this->mCtrler);
			else
				$this->mPageTitle = humanize($this->mAction);
		}

		$this->mViewData['module'] = $this->mModule;
		$this->mViewData['ctrler'] = $this->mCtrler;
		$this->mViewData['action'] = $this->mAction;

		$this->mViewData['site_name'] = $this->mSiteName;
		$this->mViewData['page_title'] = $this->mPageTitlePrefix . $this->mPageTitle;
		// Usefull to add your custom html
		$this->mViewData['inject_after_page_title'] = $this->mInjectAfterPageTitle;
		$this->mViewData['inject_before_footer'] = $this->mInjectBeforeFooter;

		$this->mViewData['current_uri'] = empty($this->mModule) ? uri_string() : str_replace($this->mModule . '/', '', uri_string());
		$this->mViewData['meta_data'] = $this->mMetaData;
		$this->mViewData['scripts'] = $this->mScripts;
		$this->mViewData['stylesheets'] = $this->mStylesheets;
		$this->mViewData['page_auth'] = $this->mPageAuth;

		$this->mViewData['base_url'] = $this->mBaseUrl;
		$this->mViewData['menu'] = $this->mMenu;
		$this->mViewData['user'] = $this->mUser;
		$this->mViewData['ga_id'] = empty($this->mConfig['ga_id']) ? '' : $this->mConfig['ga_id'];
		$this->mViewData['body_class'] = $this->mBodyClass;
		$this->mViewData['navbar_class'] = $this->mNavbarClass;
		$this->mViewData['navmenu_bg'] = $this->mNavmenuBg;
		$this->mViewData['side_class'] = $this->mSideClass;
		$this->mViewData['aside_class'] = $this->mAsideClass;
		$this->mViewData['footer_class'] = $this->mFooterClass;
		// automatically push current page to last record of breadcrumb
		$this->push_breadcrumb($this->mPageTitle);
		$this->mViewData['breadcrumb'] = $this->mBreadcrumb;

		// multilingual
		$this->mViewData['multilingual'] = $this->mMultilingual;
		if ($this->mMultilingual) {
			$this->mViewData['available_languages'] = $this->mAvailableLanguages;
			$this->mViewData['language'] = $this->mLanguage;
		}

		// debug tools - CodeIgniter profiler
		//$debug_config = $this->mConfig['debug'];
		//if (ENVIRONMENT === 'development' && !empty($debug_config)) {
			//$this->response->enable_profiler($debug_config['profiler']);
		//}
		
		$this->mViewData['inner_view'] = $view_file;
		echo view('_base/head', $this->mViewData);
		echo view('_layouts/' . $layout, $this->mViewData);

		// debug tools - display view data
		//if (ENVIRONMENT === 'development' && !empty($debug_config) && !empty($debug_config['view_data'])) {
			//$this->output->append_output('<hr/>' . print_r($this->mViewData, TRUE));
		//}

		echo view('_base/foot', $this->mViewData);
	}

	// Output JSON string
	protected function render_json($data, $code = 200)
	{
		$this->output
			->set_status_header($code)
			->set_content_type('application/json')
			->set_output(json_encode($data));

		// force output immediately and interrupt other scripts
		global $OUT;
		$OUT->_display();
		exit;
	}

	// Add breadcrumb entry
	// (Link will be disabled when it is the last entry, or URL set as '#')
	protected function push_breadcrumb($name, $url = '#', $append = TRUE)
	{
		$entry = array('name' => $name, 'url' => $url);

		if ($append)
			$this->mBreadcrumb[] = $entry;
		else
			array_unshift($this->mBreadcrumb, $entry);
	}
}
