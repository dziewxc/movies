<?php
//view
require_once('templates/header.php');
$user = new User;
$movie = new Movies;
if($user->isLoggedIn()) {
	require_once('templates/logged.php');
	require_once('templates/review.php');
	} else {
	require_once('templates/notlogged.php');
}