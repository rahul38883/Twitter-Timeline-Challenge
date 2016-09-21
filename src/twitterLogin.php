<?php
session_start();

require '../vendor/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

$consumer_key = 'MRH0aS7lMOYkgHgcGg1Myc1qR';
$consumer_secret = 'Tphbq5XtCHZU3GDU4bFwvEz7OeF2xAEG6c0DdD7ewhUVhihQeV';

$connection = new TwitterOAuth($consumer_key, $consumer_secret);
$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => 'https://tweetrcheck11771177.herokuapp.com/src/callback.php'));

$_SESSION['oauth_token'] = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

$url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

header('Location: '.$url);
?>