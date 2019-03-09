<?php

class Limbs {
	public $twig;
	public $settings;
	public $templateParameters;
	public $addonParameters;

	function __construct($database) {
		$this->database = $database;
		$this->templateParameters = array();
		$this->addonParameters = array();
	}

	public function initialize() {
		$this->settings = new Settings($this->database);
		$this->settings->initialize();

		$this->coreUrl = $this->settings->get('core_url');
		$this->themeSettings = (object) $this->getThemeSettings();

		$loader = new Twig_Loader_Filesystem(IS_ADMIN ? $this->themeSettings->_adminSkeletonDirectory : $this->themeSettings->_skeletonDirectory);
		$twig = new Twig_Environment($loader);

		$this->loader = $loader;
		$this->twig = $twig;
		$this->errors = new Errors();

		define('CORE_URL', $this->coreUrl);
		define('ADDONS_URL', CORE_URL . '/addons');

		$this->initializeCustomFunctions();
	}

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

	public function buildTitle($params) {
		$title = empty($params['_title']) ? 'Untitled' : $params['_title'];
		return $title . ' ' . $this->settings->get('title_separator') . ' ' . $this->settings->get('title');
	}

	public function buildParameters($parameters) {
		$params = array_merge(
			(array)$this->themeSettings, 
			array('settings' => $this->settings->fetch()),
			$parameters, 
			$this->collectTemplateParamters()
		);

		$params['_addons'] = $this->collectAddonParamters();
		$params['_title'] = $this->buildTitle($parameters);
		return $params;
	}

	public function display($page, $parameters) {
		$this->twig->display($page, $this->buildParameters($parameters));
	}

	public function displayErrorPage($parameters, $message) {
		$parameters['messages'] = array($message);
		return $this->display('blank.html', $parameters);
	}

	public function stretch($page) {
		$addons = new Addons();
		$addons->load();

		$themeDirectory = IS_ADMIN ? $this->themeSettings->_adminDirectory : $this->themeSettings->_directory;
		$fullPath = $themeDirectory . '/' . $page . '.php';
		$notFound = $themeDirectory . '/404.php';
		if (file_exists($fullPath)) {
			require($fullPath);
		} elseif (empty($page)) {
			require($themeDirectory . '/index.php');
		} else {
			require($notFound);
		}
	}

	public function addTemplateParameter($parameter, $value) {
		return $this->templateParameters[$parameter] = $value;
	}

	public function getTemplateParameter($parameter) {
		return isset($this->templateParameters[$parameter]) ? $this->templateParameters[$parameter] : false;
	}

	public function collectTemplateParamters() {
		return $this->templateParameters;
	}

	public function addAddonParameter($parameter, $value) {
		return $this->addonParameters[$parameter] = $value;
	}

	public function getAddonParameter($parameter) {
		return isset($this->addonParameters[$parameter]) ? $this->addonParameters[$parameter] : false;
	}

	public function collectAddonParamters() {
		return $this->addonParameters;
	}

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
	  
	  $hook = new \Twig\TwigFunction('hook', function ($function, $parameters = false) {
		  hook($function, $parameters);
	  });
		
		$this->twig->addFunction($durationHook);
		$this->twig->addFunction($dateHook);
	  $this->twig->addFunction($printHook);
	  $this->twig->addFunction($printHookExit);
	  $this->twig->addFunction($hook);

		return true;
	}

	public function addDirectory($path, $name) {
		return $this->loader->addPath($path, $name);
	}
}