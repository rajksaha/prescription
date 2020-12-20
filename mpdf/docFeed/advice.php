<?php
function getPrescribedAdvice($appointmentID){
	$sql = "SELECT
				pa.`presAdviceID`,
				pa.`appointmentID`,
				pa.`adviceID`,
				ca.advice,
				ca.lang,
				pa.`updatedBy`,
				pa.`updatedOn`,
				pa.`createdBy`,
				pa.`createdOn`
			FROM doctor_feed.`prescription_advice` pa
			JOIN doctor_feed.content_advice ca ON pa.adviceID = ca.adviceID
			WHERE pa.appointmentID = '$appointmentID'";

	$result=mysql_query($sql);
	return $result;
}
?>