<?php

function getCertDetail($conn, $appointmentID){

	$sql = "SELECT medicalCertID, appointmentID, startDate, endDate, addComment, updatedBy, createdOn, createdBy
		FROM medical_certificate
		WHERE appointmentID = '$appointmentID'";

	$result = $conn->prepare($sql);
	$result->execute();
	return $result->fetch(PDO::FETCH_ASSOC);

}

?>
