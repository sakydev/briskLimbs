<?php

class Errors {

	function __construct() {
		$this->messages = array();
	}
	
	public function add($message) {
    $this->messages[] = $message;
  }

  public function collect() {
    return $this->messages;
  }
}