<?php

function getAppointmentInfo($conn, $appointmentID){

	$sql="SELECT
		appointmentID AS appointmentID,
		doctorID AS doctorID,
		patientID AS patientID,
		appDate AS appDate,
		appTime AS appTime,
		status,
		appointmentType AS appointmentType,
		updatedBy AS updatedBy,
		updatedOn AS updatedOn,
		createdBy AS createdBy,
		createdOn AS createdOn
	FROM appointment
	WHERE appointmentID = '$appointmentID'";

	
	$sth = $conn->prepare($sql);
	
	$sth->execute();
	
	return $sth->fetch(PDO::FETCH_ASSOC);
}
?>
