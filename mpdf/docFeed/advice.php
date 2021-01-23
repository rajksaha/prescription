<?php
function getPrescribedAdvice($conn, $appointmentID){
	$sql = "SELECT
				ca.advice,
				ca.lang
			FROM doctor_feed.`prescription_advice` pa
			JOIN doctor_feed.content_advice ca ON pa.adviceID = ca.adviceID
			WHERE pa.appointmentID = '$appointmentID'";

	return mysqli_query($conn, $sql);
}
?>