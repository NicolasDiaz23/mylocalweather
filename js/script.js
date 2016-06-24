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
	userCenter = new google.maps.LatLng(lat,lon);   
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
			$("#getlocation").children().attr("class","fa fa-location-arrow");
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
			$("#todayhigh").html("High <b>" + todayHigh + "&#176;F</b> @ " + highTime);
			$("#todaylow").html("Low <b>" + todayLow + "&#176;F</b> @ " + lowTime);
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
		//document.getElementById('getlocation').addEventListener('click', function(){
		$("#getlocation").on('click', function() {
			var $btn = $(this);
			$btn.children().attr("class","fa fa-spinner fa-spin");
			clearRows();
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
function clearRows(){
	$("#dailyList").html("");
	$("#hrlyList").html("");
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