<?php
function getPrescribedDiagnosis($conn, $appointmentID){

	$sql = "SELECT
			cd.diseaseName AS diseaseName,
			pd.note
		FROM prescription_diagnosis pd
		JOIN content_disease cd ON pd.diseaseID = cd.diseaseID
	WHERE pd.appointmentID= '$appointmentID'";

	$sth = $conn->prepare($sql);
	$sth->execute();
	return $sth->fetch(PDO::FETCH_ASSOC);

}
?>