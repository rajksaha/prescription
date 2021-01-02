<?php

function getPrescribedComplain($conn, $appointmentID){

	$sql = "SELECT
			pc.`complainID`,
			pc.`appointmentID`,
			pc.`symptomID`,
			cs.symptomName,
			pc.`durationNum`,
			pc.`durationType`,
			CDT.`bangla` AS durationTypeName,
			pc.`detail`,
			pc.`updatedBy`,
			pc.`updatedOn`,
			pc.`createdBy`,
			pc.`createdOn`
		FROM doctor_feed.`prescription_complain` pc
		JOIN doctor_feed.content_symptom cs ON pc.symptomID = cs.symptomID
		LEFT JOIN doctor_feed.content_duration_type CDT ON pc.durationType = CDT.durationType
		WHERE pc.`appointmentID` = '$appointmentID' ORDER BY pc.complainID";

	$result=mysqli_query($conn, $sql);

	return $result;

}
?>