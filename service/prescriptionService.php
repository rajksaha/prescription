<?php

function getDoctorInfoByDoctorCode($doctorCode){
	
	$result = mysql_query("SELECT `doctorID` FROM `doctor` WHERE `doctorCode` = '$doctorCode'");
	
	return mysql_fetch_assoc($result);
}

function getPresCribedDrugs($appointmentID){
	
	
	$sql = "SELECT  
			dp.id, dp.appointMentID, dp.drugTypeID, dp.drugID, dp.drugTimeID, dp.drugDoseUnit, dp.drugAdviceID, dt.initial AS typeInitial, dp.drugWhenID,
			d.drugName AS drugName, d.strength AS drugStrength, 
			dwt.bangla AS whenTypeName, dwt.pdf AS whenTypePdf, dat.bangla AS adviceTypeName, dat.pdf AS adviceTypePdf
			FROM drug_prescription dp 
				JOIN drugtype dt ON dp.drugTypeID = dt.id
				JOIN drug d ON dp.drugID = d.drugID
				JOIN drugwhentype dwt ON dp.drugWhenID = dwt.id
				JOIN drugadvicetype dat ON dp.drugAdviceID = dat.id
			WHERE dp.appointMentID = '$appointmentID' ORDER BY dp.presNum" ;
	
	$result=mysql_query($sql);
	
	return $result;
}

function getPrescribedInv($appointmentID){
	
	$sql = "SELECT ip.`id`, ip.`appointMentID`, ip.`invID`, ip.`note`, ip.`checked`, i.name AS invName
	FROM `inv_prescription` ip
	JOIN inv i ON ip.invID = i.id
	WHERE `appointMentID` = '$appointmentID'";
	
	$result=mysql_query($sql);
	
	return $result;
	
}

function getPrescribedAdvice($appointmentID){

	$sql = "SELECT pa.`id`, pa.`appointMentID`, pa.`adviceID`, a.advice, a.lang, a.pdf 
			FROM `prescription_advice` pa
			JOIN advice a ON pa.adviceID = a.id
			WHERE `appointMentID` = '$appointmentID'";

	$result=mysql_query($sql);

	return $result;

}

function getPrescribedVital($appointmentID){

	$sql = "SELECT vp.`id`, vp.`appointMentID`, vp.`vitalID`, vp.`vitalResult` , IF(v.shortName IS NULL or v.shortName = '', v.vitalName,   v.shortName) AS vitalDisplayName, v.vitalUnit 
			FROM `vital_prescription` vp 
			JOIN vital v ON vp.vitalID = v.vitalId
 			WHERE `appointMentID`= '$appointmentID'";

	$result=mysql_query($sql);
	
	return $result;

}
function getPrescribedHistory($appointmentID, $typeCode){

	$sql = "SELECT hp.`id`,  hp.`appointMentID`,  hp.`patientHistoryID` , IF(h.shortName IS NULL or h.shortName = '', h.name,   h.name) AS historyName , ph.historyResult
			FROM `history_prescription` hp
			JOIN patient_history ph ON hp.patientHistoryID = ph.id
			JOIN history h ON ph.historyID = h.id AND  h.typeCode = '$typeCode'
			WHERE hp.`appointMentID`= '$appointmentID'";

	$result=mysql_query($sql);

	return $result;

}

function getPrescribedComplain($appointmentID){

	$sql = "SELECT c.`id`, c.`appointMentID`, c.`symptomID`, c.`durationNum`, c.`durationType` AS durationID, s.name AS symptomName, ddt.english AS durationType 
			FROM `complain` c
			JOIN symptom s ON c.symptomID = s.symptomID
			JOIN drugdaytype ddt ON c.durationType= ddt.id
			 WHERE c.`appointMentID` = '$appointmentID' ORDER BY c.id";

	$result=mysql_query($sql);

	return $result;

}
function getPrescribedDiagnosis($appointmentID){

	$sql = "SELECT dia.`id`, dia.`appointMentID`, dia.`diseaseID`, `note` , d.name AS diseaseName
			FROM `diagnosis` dia
			JOIN disease d ON dia.diseaseID = d.id
			WHERE dia.`appointMentID`= '$appointmentID'";

	$result=mysql_query($sql);
	return $result;

}

