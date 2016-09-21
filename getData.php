<?php
session_start();

require 'login_check.php';

require 'vendor/abraham/twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

$consumer_key = 'MRH0aS7lMOYkgHgcGg1Myc1qR';
$consumer_secret = 'Tphbq5XtCHZU3GDU4bFwvEz7OeF2xAEG6c0DdD7ewhUVhihQeV';

/*
*	type =
*		1 : user data
*		2 : home timeline (fetch tweets)
*		3 : followers
*		4 : user timeline (fetch tweets)
*/

if(verify_vars($_POST['type'])){
	$type=$_POST['type'];
}else{
	$response = array("status"=>"error", "data"=>"POST variable 'type' not set");
	die(json_encode($response));
}

if($type==1){
	if(verify_vars($_SESSION['user'])){
		$status = "success";
		$data = $_SESSION['user'];
		
	}else{
		if(verify_vars($_SESSION['oauth_token'], $_SESSION['oauth_token_secret'])){
			$oauth_token = $_SESSION['oauth_token'];
			$oauth_token_secret = $_SESSION['oauth_token_secret'];
			
			$connection = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
			$user = $connection->get("account/verify_credentials");
			if($connection->getLastHttpCode() == 200){
				$status = "success";
				$data = $user;
			}else{
				$status = "error";
				$data = $user->error;
			}
		}else{
			$status = "redirect";
			$data = array("url"=>"index.php");
		}
	}
}else{
	if(verify_vars($_SESSION['oauth_token'], $_SESSION['oauth_token_secret'])){
		$oauth_token = $_SESSION['oauth_token'];
		$oauth_token_secret = $_SESSION['oauth_token_secret'];
		$connection = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
		
		if($type==2){
			$home_timeline = $connection->get("statuses/home_timeline", ["count"=>10]);
			if($connection->getLastHttpCode() == 200){
				$status = "success";
				$data = $home_timeline;
			}else{
				$status = "error";
				$data = $home_timeline->error;
			}
		}else if($type==3){
			$followers = $connection->get("followers/list", ["count"=>10]);
			if($connection->getLastHttpCode() == 200){
				$status = "success";
				$data = $followers;
			}else{
				$status = "error";
				$data = $followers->error;
			}
		}else if($type==4){
			if(verify_vars($_POST['screen_name'], $_POST['count'])){
				$screen_name = $_POST['screen_name'];
				$count = $_POST['count'];
				$user_timeline = $connection->get("statuses/user_timeline", ["screen_name"=>$screen_name, "count"=>$count]);
				if($connection->getLastHttpCode() == 200){
					$status = "success";
					$data = $user_timeline;
				}else{
					$status = "error";
					$data = $user_timeline->error;
				}
			}else{
				$status = "redirect";
				$data = array("url"=>"userTimeline.php");
			}
		}else{
			$status = "error";
			$data = "incorrect value of POST variable 'type'";
		}
	}else{
		$status = "redirect";
		$data = array("url"=>"index.php");
	}
}
$response = array("status"=>$status, "data"=>$data);
echo json_encode($response);

function verify_vars(){
	foreach(func_get_args() as $arg){
		if(isset($arg) && !empty($arg)) continue;
		else return false;
	}
	return true;
}

?>