<?php

function getCertDetail($conn, $appointmentID){

	$sql = "SELECT medicalCertID, appointmentID, startDate, endDate, addComment, updatedBy, createdOn, createdBy
		FROM medical_certificate
		WHERE appointmentID = '$appointmentID'";
	$res = pg_query($conn, $sql);
	return pg_fetch_assoc($res, 0);

}

?>
