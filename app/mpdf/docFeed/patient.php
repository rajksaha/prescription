<?php
function getPatientInfo($conn, $patientID){
	$sql = "SELECT
			p.patientID,
			p.userID,
			p.patientCode,
			p.occupation,
			p.referredBy,
			p.hospitalName,
			p.bedNum,
			p.wardNum,
			p.headOfUnit,
			p.patientType,
			p.imageURL,
			p.updatedBy,
			p.updatedOn,
			p.createdBy,
			p.createdOn,
			pup.firstName AS firstName,
			pup.lastName AS lastName,
			pup.address AS address,
			pup.contactNo AS contactNo,
			pup.dateOfBirth AS dateOfBirth,
            extract(year from age(now(), pup.dateOfBirth)) AS age,
			pu.userName,
			pup.sex AS sex,
			cd.shortName as patientImage
		FROM patient p
		JOIN bottom_up_user pu ON p.userID = pu.userID
		JOIN bottom_up_user_profile pup ON pu.userID = pup.userID
		LEFT JOIN content_detail cd ON cd.entityType = 'PATIENTIMG'  AND cd.entityID = '$patientID'
		WHERE p.patientID = '$patientID'" ;

	$sth = $conn->prepare($sql);
	
	$sth->execute();
	
	return $sth->fetch(PDO::FETCH_ASSOC);
}
?>