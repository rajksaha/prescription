<?php

function getCustomHistory($conn, $appointmentID, $typeCode){
	$sql="SELECT
		h.name AS historyName,
		h.shortName historyShortName,
		pathis.historyResult AS historyResult
		FROM prescription_history ph
		JOIN patient_history pathis ON ph.patientHistoryID = pathis.patientHistoryID
		JOIN history h ON pathis.historyID = h.historyID
		WHERE 1 = 1 AND h.typeCode = '$typeCode' 
		AND ph.appointmentID = '$appointmentID'";
	$result = $conn->prepare($sql);
	$result->execute();
	return $result;
}

function getPresPastDisease($conn, $appointmentID, $status){
	$sql="SELECT
		cd.diseaseName
		FROM prescription_past_disease pd
		JOIN patient_past_disease ppd ON ppd.patientPastDiseaseID = pd.pastDiseaseID
		JOIN content_disease cd ON pd.pastDiseaseID = cd.diseaseID
		WHERE 1 = 1 
		AND ppd.isPresent = $status
		AND pd.appointmentID = '$appointmentID'";
	$result = $conn->prepare($sql);
	$result->execute();
	return $result;
}

function getPresFamilyDisease($conn, $appointmentID){
	$sql="SELECT
		cr.relationName,
		cd.diseaseName,
		pfh.present
		FROM prescription_family_disease pfd
		JOIN patient_family_history pfh ON pfd.familyDiseaseID = pfh.familyHistoryID
		JOIN content_relation cr ON pfh.relation = cr.relationID
		JOIN content_disease cd ON pfh.diseaseID = cd.diseaseID
		WHERE 1 = 1
		AND pfd.appointmentID = '$appointmentID'";
	$result = $conn->prepare($sql);
	$result->execute();
	return $result;
}
?>