<?php

function getPrescribedVital($conn, $appointmentID){

	$sql = "SELECT
		cv.vitalName,
		cv.vitalUnit,
		pv.`vitalResult`
		FROM `prescription_vital` pv
		JOIN doctor_feed.content_vital cv ON pv.vitalID = cv.vitalID
	WHERE pv.`appointmentID`= '$appointmentID'";

	return mysqli_query($conn, $sql);

}

?>
