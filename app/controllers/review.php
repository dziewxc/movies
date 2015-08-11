<?php
//controller
require_once('../app/core/Controller.php');

class Review extends Controller {
	public function index() {
		if(Input::exists()) {
			$review = new Reviews();
			$user = new User();
			$url = App::parseUrl();
			$date = date('Y-m-d H:i:s');
			try {
				$review->create(array(
					'movie_id' => $url[1],
					'user_id' => $user->data()->id,
					'content' => Input::get('review'),
					'date' => $date,
					'status' => 'pending',
				));		
			} catch(Exception $e) {
				die($e->getMessage());
			}
		$this->view('movie');
		}
		$this->view('review');
	}
}