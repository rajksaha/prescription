<?php

function getAppointmentByDateRange($doctorId, $filteredDate, $endDate){
	$sql = "SELECT
			app.appointmentID, app.doctorCode, app.patientCode, app.date, app.time, app.status, app.addedBy, p.patientCode, p.name, p.age, p.address, p.phone, p.sex, IFNULL(p.name, 0) AS patientState, 
				app.appointmentType, at.shortName AS appointmentTypeName, pt.typeName,  ds.name diseaseName
			FROM `appointment` app
			JOIN appointment_type at ON at.id= app.appointmentType
			JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
			LEFT JOIN patient p ON app.patientCode = p.patientCode
			left join patient_detail pd on pd.patientID=p.patientID
			left join patient_type pt on pt.id=pd.`type`
			left join diagnosis d on d.appointMentID=app.appointmentID
			left join disease ds on d.diseaseID = ds.id		
	WHERE doc.doctorID = $doctorId AND app.date >= '$filteredDate'  AND app.date <= '$endDate'  group by app.patientCode order by app.appointmentID DESC";
	
	$result=mysql_query($sql);	
	$data = array();
	while ($row=mysql_fetch_array($result)){
		$total_visit_sql = "select  count(app.appointmentID) 
                            from  `appointment` app 
                            JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
							where  doc.doctorID = $doctorId   and patientCode='".$row['patientCode']."'  and app.appointmentType >0  ";
		$visit_result=mysql_fetch_row(mysql_query($total_visit_sql));	
		$row['total_visit']=$visit_result[0];
		array_push($data,$row);
	}
	return  json_encode($data);
}



function getAppointment($doctorId, $date){
	
	
	$sql = "SELECT
	app.appointmentID, app.doctorCode, app.patientCode, app.date, app.time, app.status, app.addedBy, p.patientID, p.name, p.age, p.address, p.phone, p.sex, IFNULL(p.name, 0) AS patientState, 
		app.appointmentType, at.shortName AS appointmentTypeName
	FROM `appointment` app
	JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
	JOIN appointment_type at ON at.id= app.appointmentType
	LEFT JOIN patient p ON app.patientCode = p.patientCode
	WHERE doc.doctorID = $doctorId AND app.date='$date' order by app.appointmentID DESC";
	$result=mysql_query($sql);
	
	$data = array();
	while ($row=mysql_fetch_array($result)){
		$total_visit_sql = "select  count(app.appointmentID) 
                            from  `appointment` app 
                            JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
							where  doc.doctorID = $doctorId   and patientCode='".$row['patientCode']."'  and app.appointmentType >0  ";
		$visit_result=mysql_fetch_row(mysql_query($total_visit_sql));	
		$row['total_visit']=$visit_result[0];
		array_push($data,$row);
	}
	return  json_encode($data);
}




function getNextDateAppointment($doctorId, $filteredDate){
	$sql = "	
		SELECT distinct  app.doctorCode, p.patientCode, pt.typeName, 
		 ds.name diseaseName,  p.name, p.age, p.address, p.phone, p.sex, pt.typeName
		FROM  patient p
		LEFT JOIN patient_detail pd on pd.patientID=p.patientID
		LEFT JOIN patient_type pt on pt.id=pd.`type`
		LEFT JOIN `appointment` app on app.patientCode=p.patientCode
		LEFT JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
		left join diagnosis d on d.appointMentID=app.appointmentID
		left join disease ds on d.diseaseID = ds.id	
		LEFT JOIN next_visit n on n.appointmentID = app.appointmentID  	
		WHERE  doc.doctorID = $doctorId   and app.appointmentType >0     AND n.date >= '$filteredDate' 
		group by  app.patientCode
		order by p.name 
	";	
	$result=mysql_query($sql);	
	$data = array();
	while ($row=mysql_fetch_array($result)){
		$total_visit_sql = "select  count(app.appointmentID) from  `appointment` app 
                            JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
							where  doc.doctorID = $doctorId   and patientCode='".$row['patientCode']."'  and app.appointmentType >0  ";
		$visit_result=mysql_fetch_row(mysql_query($total_visit_sql));	
		$row['total_visit']=$visit_result[0];
		array_push($data,$row);
	}
	return  json_encode($data);
}

