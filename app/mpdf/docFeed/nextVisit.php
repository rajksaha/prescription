<?php
function getPrescribedNextVisit($conn, $appointmentID){

	$sql = "SELECT
			pnv.nextVisitType,
			pnv.visitDate,
			pnv.numOfDay,
			pnv.durationType,
			CDT.bangla AS durationTypeName
		FROM prescription_next_visit pnv
		LEFT JOIN content_duration_type CDT ON pnv.durationType = CDT.durationType
		WHERE pnv.appointmentID =  '$appointmentID'";

	$sth = $conn->prepare($sql);
	
	$sth->execute();
	
	return $sth->fetch(PDO::FETCH_ASSOC);

}
?>