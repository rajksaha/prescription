<?php

function getDoctorInfo ($doctorId){

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
	$result=mysql_fetch_assoc($sql);

	return $result;

}
?>
