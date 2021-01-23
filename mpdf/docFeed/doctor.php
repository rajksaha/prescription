<?php

function getDoctorInfo ($conn, $doctorId){

	$sql=mysql_query("SELECT
						`doctorSettingID`,
						`doctorID`,
						`category`,
						`state`,
						`patientType`,
						`patientState`,
						`prescriptionStyle`,
						`companyID`,
						`photoSupport`,
						`personCodeInitial`,
						`pdfPage`,
						`updatedBy`,
						`updatedOn`,
						`createdBy`,
						`createdOn`
						FROM `doctor_setting`
						WHERE d.doctorID = $doctorId");
	$result=mysqli_fetch_assoc($conn, $sql);
	return $result;

}

function getDotorHistory($conn, $doctorID){
	$sql="SELECT
					ms.`menuHeader`,
					m.defaultName
					FROM `menu_setting` ms
					JOIN menu m ON ms.menuID = m.menuID
					WHERE 1 = 1 AND m.isPopUp = 1 AND m.functionName = 'HISTORY'
			AND ms.doctorID = $doctorID ORDER BY ms.displayOrder";
	$result=mysqli_query($conn, $sql);
	return $result;
	
}
?>
