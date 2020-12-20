<?php

function getAppointmentInfo($conn, $appointmentID){

	$sql = "SELECT
			`appointmentID`,
			`doctorID`,
			`patientID`,
			`date`,
			`time`,
			`status`,
			`appointmentType`,
			`updatedBy`,
			`updatedOn`,
			`createdBy`,
			`createdOn`
		FROM `doctor_feed`.`appointment`
		WHERE appointmentID = '$appointmentID'";
	
	return mysql_fetch_assoc(mysql_query($sql));
}
?>
