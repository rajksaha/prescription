<?php
class Appoientment {
	public  $appointmentID;
	public  $doctorID;
	public  $patientID;
	public  $date;
	public  $time;
	public  $status;
	public  $appointmentType;
	
	function get_appoientment_detail($appID) {
		$sql = "SELECT a.`appointmentID`, a.`doctorCode`, a.`patientCode`, a.`date`, a.`time`, a.`status`, a.`appointmentType`, a.`addedBy`, p.patientID
	FROM `appointment` a
	JOIN patient p ON a.patientCode = p.patientCode
	WHERE appointmentID = '$appointmentID'";
	
	$result = mysql_query($sql);
	}
}