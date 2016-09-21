<?php
session_start();

require 'login_check.php';

$response = "";

if(verify_vars($_POST['value'])){
	$value = $_POST['value'];
	
	if(verify_vars($_SESSION['user'])){
		$user = $_SESSION['user'];
	
		$conn = mysqli_connect("107.170.91.176", "abcd", "abcd1234", "twittercheck");
		if(!$conn){
			$response = error_data("database connection failed");
		}else{
			
			$query = "SELECT follower_name, follower_username FROM `group` WHERE (follower_name LIKE '".$value."%' OR follower_username LIKE '".$value."%') AND group_id IN (SELECT group_id FROM users WHERE username='".$user->screen_name."') LIMIT 10";
			if($result = mysqli_query($conn, $query)){
				$temp_response = array();
				while($row = mysqli_fetch_assoc($result)){
					$temp_response[] = $row;
				}
				$response = success_data($temp_response);
			}else{
				$response = error_data("database operation error");
			}
			
		}
		
	}else{
		$response = redirect_data("/index.php");
	}
	
}else{
	$response = error_data("POST variable 'value' not set");
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