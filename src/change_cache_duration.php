<?php

$hour = 60*60*1000;
$arr = array(1*$hour, 2*$hour, 24*$hour, 2*24*$hour, 7*24*$hour, 31*24*$hour);
$response = '';

session_start();

if(verify_vars($_SESSION['user'], $_POST['cache_duration'])){
	$user = $_SESSION['user'];
	$cache_duration = $_POST['cache_duration'];
	if($arr[$cache_duration-1]){
		$conn = mysqli_connect("107.170.91.176", "abcd", "abcd1234", "twittercheck");
		if(!$conn){
			$response = error_data("Database connection failed");
		}
		$query = "UPDATE users SET cache_duration=".$arr[$cache_duration-1]." WHERE username='".$user->screen_name."'";
		if(!mysqli_query($conn, $query)){
			$response = error_data(mysqli_error($conn));
		}else{
			$response = success_data("changed");
		}
	}
}else{
	$response = redirect_data("/index.php");
}

echo json_encode($response);

function verify_vars(){
	foreach(func_get_args() as $arg){
		if(isset($arg) && !empty($arg)) continue;
		else return false;
	}
	return true;
}

function error_data($data){
	return array("status"=>"error", "data"=>$data);
}

function success_data($data){
	return array("status"=>"success", "data"=>$data);
}

function redirect_data($data){
	return array("status"=>"redirect", "data"=>array("url"=>$data));
}

?>