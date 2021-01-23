<?php

function getCustomHistory($conn, $appointmentID, $typeCode){
	$sql="SELECT
		h.name AS historyName,
		h.shortName historyShortName,
		pathis.historyResult AS historyResult
		FROM doctor_feed.`prescription_history` ph
		JOIN doctor_feed.patient_history pathis ON ph.patientHistoryID = pathis.patientHistoryID
		JOIN doctor_feed.history h ON pathis.historyID = h.historyID
		WHERE 1 = 1 AND h.typeCode = '$typeCode' 
		AND ph.appointmentID = $appointmentID";
	$result=mysqli_query($conn, $sql);
	return $result;
}

function getPresPastDisease($conn, $appointmentID, $status){
	$sql="SELECT
		cd.diseaseName
		FROM doctor_feed.`prescription_past_disease` pd
		JOIN doctor_feed.patient_past_disease ppd ON ppd.patientPastDiseaseID = pd.pastDiseaseID
		JOIN doctor_feed.content_disease cd ON pd.pastDiseaseID = cd.diseaseID
		WHERE 1 = 1 
		AND ppd.isPresent = $status
		AND pd.appointmentID = $appointmentID";
	$result=mysqli_query($conn, $sql);
	return $result;
}

function getPresFamilyDisease($conn, $appointmentID){
	$sql="SELECT
		cr.relationName,
		cd.diseaseName,
		pfh.present
		FROM doctor_feed.`prescription_family_disease` pfd
		JOIN doctor_feed.patient_family_history pfh ON pfd.familyDiseaseID = pfh.familyHistoryID
		JOIN doctor_feed.content_relation cr ON pfh.relation = cr.relationID
		JOIN doctor_feed.content_disease cd ON pfh.diseaseID = cd.diseaseID
		WHERE 1 = 1
		AND pfd.appointmentID = $appointmentID";
	$result=mysqli_query($conn, $sql);
	return $result;
}
?>