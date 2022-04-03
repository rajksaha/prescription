<?php

function getPrescribedComplain($conn, $appointmentID){

	$sql = "SELECT
			cs.symptomName,
			pc.durationNum,
			pc.durationType,
			CDT.bangla AS durationTypeName,
			pc.detail
		FROM prescription_complain pc
		JOIN content_symptom cs ON pc.symptomID = cs.symptomID
		LEFT JOIN content_duration_type CDT ON pc.durationType = CDT.durationType
		WHERE pc.appointmentID = '$appointmentID' ORDER BY pc.complainID";

	$sth = $conn->prepare($sql);
	$sth->execute();
	return $sth;

}
?>