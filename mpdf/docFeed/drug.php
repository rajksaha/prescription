<?php
function getPresCribedDrugs($appointmentID){


	$sql = "SELECT
			PD.`presDrugID`,
			PD.`appointmentID`,
			PD.`drugTypeID`,
			PD.`drugID`,
			PD.`doseTypeCode`,
			PD.`drugDoseUnit`,
			PD.`drugWhenID`,
			PD.`drugAdviceID`,
			PD.`presNum`,
			CD.drugName,
			CD.strength AS drugStrength,
			CDT.initial AS drugTypeInitial,
			CDT.name AS drugTypeName,
			CDA.drugAdviceID AS drugAdviceID,
			CDA.bangla AS drugAdviceName,
			CWT.whenTypeID AS drugWhenID,
			CWT.bangla AS drugWhenName,
			DT.doseTypeID as doseTypeID,
			DT.bangla AS doseTypeName,
			DT.english AS doseTypeEngName,
			PD.`updatedBy`,
			PD.`updatedOn`,
			PD.`createdBy`,
			PD.`createdOn`
		FROM doctor_feed.`prescription_drug` PD
		JOIN doctor_feed.content_drug CD ON PD.drugID = CD.drugID
		JOIN doctor_feed.content_drug_type CDT ON CD.typeID = CDT.drugTypeID
		JOIN doctor_feed.content_dose_type DT ON PD.doseTypeCode = DT.doseCode
		LEFT JOIN doctor_feed.content_drug_advice CDA ON PD.drugAdviceID = CDA.drugAdviceID
		LEFT JOIN doctor_feed.content_when_type CWT ON PD.drugWhenID = CWT.whenTypeID
		WHERE PD.appointMentID = '$appointmentID' ORDER BY PD.presNum" ;

	$result=mysql_query($sql);

	return $result;
}

function getPreiodicList($presDrugID){

	$sql = "SELECT
			PDD.`presDrugID`,
			PDD.`dose`,
			PDD.`numOfDay`,
			PDD.`durationType`,
			CDT.bangla as bngDurationName,
			CDT.english as engDurationName,
			CDT.pdf as pdfDurationName,
			PDD.`updatedBy`,
			PDD.`updatedOn`,
			PDD.`createdBy`,
			PDD.`createdOn`
			FROM doctor_feed.`prescription_drug_dose` PDD
			JOIN doctor_feed.content_duration_type CDT ON PDD.durationType = CDT.durationType
			WHERE PDD.presDrugID = $presDrugID";
			
	$dose = mysql_query($sql);


	return $dose;
}
?>