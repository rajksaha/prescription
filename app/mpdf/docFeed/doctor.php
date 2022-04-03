<?php

function getDoctorInfo ($conn, $doctorId){

	$sql="SELECT
						doctorSettingID,
						doctorID,
						category,
						state,
						patientType,
						patientState,
						prescriptionStyle,
						companyID,
						photoSupport,
						personCodeInitial,
						pdfPage,
						updatedBy,
						updatedOn,
						createdBy,
						createdOn
						FROM doctor_setting
						WHERE d.doctorID = '$doctorId'";
	
	$sth = $conn->prepare($sql);
	$sth->execute();
	return $sth->fetch(PDO::FETCH_ASSOC);;

}

function getDotorHistory($conn, $doctorID){
	$sql="SELECT
					ms.menuHeader,
					m.defaultName
					FROM menu_setting ms
					JOIN menu m ON ms.menuID = m.menuID
					WHERE 1 = 1 AND m.functionName = 'HISTORY'
			AND ms.doctorID = '$doctorID' ORDER BY ms.displayOrder";
	
	$result = $conn->prepare($sql);
	$result->execute();
	return $result;
	
}
?>
