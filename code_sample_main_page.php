<?php

session_start();

// Check if user is logged in using the session variable
if ( $_SESSION['logged_in'] != 1 ) {
  $_SESSION['message'] = "You must log in before viewing this page!";
  header("location: error.php");    
}
else {
// Set up the session info   
    $first_name = $_SESSION['first_name'];
    $last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];
    $user = $_SESSION['user_id'];  
    $username = $_SESSION['user_name'];
}
?>

<html>
<head>
    
    <title>Parks Hunt</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    
<style>
      body {
        padding: 0;
        margin: 0;
        overflow: hidden;
      }
      #map {
        height: 100%;
        width: 100%;
        position: absolute;
        z-index: 1;
      }
      #compassHousing {
        margin-left: 1%;
        margin-top: 1%;
        background-color: #CCC;
        border-style: solid;
        border-width: 1px;
        border-radius: 62.5px;
        padding: 2px;
        position: absolute;
        z-index: 2;
        opacity: 0.77;
        -moz-box-shadow: 2px 3px 10px 2px #333;
        -webkit-box-shadow: 2px 3px 10px 2px #333;
        box-shadow: 2px 3px 10px 2px #333;
      }
      #compassFace {
        z-index: -1;    
        position: absolute;
      }
      #compassNeedle {
        position: absolute;
        -webkit-transition-property: -webkit-transform;
        -webkit-transition-duration: .5s;
        -webkit-transition-timing-function: ease-out;
      }
      @-webkit-keyframes{
        0% {
          opacity: 1.0;
        }
        45% {
          opacity: 0.20;
        }
        100% {
          opacity: 1.0;
        }
      }
      @-moz-keyframes{
        0% {
          opacity: 1.0;
        }
        45% {
          opacity: 0.20;
        }
        100% {
          opacity: 1.0;
        }
      }
      #map_graphics_layer {
        -webkit-animation-duration: 3s;
        -webkit-animation-iteration-count: infinite;
        -webkit-animation-name: pulse;
        -moz-animation-duration: 3s;
        -moz-animation-iteration-count: infinite;
        -moz-animation-name: pulse;
      }
      /* compass */
      @media(orientation: landscape) {
        #compass {
          margin-top: 30px;
          margin-left: 30px;
          opacity: 0.85;
          filter: alpha(opacity=85);
          position: absolute;
          z-index: 2;
        }
      }
      @media(orientation: portrait) {
        #compass {
          margin-top: 30px;
          margin-left: 30px;
          opacity: 0.85;
          filter: alpha(opacity=85);
          position: absolute;
          z-index: 2;
        }
      }
    
          /* The Modal for pop-up questions */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            padding-top: 10px; /* Location of the box */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        /* first question in Modal*/       
        .firstquestion{
        display:block; 
           background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
                }
    
        /* second question in Modal*/         
        .secondquestion{
        display:none;  /* hidden by default, wait until the first question is answered*/   
           background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }       



        /*navgation var*/
        .topnav {
          overflow: hidden;
          background-color: #333;
        }

        .topnav a {
          float: left;
          display: block;
          color: #f2f2f2;
          text-align: center;
          padding: 14px 16px;
          text-decoration: none;
          font-size: 17px;
        }

        .topnav a:hover {
          background-color: #ddd;
          color: black;
        }

        .active {
          background-color: #1ab188;
          color: white;
        }

        .topnav .icon {
          display: none;
        }

        @media screen and (max-width: 600px) {
          .topnav a:not(:first-child) {display: none;}
          .topnav a.icon {
            float: right;
            display: block;
          }
        }

        @media screen and (max-width: 600px) {
          .topnav.responsive {position: relative;}
          .topnav.responsive .icon {
            position: absolute;
            right: 0;
            top: 0;
          }
          .topnav.responsive a {
            float: none;
            display: block;
            text-align: left;
          }
        }