function getPastDisease($appointmentID, $patientID){

	$sql = "SELECT pas.`id`, ppd.`appointMentID`, ppd.`pastDiseaseID` , d.name AS diseaseName,  pas.detail, pas.isPresent, ppd.id AS prescribedID, IF(ppd.id  IS NULL, false, true) AS addedToPres
			FROM patient_past_disease pas
			JOIN disease d ON pas.diseaseID = d.id
			LEFT JOIN `prescription_past_disease` ppd ON ppd.pastDiseaseID = pas.id AND ppd .appointMentID = '$appointmentID'
			WHERE pas.patientID = '$patientID'";

	$result=mysql_query($sql);

	return $result;

}

function getFamilyDisease($appointmentID, $patientID){

	$sql = "SELECT pfh.`id`, pfh.`patientID`, pfh.`diseaseID`, d.name AS diseaseName, pfh.`relation`, pfh.`present`, pfh.`type`, pfh.`detail`, r.name AS relationName, IF(pfd.id  IS NULL, false, true) AS addedToPres
				FROM `patient_family_history` pfh
				JOIN disease d ON pfh.diseaseID = d.id
				JOIN relation r ON r.id = pfh.relation
				LEFT JOIN prescription_family_disease pfd ON pfd.familyDiseaseID = pfh.id AND pfd.appointMentID = '$appointmentID'
				WHERE pfh.patientID =  '$patientID'";

	$result=mysql_query($sql);

	return $result;

}

function getPrescribedPastDisease($appointmentID){

	$sql = "SELECT pas.isPresent, pas.`id`, ppd.`appointMentID`, ppd.`pastDiseaseID` , d.name AS diseaseName, pas.detail, ppd.id AS prescribedID, IF(ppd.id  IS NULL, false, true) AS addedToPres
	FROM patient_past_disease pas
	JOIN disease d ON pas.diseaseID = d.id
	JOIN `prescription_past_disease` ppd ON ppd.pastDiseaseID = pas.id
	WHERE ppd.appointMentID = '$appointmentID'";

	$result=mysql_query($sql);

	return $result;

}

function getPrescribedPastDisease2($appointmentID, $status){

	$sql = "SELECT pas.`id`, ppd.`appointMentID`, ppd.`pastDiseaseID` , d.name AS diseaseName, pas.detail, ppd.id AS prescribedID, IF(ppd.id  IS NULL, false, true) AS addedToPres
	FROM patient_past_disease pas
	JOIN disease d ON pas.diseaseID = d.id
	JOIN `prescription_past_disease` ppd ON ppd.pastDiseaseID = pas.id
	WHERE ppd.appointMentID = '$appointmentID' AND pas.isPresent = $status";

	$result=mysql_query($sql);

	return $result;

}

function getPrescribedFamilyDisease($appointmentID){

	$sql = "SELECT pfh.`id`, pfh.`patientID`, pfh.`diseaseID`, d.name AS diseaseName, pfh.`relation`, pfh.`present`, pfh.`type`, pfh.`detail`, r.name AS relationName, IF(pfd.id  IS NULL, false, true) AS addedToPres
	FROM `patient_family_history` pfh
	JOIN disease d ON pfh.diseaseID = d.id
	JOIN relation r ON r.id = pfh.relation
	JOIN prescription_family_disease pfd ON pfd.familyDiseaseID = pfh.id 
	WHERE pfd.appointMentID = '$appointmentID'";

	$result=mysql_query($sql);

	return $result;

}

function getPrescribedNextVisit($appointmentID){

	$sql = "SELECT nv.`appointmentID`, nv.`nextVisitType`, nv.`date`, nv.`numOfDay`, nv.`dayType`, ddt.pdf, ddt.english, ddt.bangla  
			FROM `next_visit` nv
			LEFT JOIN drugdaytype ddt ON nv.dayType = ddt.id
			WHERE `appointmentID` =  '$appointmentID'";

	$result=mysql_query($sql);

	return $result;

}

function getPrescribedReffredDoctor($appointmentID){

	$sql = "SELECT pr.`id`, pr.`appointMentID`, pr.`refferedDoctorID` , rd.doctorName , rd.doctorAdress
			FROM `prescription_reference` pr 
			JOIN reffered_doctor rd ON pr.refferedDoctorID = rd.id
			WHERE `appointMentID` =  '$appointmentID'";

	$result=mysql_query($sql);

	return $result;

}

