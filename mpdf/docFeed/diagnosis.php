<?php
function getPrescribedDiagnosis($conn, $appointmentID){

	$sql = "SELECT
			cd.diseaseName AS diseaseName,
			pd.`note`
		FROM doctor_feed.`prescription_diagnosis` pd
		JOIN doctor_feed.content_disease cd ON pd.diseaseID = cd.diseaseID
	WHERE pd.`appointmentID`= '$appointmentID'";

	return mysqli_query($conn, $sql);

}
?>