</style>
    
   
<link rel="stylesheet" href="https://js.arcgis.com/3.24/esri/css/esri.css" />  
<script src="https://js.arcgis.com/3.24compact/"></script>
<script>
//Implement ArcGIS JavsScript API
    
      require([
        "esri/Color",
        "dojo/dom",
        "dojo/dom-geometry",
        "dojo/has",
        "dojo/on",
        "dojo/parser",   
        "dojo/ready",
        "dojo/window",
        "esri/geometry/Point",
        "esri/graphic",
        "esri/map", 
        "esri/symbols/SimpleLineSymbol",
        "esri/symbols/SimpleMarkerSymbol",
        "esri/InfoTemplate", 
        "esri/layers/ArcGISTiledMapServiceLayer", 	
        "esri/SpatialReference", 
        "dojo/store/JsonRest"
      ], function(Color, dom, domGeom, has, on, parser, ready, win, Point, Graphic, Map, SimpleLineSymbol, SimpleMarkerSymbol,InfoTemplate, ArcGISTiledMapServiceLayer,SpatialReference, sonRest ) {

        var map;
        var COMPASS_SIZE = 80;
        var pt;
        var graphic;
        var watchId;
        var compassFaceRadius, compassFaceDiameter;
        var needleAngle, needleWidth, needleLength, compassRing;
        var renderingInterval = -1;
        var currentHeading;
        var hasCompass;
        var compassHousing;
        var containerX;
        var containerY;
        var compassNeedleContext;

        ready(function() {
        parser.parse();

        var supportsOrientationChange = "onorientationchange" in window,
        orientationEvent = supportsOrientationChange ? "orientationchange" : "resize";

        window.addEventListener(orientationEvent, function () {
        orientationChanged();
        }, false);

        //add map
        map = new Map("map", {
        basemap: "streets-vector", //you may change the basemap here
        zoom: 16,
        slider: false
        });
		  
		  
		        
        //add layer
        var agoServiceURL = "http://montgomeryplans.org/arcgis4/rest/services/Overlays/MCAtlas_Park_Information/MapServer";
        var agoLayer = new ArcGISTiledMapServiceLayer(agoServiceURL);
        map.addLayer(agoLayer);
  
        //set info tag when user clicks on a points of interest
        var infoTemplate = new InfoTemplate ("Point Info");
            
		
        //load compass 
        on(map, "load", mapLoadHandler);
        loadCompass();
            
        
        // The HTML5 geolocation API is used to get the user's current position.
        function mapLoadHandler() {
          on(window, 'resize', map, map.resize);
          // check if geolocaiton is supported
          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(zoomToLocation, locationError, {maximumAge:0, timeout:10000, enableHighAccuracy:true});
          // retrieve update about the current geographic location of the device
            watchId = navigator.geolocation.watchPosition(showLocation, locationError , {maximumAge:0, timeout:10000, enableHighAccuracy:true});
          } else {
            alert("Browser doesn't support Geolocation. Visit http://caniuse.com to discover browser support for the Geolocation API.");
          }
        }

        function zoomToLocation(location) {
          pt = esri.geometry.geographicToWebMercator(new Point(location.coords.longitude, location.coords.latitude));
          addGraphic(pt);
          map.centerAndZoom(pt, 17);
        }

        function showLocation(location) {
          pt = esri.geometry.geographicToWebMercator(new Point(location.coords.longitude, location.coords.latitude));
          if (!graphic) {
            addGraphic(pt);
          } else {
            //move the graphic if it already exists
            graphic.setGeometry(pt);
          }
          map.centerAt(pt);
        }

        function locationError(error) {
          //error occurred so stop watchPosition
          if (navigator.geolocation) {
            navigator.geolocation.clearWatch(watchId);
          }
          switch (error.code) {
            case error.PERMISSION_DENIED:
              alert("Location not provided");
              break;

            case error.POSITION_UNAVAILABLE:
              alert("Current location not available");
              break;

            case error.TIMEOUT:
              alert("Timeout");
              break;

            default:
              alert("unknown error");
              break;
          }
        }

        // Add a pulsating graphic to the map
        function addGraphic(pt) {
          var symbol = new SimpleMarkerSymbol(SimpleMarkerSymbol.STYLE_CIRCLE, 12, new SimpleLineSymbol(SimpleLineSymbol.STYLE_SOLID, new Color([210, 105, 30, 0.5]), 8), new Color([210, 105, 30, 0.9]));
          graphic = new Graphic(pt, symbol);
          map.graphics.add(graphic);
  
   
        }

        function loadCompass() {
          compassHousing = dom.byId("compassHousing");
          // assign the compass housing dimensions
          compassHousing.style.height = compassHousing.style.width = COMPASS_SIZE + "px";
          // return the absolute position of the compass housing
          containerX = domGeom.position(compassHousing).x;
          containerY = domGeom.position(compassHousing).y;
          currentHeading = 0;
          needleAngle = 0;
          if (!buildCompassFace()) {
            return;
          }
          drawCompassFace();
          drawCompassNeedle();
          hasWebkit();
        }

        // Creates the diameter of the compass face
        // Creates the radius
        function buildCompassFace() {
          // compass housing diameter and radius
          compassFaceDiameter = COMPASS_SIZE;
          compassFaceRadius = compassFaceDiameter / 2;
          // needle length
          needleLength = compassFaceDiameter;
          // needle width
          needleWidth = needleLength / 10;
          // tick marks
          compassRing = compassFaceDiameter / 50;
          return true;
        }

        var compassFaceContext;
        // Draw the coppass face, text labels and font, and tick marks
        function drawCompassFace() {
          var compassFaceCanvas = dom.byId("compassFace");
          compassFaceCanvas.width = compassFaceCanvas.height = compassFaceDiameter;
          compassFaceContext = compassFaceCanvas.getContext("2d");
          compassFaceContext.clearRect(0, 0, compassFaceCanvas.width, compassFaceCanvas.height);

          // draw the tick marks and center the compass ring
          var xOffset, yOffset;
          xOffset = yOffset = compassFaceCanvas.width / 2;
          for (var i = 0; i < 360; ++i) {
            var x = (compassFaceRadius * Math.cos(degToRad(i))) + xOffset;
            var y = (compassFaceRadius * Math.sin(degToRad(i))) + yOffset;
            var x2 = ((compassFaceRadius - compassRing) * Math.cos(degToRad(i))) + xOffset;
            var y2 = ((compassFaceRadius - compassRing) * Math.sin(degToRad(i))) + yOffset;
            compassFaceContext.beginPath();
            compassFaceContext.moveTo(x, y);
            compassFaceContext.lineTo(x2, y2);
            compassFaceContext.closePath();
            compassFaceContext.stroke();
            i = i + 4;
          }

          // The measureText method returns an object, with one attribute: width.
          // The width attribute returns the width of the text, in pixels.
          compassFaceContext.font = "10px Arial";
          compassFaceContext.textAlign = "center";
          var metrics = compassFaceContext.measureText('N');
          compassFaceContext.fillText('N', compassFaceRadius, 15);
          compassFaceContext.fillText('S', compassFaceRadius, compassFaceDiameter - 10);
          compassFaceContext.fillText('E', (compassFaceRadius + (compassFaceRadius - metrics.width)), compassFaceRadius);
          compassFaceContext.fillText('W', 10, compassFaceRadius);
        }

        // Draw the compass needle
        function drawCompassNeedle() {
          var compassNeedle = dom.byId("compassNeedle");
          compassNeedle.width = compassNeedle.height = compassFaceDiameter;
          compassNeedle.style.left = Math.floor(compassFaceContext.width / 2) + "px";
          compassNeedle.style.top = Math.floor(compassFaceContext.height / 2) + "px";
          compassNeedleContext = compassNeedle.getContext("2d");
          compassNeedleContext.translate(compassFaceRadius, compassFaceRadius);
          compassNeedleContext.clearRect((compassNeedleContext.canvas.width / 2) * -1, (compassNeedleContext.canvas.height / 2) * -1, compassNeedleContext.canvas.width, compassNeedleContext.canvas.height);

          // The first step to create a path is calling the beginPath method. Internally, paths are stored as a list of sub-paths
          // (lines, arcs, etc) which together form a shape. Every time this method is called, the list is reset and we can start
          // drawing new shapes.

          // SOUTH
          compassNeedleContext.beginPath();
          compassNeedleContext.lineWidth = 1;
          compassNeedleContext.moveTo(0, 5);
          compassNeedleContext.lineTo(0, compassFaceRadius);
          compassNeedleContext.stroke();
          // circle around label
          compassNeedleContext.beginPath();
          compassNeedleContext.arc(0, compassFaceRadius - 15, 8, 0, 2 * Math.PI, false);
          compassNeedleContext.fillStyle = "#FFF";
          compassNeedleContext.fill();
          compassNeedleContext.lineWidth = 1;
          compassNeedleContext.strokeStyle = "black";
          compassNeedleContext.stroke();
          // S
          compassNeedleContext.beginPath();
          compassNeedleContext.moveTo(0, 0);
          compassNeedleContext.font = "normal 10px Verdana";
          compassNeedleContext.fillStyle = "#000";
          compassNeedleContext.textAlign = "center";
          compassNeedleContext.fillText("S", 0, compassFaceRadius - 10);
          // needle
          compassNeedleContext.beginPath();
          compassNeedleContext.fillStyle = "#000";
          compassNeedleContext.moveTo(0, 0);
          compassNeedleContext.lineTo(0, needleLength / 4);
          compassNeedleContext.lineTo((needleWidth / 4) * -1, 0);
          compassNeedleContext.fill();
          compassNeedleContext.beginPath();
          compassNeedleContext.fillStyle = "#000";
          compassNeedleContext.moveTo(0, 0);
          compassNeedleContext.lineTo(0, needleLength / 4);
          compassNeedleContext.lineTo(needleWidth / 4, 0);
          compassNeedleContext.fill();


          // NORTH
          compassNeedleContext.beginPath();
          compassNeedleContext.lineWidth = 1;
          compassNeedleContext.moveTo(0, 0);
          compassNeedleContext.lineTo(0, - compassFaceRadius);
          compassNeedleContext.stroke();
          // circle
          compassNeedleContext.beginPath();
          compassNeedleContext.arc(0, - (compassFaceRadius - 16), 8, 0, 2 * Math.PI, false);
          compassNeedleContext.fillStyle = "#FFF";
          compassNeedleContext.fill();
          compassNeedleContext.lineWidth = 1;
          compassNeedleContext.strokeStyle = "black";
          compassNeedleContext.stroke();
          // N
          compassNeedleContext.beginPath();
          compassNeedleContext.moveTo(0, 0);
          compassNeedleContext.font = "normal 10px Verdana";
          compassNeedleContext.fillStyle = "#000";
          compassNeedleContext.textAlign = "center";
          compassNeedleContext.fillText("N", 0, - (compassFaceRadius - 20));
          // needle
          compassNeedleContext.beginPath();
          compassNeedleContext.fillStyle = "#000";
          compassNeedleContext.moveTo(0, 0);
          compassNeedleContext.lineTo(0, (needleLength / 4) * -1);
          compassNeedleContext.lineTo((needleWidth / 4) * -1, 0);
          compassNeedleContext.fill();
          compassNeedleContext.beginPath();
          compassNeedleContext.fillStyle = "#000";
          compassNeedleContext.moveTo(0, 0);
          compassNeedleContext.lineTo(0, (needleLength / 4) * -1);
          compassNeedleContext.lineTo(needleWidth / 4, 0);
          compassNeedleContext.fill();

          // center pin color
          compassNeedleContext.beginPath();
          compassNeedleContext.arc(0, 0, 10, 0, 2 * Math.PI, false);
          compassNeedleContext.fillStyle = "rgb(255,255,255)";
          compassNeedleContext.fill();
          compassNeedleContext.lineWidth = 1;
          compassNeedleContext.strokeStyle = "black";
          compassNeedleContext.stroke();

          compassNeedleContext.beginPath();
          compassNeedleContext.moveTo(0, 0);
          compassNeedleContext.arc(0, 0, (needleWidth / 4), 0, degToRad(360), false);
          compassNeedleContext.fillStyle = "#000";
          compassNeedleContext.fill();
        }

        var orientationHandle;
        function orientationChangeHandler() {
          // An event handler for device orientation events sent to the window.
          orientationHandle = on(window, "deviceorientation", onDeviceOrientationChange);
          // The setInterval() method calls rotateNeedle at specified intervals (in milliseconds).
          renderingInterval = setInterval(rotateNeedle, 100);
        }

        var compassTestHandle;
        function hasWebkit() {
          if (has("ff") || has("ie") || has("opera")) {
            hasCompass = false;
            orientationChangeHandler();
            alert("Your browser does not support WebKit.");
          } else if (window.DeviceOrientationEvent) {
            compassTestHandle = on(window, "deviceorientation", hasGyroscope);
          } else {
            hasCompass = false;
            orientationChangeHandler();
          }
        }

        // Test if the device has a gyroscope.
        // Instances of the DeviceOrientationEvent class are fired only when the device has a gyroscope and while the user is changing the orientation.
        function hasGyroscope(event) {
          dojo.disconnect(compassTestHandle);
          if (event.webkitCompassHeading !== undefined || event.alpha != null) {
            hasCompass = true;
          } else {
            hasCompass = false;
          }
          orientationChangeHandler();
        }

        // Rotate the needle based on the device's current heading
        function rotateNeedle() {
          var multiplier = Math.floor(needleAngle / 360);
          var adjustedNeedleAngle = needleAngle - (360 * multiplier);
          var delta = currentHeading - adjustedNeedleAngle;
          if (Math.abs(delta) > 180) {
            if (delta < 0) {
              delta += 360;
            } else {
              delta -= 360;
            }
          }
          delta /= 5;
          needleAngle = needleAngle + delta;
          var updatedAngle = needleAngle - window.orientation;
          // rotate the needle
          dom.byId("compassNeedle").style.webkitTransform = "rotate(" + updatedAngle + "deg)";
        }

        function onDeviceOrientationChange(event) {
          var accuracy;
          if (event.webkitCompassHeading !== undefined) {
            // Direction values are measured in degrees starting at due north and continuing clockwise around the compass.
            // Thus, north is 0 degrees, east is 90 degrees, south is 180 degrees, and so on. A negative value indicates an invalid direction.
            currentHeading = (360 - event.webkitCompassHeading);
            accuracy = event.webkitCompassAccuracy;
          } else if (event.alpha != null) {
            // alpha returns the rotation of the device around the Z axis; that is, the number of degrees by which the device is being twisted
            // around the center of the screen
            // (support for android)
            currentHeading = (270 - event.alpha) * -1;
            accuracy = event.webkitCompassAccuracy;
          }

          if (accuracy < 11) {
            compassNeedleContext.fillStyle = "rgba(0, 205, 0, 0.9)";
          } else if (accuracy >= 15 && accuracy < 25) {
            compassNeedleContext.fillStyle = "rgba(255, 255, 0, 0.9)";
          } else if (accuracy > 24) {
            compassNeedleContext.fillStyle = "rgba(255, 0, 0, 0.9)";
          }
          compassNeedleContext.fill();

          if (renderingInterval == -1) {
            rotateNeedle();
          }
        }

        // Convert degrees to radians
        function degToRad(deg) {
          return (deg * Math.PI) / 180;
        }

        // Handle portrait and landscape mode orientation changes
        function orientationChanged() {
          if (map) {
            map.reposition();
            map.resize();
          }
        }
      });
          
        
      //adding points of interse on the map, you can add as many as you want with different park site and geolocation information. The code are repeative with different geolocation info for each point 
      //before adding the points, check if the user has been to this point. If YES, set color of the point to grey, if No, set color of point to red
        <?php include ('mysqli_connect.php');
        //get all the points of interest this user has been to 
        $query = "QUERY FOR GETTING THE USER'S VISIT RECORDS" ;
        $sql = mysqli_query($dbc, $query);
        if (!$sql) {
        printf("Error: %s\n", mysqli_error($dbc));
        exit();
        }
        while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC)) {
        //check if user's visited points include point "1"
        if ($row['XXX_id'] == $user && $row['YYY_id'] == '1'){           
        //if Yes, set the point color to grey
        $color = array (150, 151, 149, 0.5);
        $color2 = array(150, 151, 149);
        break;
        }
        //if No, set the point color to red            
        else   { 
        $color = array (255, 0 ,0, 0.5);  
        $color2 = array (255, 0 ,0);  
        }}
        ?>    
             //use function to add a point to the map                  
             function addPointGraphics()
             {
          
             let color = new Color (<?php echo json_encode($color) ; ?>)    
             let color2 = new Color (<?php echo json_encode($color2) ; ?>)  
             let symbol = new SimpleMarkerSymbol (SimpleMarkerSymbol.STYLE_CIRCLE, 9,
             new SimpleLineSymbol(SimpleLineSymbol.STYLE_SOLID, color,9),  color2);
             //set the site info 
             let attr = {"site name": "\"A Dahlia Garden\"", "park name":"Agricultural History Farm Park", "compass coordinates":"39 9\'51\"N 77 8\'2\"W", lat: -77.132439, long: 39.165291}  ; 
             //mark this site to the following geolocation    
             let loc= new Point(-77.132439, 39.165291);
             let graphic = new Graphic(loc, symbol, attr, infoTemplate);
             map.graphics.add(graphic);
             }
             map.on("load", addPointGraphics);

        //you can add more park site if you want...              

   
  </script>
  </head>     


  
