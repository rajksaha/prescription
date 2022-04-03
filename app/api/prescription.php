<?php

header("Content-Type:application/json");
if (isset($_GET['appointmentID']) && $_GET['appointmentID']!="") {
	include_once '../config/config.php';
	include_once '../mpdf/default.php';
		
		$host        = "host = $host_val";
    	$port        = "port = $port_val";
    	$dbname      = "dbname = $db_val";
    	$credentials = "user = $user_val password=$ps_val";
    	
    	
		try {
			$dsn = "pgsql:host=$host_val;port=$port_val;dbname=$db_val";
			// make a database connection
			$pdo = new PDO($dsn, $user_val, $ps_val);
		
			if ($pdo) {
				//echo "Connected to the $db_val database successfully!";
			}
		} catch (PDOException $e) {
			echo "ERROR: $e";
			die($e->getMessage());
		}
	
	$appointmentID = $_GET['appointmentID'];
	
	try {
		$pdf = new PDF();
		return $pdf.preparePrescription($pdo, $appointmentID);
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