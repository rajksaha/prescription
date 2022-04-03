<?php

function getPrescribedVital($conn, $appointmentID){

	$sql = "SELECT
		cv.vitalName,
		cv.vitalUnit,
		pv.vitalResult
		FROM prescription_vital pv
		JOIN content_vital cv ON pv.vitalID = cv.vitalID
	WHERE pv.appointmentID= '$appointmentID'";

	$result = $conn->prepare($sql);
	$result->execute();
	return $result;

}

?>
