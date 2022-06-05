<?php

function getAppointmentInfo($conn, $appointmentID){

	$sql="SELECT
		appointmentid,
		doctorid,
		patientid,
		appdate,
		apptime,
		status,
		appointmenttype AS appointmentType
	FROM appointment
	WHERE appointmentid = '$appointmentID'";

	$sth = $conn->prepare($sql);
	$sth->execute();
	return $sth->fetch(PDO::FETCH_ASSOC);
}
?>