<body>
    
<!-- navigation bar -->
<div class="topnav" id="myTopnav">
<a href="" class="active">Welcome: <?php echo $username; ?></a>
<a href="">Leaderboard</a>
<a href="">Achievements</a>
<a href="">Rewards</a>
<a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="myFunction()">&#9776;</a>
<a href="logout.php">Logout</a>
</div>

<!-- navigation bar -->    
<div id="map" style="width:100%; height:100%;">   

<!-- map here -->       
<p id="demo"></p> 
 
<!-- compass here --> 
    <article id="compassHousing">
      <canvas id="compassFace" ></canvas>
      <canvas id="compassNeedle"></canvas>
    </article>  
</div>
         
<!-- site questions forms, each park site have two questions upon arrival, answer the first one correctly to see the second one-->
     
<!-- first site questions-->   
    
<!-- get the site questions -->   
<div id="Q1" class="modal">       
<div class="modal-content">
    
<!-- dispaly the first question -->   
<form id="Q1.1" class ='firstquestion'> 
<h2><strong>You have arrived! Answer the following two questions.</strong></h2>
<h2>What material are the flower beds made of?</h2>  
<p>Question 1/2</p>
    
<!-- check if answer is correst for the first question -->    
<p id = 'inputerror1.1'></p>
    
