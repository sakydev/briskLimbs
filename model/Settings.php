<?php

/**
* Name: Settings
* Description: Core class for handling Settings CRUD and other actions. This class is globally
* available under Limbs object
* @author: Saqib Razzaq
* @since: v1, Feburary, 2019
* @link: https://github.com/briskLimbs/briskLimbs/blob/master/model/Settings.php
*/

class Settings {

	/*
	* Holds global database object
	*/
	public $database;

	/*
	* Holds simplified settings array
	*/
	public $settings;

	/*
	* Holds settings table name
	*/
	private $table;

	function __construct($database) {
		$this->initialize($database);
	}

	/**
	* Fetch settings from database and make ready for all other methods
	* @return: { array }
	*/
	public function initialize($database) {
		$this->database = $database;
		$this->settings = array();
		$this->table = 'settings';
		
		$results = $this->database->get($this->table);
		foreach ($results as $key => $value) {
			$this->settings[$value['name']] = $value['value'];
		}

		return $this->settings;
	}

	/**
	* Get a setting
	* @param: { $name } { string } { name of setting }
	* @param: { $fromDb } { boolean } { false by default, if true fetch value from database }
	* @return: { mixed }
	*/
	public function get($name, $fromDb = false) { // fromDb means selecting from db again
		if ($fromDb) {
			$this->database->where('name', $name, '=');
			return $this->database->get($this->table);
		} else {
			return isset($this->settings[$name]) ? $this->settings[$name] : false;
		}
	}

	/**
	* Update a setting's value
	* @param: { $name } { string } { name of setting to update }
	* @param: { $value } { mixed } { new value to set }
	* @return: { boolean } 
	*/
	public function set($name, $value) {
		return $this->database->update(array($name => $value));
	}

	/**
	* Update multiple settings
	* @param: { $fields } { array } { assoc array of fields => values to update }
	* @return: { boolean }
	*/
	public function bulkSet($fields) {
		$keys = '';
		$query = "UPDATE $this->table SET value = (CASE name ";
    foreach ($fields as $name => $value) {
    	$query .= "WHEN '$name' THEN '$value' ";
    	$keys .= "'$name',";
    }
    $query .=  ' END) WHERE name IN (' . trim($keys, ',') . ')';
    $this->database->rawQuery($query);
    return $this->database->getLastErrno() === 0 ? true : false;
	}

	/**
	* Get all settings
	* @return: { array }
	*/
	public function fetch() {
		return $this->settings;
	}

	/*
	* re select from database instead
	*/
	public function reFetch() {
		$this->initialize();
		return $this->fetch();
	}

	/**
	* Check if signups are allowed
	* @return: { boolean }
	*/
	public function allowSignups() {
		return $this->get('signups') == 'yes' ? true : false;
	}

	/**
	* Check if uploads are allowed
	* @return: { boolean }
	*/
	public function allowUploads() {
		return $this->get('uploads') == 'yes' ? true : false;	
	}

	public function enabled($setting) {
		return isset($this->settings[$setting]) && $this->settings[$setting] == 'yes' ? true : false;
	}
}