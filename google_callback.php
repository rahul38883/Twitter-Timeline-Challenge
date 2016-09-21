<html>
<head>
	<link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<script>
		function startSetup(filename){
			var seconds = 10;
			document.getElementById('filename').innerHTML = filename;
			var seconds_span = document.getElementById('seconds');
			seconds_span.innerHTML = seconds;
			document.getElementById('displa_div').style.display = 'block';
			var interval = setInterval(function(){
				if(--seconds == 0){
					clearInterval(interval);
					window.close();
				}
				seconds_span.innerHTML = seconds;
			}, 1000);
		}
	</script>
</head>
<body>

<div id="displa_div" style="text-align:center; display:none;">
	<h3>Spreadsheet "<span id="filename"></span>" has been created in your Google Drive</h3>
	<br><br>
	<span>Closing Window in <span id="seconds"></span> seconds</span>
	<br><br>
	<button class="btn btn-primary" onclick="window.close()">Close Window</button>
</div>

<?php
session_start();

require_once 'vendor/autoload.php';

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

$client = new Google_Client();
$client->setAuthConfig('files/client_secret_964042762601-j7ui5d9d5ho9mtrtrs6e2a80d9jrb22j.apps.googleusercontent.com.json');
$client->setScopes(array("https://spreadsheets.google.com/feeds",
	"https://www.googleapis.com/auth/drive",
	"https://www.googleapis.com/auth/drive.file",
	"https://www.googleapis.com/auth/drive.appdata"));

if(isset($_GET['code']) && !empty($_GET['code'])){
	$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
}else{
	header('Location: google_auth.php');
}


if(verify_vars($_SESSION['tweet_data'], $_SESSION['filename'], $_SESSION['worksheetname'])){
	$data = json_decode($_SESSION['tweet_data']);
	$filename = $_SESSION['filename'];
	$worksheetname = $_SESSION['worksheetname'];
}else{
	header('Location: userTimeline.php');
}

if(count($data) == 0){
	header('Location: userTimeline.php');
}else{
	
	$driveService = new Google_Service_Drive($client);

	$fileMetadata = new Google_Service_Drive_DriveFile(array(
	  'name' => $filename,
	  'mimeType' => 'application/vnd.google-apps.spreadsheet'));
	$file = $driveService->files->create($fileMetadata, array(
	  'fields' => 'id'));

	$serviceRequest = new DefaultServiceRequest($token["access_token"]);
	ServiceRequestFactory::setInstance($serviceRequest);

	$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();

	$spreadsheetFeed = $spreadsheetService->getSpreadsheetFeed();
	$spreadsheet = $spreadsheetFeed->getByTitle($filename);
	
	$keys = array_keys((array)$data[0]);
	
	$worksheet = $spreadsheet->addWorksheet($worksheetname, count($data)+1, count($keys));
	
	$cellFeed = $worksheet->getCellFeed();
	
	$i=1;
	$j=1;
	foreach($keys as $key){
		$cellFeed->editCell($j, $i++, $key);
	}
	$j++;
	
	$batchRequest = new Google\Spreadsheet\Batch\BatchRequest();
	
	foreach($data as $obj){
		$i=1;
		foreach($keys as $key){
			$temp_data = '';
			if($key == 'media' || $key == 'links'){
				
				if(count($obj->$key) > 0){
					foreach($obj->$key as $val){
						$temp_data .= ($key=='media' ? $val->url : $val).'\n';
					}
					chop($temp_data, '\n');
				}
				
			}else{
				$temp_data = $obj->$key;
			}
			$batchRequest->addEntry($cellFeed->createCell($j, $i++, $temp_data));
		}
		$j++;
	}
	
	$batchResponse = $cellFeed->insertBatch($batchRequest);
	
	echo '<script>startSetup("'.$filename.'")</script>';
	
}

function verify_vars(){
	foreach(func_get_args() as $arg){
		if(isset($arg) && !empty($arg)) continue;
		else return false;
	}
	return true;
}

?>

</body>
</html>