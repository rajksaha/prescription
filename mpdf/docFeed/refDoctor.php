<?php
function getPrescribedReffredDoctor($conn, $appointmentID){

	$sql = "SELECT
			rd.doctorName,
			rd.doctorAddress
		FROM `prescription_reference` pr
		JOIN doctor_feed.referred_doctor rd ON pr.referredDoctorID = rd.referredDoctorID
	WHERE pr.`appointmentID`= '$appointmentID'";

	return mysqli_query($conn, $sql);

}
?>