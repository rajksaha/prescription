<?php

function getPrescribedVital($appointmentID){

	$sql = "SELECT
		pv.`presVitalID`,
		pv.`appointmentID`,
		pv.`vitalID`,
		cv.vitalName,
		cv.vitalUnit,
		pv.`vitalResult`,
		pv.`updatedBy`,
		pv.`updatedOn`,
		pv.`createdBy`,
		pv.`createdOn`
		FROM `prescription_vital` pv
		JOIN doctor_feed.content_vital cv ON pv.vitalID = cv.vitalID
	WHERE pv.`appointmentID`= '$appointmentID'";

	$result=mysql_query($sql);

	return $result;

}

?>