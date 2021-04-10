<?php

header("Content-Type:application/json");
if (isset($_GET['appointmentID']) && $_GET['appointmentID']!="") {
	include_once '../mpdf/medicalCert.php';
	include_once '../config/database.php';
	$host = "bottom-up-dev.c8lq1wttwtce.ap-southeast-1.rds.amazonaws.com:3306";
	$db_name = "doctor_feed";
	$username = "admin";
	$password = "5tgbvfr4";
	$con = mysqli_connect($host,$username,$password,$db_name);
	if (mysqli_connect_errno()){
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
		die();
	}
	$appointmentID = $_GET['appointmentID'];
	
	try {
		$pdf = new PDF();
		return $pdf.TestMethod($con, $appointmentID);
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