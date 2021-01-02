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
?>
