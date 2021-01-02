<?php

function getAppointmentInfo($conn, $appointmentID){

	$sql = "SELECT
			`appointmentID`,
			`doctorID`,
			`patientID`,
			`appDate`,
			`appTime`,
			`status`,
			`appointmentType`,
			`updatedBy`,
			`updatedOn`,
			`createdBy`,
			`createdOn`
		FROM `doctor_feed`.`appointment`
		WHERE appointmentID = '$appointmentID'";
	$result = mysqli_query($conn, $sql);
	if($result != null){
		return mysqli_fetch_assoc($result);
	}
	return $result;
}
?>
