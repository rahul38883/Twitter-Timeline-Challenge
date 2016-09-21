<?php
session_start();

require 'vendor/abraham/twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

$consumer_key = 'MRH0aS7lMOYkgHgcGg1Myc1qR';
$consumer_secret = 'Tphbq5XtCHZU3GDU4bFwvEz7OeF2xAEG6c0DdD7ewhUVhihQeV';

if(isset($_GET['oauth_verifier']) && !empty($_GET['oauth_verifier'])){
	$oauth_verifier = $_GET['oauth_verifier'];
}else die("Error : oauth_verifier required");

if(isset($_SESSION['oauth_token']) && isset($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])){
	$oauth_token = $_SESSION['oauth_token'];
	$oauth_token_secret = $_SESSION['oauth_token_secret'];
}else die('Error : oauth_token/oauth_token_secret from session required');

$connection = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
$access_token = $connection->oauth('oauth/access_token', array('oauth_verifier' => $oauth_verifier));

$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
$user = $connection->get("account/verify_credentials");

$_SESSION['user'] = $user;
$_SESSION['oauth_token'] = $access_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $access_token['oauth_token_secret'];
$_SESSION['logged_in'] = 1;
header('Location: userTimeline.php');
?>