<?php

 $link = mysqli_connect("shareddb-h.hosting.stackcp.net", "techargro-arduino-3333823c", "c6774663dd", "techargro-arduino-3333823c");

    if(mysqli_connect_error()){
        die("Connection was not Succesful.");
    }

	if($_POST){
      
     	$humidity = $_POST["humidity"];
      	$temperature = $_POST["temperature"];
      	$moisture =  $_POST["moisture"];
      
      	$query = "INSERT INTO `techargo`  (humidity, temperature, moisture) VALUES('".$humidity."','".$temperature."', '".$moisture."')";

        if($link->query($query) === TRUE){

            echo "Done";

        }else{

            echo "failed";
        }
    }
    
     
      
    



	


?>