<!-- the right answer's value is 1 --> 
Wood <input type="radio"   name="yesno" value = '1'><br>  
Brick <input type="radio"   name="yesno" value = '2'><br>
Concrete <input type="radio"   name="yesno" value = '3'><br>
Stone <input type="radio"   name="yesno" value = '4'><br>
    
<!-- fade the first question and display the second question -->   
<br><input  onclick="return validateForm1q1()" type="button" value="Next">  
</form>
    
<!-- display the second question -->   
<form id="Q1.2" class = 'secondquestion'> 
<h2>When the flowers are in bloom, what are two colors you see?</h2>
<p>Question 2/2</p>
    
<!-- check if answer is correst for the second question -->   
<p id = 'inputerror1.2'></p>
    
<!-- the right answer's value is always 1 --> 
Any combination of two of the colors <input type="radio"   name="yesno2" value = '1'><br>
Pink <input type="radio"   name="yesno2" value = '2'><br>
Yellow <input type="radio"   name="yesno2" value = '3'><br>
Orange <input type="radio"   name="yesno2" value = '4'><br>

<!--fade the question pop window, initate php and record the user has done site 1 question to prevent further pop-up upon revisit   -->    
<br><input  onclick="return validateForm1q2();" type="button" value="Submit"> 
</form>
</div>
</div>
   