function getPatientOldPrecription($appointmentID, $patientID, $doctorID){
	$sql = "SELECT app.`appointmentID`, app.`doctorCode`, app.`patientCode`, app.`date`, app.`time`, app.`status`, app.`appointmentType`, app.`addedBy`, p.patientID, p.name, at.name AS appointmentTypeName, at.shortName AS appointmentTypeShortName
			FROM `appointment` app
			JOIN `doctor` d ON d.doctorCode = app.doctorCode
			JOIN patient p ON app.patientCode = p.patientCode
			JOIN appointment_type at ON at.id = app.appointmentType
			WHERE p.patientID = '$patientID' AND d.doctorID  = $doctorID AND app.appointmentID <> '$appointmentID'
			ORDER BY app.date DESC";
	
	$result=mysql_query($sql);
	
	return $result;
}

function getDoctorsDrugSettingByDisease($doctorID, $diseaseID){
	
	
	$result = mysql_query("SELECT sd.`id` , sd.`doctorID` , sd.`diseaseID` , sd.`drugTypeID` , sd.`drugID` , sd.`drugTimeID`  , sd.`drugDoseUnit` , sd.`drugWhenID` , sd.`drugAdviceID` , dt.initial AS typeInitial, d.drugName AS drugName, d.strength AS drugStrength, dwt.bangla AS whenTypeName, dat.bangla AS adviceTypeName
							FROM `settings_drug` sd
							JOIN drugtype dt ON sd.drugTypeID = dt.id
							JOIN drug d ON sd.drugID = d.drugID
							JOIN drugadvicetype dat ON sd.drugAdviceID = dat.id
							JOIN drugwhentype dwt ON sd.`drugWhenID` = dwt.id
			WHERE  sd.doctorID = $doctorID AND sd.diseaseID = '$diseaseID'");
	
	return $result;
	
	
}

function getDoctorsInvSettingByDisease($doctorID, $diseaseID){


	$result = mysql_query("SELECT si.`id`, si.`doctorID`, si.`diseaseID`, si.`invID`, si.`note`, i.name AS invName
						FROM `settings_inv` si
						JOIN inv i ON si.invID = i.id
						WHERE  si.doctorID = $doctorID AND si.diseaseID = '$diseaseID'");

	return $result;


}

function getDoctorsAdviceSettingByDisease($doctorID, $diseaseID){


	$result = mysql_query("SELECT sa.`id`, sa.`doctorID`, sa.`diseaseID`, sa.`adviceID`, a.advice
						FROM `settings_advice` sa
						JOIN doctor d ON sa.doctorID = d.doctorID
						JOIN advice a ON sa.`adviceID` = a.id	
						WHERE sa.doctorID = $doctorID AND sa.diseaseID = '$diseaseID'");

	return $result;


}

function getPreiodicListforPdf($drugPrescribeID){

	$dose = mysql_query("SELECT dp.`drugPrescribeID`, dp.`dose`, dp.`numOfDay`, dp.`durationType`, ddt.`bangla`, ddt.`pdf`, ddt.`english`
			FROM `dose_period`dp
			JOIN drugdaytype ddt ON  dp.`durationType` = ddt.id
			WHERE `drugPrescribeID` = $drugPrescribeID");


	return $dose;
}

function getContentDetail($entityID,  $entityType){
	
	$sql = mysql_query("SELECT `contentDetailID`, `contentType`, `entityID`, `detail`, `code` FROM `contentdetail` WHERE `contentType` = '$entityType' AND `entityID` = $entityID");
	
	return $sql;
}

function getContentDetailForPres($entityID,  $entityType, $detail){

	$sql = mysql_query("SELECT `contentDetailID`, `contentType`, `entityID`, `detail`, `code` FROM `contentdetail` WHERE `contentType` = '$entityType' AND `entityID` = $entityID AND `detail`= '$detail'");

	return $sql;
}

function getClinicalDate($entityID,  $entityType){

    $sql = mysql_query("SELECT `contentDetailID`, `contentType`, `entityID`, `detail`, `code` FROM `contentdetail` WHERE `contentType` = '$entityType' AND `entityID` = $entityID GROUP BY code");

    return $sql;
}

function getClinicalDetail($entityID,  $entityType, $code){

    $sql = mysql_query("SELECT `contentDetailID`, `contentType`, `entityID`, `detail`, `code` FROM `contentdetail` WHERE `contentType` = '$entityType' AND `entityID` = $entityID AND `code` = '$code'");

    return $sql;
}
?>