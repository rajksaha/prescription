<?php
function getPresCribedDrugs($conn, $appointmentID){


	$sql = "SELECT
			PD.presDrugID AS presDrugID,
			PD.appointmentID AS appointmentID,
			PD.drugTypeID AS drugTypeID,
			PD.drugID AS drugID,
			PD.doseTypeCode,
			PD.drugDoseUnit,
			PD.drugWhenID,
			PD.drugAdviceID,
			PD.doseString,
			PD.presNum,
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
			PD.updatedBy,
			PD.updatedOn,
			PD.createdBy,
			PD.createdOn
		FROM prescription_drug PD
		JOIN content_drug CD ON PD.drugID = CD.drugID
		JOIN content_drug_type CDT ON CD.typeID = CDT.drugTypeID
		JOIN content_dose_type DT ON PD.doseTypeCode = DT.doseCode
		LEFT JOIN content_drug_advice CDA ON PD.drugAdviceID = CDA.drugAdviceID
		LEFT JOIN content_when_type CWT ON PD.drugWhenID = CWT.whenTypeID
		WHERE PD.appointMentID = '$appointmentID' ORDER BY PD.presNum ASC" ;

	$sth = $conn->prepare($sql);
	$sth->execute();
	return $sth;
}

function getPreiodicList($conn, $presDrugID){

	$sql = "SELECT
			PDD.presDrugID,
			PDD.dose,
			PDD.numOfDay,
			PDD.durationType,
			CDT.bangla as bngDurationName,
			CDT.english as engDurationName,
			CDT.pdf as pdfDurationName,
			PDD.updatedBy,
			PDD.updatedOn,
			PDD.createdBy,
			PDD.createdOn
			FROM prescription_drug_dose PDD
			JOIN content_duration_type CDT ON PDD.durationType = CDT.durationType
			WHERE PDD.presDrugID = $presDrugID";
			
	$sth = $conn->prepare($sql);
	$sth->execute();
	return $sth;
}
?>