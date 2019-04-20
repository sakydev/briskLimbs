<?php

/**
* Name: Addons
* Description: Core class for handling Addon related actions
* @author: Saqib Razzaq
* @since: v1, Feburary, 2019
* @link: https://github.com/briskLimbs/briskLimbs/blob/master/model/Addons.php
*/

class Addons {
  /*
  * Holds global Limbs object
  */
  public $limbs;

  /*
  * Holds global Database object
  */
  public $database;

  /*
  * Holds table name for addons
  */
  public $table;

  /*
  * Holds directoy path for addons
  */
  public $directory;

  /*
  * Holds columns list from $table
  */
  public $KEYS;

  /*
  * Holds global Errors object
  */
  public $errors;

  /*
  * Holds default listing limit
  */
  public $defaultLimit;

  function __construct() {
    global $limbs;
    $this->limbs = $limbs;
    $this->database = $limbs->database;
    $this->table = 'addons';
    $this->directory = ADDONS_DIRECTORY;
    $this->KEYS = $limbs->database->getColumnsList($this->table);
    $this->errors = new Errors();
    $this->defaultLimit = 10;
  }

  /**
  * Scans Addons directory and returns list of addons available to be installed
  * @return: { array }
  */
  public function idle() {
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

  /**
  * Count total installed addons
  * @param: { $parameters } { array } { any additional parameters }
  * @return: { integer }
  */
  public function count($parameters = false) {
    if ($parameters) {
      $parameters['count'] = true;
      return $this->list($parameters);
    } else {
      return $this->database->getValue($this->table, 'count(*)');
    }
  }
  
  /**
  * List Addons matching several dynamic parameters
  * @param: { $parameters } { array } { array of parameters }
  * This array can include any column from $this->table table which
  * is addons by default. You can specify fields and values in
  * $field => $value format which is then turned in MySQL conditions
  * Please refer to our Github page for usage examples
  * @return: { array }
  */
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

  /**
  * Check if an addon is installed
  * @param : { string } { $name } { name of addon to check }
  * @return: { boolean }
  */
  public function exists($name) {
    $this->database->where('name', $name);
    return $this->database->getValue($this->table, 'count(*)');
  }

  /**
  * List active addons
  * @param: { $limit } { integer } { number or mysql style limit }
  * @param: { $parameters } { array } { false by default, any additional paramters e.g select within range }
  * @return: { array }
  */
  public function listActive($limit, $parameters) {
    $parameters['status'] = 'active';
    $parameters['limit'] = $limit;
    return $this->list($parameters);
  }

  /**
  * List inactive addons
  * @param: { $limit } { integer } { number or mysql style limit }
  * @param: { $parameters } { array } { false by default, any additional paramters e.g select within range }
  * @return: { array }
  */
  public function listInactive($limit, $parameters) {
    $parameters['status'] = 'inactive';
    $parameters['limit'] = $limit;
    return $this->list($parameters);
  }

  /**
  * Installs an addon
  * @param : { string } { $name } { name of addon to install }
  * @return: { integer } { addon id }
  * 
  */
  public function install($name) {
    $list = $this->idle();
    foreach ($list as $folder => $addon) {
      if ($addon['name'] == $name) {
        $file = $folder . '/' . $addon['file'];
        $install = $folder . '/install.php';
        if (!file_exists($file)) {
          return $this->errors->add("Skipping $name because main file $file doesn't exist");
        }

        if (file_exists($install)) {
          require $install;
        }

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

  /**
  * Get all details for an addon
  * @param: { string } { $name } { name of addon }
  * @return: { array }
  */
  public function get($name) {
    $this->database->where('name', $name);
    return $this->database->getOne($this->table);
  }

  /**
  * Get a single field of an addon
  * @param: { string } { $name } { name of addon }
  * @param: { string } { $field } { field to fetch }
  * @return : { mixed }
  */
  public function getField($name, $field) {
    $this->database->where('name', $name);
    $results = $this->database->get($this->table, null, array($field));
    return isset($results['0'][$field]) ? $results['0'][$field] : false;
  }

  /**
  * Get multiple fields of an addon
  * @param: { string } { $name } { name of addon }
  * @param: { string or array } { $field } { single field or array of fields }
  * @return : { mixed }
  */
  public function getFields($name, $fields) {
    $this->database->where('name', $name);
    $results = $this->database->get($this->table, null, is_array($fields) ? $fields : array($fields));
    return isset($results['0']) ? $results['0'] : false;
  }

  /**
  * Get an addon's display name
  * @param: { string } { $name } { dev name of addon }
  * @return: { string }
  */
  public function displayName($name) {
    return $this->getField($name, 'display_name');
  }
  
  /**
  * Get an addon's version
  * @param: { string } { $name } { dev name of addon }
  * @return: { string }
  */
  public function version($name) {
    return $this->getField($name, 'version');
  }

  /**
  * Get an addon's description
  * @param: { string } { $name } { dev name of addon }
  * @return: { string }
  */
  public function description($name) {
    return $this->getField($name, 'description');
  }

  /**
  * Get an addon's author name
  * @param: { string } { $name } { dev name of addon }
  * @return: { string }
  */
  public function author($name) {
    return $this->getField($name, 'author');
  }

  /**
  * Get an addon's status
  * @param: { string } { $name } { dev name of addon }
  * @return: { string }
  */
  public function status($name) {
    return $this->getField($name, 'status');
  }

  /**
  * Get an addon's directory
  * @param: { string } { $name } { dev name of addon }
  * @return: { string }
  */
  public function directory($name) {
    return $this->getField($name, 'directory');
  }

  /**
  * Update a single field of single addon
  * @param: { $field } { string } { field to update }
  * @param: { $value } { mixed } { new value to set }
  * @param: { $identiferValue } { mixed } { value to search addon by }
  * @param: { $identifier } { string } { vkey by default, column to search against }
  * @return: { boolean }
  */
  public function setField($field, $value, $identifierValue, $identifier = 'name') {
    $this->database->where($identifier, $identifierValue);
    return $this->database->update($this->table, array($field => $value));
  }

  /**
  * update a single field of multiple addons
  * @param: { $field } { string } { field to update }
  * @param: { $value } { mixed } { new value to set }
  * @param: { $identifierValueArray } { array } { values array to search addons by }
  * @param: { $identifier } { string } { name by default, column to search against }
  * @return: { boolean }
  */
  public function setFieldBulk($field, $value, $identifierValueArray, $identifier = 'name') {
    $this->database->where($identifier, $identifierValueArray, 'IN');
    return $this->database->update($this->table, array($field => $value));
  }

  /**
  * update multiple fields of single video
  * @param: { $fieldValueArray } { array } { field => value array to update }
  * @param: { $identiferValue } { mixed } { value to search video by }
  * @param: { $identifier } { string } { name by default, column to search against }
  * @return: { boolean }
  */
  public function setFields($fieldValueArray, $identifierValue, $identifier = 'name') {
    $this->database->where($identifier, $identifierValue);
    return $this->database->update($this->table, $fieldValueArray);
  }
  
  /**
  * update multiple columns of multiple addons
  * @param: { $fieldValueArray } { array } { field => value array to update }
  * @param: { $identifierValueArray } { array } { values array to search addons by }
  * @param: { $identifier } { string } { vkey by default, column to search against }
  * @return: { boolean }
  */
  public function setFieldsBulk($fieldValueArray, $identifierValueArray, $identifier = 'vkey') {
    $this->database->where($identifier, $identifierValueArray, 'IN');
    return $this->database->update($this->table, $fieldValueArray);
  }

  /**
  * Set addon state to active
  * @param: { $addon } { string / integer } { addon id or name }
  * @return: { boolean }
  */
  public function activate($addon) {
    return $this->setField('status', 'active', $addon, is_numeric($addon) ? 'id' : 'name');

  /**
  * Set addon state to active for multiple addons
  * @param: { $addonsArray } { mixed array } { list of addon ids or names }
  * @param: { $identifer } { string } { specify if list contains ids or names }
  * @return: { boolean }
  */
  public function bulkActivate($addonsArray, $identifier = 'name') {
    return $this->setFields('status', 'active', $addonsArray, $identifier);
  }

  /**
  * Set addon state to inactive
  * @param: { $addon } { string / integer } { addon id or name }
  * @return: { boolean }
  */
  public function deactivate($addon) {
    return $this->setField('status', 'inactive', $addon, is_numeric($addon) ? 'id' : 'name');
  }

  /**
  * Set addon state to inactive for multiple addons
  * @param: { $addonsArray } { mixed array } { list of addon ids or names }
  * @param: { $identifer } { string } { specify if list contains ids or names }
  * @return: { boolean }
  */
  public function bulkDeactivate($addonsArray, $identifier = 'name') {
    return $this->setFields('status', 'inactive', $addonsArray, $identifier);
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

  public function addTrigger($function, $location, $return = false) {
    $locationsList = array(
      'admin_videos_actions_top',
      'admin_videos_actions_bottom',
      'admin_users_actions_bottom',
      'admin_users_actions_top',
      'user_videos_actions_top',
      'user_videos_actions_bottom',
      'head_start',
      'head_end',
      'navbar_before',
      'navbar_after',
      'body_before',
      'body_after',
      'footer_before',
      'footer_after',
      'footer_start',
      'footer_end',
      'all_sidebars_top',
      'all_sidebars_bottom',
      'watch_sidebar_top',
      'watch_sidebar_bottom',
      'browse_sidebar_top',
      'browse_sidebar_bottom',
      'channel_sidebar_top',
      'channel_sidebar_bottom'
    );

    if (!in_array($location, $locationsList)) {
      return $this->limbs->errors->add("Invalid addon trigger ($function) location ($location)");
    }

    if (!function_exists($function)) {
        return $this->limbs->errors->add("Function $function for $location doesn't exist");
    }

    return $this->limbs->addAddonTrigger($function, $location, $return);
  }
}