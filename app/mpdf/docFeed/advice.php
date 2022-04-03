<?php
function getPrescribedAdvice($conn, $appointmentID){
	$sql = "SELECT
				ca.advice,
				ca.lang
			FROM prescription_advice pa
			JOIN content_advice ca ON pa.adviceID = ca.adviceID
			WHERE pa.appointmentID = '$appointmentID'";

	$sth = $conn->prepare($sql);
	$sth->execute();
	return $sth;
}
?>