function PatientAddd($doctorId, $date){
	$sql = "SELECT
	app.appointmentID, app.doctorCode, app.patientCode, app.date,  ds.name diseaseName, app.time, app.status, app.addedBy,  pt.typeName, p.patientID, p.name, p.age, p.address, p.phone, p.sex, IFNULL(p.name, 0) AS patientState,
		app.appointmentType, at.shortName AS appointmentTypeName
	FROM `appointment` app
	JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
	JOIN appointment_type at ON at.id= app.appointmentType
	LEFT JOIN patient p ON app.patientCode = p.patientCode
	left join patient_detail pd on pd.patientID=p.patientID
	left join patient_type pt on pt.id=pd.`type`
	left join diagnosis d on d.appointMentID=app.appointmentID
	left join disease ds on d.diseaseID = ds.id	
	WHERE doc.doctorID = $doctorId AND app.patientCode=$date order by app.appointmentID DESC";

	$result=mysql_query($sql);

	$data = array();
	while ($row=mysql_fetch_array($result)){
		array_push($data,$row);
	}
	return  json_encode($data);
}



function getPatientByDisease($doctorId, $disease_search_str){
	$sql = "		
			SELECT distinct  app.doctorCode,  ds.name diseaseName, app.date, p.patientCode, count(app.appointmentID) total_visit, p.name, p.age, p.address, p.phone, p.sex, pt.typeName
			FROM  patient p
			LEFT JOIN patient_detail pd on pd.patientID=p.patientID
			LEFT JOIN patient_type pt on pt.id=pd.`type`
			LEFT JOIN `appointment` app on app.patientCode=p.patientCode
			LEFT JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
			LEFT JOIN diagnosis d on d.appointMentID=app.appointmentID 
			LEFT JOIN disease ds on ds.id=d.diseaseID
			WHERE doc.doctorID = $doctorId  and  ds.name like '%$disease_search_str%'  and app.appointmentType >0   
			group by  app.patientCode
			order by p.name
	";	
	//echo $sql;die;
	$result=mysql_query($sql);	
	$data = array();
	while ($row=mysql_fetch_array($result)){
		$total_visit_sql = "select  count(app.appointmentID) from  `appointment` app 
                            JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
							where doc.doctorID = $doctorId   and patientCode='".$row['patientCode']."'  and app.appointmentType >0  ";
		$visit_result=mysql_fetch_row(mysql_query($total_visit_sql));	
		$row['total_visit']=$visit_result[0];
		array_push($data,$row);
	}
	return  json_encode($data);
}


function getPatientByDrugs($doctorId, $drug_search_str){
	$sql = "
			SELECT distinct  app.doctorCode, dg.drugName, ds.name diseaseName, app.date,  p.patientCode, p.name, p.age, p.address, p.phone, p.sex, pt.typeName
			FROM  patient p
			LEFT JOIN patient_detail pd on pd.patientID=p.patientID
			LEFT JOIN patient_type pt on pt.id=pd.`type`
			LEFT JOIN `appointment` app on app.patientCode=p.patientCode
			LEFT JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
			left join drug_prescription d on d.appointMentID=app.appointmentID
			left join drug dg on dg.drugID=d.drugID
			LEFT JOIN diagnosis di on di.appointMentID=app.appointmentID 
			LEFT JOIN disease ds on ds.id=di.diseaseID		
			WHERE doc.doctorID = $doctorId  and  dg.drugName like '%$drug_search_str%'  and app.appointmentType >0   
			group by  app.patientCode
			order by p.name
	";
	//echo $sql;die;

	$result=mysql_query($sql);
	$data = array();
	while ($row=mysql_fetch_array($result)){
		$total_visit_sql = "select  count(app.appointmentID) from  `appointment` app 
                            JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
							where  doc.doctorID = $doctorId   and patientCode='".$row['patientCode']."'  and app.appointmentType >0  ";
		$visit_result=mysql_fetch_row(mysql_query($total_visit_sql));	
		$row['total_visit']=$visit_result[0];
		array_push($data,$row);
	}
	return  json_encode($data);
}





