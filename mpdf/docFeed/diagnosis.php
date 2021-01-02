<?php
function getPrescribedDiagnosis($conn, $appointmentID){

	$sql = "SELECT
			pd.`diagnosisID`,
			pd.`appointmentID`,
			pd.`diseaseID`,
			cd.diseaseName AS diseaseName,
			pd.`note`,
			pd.`updatedBy`,
			pd.`updatedOn`,
			pd.`createdBy`,
			pd.`createdOn`
		FROM doctor_feed.`prescription_diagnosis` pd
		JOIN doctor_feed.content_disease cd ON pd.diseaseID = cd.diseaseID
	WHERE pd.`appointmentID`= '$appointmentID'";

	return mysqli_query($conn, $sql);

}
?>