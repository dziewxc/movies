<?php
//view
require_once('templates/header.php');
$user = new User();                     //do kontrolera
$movie = new Movies();
$order = new Order();

if($user->isLoggedIn()) {
	require_once('templates/logged.php');
	} else {
	require_once('templates/notlogged.php');
	}

$allgenres = array();
foreach($movie->getgenres() as $result) {
	$onegenre = $result->genre;
	require('templates/genre.php');
	array_push($allgenres, $onegenre);
}

$url = App::parseUrl();

if((!(isset($url[1]) AND in_array(($url[1]), $allgenres))) OR (!isset($url[1]))) {
require_once('templates/popular.php');

$order->getmostpopular();
foreach($order->popular() as $value) {
	$movieid = $value->movie_id;
	$movie->find($movieid);
	$id = $movie->data()->id;
	require('templates/popularmovie.php');
};
$url = App::parseUrl();
require('templates/clear.php');
}

for($i = 1; $i < 5; $i++) {
	$movie->find($i);
	if((!isset($url[1])) OR (isset($url[1]) AND ($url[1] === $movie->data()->genre))) {
	$title = $movie->data()->title;
	$id = $movie->data()->id;
	$movietime = explode(':', $movie->data()->movietime);
	
	//wyświetlamy tyle zdań z opisu, ile zmieści się w 40 słowach
	$smalldesc = array_slice(explode(' ', $movie->data()->description), 0, 40);  //ucinamy opis do 40 słów
	$lastperiod = strrpos(implode(' ',  $smalldesc), '.');                       //zwracamy pozycję ostatniej kropki
	$desctoperiod = substr(implode(' ',  $smalldesc), 0, $lastperiod + 1);       //ucinamy opis do ostatniej kropki
	
	if($user->isLoggedIn()) {
	if($order->getorder($id, $user->data()->id)) {
		$watch = 1;
	}
	}
	
	require('templates/movielist.php');
	unset($watch);
	}
}

require_once('templates/footer.php');
?>