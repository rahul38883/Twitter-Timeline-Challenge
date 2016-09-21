<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link type="text/css" rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css"/>
		<style type="text/css">
			body{
				text-align:center;
			}
			#header{
				color:#6699af;
			}
			#texts{
				text-align:center;
				margin-top:3%;
			}
			#features{
				display:inline-block;
				text-align:left;
			}
			#features_header{
				text-align:center;
			}
			#link_button_div{
				margin-top:5%;
			}
			@media only screen and (max-width: 500px) {
				#connect_btn{
					width:80%;
				}
			}
		</style>
	</head>
	<body>
		<div id="header">
			<h1>Twitter Timeline Challenge</h1>
		</div>
		<div id="texts">
			<div id="features">
				<div id="features_header">
					<h3>Features</h3>
				</div>
				<div id="features_list_div">
					<ul>
						<li>Tweets from Home Timeline</li>
						<li>Followers</li>
						<li>Tweets from User Timeline of followers</li>
						<li>Search followers</li>
						<li>Download tweets in various formats</li>
					</ul>
				</div>
			</div>
		</div>
		<div id="link_button_div">
			<a href="src/twitterLogin.php">
				<button class="btn btn-primary">Connect to Twitter</button>
			</a>
		</div>
	</body>
</html>