<?php

/**
 * 
 */
class Video extends Videos {
	
	function __construct($videoKey) {
		$this->vKey = $videoKey;
	}

	public function fetch() {
		$this->initialize();
		return $this->data = is_numeric($this->vKey) ? $this->getById($this->vKey) : $this->getByKey($this->vKey);
	}

	public function title() {
		return $this->data['title'];
	}

	public function uploaderName() {
		return $this->data['uploader_name'];
	}

	public function duration() {
		return $this->data['duration'];
	}

	public function prettyDuration() {
		return formatDuration($this->duration());
	}

	public function filename() {
		return $this->data['filename'];
	}

	public function date() {
		return $this->data['date'];
	}

	public function directory() {
		return directory($this->date()); 
	}

	public function comments() {
		return $this->data['comments'];
	}

	public function scope() {
		return $this->data['scope'];
	}

	public function isPublic() {
		return $this->scope() == 'public' ? true : false;
	}

	public function isPrivate() {
		return $this->scope() == 'private' ? true : false;
	}
}