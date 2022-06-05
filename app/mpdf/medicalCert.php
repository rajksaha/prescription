<?php
include_once('docFeed/appointment.php');
include_once('docFeed/doctor.php');
include_once('docFeed/patient.php');
include_once('docFeed/medCertMapper.php');
include_once('mpdf.php');


class MedicalCertificate extends mPDF {
	function genMedicalCertificate($conn, $appointmentID): PDF
	{
		$pdf = new MedicalCertificate('','A4',10,'nikosh');
		$pdf->WriteHTML('');
		$size = 12;
		$yAxis = 20;
		$appData = getAppointmentInfo($conn,$appointmentID);
		$patientID = $appData['patientid'];
		$doctorID = $appData['doctorid'];
		$patientData = getPatientInfo($conn, $patientID);

		$date=date_create($appData['appdate']);
		$formattedDate = date_format($date,"d/m/Y");
		$pdf->SetXY(80, $yAxis);
		$pdf->SetFont('nikosh','B',$size+3);
		$pdf->MultiCell(120,5, "Medical Certificate", 0);

		$yAxis += 20;
		$pdf->SetXY(10, $yAxis);
		$pdf->SetFont('nikosh','',$size);
		$pdf->MultiCell(120,5, $formattedDate, 0);

		$yAxis += 10;
		$pdf->SetXY(10, $yAxis);
		$pdf->SetFont('nikosh','',$size);
		$pdf->MultiCell(120,5, "Doctor's Statement", 0);

		$yAxis += 10;
		$pdf->SetXY(10, $yAxis);
		$pdf->SetFont('nikosh','',$size);
		$pdf->MultiCell(45,5, "In confidence to:", 0);

		$firstName = $patientData['firstname'];
		$dob=date_create($patientData['dateofnirth']);
		$formattedDob = date_format($dob,"d/m/Y");
		$pdf->SetXY(50, $yAxis);
		$pdf->SetFont('nikosh','',$size+1);
		$pdf->MultiCell(120,5, $firstName, 0);
		$pdf->SetXY(50, $yAxis+5);
		$pdf->SetFont('nikosh','',$size-1);
		$pdf->MultiCell(120,5, $formattedDob, 0);

		$yAxis += 20;
		$pdf->SetXY(70, $yAxis);
		$pdf->SetFont('nikosh','',$size);
		$pdf->MultiCell(120,5, "THIS IS TO CERTIFY THAT", 0);

		$yAxis += 10;
		$longText = "" . $firstName . " has reported a medical condition and will be unable to attend thier work or promised commitments.";
		$pdf->SetXY(10, $yAxis);
		$pdf->SetFont('nikosh','',$size-2);
		$pdf->MultiCell(200,5, $longText, 0);

		$medCertData = getCertDetail($conn, $appointmentID);
		$startDate = date("d/m/Y", strtotime($medCertData['startdate']));
		$endDate = date("d/m/Y", strtotime($medCertData['enddate']));
		$addComment = $medCertData['addcomment'];

		$yAxis += 15;
		$pdf->SetXY(10, $yAxis);
		$pdf->SetFont('nikosh','',$size);
		$pdf->MultiCell(120,5, "From: ". $startDate , 0);

		$yAxis += 5;
		$pdf->SetXY(10, $yAxis);
		$pdf->SetFont('nikosh','',$size);
		$pdf->MultiCell(120,5, "To:" . $endDate, 0);

		$yAxis += 10;
		$pdf->SetXY(10, $yAxis);
		$pdf->SetFont('nikosh','',$size);
		$pdf->MultiCell(200,5, "Doctor's additional comments:" .$addComment, 0);


		$pdf->SetXY(10, $pdf->GetY() + 20);
		$pdf->SetFont('nikosh','',$size);
		$pdf->MultiCell(200,5, "Doctor's signature", 0);

		$dateFormat = date("d M Y", strtotime($appData['appdate']));
		$pdf->SetXY(10, $pdf->GetY() + 20);
		$pdf->SetFont('nikosh','',$size);
		$pdf->MultiCell(200,5, "Date of issue: " . $dateFormat, 0);

		$pdf->Output('');
		return $pdf;
	}
}



?>
