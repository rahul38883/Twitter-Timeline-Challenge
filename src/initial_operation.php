<?php

session_start();

require 'login_check.php';

require 'vendor/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

$consumer_key = 'MRH0aS7lMOYkgHgcGg1Myc1qR';
$consumer_secret = 'Tphbq5XtCHZU3GDU4bFwvEz7OeF2xAEG6c0DdD7ewhUVhihQeV';

$db_info = json_decode(file_get_contents('files/db_config.json'));
$conn = mysqli_connect($db_info->host, $db_info->username, $db_info->password, $db_info->db_name);
if(!$conn){
	die("Database connection failed");
}

if(isset($_SESSION['user']) && !empty($_SESSION['user'])){
	$user = $_SESSION['user'];
	$screen_name = $user->screen_name;
	$time = time();
	$query = "SELECT group_id FROM users ORDER BY group_id DESC LIMIT 1";
	if($result = mysqli_query($conn, $query)){
		if(mysqli_num_rows($result) == 0){
			$gr_id = 1;
		}else{
			$row = mysqli_fetch_assoc($result);
			$gr_id = $row['group_id']+1;
		}
		$query = "INSERT IGNORE INTO users VALUES('".$user->screen_name."', '-1', ".(24*60*60*1000).", ".($time-24*60*60*1000).", ".$gr_id.", ".$time.")";
		if(!mysqli_query($conn, $query)){
			die("Database operation error");
		}else{
			
			$query = "SELECT * FROM users WHERE username='".$user->screen_name."'";
			if($result = mysqli_query($conn, $query)){
				$row = mysqli_fetch_assoc($result);
				$cache_expiration = $row["cache_duration"];
				
				if($time - $row["last_limit_time"] > 15*60*1000){
					
					if($time - $row["group_time_created"] >= $row["cache_duration"]){
						
						$query = "DELETE * FROM `group` WHERE `group_id`=".$row['group_id'];
						if(mysqli_query($conn, $query)){
							$query = "UPDATE `users` SET `next_cursor`=-1, `group_created_time`=".$time;
							if(!mysqli_query($conn, $query)){
								die("Database operation error");
							}
						}else{
							die("Database operation error");
						}
						
					}
					
					if($row["next_cursor"] == 0){
						$next_cursor = -1;
					}else{
						$next_cursor = $row["next_cursor"];
					}
					
					if(verify_vars($_SESSION['oauth_token'], $_SESSION['oauth_token_secret'])){
						
						
						$oauth_token = $_SESSION['oauth_token'];
						$oauth_token_secret = $_SESSION['oauth_token_secret'];
						$connection = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
						
						$screen_name = $row["username"];
						$done = 0;
						$do_db = 0;
						$query = "INSERT IGNORE INTO `group` VALUES ";
						$gr_id = $row["group_id"];

						do{
							
							$user_ids = $connection->get("followers/ids", ["screen_name"=>$screen_name, "cursor"=>$next_cursor, "count"=>5000]);
							$i = 0;
							while($i < count($user_ids->ids)){
								
								$arr = array_slice($user_ids->ids, $i, 100);
								$users = $connection->get("users/lookup", ["user_id"=>$arr]);
								
								if($connection->getLastHttpCode() == 200){
									
									//generate query
									$do_db = 1;
									foreach($users as $user){
										$query .= "(".$gr_id.", '".addslashes($user->screen_name)."', '".addslashes($user->name)."'),";
									}
									
								}else{
									$done = 1;
									break;
								}
								
								$i+=100;
								
							}
							if($done == 1){
								break;
							}
							$next_cursor = $user_ids->next_cursor;
							
						}while($connection->getLastHttpCode() == 200 && $next_cursor != 0);

						if($do_db){
							$query = chop($query, ',');
							if(!mysqli_query($conn, $query)){
								die("Database operation error");
							}
							if($next_cursor != 0 && $next_cursor != -1){
								$query = "UPDATE users SET next_cursor='".$next_cursor."' WHERE username='".$screen_name."'";
								if(!mysqli_query($conn, $query)){
									die("Database operation error");
								}
							}
						}
						
					}else{
						header('Location: /index.php');
					}
					
				}
				
			}else{
				die("Database operation error");
			}
			
		}
	}else die("Database connection failed");
	mysqli_close($conn);
}else header('Location: /index.php');

function verify_vars(){
	foreach(func_get_args() as $arg){
		if(isset($arg) && !empty($arg)) continue;
		else return false;
	}
	return true;
}

?>