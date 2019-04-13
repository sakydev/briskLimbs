<?php

/**
* Name: Limbs
* Description: Core class that works as a bridge between PHP and HTML and handles Twig etc
* @author: Saqib Razzaq
* @since: v1, Feburary, 2019
* @link: https://github.com/briskLimbs/briskLimbs/blob/master/model/Limbs.php
*/

class Limbs {
	/*
	* Holds global database object
	*/
	public $database;
	
	/*
	* Holds Twig object
	*/
	public $twig;

	/*
	* Holds settings object
	*/
	public $settings;

	/*
	* Holds template parameters for Twig
	*/
	public $templateParameters;

	/*
	* Holds addon parameters for Twig
	*/
	public $addonParameters;
	public $addonTriggers;

	/*
	* Holds Errors object
	*/
	public $errors;

	/*
	* Holds Twig loader
	*/
	public $loader;

	/*
	* Holds coreUrl of website
	*/
	public $coreUrl;

	/*
	* Holds theme settings
	*/
	public $themeSettings;

	function __construct($database) {
		$this->database = $database;
		$this->templateParameters = array();
		$this->addonParameters = array();

		$this->initialize();
	}

	/*
	* Intializes required variables and makes ready for other methods
	*/
	public function initialize() {
		$this->settings = new Settings($this->database);
		$this->coreUrl = $this->settings->get('core_url');
		$this->themeSettings = (object) $this->getThemeSettings();

		$loader = new Twig_Loader_Filesystem(IS_ADMIN ? $this->themeSettings->_adminSkeletonDirectory : $this->themeSettings->_skeletonDirectory);
		$twig = new Twig_Environment($loader);

		$this->loader = $loader;
		$this->twig = $twig;
		$this->errors = new Errors();

		define('CORE_URL', $this->coreUrl);
		define('ADDONS_URL', CORE_URL . '/addons');
		define('MEDIA_URL', CORE_URL . '/media');

		$this->initializeCustomFunctions();
	}

	/**
	* Fetch all theme related settings
	* @return: { array }
	*/
	public function getThemeSettings() {
		$name = $this->settings->get('active_theme');
		$adminName = $this->settings->get('admin_theme');
		$url = $this->coreUrl . '/skins/' . $name;
		$adminUrl = $this->coreUrl . '/admin/skins/' . $name;
		$directory = SKINS_DIRECTORY . '/' . $name;
		$adminDirectory = ADMIN_SKINS_DIRECTORY . '/' . $adminName;
		$assetsDirectory = $directory . '/assets';
		$assetsUrl = $url .'/assets';

		return  array(
			'_coreUrl' => $this->coreUrl,
			'_adminUrl' => $this->coreUrl . '/admin',
			'_directory' => $directory,
			'_adminDirectory' => $adminDirectory,
			'_skeletonDirectory' => $directory . '/skeleton',
			'_adminSkeletonDirectory' => $adminDirectory . '/skeleton',
			'_skeletonUrl' => $url . '/skeleton',
			'_assetsDirectory' => $assetsDirectory,
			'_assetsUrl' => $assetsUrl,
			'_jsDirectory' => $assetsDirectory . '/js',
			'_jsUrl' => $assetsUrl . '/js',
			'_cssDirectory' => $assetsDirectory . '/css',
			'_cssUrl' => $assetsUrl . '/css',
			'_imagesDirectory' => $assetsDirectory . '/images',
			'_imagesUrl' => $assetsUrl . '/images',
			'_mediaUrl' => $this->coreUrl . '/media'
		);
	}

	/**
	* Builds title to be used on each page
	* @param: { $params } { array } { list of parameters to be sent to HTML }
	* @return: { string }
	*/
	public function buildTitle($params) {
		$title = empty($params['_title']) ? 'Untitled' : $params['_title'];
		return $title . ' ' . $this->settings->get('title_separator') . ' ' . $this->settings->get('title');
	}

	/**
	* Builds parameters by merging from all different arrays
	* @param: { $parameters } { array } { basic list of params }
	* @return: { array }
	*/
	public function buildParameters($parameters) {
		$params = array_merge(
			(array)$this->themeSettings, 
			array('settings' => $this->settings->fetch()),
			$parameters, 
			$this->collectTemplateParamters()
		);

		$params['_addons'] = $this->collectAddonParamters();
		$params['_triggers'] = $this->collectAddonTriggers();
		$params['_title'] = $this->buildTitle($parameters);
		return $params;
	}

	/**
	* Displays a page
	* @param: { $page } { string } { name of page to display }
	* @param: { $parameters } { array } { parameters for this page }
	*/
	public function display($page, $parameters) {
		$this->twig->display($page, $this->buildParameters($parameters));
	}

