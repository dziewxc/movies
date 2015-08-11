<?php
//model
class Order {
	private $_movieid; //tablica ze wszystkimi rekordami, które mają id $usera
	private $_data;
	private $_orderlist;
	private $_db;
	private $_popular;
	
	public function __construct() {
		$this->_db = DB::getInstance();
	}
	
	public function findorderbyuser($user = null) {
		if($user) {
			$movieid = $this->_db->get('orders', array('user_id', '=', $user)); //wydobywamy id filmu
			
			if($movieid->count()) {
				$this->_movieid = $movieid->results();
			

			$orderlist = array(); //tworzymy pustą tablicę i będziemy ją po kolei wypełniać zamowieniami
			
			foreach($this->_movieid as $key => $value) {
				$data = $this->_db->get('movie', array('id', '=', $value->movie_id));

				if($data->count()) {
					array_push($orderlist,$data->first());
				}
			}
			
			$this->_orderlist = $orderlist;
			return true;
			}
		}
		return false;
	}
	
	public function addorder($userid, $movieid) {
		if($check = $this->_db->get('orders', array('movie_id', '=', $movieid), array('user_id', '=', $userid))) {
			if($check->count()) {
			return false;
			} else {
				try {
					$this->_db->insert('orders', array(
					'user_id' => $userid,
					'movie_id' => $movieid
					));
					} catch(Exception $e) {
						die($e->getMessage());
						}
				$this->email('39dz@gazeta.pl');
					}
		}
	}
	
	public function email() {
		function send_email($email) {
			require_once '/mymvc/app/core/mandrill/src/Mandrill.php';
			$mandrill = new Mandrill('ePRmd7nl9Ea5QGk7xmr3Og');

			try {
				$mandrill = new Mandrill('ePRmd7nl9Ea5QGk7xmr3Og');
				$message = array(
					'html' => "Zamówiłeś przedmiot",
					'subject' => "Potwierdzenie zamówienia",
					'from_email' => 'jolanta.wojcik@uj.edu.pl',   // mail nadawcy
					'from_name' => 'Jola',					 // imię nadawcy
					'to' => array(							 // dane odbiorcy 
						array(
							'email' => "$email",
							'name' => 'Recipient Name',
							'type' => 'to'
						)
					),
					'headers' => array('Reply-To' => 'jolanta.wojcik@uj.edu.pl'),
					'important' => true
				);
				$async = false;
				$ip_pool = 'Main Pool';
				$send_at = '';
				$result = $mandrill->messages->send($message, $async, $ip_pool, $send_at);
				print_r($result);

			} 
			catch(Mandrill_Error $e) {
				// Mandrill errors are thrown as exceptions
				echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
				// A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
				throw $e;
			}
			}
	}
	
	public function getmostpopular() {  //chcemy dostać id filmu, który pojawia się najczęściej
		$popular = $this->_db->query('SELECT count(movie_id), movie_id FROM orders GROUP BY movie_id LIMIT 3');
		if($popular->count()) {
			$this->_popular = $popular->results();
			return true;
		}
	}
	
	public function getorder($movieid, $userid) {
		if($check = $this->_db->get('orders', array('movie_id', '=', $movieid), array('user_id', '=', $userid))) {
			if($check->count()) {
				return true;
			}
		return false;
		}
	}

	public function popular() {
		return $this->_popular;
	}

	public function orderlist() {
		return $this->_orderlist;
	}
	
	public function data() {
		return $this->_data;
	}
}