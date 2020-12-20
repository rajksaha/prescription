<?php
function getPrescribedNextVisit($appointmentID){

	$sql = "SELECT
			`appointmentID`,
			`nextVisitType`,
			`date`,
			`numOfDay`,
			`dayType`,
			`updatedBy`,
			`updatedOn`,
			`createdBy`,
			`createdOn`
		FROM `prescription_next_visit`
		WHERE `appointmentID` =  '$appointmentID'";

	$result=mysql_query($sql);

	return $result;

}
?>