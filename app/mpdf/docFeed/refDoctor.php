<?php
function getPrescribedReffredDoctor($conn, $appointmentID){

	$sql = "SELECT
			rd.doctorName,
			rd.doctorAddress
		FROM prescription_reference pr
		JOIN referred_doctor rd ON pr.referredDoctorID = rd.referredDoctorID
	WHERE pr.appointmentID= '$appointmentID'";

	$result = $conn->prepare($sql);
	$result->execute();
	return $result;

}
?>