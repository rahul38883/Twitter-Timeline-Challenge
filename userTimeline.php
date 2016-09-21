<?php

require 'initial_operation.php';

?>

<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script type="text/javascript" src="lib/jquery-2.1.4.js"></script>
		<script type="text/javascript" src="lib/bootstrap/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="lib/FileSaver.js"></script>
		<script type="text/javascript" src="lib/tableExport/tableExport_modified.js"></script>
		<script type="text/javascript" src="lib/tableExport/jquery.base64_modified.js"></script>
		<script type="text/javascript" src="lib/tableExport/jspdf/libs/sprintf.js"></script>
		<script type="text/javascript" src="lib/tableExport/jspdf/libs/base64.js"></script>
		<script type="text/javascript" src="js/userTimeline.js"></script>
		<link type="text/css" rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css"/>
		<link type="text/css" rel="stylesheet" href="css/userTimeline.css"/>
	</head>
	<body>
	
		<nav class="navbar navbar-default navbar-fixed-top">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#tweet_navbar">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span> 
					</button>
					<a href="#" class="navbar-brand">Twitter</a>
				</div>
				<div class="collapse navbar-collapse" id="tweet_navbar">
					<ul class="nav navbar-nav navbar_ul">
						<li class="active"><a href="#user_timeline">User Timeline</a></li>
						<li><a href="#follower_timeline">Followers Timeline</a></li>
						<li><a href="#download_tweets">Download Tweets</a></li>
						<li class="dropdown">
							<div class="dropdown-toggle profile" data-toggle="dropdown">
								<div id="profile_pic">
									<img id="profile_pic_img" src=""></img>
								</div>
							</div>
							<ul class="dropdown-menu">
								<li><a href="http://www.twitter.com"><span id="user_name"></span><br><span id="user_uname"></span></a></li>
								<li><a href="logout.php">Logout</a></li> 
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</nav>
		
		<div id="main_container" class="container">
		
			<div id="user_timeline">
				
				<div class="fake_height_div"></div>
				
				<div class="section_header">
					<h1>User Timeline</h1>
				</div>
				
				<div id="myCarousel" class="carousel slide" data-ride="carousel">
					<ol id="tweet_indicators" class="carousel-indicators">
					
					</ol>
						
					<div id="tweet_main_box" class="carousel-inner tweet_box" role="listbox">
					
					</div>
					
					<a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
						<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
						<span class="sr-only">Previous</span>
					</a>
					<a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
						<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
						<span class="sr-only">Next</span>
					</a>
				</div>
			</div>
			
			<div id="follower_timeline">
			
				<div class="fake_height_div"></div>
			
				<div class="section_header">
					<h1>Follower Timeline</h1>
				</div>
				
				<div id="search_follower_div">
				
					<div id="change_cache_durationdiv" class="form-group">
						<label for="change_cache_duration" id="change_cache_duration_text">Change Cache Expiration: </label>
						<select id="change_cache_duration" class="form-control">
							<option value="1">1 hour</option>
							<option value="2">2 hour</option>
							<option value="3" selected>1 day (default)</option>
							<option value="4">2 days</option>
							<option value="5">1 week</option>
							<option value="6">1 month</option>
						</select>
					</div>
				
					<div id="follower_input_div">
						<input type="text" id="follower_input" class="form-control" placeholder="search followers"/>
						<div id="followers_dropdown">
						
						</div>
					</div>
					
				</div>
				
				<div id="followers_list">
				
				</div>
			
			</div>
			
			<div id="download_tweets">
			
				<div class="fake_height_div"></div>
			
				<div class="section_header">
					<h1>Download Tweets</h1>
				</div>
				
				<div id="download_tweets_main">
					
					<div id="download_tweets_format" class="form-group">
						<label for="download_format" id="donwload_tweets_text">Select format: </label>
						<select id="download_format" class="form-control">
							<option value="1" selected>csv</option>
							<option	value="2">xls</option>
							<option	value="3">google-spreadhseet</option>
							<option	value="4">pdf</option>
							<option	value="5">xml</option>
							<option	value="6">json</option>
						</select>
					</div>
					
					<div id="download_tweets_btn">
						<button class="btn btn-primary" id="download_tweets">Download Tweets</button>
					</div>
					
					<a id="donwloadLink" class="downloadElements" href="" download=""></a>
					<table id="donwloadTable" class="downloadElements"></table>
					<form id="pdf_form" name="pdf_form" action="create_pdf.php" method="POST" target="_blank">
						<input type="hidden" id="obj_var" name="obj_var" value="">
						<input type="hidden" id="pdf_filename" name="pdf_filename" value="">
					</form>
					
				</div>
				
			</div>
		</div>
	</body>
</html>