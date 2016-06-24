
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
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
	<script>
			var lat, lon, icon;
			var myLatLng;
			var icons = {	
			  "clear-day" : "B",
			  "clear-night" : "C",
			  "rain" : "R",
			  "snow" : "G",
			  "sleet" : "X",
			  "wind" : "S",
			  "fog" : "N",
			  "cloudy" : "Y",
			  "partly-cloudy-day" : "H",
			  "partly-cloudy-night" : "I" 

			}
			window.onload = function(){
				if (navigator.geolocation) {
			        navigator.geolocation.getCurrentPosition(showPosition);
			    } else { 
			        x.innerHTML = "Geolocation is not supported by this browser.";
			    }
			}
			
			function getLocation() {
			    if (navigator.geolocation) {
			        navigator.geolocation.getCurrentPosition(showPosition);
			    } else { 
			        x.innerHTML = "Geolocation is not supported by this browser.";
			    }
			    
			}
			function showPosition(position) {
				lat = position.coords.latitude;
				lon = position.coords.longitude;
			    myLatLng = {lat: lat, lng: lon};
				loadWeather(lat, lon)
				userCenter = new google.maps.LatLng(
					lat,
					lon);   
			}

			function loadWeather(lat, lon){

				var forecastUrl = "https://api.forecast.io/forecast/0401e5d5cfbdebd000414494b0263818/" + lat + "," + lon;

				options = {center : myLatLng, zoom : 12, mapTypeId : google.maps.MapTypeId.ROADMAP }
				map = new google.maps.Map(document.getElementById('map'), options);
				geocoder = new google.maps.Geocoder;
	  			infowindow = new google.maps.InfoWindow;
				$.ajax({
					url: forecastUrl,
					jsonpCallback: "jsonCallback",
					ContentType: "application/json",
					dataType: 'jsonp',
					success: function(json){
						console.log(json);
						/*current temperature*/
						icon = json.currently.icon;
						$("#current_temp").html(Math.round(json.currently.temperature) + "&#176;F");
						$("#current_summary").html(json.currently.summary + ", feels like  " + Math.round(json.currently.apparentTemperature) + "&#176;F");
						$("#current_temp").attr("data-icon",icons[json.currently.icon]);
						/*Rest of the Day*/
						var daily = json.daily.data;
						var secDate= new Date();
						var todayHigh = Math.round(daily[0].temperatureMax);
						var highTime = convertTime(daily[0].temperatureMaxTime);
						var todayLow = Math.round(daily[0].temperatureMin);
						var lowTime = convertTime(daily[0].temperatureMinTime);
						$("#todayhigh").html("High " + todayHigh + "&#176;F @ " + highTime);
						$("#todaylow").html("Low " + todayLow + "&#176;F @ " + lowTime);
						$("#today").html(json.currently.summary + ".");
						/*Hourly*/
						var hourly = json.hourly.data;
						var nextHour = hourly[1].summary;
						var next24 = json.hourly.summary;
						$("#hour").html(nextHour + " for the hour.");
						$("#hours").html(next24);
						for(var i = 0; i < 24; i++){
							var time = convertTime(hourly[i].time);
							var today = convertTime(hourly[0].time);	
							var hrtemp = Math.round(hourly[i].temperature);	
							var hourlyListRow = "<tr><th>" + time + "<th><td> <h3 id='hrlyIcon" + i + "' class='icon hrlyIcon' data-icon='"+ icons[hourly[i].icon] +"'></h3></td><td>" + hourly[i].summary + "</td><td>" + hrtemp + "&#176;F </td></tr>";	
							$("#hrlyList").append(hourlyListRow);
						}
						/*Rest of Week*/
						$("#days").html(json.daily.summary);
						for(var d = 1; d < daily.length; d++){
							var wday = convertTime(daily[d].time, false, true);
							var dailyHigh = Math.round(daily[d].temperatureMax);
							var dailyHighTime = convertTime(daily[d].temperatureMaxTime);
							var dailyLow = Math.round(daily[d].temperatureMin);
							var dailyLowTime = convertTime(daily[d].temperatureLowTime);
							var sunrise = convertTime(daily[d].sunriseTime);
							var sunset = convertTime(daily[d].sunsetTime);
							/*
							var dailyList = "<a href=# class='list-group-item'>" + wday + " <span id='dailyIcon" + d + "' class='icon hrlyIcon' data-icon='"+ icons[daily[d].icon] +"'></span>" +  daily[d].summary + " " + dailyHigh + "&#176; | " + dailyLow + "&#176; </a>";	*/
							var dailyListRow = "<tr><th>" + wday + "</th><td>" + "<h3 id='dailyIcon" + d + "' class='icon hrlyIcon' data-icon='"+ icons[daily[d].icon] +"'></h3></td><td>" + daily[d].summary + "</td><td>" + dailyHigh + "&#176; / " + dailyLow + "&#176;</td></tr>"; /*<tr><td colspan='4'><p><span class='gray'>High</span><span class='desc'>" + dailyHigh + "&#176; at " + dailyHighTime + "</span><span class='gray'>Low</span><span class='desc'>" + dailyLow + "&#176; at " + dailyLowTime + "</span><span class='gray'>Sunrise</span><span class='desc'>" + sunrise + "</span><span class='gray'>Sunset</span><span class='desc'>" + sunset + "</span></p></td></tr>"; */

							$("#dailyList").append(dailyListRow);
						}
						

						geocodeLatLng(geocoder, map, infowindow, icon);						
						},
					error: function(e){
						console.log(e.message);
					}
				});
			}
			var map, marker, geocoder, infowindow, options;
			
			function initMap(){
					
				options = {center : myLatLng, zoom : 12, mapTypeId : google.maps.MapTypeId.ROADMAP }
				map = new google.maps.Map(document.getElementById('map'), options);
				geocoder = new google.maps.Geocoder;
	  			infowindow = new google.maps.InfoWindow;

	  			document.getElementById('getlocation').addEventListener('click', function(){
	  				getLocation();
	  				geocodeLatLng(geocoder, map, infowindow);
	  			});

			}
			function geocodeLatLng(geocoder, map, infowindow, icon){
				var image = '/localWeather/images/' + icon + '.png';
	    		geocoder.geocode({'location': myLatLng}, function(results, status) {
				    if (status === google.maps.GeocoderStatus.OK) {
				      if (results[1]) {
				      	$('#location').html(results[4].formatted_address);
				        var marker = new google.maps.Marker({
				          position: myLatLng,
				          map: map,
				          icon: image
				        });
				        infowindow.setContent(results[4].formatted_address);
				        infowindow.open(map, marker);
				      } else {
				        window.alert('No results found');
				      }
				    } else {
				      window.alert('Geocoder failed due to: ' + status);
				    }
				  });
			}	
			function convertTime(UNIX_time, hasTime, day)	{
				var a = new Date(UNIX_time * 1000);
				var hr = a.getHours();
				var min = (a.getMinutes() == 0) ? "00" : a.getMinutes();
				var mid = (hr >= 12) ? "PM" : "AM";
				var weekday = new Array(7);
				weekday[0]=  "Sunday";
				weekday[1] = "Monday";
				weekday[2] = "Tuesday";
				weekday[3] = "Wednesday";
				weekday[4] = "Thursday";
				weekday[5] = "Friday";
				weekday[6] = "Saturday";
				if(hasTime){
					return a.toDateString();
				}if(day){
					return weekday[a.getDay()];
				}else{
					return (hr + ":"+ min + " " + mid);
				}    
			}
			</script>
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
    			<li><a href="index.php?city=Boston,MA" data-ajax="false" class="city" data-rel="close">Boston, MA</a></li>
    			<li><a href="index.php?city=Eastport,ME" data-ajax="false" class="city" data-rel="close">EastPort, ME</a></li>
    			<li><a href="index.php?city=Seattle,WA" data-ajax="false" class="city" data-rel="close">Seattle, WA</a></li>

    		</ul>
		</div>
		<div data-role="panel" data-position="right" data-display="push" data-theme="a" id="add-form">
			<form class="userform">
				<h2>Login</h2>
				<label for="name">Username:</label>
				<input type="text" name="name" id="name" value="" data-clear-btn="true" data-mini="true">
				<label for="password">Password:</label>
				<input type="password" name="password" id="password" value="" data-clear-btn="true" autocomplete="off" data-mini="true"><div class="ui-grid-a"><div class="ui-block-a"><a href="#" data-rel="close" class="ui-btn ui-shadow ui-corner-all ui-btn-b ui-mini">Cancel</a></div>
				<div class="ui-block-b"><a href="#" data-rel="close" class="ui-btn ui-shadow ui-corner-all ui-btn-a ui-mini">Save</a></div>
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
			<button id="getlocation" class="btn btn-primary" data-loading-text="<i class='icon-spinner icon-spin icon-large'></i> Getting Data"><i class="fa fa-location-arrow"></i> Current Location</button>
			<p id="location">Current Location</p>
			<h1 id="current_temp" class="icon" data-icon=""></h1>
			<p id="current_summary"></p>
			
			
			<h4>Today</h4>
			<p id="today"></p>
			<p id="todayhigh"></p>
			<p id="todaylow"></p>
			<h4>Next Hour</h4>
			<p id="hour"></p>
			<h4>Next 24 Hours</h4>
			<p id="hours"></p>
			<h4>Next 7 Days</h4>
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

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBmaLFzF7chT42ww2vgBh0F9xp_C_5pesc&signed_in=true&callback=initMap"
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