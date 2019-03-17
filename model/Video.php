<?php

/**
* Name: Video
* Description: A helpful class to interact with single video
* @author: Saqib Razzaq
* @since: v1, Feburary, 2019
* @link: https://github.com/briskLimbs/briskLimbs/blob/master/model/Video.php
*/

class Video extends Videos {
	
	function __construct($videoKey) {
		$this->vKey = $videoKey;
	}

	/**
	* Fetches initial video data for video and makes other methods available
	* @return: { $data } { object }
	*/
	public function fetch() {
		$this->initialize();
		return $this->data = is_numeric($this->vKey) ? $this->getById($this->vKey) : $this->getByKey($this->vKey);
	}

	/**
	* Get video title
	* @return: { string }
	*/
	public function title() {
		return $this->data['title'];
	}

	/**
	* Get video uploader name
	* @return: { string }
	*/
	public function uploaderName() {
		return $this->data['uploader_name'];
	}

	/**
	* Get video duration seconds
	* @return: { integer }
	*/
	public function duration() {
		return $this->data['duration'];
	}

	/**
	* Get video duration in human readable format
	* @return: { string }
	*/
	public function prettyDuration() {
		return formatDuration($this->duration());
	}

	/**
	* Get video filename
	* @return: { string }
	*/
	public function filename() {
		return $this->data['filename'];
	}

	/**
	* Get video upload date
	* @return: { string }
	*/
	public function date() {
		return $this->data['date'];
	}

	/**
	* Get video directory in Y/m/d format
	* @return: { string }
	*/
	public function directory() {
		return directory($this->date()); 
	}

	/**
	* Get total comments count
	* @return: { string }
	*/
	public function comments() {
		return $this->data['comments'];
	}

	/**
	* Get video scope
	* @return: { string }
	*/
	public function scope() {
		return $this->data['scope'];
	}

	/**
	* Check if video is public
	* @return: { boolean }
	*/
	public function isPublic() {
		return $this->scope() == 'public' ? true : false;
	}

	/**
	* Get if video is private
	* @return: { boolean }
	*/
	public function isPrivate() {
		return $this->scope() == 'private' ? true : false;
	}
}