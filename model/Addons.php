<?php

class Addons {
  
  function __construct() {
    global $limbs;
    $this->limbs = $limbs;
    $this->database = $limbs->database;
    $this->table = 'addons';
    $this->directory = ADDONS_DIRECTORY;
    $this->KEYS = $limbs->database->getColumnsList($this->table);
    $this->errors = new Errors();
  }

  public function idle($skip = array()) {
    $response = array();
    $folders = glob($this->directory . '/*');
    if ($folders) {
      foreach ($folders as $key => $folder) {
        if (!file_exists("{$folder}/plugin.json")) {
          $this->errors->add("skipping $folder because it is missing plugin.json");
          continue;
        }

        $plugin = json_decode(file_get_contents("{$folder}/plugin.json"), true);
        if (!isset($plugin['name']) || !isset($plugin['file']) || !isset($plugin['author'])) {
          $this->errors->add("skipping $folder because it is missing name, file or author");
          continue;
        }

        if ($this->exists($plugin['name'])) { // no error here
          continue;
        }

        $response[$folder] = $plugin;
      }
    }

    return $response;
  }

  public function count($parameters = false) {
    if ($parameters) {
      $parameters['count'] = true;
      return $this->list($parameters);
    } else {
      return $this->database->getValue($this->table, 'count(*)');
    }
  }

  public function list($parameters = false) {
    if (is_array($parameters)) {
      foreach ($parameters as $column => $condition) {
        if (in_array($column, $this->KEYS)) {
          if (is_array($condition)) {
            $this->database->where($column, $condition['0'], $condition['1']);
          } else {
            $this->database->where($column, $condition);
          }
        }
      }
    }

    $limit = isset($parameters['limit']) ? $parameters['limit'] : $this->defaultLimit;
    $sort = isset($parameters['sort']) ? $parameters['sort'] : false;
    if ($sort) {
      if (is_array($sort)) {
        $this->database->orderBy($sort['0'], isset($sort['1']) ? $sort['1'] : 'DESC');
      } else {
        $this->database->orderBy($sort);
      }
    }

    return isset($parameters['count']) ? $this->database->getValue($this->table, 'count(*)') : $this->database->get($this->table, $limit);
  }

  public function exists($name) {
    $this->database->where('name', $name);
    return $this->database->getValue($this->table, 'count(*)');
  }

  public function active($parameters) {
    $parameters['status'] = 'active';
    return $this->list($parameters);
  }

  public function inactive($limit) {
    $parameters['status'] = 'inactive';
    return $this->list($parameters);
  }

  public function install($name) {
    $list = $this->idle();
    foreach ($list as $folder => $addon) {
      if ($addon['name'] == $name) {
        $file = $folder . '/' . $addon['file'];
        $install = $folder . '/install.php';
        if (!file_exists($file)) {
          return $this->errors->add("Skipping $name because main file $file doesn't exist");
        }

        if (!file_exists($install)) {
          return $this->errors->add("Skipping $name because install file doesn't exist at $install");
        }

        require $install;

        $parameter = array();
        $parameters['name'] = $addon['name'];
        $parameters['display_name'] = $addon['display_name'];
        $parameters['description'] = $addon['description'];
        $parameters['file'] = $addon['file'];
        $parameters['author'] = $addon['author'];
        $parameters['version'] = $addon['version'];
        $parameters['directory'] = basename($folder);
        $parameters['status'] = 'active';
        return $this->database->insert($this->table, $parameters);
      }
    }
  }

  public function get($name) {
    $this->database->where('name', $name);
    return $this->database->getOne($this->table);
  }

  public function getField($name, $field) {
    $this->database->where('name', $name);
    $results = $this->database->get($this->table, null, array($field));
    return isset($results['0'][$field]) ? $results['0'][$field] : false;
  }

  public function getFields($name, $fields) {
    $this->database->where('name', $name);
    $results = $this->database->get($this->table, null, is_array($fields) ? $fields : array($fields));
    return isset($results['0']) ? $results['0'] : false;
  }

  public function displayName($name) {
    return $this->getField($name, 'display_name');
  }
  
  public function version($name) {
    return $this->getField($name, 'version');
  }

  public function description($name) {
    return $this->getField($name, 'description');
  }

  public function author($name) {
    return $this->getField($name, 'author');
  }

  public function status($name) {
    return $this->getField($name, 'status');
  }

  public function directory($name) {
    return $this->getField($name, 'directory');
  }

  // set('status', 'successful', 'sad2314', 'vkey');
  public function set($field, $value, $identifierValue, $identifier = 'name') {
    $this->database->where($identifier, $identifierValue);
    return $this->database->update($this->table, array($field => $value));
  }

  // update a single column of multiple videos
  public function bulkSet($field, $value, $identifierValueArray, $identifier = 'name') {
    $this->database->where($identifier, $identifierValueArray, 'IN');
    return $this->database->update($this->table, array($field => $value));
  }
  
  public function activate($video) {
    return $this->set('status', 'active', $video, is_numeric($video) ? 'id' : 'name');
  }

  public function bulkActivate($videosArray, $identifier = 'username') {
    return $this->bulkSet('status', 'active', $videosArray, $identifier);
  }

  public function deactivate($video) {
    return $this->set('status', 'inactive', $video, is_numeric($video) ? 'id' : 'name');
  }

  public function bulkDeactivate($videosArray, $identifier = 'name') {
    return $this->bulkSet('status', 'inactive', $videosArray, $identifier);
  }
  
  public function uninstall($name) {
    $directory = ADDONS_DIRECTORY . '/' . $this->directory($name);
    $file = $directory . '/uninstall.php';
    if (file_exists($file)) {
      require $file;
    }

    $this->database->where('name', $name);
    return $this->database->delete($this->table);
  }

  public function bulkUninstall($names) {
    foreach ($names as $key => $name) {
      if (!$this->uninstall($name)) {
        return false;
      }
    }

    return true;
  }

  public function load() {
    $this->database->where('status', 'active');
    $addons = $this->database->get($this->table, null, array('file', 'directory'));
    foreach ($addons as $key => $addon) {
      $path = ADDONS_DIRECTORY . '/' . $addon['directory'] . '/' . $addon['file'];
      if (file_exists($path)) {
        require $path;
      }
    }
  }

  // name is what you will use in twig template
  // function is what name will call
  public function addHook($name, $function) {
    if (function_exists($function)) {
      $hook = new \Twig\TwigFunction($name, function () use (&$function) {
          $function(func_get_args());
      });

      $this->limbs->twig->addFunction($hook);

      return true;
    }
  }

  public function addMenu($menu) {
    $existing = $this->limbs->getAddonParameter('admin_menu');
    $menu = is_array($existing) ? array_merge($existing, $menu) : $menu;
    $this->limbs->addAddonParameter('admin_menu', $menu);
  }

  public function display($addonCoreDirectoryName, $file, $parameters = array()) {
    $directory = ADDONS_DIRECTORY . '/' . $addonCoreDirectoryName;
    if (($fileDirectory = dirname($file)) != '.') {
      $directory .= '/' . $fileDirectory;
    }

    $this->limbs->addDirectory($directory, $addonCoreDirectoryName);
    $this->limbs->display("@$addonCoreDirectoryName/" . basename($file), $parameters); // @
  }

  public function url($name) {
    return ADDONS_URL . '/' . $name;
  }
}