<!-- you may add more park site quesitons. -->   
       
</body>    
    
    
<script type="text/javascript" ,  src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>

    
    //navgation menu function     
    function myFunction() {
    var x = document.getElementById("myTopnav");
    if (x.className === "topnav") {
        x.className += " responsive";
    } else {
        x.className = "topnav";
           }
    }
      

    
    //functions that track and display user's geolocation    
    var lat;     //set user's lat geolication 
    var long;    //set user's long geolication 
    var lat4Decimals;   
    var long4Decimals;
    
    window.onload=function(){
    getLocation();
     }
      
    var x = document.getElementById("demo");
    function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(showPosition);
    } else { 
        x.innerHTML = "Geolocation is not supported by this browser.";}
    }
    
    
    function showPosition(position) {
    x.innerHTML="Current Latitude: " + position.coords.latitude + 
    "<br>Current Longitude: " + position.coords.longitude;
     lat = position.coords.latitude;
    long = position.coords.longitude;
        
    //trim lat and long down to 4 deciamls without rounding
    lat4Decimals = lat.toString().match(/^-?\d+(?:\.\d{0,4})?/)[0];
    long4Decimals = long.toString().match(/^-?\d+(?:\.\d{0,4})?/)[0];
        
        
        
    
    //compare the user's geolcation against site 1's geolocation. 
    //Once the user's current geolocation is a match, record the user has arrived at site 1 in the database using AJAX.
    //Receive data back from the database, cheching if the user has answered the quesions about this site yet. If not, proceed.
    //Display question 1 
    //Check if answer is right
    //Display question 2 
    //Check if answer is right
    //Record in the databse that the user has answered the questions for site 1 using AJAX so the quesitons wont display again when revisit the same site.
    //The user receives a score for visiting this site and the score is recorded in the datbase.
    

    
    //compare the user's geolcation against site 1's geolocation. 
    function compare1()
    
    { if (lat4Decimals == 39.1652 && long4Decimals == -77.1324)      
      {
    //if they match, insert to the database - this user has arrived at site 1 
    post1();
      }
    }
      
    //insert to the database using AJAX, get "data" back from the database from the insert.php, if the return value is 0, it means user has not answer the question form yet. Go ahead and display the question. 
    function post1(){ 
     var userID = "<?php echo $user ?>";
     $.ajax({
     url: 'insert.php',
     type: 'POST',
     data:{
    userId: userID,
    // site 1
    placeId: 1  
        },
    success: function(data) {
    //take value 0 in the function, the function sees 0, then dispaly the question form for site 1
    showme1(data);     
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
    // handle any network/server errors here
    console.log("Status: " + textStatus); 
    console.log("Error: " + errorThrown); 
        }
    });
        }
            
      
    //dispaly question form   
    function showme1(check){
  
    if(parseInt(check) == 0)
    {                        
       var modal = document.getElementById('Q1');
       modal.style.display = "block";
                   
     }
            
       else { var modal = document.getElementById('Q1');
       modal.style.display = "none"}
     }
        
       
    //check user's answer for question 1
    function validateForm1q1() {
    var radios = document.getElementsByName("yesno");
    var formValid = false;

    var selectedVal = "";
    var selected = $("input[type='radio'][name='yesno']:checked");
    selectedVal = selected.val();

    var i = 0;
    while (!formValid && i < radios.length) {
    if (radios[i].checked) formValid = true;
    i++;        
    }

    
    if (!formValid) 
    {
  
     document.getElementById("inputerror1.1").innerHTML = "<span style='color: red;'>Must select an option!</span>";
     return formValid;
    }
    
 
    else if (selectedVal!= '1')
    document.getElementById("inputerror1.1").innerHTML = "<span style='color: red;'>Wrong answer, please try again!</span>";
    else { 
        
    //show question 2 after checking
    next_step1();
         }
       }
      
    
    //show question 2 after checking
    function next_step1() {
    document.getElementById("Q1.1").style.display = "none";
    document.getElementById("Q1.2").style.display = "block";
     
     }
 

    //check user's answer for question 2     
    function validateForm1q2() {
    var radios = document.getElementsByName("yesno2");
    var formValid = false;
    var selectedVal = "";
    var selected = $("input[type='radio'][name='yesno2']:checked");
    selectedVal = selected.val();
    var i = 0;
    while (!formValid && i < radios.length) {
    if (radios[i].checked) formValid = true;
    i++;        
    }

    
    if (!formValid) 
    {
    document.getElementById("inputerror1.2").innerHTML = "<span style='color: red;'>Must select an option!</span>";
  
    return formValid;
    }
    
    else if (selectedVal!= '1') 
    document.getElementById("inputerror1.2").innerHTML = "<span style='color: red;'>Wrong answer, please try again!</span>";

    else { 
    //update 'from' filed from 0 to 1 in database. User has answered the site 1 questions, it wont show again
    update1();
         } }
    
      
           
    //update 'from' filed from 0 to 1 in database. User has answered the site 1 questions, it wont show again
       function update1(){ 
       var userID = "<?php echo $user ?>";
       $.ajax({
       url: 'update1.php',
       type: 'POST',
       data:{
       userId: userID,
       placeId: 1 
        },
        success: function(serverResponse) {
     // handle output from server here ('Success!' or 'Error' from PHP script)
       console.log("Successful");
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            // handle any network/server errors here
            console.log("Status: " + textStatus); 
            console.log("Error: " + errorThrown); 
        }
    });
         window.location.reload();
         }    
         
                
    
 //end of insert/update site 1 info   

</script>
</html>
