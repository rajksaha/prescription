<?php

header("Content-Type:application/json");
if (isset($_GET['appointmentID']) && $_GET['appointmentID']!="") {
	include_once '../mpdf/default.php';
	include_once('../mpdf/docFeed/appointment.php');
	
		getenv('DBHOST') ? $db_host=getenv('MYSQL_DBHOST') : $db_host="localhost";
    	getenv('DBUSER') ? $db_user=getenv('MYSQL_DBUSER') : $db_user="postgres";
    	getenv('DBPASS') ? $db_pass=getenv('MYSQL_DBPASS') : $db_pass="rajksaha";
    	getenv('DBNAME') ? $db_name=getenv('MYSQL_DBNAME') : $db_name="doctor_feed";
    	getenv('SCHEMA_NAME') ? $sc_name=getenv('SCHEMA_NAME') : $sc_name="public";
    	
    	$host        = "host = localhost";
    	$port        = "port = 5432";
    	$dbname      = "dbname = doctor_feed";
    	$credentials = "user = postgres password=rajksaha";
    	
        $conn = null;
  
		$db = pg_connect( "$host $port $dbname $credentials"  );
	   if(!$db) {
	      echo "Error : Unable to open database\n";
	   } else {
	      echo "Opened database successfully\n";
	   }
	
	$appointmentID = $_GET['appointmentID'];
	
	try {
		$appData = getAppointmentInfo($db,$appointmentID);
		echo "Status: " . $appData['doctorid'];
	}
	//catch exception
	catch(Exception $e) {
		echo 'Message: ' .$e->getMessage();
	}
	return $data;
}else{
	response(NULL, NULL, 400,"Invalid Request");
}
?>