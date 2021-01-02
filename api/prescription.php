<?php

header("Content-Type:application/json");
if (isset($_GET['appointmentID']) && $_GET['appointmentID']!="") {
	include_once '../mpdf/default.php';
	include_once '../config/database.php';
	$db = new Database();
	$conn = $db.getConnection();
	$appointmentID = $_GET['appointmentID'];
	$pdf = new PDF();
	return $pdf.preparePrescription(db, $appointmentID);
}else{
	response(NULL, NULL, 400,"Invalid Request");
}
?>