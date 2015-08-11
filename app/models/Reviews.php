<?php
//model
class Reviews {
	private $_db;
	private $_data;
	
	public function __construct($review = null) {
		$this->_db = DB::getInstance();
	}
	
	public function beka() {
		echo "ale beka";
	}
	
	public function create($fields = array()) {
		if(!$this->_db->insert('review', $fields)) {
			throw new Exception('Mamy pewien problem z dodaniem Twojej recenzji!');
		}
	}
}
