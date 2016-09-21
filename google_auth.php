<?php
session_start();

require_once 'vendor/autoload.php';

$client = new Google_Client();

$client->setClientId("964042762601-j7ui5d9d5ho9mtrtrs6e2a80d9jrb22j.apps.googleusercontent.com");
$client->setRedirectUri("http://tweetrcheck11771177.herokuapp.com/google_callback.php");

$client->setScopes(array("https://spreadsheets.google.com/feeds",
	"https://www.googleapis.com/auth/drive",
	"https://www.googleapis.com/auth/drive.file",
	"https://www.googleapis.com/auth/drive.appdata"));
	
$auth_url = $client->createAuthUrl();

if(verify_vars($_POST['tweet_data'], $_POST['filename'], $_POST['worksheetname'])){
	$_SESSION['tweet_data'] = $_POST['tweet_data'];
	$_SESSION['filename'] = $_POST['filename'];
	$_SESSION['worksheetname'] = $_POST['worksheetname'];
}else{
	$auth_url = 'userTimeline.php';
}

echo json_encode(array("status"=>"success", "data"=>array("auth_url"=>$auth_url)));

function verify_vars(){
	foreach(func_get_args() as $arg){
		if(isset($arg) && !empty($arg)) continue;
		else return false;
	}
	return true;
}

?>