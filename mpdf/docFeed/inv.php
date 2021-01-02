<?php

function getPrescribedInv($conn, $appointmentID){

	$sql = "SELECT
			pi.`presInvID`,
			pi.`appointmentID`,
			pi.`invID`,
			ci.name AS invName,
			pi.`note`,
			pi.`checked`,
			pi.`updatedBy`,
			pi.`updatedOn`,
			pi.`createdBy`,
			pi.`createdOn`
		FROM doctor_feed.`prescription_inv` pi
		JOIN doctor_feed.content_inv ci ON pi.invID = ci.invID
		WHERE pi.`appointmentID` = '$appointmentID'";

	$result=mysqli_query($conn, $sql);

	return $result;

}
?>