function getAppointmentByDisease($doctorId, $disease_search_str){
	$sql = "
		SELECT app.patientCode,  app.doctorCode, 	app.appointmentID, ds.name, pt.typeName, app.date, app.date, app.time, app.status, app.addedBy, p.patientID, p.name, p.age, p.address, p.phone, p.sex, IFNULL(p.name, 0) AS patientState, 
		app.appointmentType, at.shortName AS appointmentTypeName, ds.name diseaseName
		FROM `appointment` app
		JOIN appointment_type at ON at.id= app.appointmentType
		JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
		LEFT JOIN patient p ON app.patientCode = p.patientCode
		left join patient_detail pd on pd.patientID=p.patientID
		left join patient_type pt on pt.id=pd.`type`			
		left join diagnosis d on d.appointMentID=app.appointmentID
		left join disease ds on d.diseaseID = ds.id
		WHERE doc.doctorID = $doctorId  and  ds.name like '%$disease_search_str%'
		group by app.patientCode
		order by app.appointmentID DESC
	";
	
	
		
	$result=mysql_query($sql);	
	$data = array();
	while ($row=mysql_fetch_array($result)){
		$total_visit_sql = "select  count(app.appointmentID) from  `appointment` app 
                            JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
							where doc.doctorID = $doctorId   and patientCode='".$row['patientCode']."'  and app.appointmentType >0  ";
		$visit_result=mysql_fetch_row(mysql_query($total_visit_sql));	
		$row['total_visit']=$visit_result[0];
		array_push($data,$row);
	}
	return  json_encode($data);
}


function getAppointmentByDrugs($doctorId, $drug_search_str){
	$sql = "SELECT app.patientCode,  app.doctorCode, 	app.appointmentID, dg.drugName,  ds.name diseaseName,  pt.typeName, app.date, app.date, app.time, app.status, app.addedBy, p.patientID, p.name, p.age, p.address, p.phone, p.sex, IFNULL(p.name, 0) AS patientState, 
			app.appointmentType, at.shortName AS appointmentTypeName
			FROM `appointment` app
			JOIN appointment_type at ON at.id= app.appointmentType
			JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
			LEFT JOIN patient p ON app.patientCode = p.patientCode
			left join patient_detail pd on pd.patientID=p.patientID
			left join patient_type pt on pt.id=pd.`type`	
			left join drug_prescription d on d.appointMentID=app.appointmentID
			left join drug dg on dg.drugID=d.drugID
			left join diagnosis di on di.appointMentID=app.appointmentID
			left join disease ds on di.diseaseID = ds.id		
			WHERE  doc.doctorID = $doctorId AND   dg.drugName like '%$drug_search_str%'
			group by app.patientCode
			order by app.appointmentID DESC";

	$result=mysql_query($sql);
	$data = array();
	while ($row=mysql_fetch_array($result)){
		$total_visit_sql = "select  count(app.appointmentID) from  `appointment` app 
                            JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
							where  doc.doctorID = $doctorId   and patientCode='".$row['patientCode']."'  and app.appointmentType >0  ";
		$visit_result=mysql_fetch_row(mysql_query($total_visit_sql));	
		$row['total_visit']=$visit_result[0];
		array_push($data,$row);
	}
	return  json_encode($data);
}


function getAppointmentByPatientType($doctorId, $type_search_str){
	$sql = "SELECT distinct  app.doctorCode, p.patientCode, pt.typeName, ds.name diseaseName, p.name, p.age,app.date,  p.address, p.phone, p.sex, pt.typeName
			FROM  patient p
			LEFT JOIN patient_detail pd on pd.patientID=p.patientID
			LEFT JOIN patient_type pt on pt.id=pd.`type`
			LEFT JOIN `appointment` app on app.patientCode=p.patientCode
            LEFT JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
			left join diagnosis d on d.appointMentID=app.appointmentID
			left join disease ds on d.diseaseID = ds.id
			WHERE  doc.doctorID = $doctorId  AND  typeName like '%$type_search_str%'   and app.appointmentType >0  
			group by  app.patientCode
			order by p.name
		";
	//echo $sql;die;
	$result=mysql_query($sql);
	$data = array();
	while ($row=mysql_fetch_array($result)){
		$total_visit_sql = "select  count(app.appointmentID) from  `appointment` app 
                            JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
							where  doc.doctorID = $doctorId   and patientCode='".$row['patientCode']."'  and app.appointmentType >0  ";
		$visit_result=mysql_fetch_row(mysql_query($total_visit_sql));	
		$row['total_visit']=$visit_result[0];
		array_push($data,$row);
	}
	return  json_encode($data);
}



