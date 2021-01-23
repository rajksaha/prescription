<?php

function getPrescribedInv($conn, $appointmentID){

	$sql = "SELECT
			ci.name AS invName,
			pi.`note`,
			pi.`checked`
		FROM doctor_feed.`prescription_inv` pi
		JOIN doctor_feed.content_inv ci ON pi.invID = ci.invID
		WHERE pi.`appointmentID` = '$appointmentID'";

	$result=mysqli_query($conn, $sql);

	return $result;

}
?>