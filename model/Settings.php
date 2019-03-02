<?php

class Settings {
	
	function __construct($database) {
		$this->database = $database;
		$this->settings = array();
		$this->table = 'settings';
	}

	public function initialize() {
		$results = $this->database->get($this->table);
		foreach ($results as $key => $value) {
			$this->settings[$value['name']] = $value['value'];
		}

		return $this->settings;
	}

	public function get($name, $fromDb = false) { // fromDb means selecting from db again
		if ($fromDb) {
			$this->database->where('name', $name, '=');
			return $this->database->get($this->table);
		} else {
			return isset($this->settings[$name]) ? $this->settings[$name] : false;
		}
	}

	public function set($name, $value) {
		return $this->database->update(array($name => $value));
	}

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

	// get all settings
	public function fetch() {
		return $this->settings;
	}

	// re select from database instead
	public function reFetch() {
		$this->initialize();
		return $this->fetch();
	}

	/* helper functions */
	public function allowSignups() {
		return $this->get('signups') == 'yes' ? true : false;
	}

	public function allowUploads() {
		return $this->get('uploads') == 'yes' ? true : false;	
	}
}