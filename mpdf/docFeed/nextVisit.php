<?php
function getPrescribedNextVisit($conn, $appointmentID){

	$sql = "SELECT
			pnv.`nextVisitType`,
			pnv.`visitDate`,
			pnv.`numOfDay`,
			pnv.`durationType`,
			CDT.bangla AS durationTypeName
		FROM `prescription_next_visit` pnv
		LEFT JOIN doctor_feed.content_duration_type CDT ON pnv.durationType = CDT.durationType
		WHERE pnv.`appointmentID` =  '$appointmentID'";

	$result=mysqli_query($conn, $sql);

	return $result;

}
?>