function getAllPatient($doctorId){
	$sql = "SELECT distinct  app.doctorCode, p.patientCode, pt.typeName, app.date, ds.name diseaseName,  p.name, p.age, p.address, p.phone, p.sex, pt.typeName
			FROM  patient p
			LEFT JOIN patient_detail pd on pd.patientID=p.patientID
			LEFT JOIN patient_type pt on pt.id=pd.`type`
			LEFT JOIN `appointment` app on app.patientCode=p.patientCode
			LEFT JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
			left join diagnosis d on d.appointMentID=app.appointmentID
			left join disease ds on d.diseaseID = ds.id		
			WHERE  doc.doctorID = $doctorId   and app.appointmentType = 0   
			group by  app.patientCode
			order by p.name ";
	//echo $sql;die;
	$result=mysql_query($sql);
	$data = array();
	while ($row=mysql_fetch_array($result)){
		$total_visit_sql = "select  count(app.appointmentID) from  `appointment` app JOIN `doctor` doc ON doc.doctorCode = app.doctorCode
							where  doc.doctorID = $doctorId   and patientCode='".$row['patientCode']."'  and app.appointmentType >0  ";
		$visit_result=mysql_fetch_row(mysql_query($total_visit_sql));	
		$row['total_visit']=$visit_result[0];
		
		array_push($data,$row);
	}
	return  json_encode($data);
}







function addAppointMent($doctorCode, $patientCode, $appointmentType, $doctorID, $date, $time, $username){


	$sql =  mysql_query("INSERT INTO `appointment`( `doctorCode`, `patientCode`, `date`, `time`, `status`,`appointmentType`, `addedBy`) 
			VALUES 
			('$doctorCode','$patientCode','$date','$time',0,  '$appointmentType', '$username')");

	
	
	
}
function getAppointmentInfo($appointmentID){
	
	$sql = "SELECT a.`appointmentID`, a.`doctorCode`, a.`patientCode`, a.`date`, a.`time`, a.`status`, a.`appointmentType`, a.`addedBy`, p.patientID
	FROM `appointment` a
	JOIN patient p ON a.patientCode = p.patientCode
	WHERE appointmentID = '$appointmentID'";
	
	$result = mysql_query($sql);
	
	return $result;
}

function getPatientInformaition($patientCode){
	
	$sql = "SELECT p.`patientID`, p.`patientCode`, p.`name`, p.`age`, p.`sex`, p.`address`, p.`phone`, p.`occupation`, p.`referredBy`, p.`date`, p.`hospitalName`, p.`bedNum`, p.`wardNum`, p.`headOfUnit`, cd.detail as patientImage, pt.type
	FROM `patient` p
	LEFT JOIN contentdetail cd ON cd.contentType = 'PATIENTIMG'  AND cd.entityID = '$patientCode'
	LEFT JOIN patient_detail pt on pt.patientID = p.patientID
	WHERE `patientCode` = '$patientCode'"  ;
	
	$result=mysql_query($sql);
	
	return $result;
}

function getDoctorInfo ($doctorId){
	
	$sql=mysql_query("SELECT
			d.doctorID, d.doctorCode, d.password, d.name, d.sex, d.age, d.phone, ds.category, ds.state, ds.prescriptionStyle,
			ds.patientType, ds.patientState, ds.hospitalID, ds.photoSupport, ds.personCodeInitial, dc.name AS categoreyName, ds.pdfPage
			FROM doctor d
			JOIN doctorsettings ds ON d.doctorID = ds.doctorID
			JOIN doctorcategory dc ON ds.category = dc.id
			WHERE d.doctorID =$doctorId ");
	$result=mysql_fetch_assoc($sql);
	
	return $result;
	
}

function getPdfDetail($patientCode, $doctorId){
	
	$sql = "SELECT p.`patientID` , p.`patientCode` , p.`name` , p.`age` , p.`sex` , p.`address` , p.`phone` , pd.`type` , pd.`tri` , pd.`triStatus` , pd.`edb` , pd.id AS patientDetailID, COUNT( a.appointmentID ) AS visitNo
FROM `patient` p
JOIN appointment a ON a.patientCode = p.patientCode 
JOIN `doctor` doc ON doc.doctorCode = app.doctorCode AND doc.doctorID = $doctorId
LEFT JOIN patient_detail pd ON p.`patientID` = pd.`patientID`
WHERE p.patientCode = '$patientCode'";
	
	$result=mysql_query($sql);
	
	return $result;
}

function addFollowUpSetting($doctorID, $patientID){
	
	
	$sql = "SELECT dfs.`followUpSerttingID`, dfs.`doctorID`, dfs.`invID`, i.name AS invName
			FROM `doctor_followup_setteing` dfs
			JOIN inv i ON i.id = dfs.invID
			WHERE dfs.doctorID = '$doctorID'";
	
	$result=mysql_query($sql);
	
	while ($row=mysql_fetch_array($result)){
		$invID = $row['invID'];
		$innerSql = "INSERT INTO `patient_follow_up`(`patientID`, `doctorID`, `invID`) VALUES ($patientID,$doctorID, $invID)";
		mysql_query($innerSql);
	}
	
}
?>