<?php

function getPrescribedInv($conn, $appointmentID){

	$sql = "SELECT
			ci.name AS invName,
			pi.note,
			pi.checked
		FROM prescription_inv pi
		JOIN content_inv ci ON pi.invID = ci.invID
		WHERE pi.appointmentID = '$appointmentID'";

	$result = $conn->prepare($sql);
	$result->execute();
	return $result;

}
?>