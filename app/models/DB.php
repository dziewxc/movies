﻿<?php
class DB {
	private static $_instance;
	private $_pdo,
			$_query,
			$_error = false,
			$_results,
			$_count = 0;
				
	private function __construct() {
		try {
			$this->_pdo = new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'), Config::get('mysql/username'), Config::get('mysql/password'));
			if(isset($_GET['url'])) {
				if(strstr($_GET['url'], 'movie')) {
					$this->_pdo->exec("SET CHARACTER SET utf8");
			}
			}
		} catch(PDOException $e) {
		die($e->getMessage);
		}
	}
	
	public static function getInstance() {
		if(!isset(self::$_instance)) {
			self::$_instance = new DB();
		}
		return self::$_instance;
	}
	
	public function query($sql, $params = array()) {
		$this->_error = false;                               //przywracamy error do false
		if($this->_query = $this->_pdo->prepare($sql)) {
			$x = 1;
			if(count($params))  {
				foreach($params as $param)  {
					$this->_query->bindValue($x, $param);
					$x++;
				}
			}
			if($this->_query->execute()) {
				$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
				$this->_count = $this->_query->rowCount();
			} else {
				$this->_error = true;
			}
		return $this;
	}
	}
	
	public function insert($table, $data = array()) {
			$keys = array_keys($data);
			$values = '';
			$x = 1;
			
			foreach ($keys as $key) {
				$values .= '?';
				if($x < count($keys)) {
					$values .= ', ';      // --.' dokładanie do stringu
				}
				$x++;
			}			
			$sql = "INSERT INTO {$table} (`" . implode('`, `',$keys) . "`) VALUES ({$values})";
			if(!$this->query($sql, $data)->error()) {
				return true;
			}
		return false;
	}
	
	public function update($table, $id, $fields) {
		$set = '';
		$x = 1;
		
		foreach($fields as $name =>$value) {
			$set .= "{$name} = ?";
			if($x < count($fields)) {
			$set .= ', ';      // --.' dokładanie do stringu
			}
			$x++;
		}
		$sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";
		if(!$this->query($sql, $fields)->error()) {
		return true;
		}
		return false;
		echo $this->query($sql, $fields)->error();
	}
	
	
	public function action($action, $table, $where = array(), $where2 = array()) {
		if(count($where) === 3) {
			$operators = array('=', '<', '>', '<=', '>=');
				
			$field = $where[0];
			$operator = $where[1];
			$value = $where[2];
			
			if(count($where2) === 3) {  //wykonaj sprawdzanie dla where2 i wyślij z dwiema tablicami
					
				$field2 = $where2[0];
				$operator2 = $where2[1];
				$value2 = $where2[2];
				
				if(in_array($operator, $operators) AND in_array($operator2, $operators)) {
					$sql = "{$action} FROM {$table} WHERE {$field}{$operator} ? AND {$field2}{$operator2} ?";
						
					if(!$this->query($sql, array($value, $value2))->error()) {
						return $this;   
					}
				}	
			} else { //jeśli drugie where nie jest wypełnione, wyślij tylko z pierwszym
					if(in_array($operator, $operators)) {
					$sql = "{$action} FROM {$table} WHERE {$field}{$operator} ?";
						
					if(!$this->query($sql, array($value))->error()) {
						return $this;   
					}
				}	
			}
		}	else {
			$sql = "{$action} FROM {$table}";
			if($this->query($sql)) {
				return $this->_results;
			}
		}
		return false;
	}
	
	public function get($table, $where, $where2 = array()) {
		return $this->action('SELECT *', $table, $where, $where2);
	}
	
	public function getdistinct($table, $item, $where = array()) {
		return $this->action("SELECT DISTINCT {$item}", $table);
	}
	
	public function delete($table, $where) {
		return $this->action('DELETE', $table, $where);
	}
	
	public function error() {
		return $this->_error;
	}
	
	public function first() {
		return $this->_results[0];
	}
	
	public function count() {
		return $this->_count;
	}	
	
	public function results() {
		return $this->_results;
	}	
}





