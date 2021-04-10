<?php

function getCertDetail($conn, $appointmentID){

	$sql = "SELECT medicalCertID, appointmentID, startDate, endDate, addComment, updatedBy, `createdOn`, `createdBy`
		FROM medical_certificate
		WHERE appointmentID = '$appointmentID'";
	$res = mysqli_query($conn, $sql);
	return mysqli_fetch_assoc($res);

}

?>
