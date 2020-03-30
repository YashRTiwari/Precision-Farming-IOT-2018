<?php

 $link = mysqli_connect("OMITTED_FOR_SECURITY_REASONS", 
			"OMITTED_FOR_SECURITY_REASONS", 
			"OMITTED_FOR_SECURITY_REASONS", 
			"OMITTED_FOR_SECURITY_REASONS");

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
