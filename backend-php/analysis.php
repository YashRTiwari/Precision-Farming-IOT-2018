<?php
 
$humidity = array();
$temperature = array();
$moisture = array();
$temp = array();
$idLast = 0;

$link = mysqli_connect("shareddb-h.hosting.stackcp.net", "techargro-arduino-3333823c", "c6774663dd", "techargro-arduino-3333823c");

    if(mysqli_connect_error()){
        die("Connection was not Succesful.");
    }


$query = "SELECT * FROM techargo";

if($result = mysqli_query($link,$query)){

   while($row = mysqli_fetch_array($result)){
    	$i = $row[0];
    	$h = $row[2];
 		array_push($humidity, array("x" => $i,"y" => $h));
     	
     	$t = $row[3];
     	array_push($temperature, array("x" => $i,"y" => $t));
     	$m = 100 - $row[4];
     	array_push($moisture, array("x" => $i,"y" => $m));
     	$idLast = $row[0];
   }
}



?>

<html>
<head>
  
  <style type = "text/css">
    
    .real{
    	display : flex;
      	width : 25%;
    }
    .flex-display{
    	display : flex;
      justify-content : space-evenly;
      padding : 20px;
      border-style: solid;
    }
    
    .chart {
    	display : flex;
      	height : 800px;
      	width : 100%;
      	margin : 20px;
    }
    
     .chartDiv{
         height: 800px; 
        width: 95%;
        display : flex;
        flex-direction: column;
		
      }

  </style>
  
  <script type="text/javascript">
    
      window.onload = function () {
 
  var humidity = <?php echo json_encode($humidity, JSON_NUMERIC_CHECK); ?>;
  var temperature = <?php echo json_encode($temperature, JSON_NUMERIC_CHECK); ?>;
  var moisture = <?php echo json_encode($moisture, JSON_NUMERIC_CHECK); ?>;

  
var humC = new CanvasJS.Chart("humidity", {
	animationEnabled: true,
  	zoomEnabled:true,
  theme: "dark1",  
	title:{
		text: "Humidity"
	},
	axisY: {
		title: "Humidity in %",
		suffix: "%"
	},
	data: [{
		type: "spline",
		markerSize: 1,
		dataPoints: humidity, 
      	lineColor: 	"rgb(255,0,0)"
	}
          ]
});
 
humC.render();
  
  var tempC = new CanvasJS.Chart("temperature", {
	animationEnabled: true,
    zoomEnabled:true,
    theme: "dark1",  
	title:{
		text: "Temperature"
	},
	axisY: {
		title: "Temperature in Celsius",
		suffix: "C"
	},
	data: [{
		type: "spline",
		markerSize: 1,
		dataPoints: temperature, 
      	lineColor: 	"rgb(0,255,0)"
	}
          ]
});
 
tempC.render();
  
  var moiC = new CanvasJS.Chart("moisture", {
	animationEnabled: true,
    zoomEnabled:true,
    theme: "dark1",  
	title:{
		text: "Moisture"
	},
	axisY: {
		title: "Moisture in %",
		suffix: "%"
	},
	data: [{
		type: "spline",
		markerSize: 1,
		dataPoints: moisture, 
      	lineColor: 	"rgb(255,0,0)"
	}]
});
 
moiC.render();
  

}



 
    
</script>
  
  
  
  

</head>

<body>

  <h3 style = "text-align : center;">Real Time data from Moisture, Humidity and Temperature sensor.</h3>
    <div class = "flex-display" >
      
	<div class = "real" >Humidity: <span id = "humValue" ></span>%</div>
  	
  	<div class = "real" >Temperature: <span id = "tempValue" ></span>Celcius</div>
      <div class = "real" >Moisture: <span id = "moiValue" ></span>%</div>
  	</div>
  	<div class = "chartDiv">
	<div id="humidity" class = "chart" ></div>
  	<div id="temperature" class = "chart" ></div>
  	<div id="moisture" class = "chart"  ></div>
  	</div>
  	
  
  	
  	

  <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
  
  <script src="https://www.gstatic.com/firebasejs/4.13.0/firebase-app.js"></script>
        <script src="https://www.gstatic.com/firebasejs/4.13.0/firebase-database.js"></script>
        <script src="https://www.gstatic.com/firebasejs/4.13.0/firebase.js"></script>

       <script>
         
    var liveH = 20;
   // Initialize Firebase
          var config = {
            apiKey: "AIzaSyDbUFq0FTmYzrjwmuyTQwcyzZJPhNPwBB8",
            authDomain: "arduino-projects-74ff9.firebaseapp.com",
            databaseURL: "https://arduino-projects-74ff9.firebaseio.com",
            projectId: "arduino-projects-74ff9",
            storageBucket: "arduino-projects-74ff9.appspot.com",
            messagingSenderId: "1062833185725"
          };
          firebase.initializeApp(config);
           
           
           var database = firebase.database();
           
        
           var h = firebase.database().ref('humidity/');
            h.on('value', function(snapshot) {
                   document.getElementById("humValue").innerHTML = snapshot.val();
              
            });
         
         var t = firebase.database().ref('temperature/');
            t.on('value', function(snapshot) {
                   document.getElementById("tempValue").innerHTML = snapshot.val();
              
            });
         var m = firebase.database().ref('moisture/');
            m.on('value', function(snapshot) {
                   document.getElementById("moiValue").innerHTML = 100 - snapshot.val();
            });
  


</script>
  
</body>
</html>   