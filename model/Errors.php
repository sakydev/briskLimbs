<?php

/**
* Name: Errors
* Description: Core class for handling errors
* @author: Saqib Razzaq
* @since: v1, Feburary, 2019
* @link: https://github.com/briskLimbs/briskLimbs/blob/master/model/Errors.php
*/

class Errors {
  /*
  * Holds all error messages
  */
  public $messages;

	function __construct() {
		$this->messages = array();
	}
	
  /**
  * Add new error message
  * @param: { $message } { string } { message to add }
  */
	public function add($message) {
    $this->messages[] = $message;
  }

  /**
  * Collect all error messages
  * @return: { array }
  */
  public function collect() {
    return $this->messages;
  }
}