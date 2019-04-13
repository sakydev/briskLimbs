<?php

/**
* Name: Skins
* Description: Core class for handling Skin related actions
* @author: Saqib Razzaq
* @since: v1, April, 2019
* @link: https://github.com/briskLimbs/briskLimbs/blob/master/model/Skins.php
*/

class Skins {
  /*
  * Holds global Limbs object
  */
  public $limbs;

  /*
  * Holds global Database object
  */
  public $database;

  /*
  * Holds table name for skins
  */
  public $table;

  /*
  * Holds directoy path for skins
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
    $this->directory = SKINS_DIRECTORY;
    $this->errors = new Errors();
    $this->defaultLimit = 10;
  }

  /**
  * Scans skins directory and returns list of skins available to be installed
  * @return: { array }
  */
  public function list() {
    $response = array();
    $folders = glob($this->directory . '/*');
    if ($folders) {
      foreach ($folders as $key => $folder) {
        if (!file_exists("{$folder}/skin.json")) {
          $this->errors->add("skipping $folder because it is missing skin.json");
          continue;
        }

        $skin = json_decode(file_get_contents("{$folder}/skin.json"), true);
        if (!isset($skin['name'])) {
          $this->errors->add("skipping $folder because it is missing name");
          continue;
        }

        $response[$folder] = $skin;
      }
    }

    return $response;
  }

  public function install($name) {
    return $this->limbs->settings->set('active_theme', $name);
  }
}