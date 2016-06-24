
<?php
$date = new DateTime("now", new DateTimeZone('America/New_York') );

?><html>
<head>
	<title>My Local Weather</title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="apple-touch-icon" href="images/icon.png"/>
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha256-7s5uDGW3AHqw6xtJmNNtr+OBRJUlgkNJEo78P4b0yRw= sha512-nNo+yCHEyn0smMxSswnf/OnX6/KwJuZTlNZBjauKhTK0c+zT+q5JOCx0UFhXQ6rJR9jg6Es8gPuD2uZcYDLqSw==" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
	<script type="text/javascript" src="js/script.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="css/stylesheet.css">

</head>
<body>
	<<div data-role="page" id="one" class="ui-responsive-panel" id="landing-page" data-url="landging-page">
		<div data-role="panel" id="left-panel"  data-display="push">
    		<ul data-role="listview">
    			<li data-icon="delete"><a href="#" data-rel="close">Close</a></li>
    			<li data-role="list-divider">Extended Forecast</li>
    			<li><a href="#days" class="days" data-rel="close">Next 7 days</a></li>
    			<li><a href="#hourly" class="days" data-rel="close">Next 24 Hours</a></li>
    			<li data-role="list-divider">Saved Locations</li>
    			<!-- section going to be added with settings for each account
    			<li><a href="index.php?city=Boston,MA" data-ajax="false" class="city" data-rel="close">Boston, MA</a></li>
    			<li><a href="index.php?city=Eastport,ME" data-ajax="false" class="city" data-rel="close">EastPort, ME</a></li>
    			<li><a href="index.php?city=Seattle,WA" data-ajax="false" class="city" data-rel="close">Seattle, WA</a></li>
-->
    		</ul>
		</div>
		<div data-role="panel" data-position="right" data-display="push" data-theme="a" id="add-form">
			<form class="userform">
				<h2>Login</h2>
				<label for="name">Username:</label>
				<input type="text" name="name" id="name" value="" data-clear-btn="true" data-mini="true">
				<label for="password">Password:</label>
				<input type="password" name="password" id="password" value="" data-clear-btn="true" autocomplete="off" data-mini="true"><div class="ui-grid-a"><div class="ui-block-a"><a href="#" data-rel="close" class="ui-btn ui-shadow ui-corner-all ui-btn-b ui-mini">Cancel</a></div>
				<div class="ui-block-b"><a href="#" data-rel="close" class="ui-btn ui-shadow ui-corner-all ui-btn-a ui-mini">Login</a></div>
			</div>
		</form>
	</div><!-- /panel -->
		<div data-role="header" data-position="fixed">
			<h1>MyLocalWeather</h1>
			<a href="#left-panel" data-icon="bars" data-iconpos="notext" data-iconshadow="false">Menu</a>
			<a href="#add-form" data-icon="user" data-iconpos="notext" data-iconshadow="false">User</a>
		</div>
    </div>
		<div data-role="content" class="content">
			<button type="button" id="getlocation" class="btn btn-primary" data-loading-text="Loading..." autocomplete="off"><i class="fa fa-location-arrow"></i> Current Location</button>
			<p id="location">Current Location</p>
			<h1 id="current_temp" class="icon" data-icon=""></h1>
			<p id="current_summary"></p>	
			<h4>Today</h4>
			<p id="today"></p>
			<p id="todayhigh"></p>
			<p id="todaylow"></p>
			<h4>Next Hour</h4>
			<p id="hour"></p>
			<h4><a href="#hourly">Next 24 Hours</a></h4>
			<p id="hours"></p>
			<h4><a href="#days">Next 7 Days</a></h4>
			<p id="days"></p>
			<div id="map"></div>
			<div data-role="footer" data-position="fixed"><a href="http://www.nd-dd.com">&#169; Nicolas Diaz Designs & Develops 2016 </a></div>
		</div>	
	</div>
	<div data-role="page" id="days">
		<div data-role="header">
			<a href="#one" data-icon="back" data-iconpos="notext" data-iconshadow="false"></a><h1>Extended Forecast</h1>
		</div>
		<div data-role="main" class="ui-content">				
			<table class="table table-condensed" id="dailyList">
			</table>
		</div>
		<div data-role="footer">			
		</div>
	</div>
	<div data-role="page" id="hourly">
		<div data-role="header">
			<a href="#one" data-icon="back" data-iconpos="notext" data-iconshadow="false"></a><h1>Hourly</h1>
		</div>
		<div data-role="main" class="ui-content">		
			<table class="table table-condensed" id="hrlyList">
			</table>
		</div>
		<div data-role="footer">			
		</div>
	</div>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBmaLFzF7chT42ww2vgBh0F9xp_C_5pesc&?>signed_in=true&callback=initMap"
    ></script> 
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
  ga('create', 'UA-39459723-8', 'auto');
  ga('send', 'pageview');
</script>

</body>

</html>