	/**
	* Displays an error page
	* @param: { $parameters } { array } { parameters for this page }
	* @param: { $message } { string } { error message for this page }
	*/
	public function displayErrorPage($parameters, $message) {
		$parameters['messages'] = array($message);
		$this->display('blank.html', $parameters);
		exit;
	}

	/**
	* Load required php file for page
	* @param: { $page } { string } { name of page to load } 
	*/
	public function stretch($page) {
		$addons = new Addons();
		$addons->load();

		$themeDirectory = IS_ADMIN ? $this->themeSettings->_adminDirectory : $this->themeSettings->_directory;
		$fullPath = $themeDirectory . '/' . $page . '.php';
		$notFound = $themeDirectory . '/error_404.php';
		if (file_exists($fullPath)) {
			require($fullPath);
		} elseif (empty($page)) {
			require($themeDirectory . '/index.php');
		} else {
			require($notFound);
		}
	}

	/**
	* Add a new template parameter
	* @param: { $parameter } { string } { name of parameter }
	* @param: { $value } { string } { value of parameter }
	* @return: { boolean }
	*/
	public function addTemplateParameter($parameter, $value) {
		return $this->templateParameters[$parameter] = $value;
	}

	/**
	* Get value of a template parameter
	* @param: { $parameter } { string } { name of parameter }
	* @return: { mixed }
	*/
	public function getTemplateParameter($parameter) {
		return isset($this->templateParameters[$parameter]) ? $this->templateParameters[$parameter] : false;
	}

	/**
	* Get all template parameters
	* @return: { array }
	*/
	public function collectTemplateParamters() {
		return $this->templateParameters;
	}

	/**
	* Adds an addon parameter
	* @param: { $parameter } { string } { name of parameter }
	* @param: { $valie } { string } { value of parameter }
	* @return: { boolean }
	*/
	public function addAddonParameter($parameter, $value) {
		return $this->addonParameters[$parameter] = $value;
	}

	/**
	* Get an addon parameter
	* @param: { $parameter } { string } { name of parameter }
	* @return: { mixed }
	*/
	public function getAddonParameter($parameter) {
		return isset($this->addonParameters[$parameter]) ? $this->addonParameters[$parameter] : false;
	}

	/**
	* Fetch all addon parameters
	* @return: { array }
	*/
	public function collectAddonParamters() {
		return $this->addonParameters;
	}

		/**
	* Adds an addon trigger
	* @param: { $parameter } { string } { name of parameter }
	* @param: { $valie } { string } { value of parameter }
	* @return: { boolean }
	*/
	public function addAddonTrigger($parameter, $value, $return = false) {
		return $this->addonTriggers[$value][] = $return ? array($parameter => $return) : $parameter;
	}

	/**
	* Get an addon trigger
	* @param: { $parameter } { string } { name of parameter }
	* @return: { mixed }
	*/
	public function getAddonTrigger($parameter) {
		return isset($this->addonTriggers[$parameter]) ? $this->addonTriggers[$parameter] : false;
	}

	/**
	* Fetch all addon triggers
	* @return: { array }
	*/
	public function collectAddonTriggers() {
		return $this->addonTriggers;
	}

	/**
	* Intializes custom functions to be access in Twig templates
	* @return: { true }
	*/
	private function initializeCustomFunctions() {
		$durationHook = new \Twig\TwigFunction('formatDuration', function ($seconds) {
		  echo formatDuration($seconds);
	  });

	  $dateHook = new \Twig\TwigFunction('formatDate', function ($date) {
		  echo formatDate($date);
	  });

	  $printHook = new \Twig\TwigFunction('pr', function ($array) {
		  pr($array);
	  });

	  $printHookExit = new \Twig\TwigFunction('pex', function ($array) {
		  pex($array);
	  });

	  $printHookExit = new \Twig\TwigFunction('addonInstalled', function ($name) {
		  $addons = new Addons();
		  return $addons->active(array('name' => $name));
	  });
	  
	  $hookable = new \Twig\TwigFunction('hookable', function ($function) {
		  return hookable($function);
	  });

	  $hook = new \Twig\TwigFunction('hook', function ($function, $parameters = false) {
		  hook($function, $parameters);
	  });
		
		$this->twig->addFunction($durationHook);
		$this->twig->addFunction($dateHook);
	  $this->twig->addFunction($printHook);
	  $this->twig->addFunction($printHookExit);
	  $this->twig->addFunction($hookable);
	  $this->twig->addFunction($hook);

		return true;
	}

	/**
	* Adds a custom directory to search template files in
	* @param: { $path } { string } { path of directory }
	* @param: { $name } { string } { name of directory }
	* @return: { boolean }
	*/
	public function addDirectory($path, $name) {
		return $this->loader->addPath($path, $name);
	}
}