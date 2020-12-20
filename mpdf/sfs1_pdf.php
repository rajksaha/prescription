<?php
include("BasicFunction.php");
session_start();
include('../phpServices/config.inc');
$username=$_SESSION['username'];
$doctorID = $_SESSION['doctorID'];
$appointmentID = $_SESSION['printAppointmentID'];
$patientCode = $_SESSION['printPatientCode'];
$date = date('d M, Y');
include('../phpServices/commonServices/appointmentService.php');
include('../phpServices/commonServices/prescriptionService.php');

class PDF extends mPDF {
	
function Head()
{
    $this->Image('head.jpg',5,5,200);
}

function Foot()
{
    //$this->SetY(-15);
   $this->Image('foot.jpg',5,260,200);
}

function Show_med($appointmentID, $xAxis, $yAxis, $size){

	$resultData = getPresCribedDrugs($appointmentID);

	if(mysql_num_rows($resultData) > 10){
		$size = $size - 2;
	}
	
	if(mysql_num_rows($resultData) > 0){
		$this->SetFont('Times','B',$size + 3);
		$this->SetXY($xAxis , $yAxis);
		$this->MultiCell(40,5,"Rx");
		$yAxis += 6;
		
	}if(mysql_num_rows($resultData) == 0){
		return $yAxis - 5;
	}
	
	$nameCell = 100;
	$doseeCell = 40;
	$durationCell = 70;
	$whenCell = 15;
	$var = 1;
	while($row=  mysql_fetch_array($resultData)){
		
		$this->SetFont('Times','U',$size + 2);
		
		$drugType = $row['typeInitial'];
		$drugTypeID = $row['drugTypeID'];
		$drugName = $row['drugName'];
		$drugStr = $row['drugStrength'];
		$drugPrescribeID = $row['id'];
		$drugTime = $row['drugTimeID'];
		$drugDoseInitial = $row['drugDoseUnit'];
		$drugWhen = $row['whenTypePdf'];
		$drugWhenID = $row['drugWhenID'];
		$drugAdvice = $row['adviceTypePdf'];
		$drugAdviceID = $row['drugAdviceID'];
		
		
		$yAxis =  $this->GetY() + 2;
		
		$this->SetXY($xAxis, $yAxis);
		if($drugStr  == ""){
				$this->MultiCell($nameCell,5,"$var. $drugType. $drugName");
		}else{
			$this->MultiCell($nameCell,5,"$var. $drugType. $drugName- $drugStr");
		}
		$var = $var + 1;
		$xInnerAxis = $nameCell + 5;
		
		$this->SetFont('prolog','',$size + 1);
		
		
		
		$this->SetXY($xAxis + 5, $yAxis + 6);
		$realY =  $this->GetY();
		if($drugDoseInitial != ""){
			if($drugTypeID == 3 || $drugTypeID == 15 || $drugTypeID == 41){
				$drugDoseInitial = str_replace("s"," Pv PvgP ", $drugDoseInitial);
			}else if($drugTypeID == 4){
				$drugDoseInitial = str_replace("ampl","G¤cj", $drugDoseInitial);
				$drugDoseInitial = str_replace("vial","fvqvj", $drugDoseInitial);
			}else if($drugTypeID == 10 || $drugTypeID == 14){
				$drugDoseInitial = str_replace("puff","cvd", $drugDoseInitial);
			}else if($drugTypeID == 7){
				$drugDoseInitial = str_replace("d","Wªc", $drugDoseInitial);
			}else if($drugTypeID == 6){
				$drugDoseInitial = str_replace("u","BDwbU", $drugDoseInitial);
			}else if($drugTypeID == 11){
				$drugDoseInitial = str_replace("drp/min","Wªc/wgwbU", $drugDoseInitial);	
			}
		}
		
		$doseData = getPreiodicListforPdf($drugPrescribeID);
		
		
		
		if($drugTime != -1){
				
			$dose = mysql_fetch_assoc($doseData);
			$drugDose = $dose['dose'];
			$drugNoDay = $dose['numOfDay'];
			$drugNoDayType = $dose['pdf'];
				
			$drugDose = str_replace("-","+", $drugDose);
			if($drugTime == 1){
				if($drugAdviceID == 14 || $drugWhenID == 11 || $drugAdviceID == 54 || $drugAdviceID == 62 || $drugWhenID == 23){
					$drugDose =  "$drugDose + 0 + 0";
				}else if ($drugAdviceID == 15 || $drugWhenID == 13){
					$drugDose =  "0 + 0 + $drugDose";
				}else if($drugAdviceID == 16 || $drugWhenID == 12){
					$drugDose =  "0 + $drugDose + 0";
				}else{
					$drugDose =  "0 + 0 + $drugDose";
				}
					
			}else if($drugTime == 2){
				list($num,$type) = explode('+', $drugDose, 2);
				$drugDose =  "$num + 0 + $type";
			}
				
			
			if($drugNoDay == 0){
				$drugNoDay = "";
			}
			
			$restOftheString = "- $drugWhen - $drugAdvice - $drugNoDay $drugNoDayType";
			if($drugDoseInitial == ""){
					
				$this->MultiCell(110,5,"$drugDose $restOftheString|");
			}else if($drugDose == ''){
				
				$this->MultiCell(110,5,"$drugDose $restOftheString|");
			}else{
				$this->MultiCell(110,5,"($drugDose)$drugDoseInitial $restOftheString|");
			}
		}else{
			$realY =  $yAxis;
			 while ($dose = mysql_fetch_array($doseData)){
				
			 	$drugDose = $dose['dose'];
			 	$drugNoDay = $dose['numOfDay'];
			 	$drugNoDayType = $dose['pdf'];
			 	
			 	$drugDose = str_replace("-","+", $drugDose);
				if($drugTime == 1){
					if($drugAdviceID == 14){
						$drugDose =  "$drugDose + 0 + 0";
					}else if ($drugAdviceID == 15){
						$drugDose =  "0 + 0 + $drugDose";
					}else{
						$drugDose =  "0 + $drugDose + 0";
					}
		
				}else if($drugTime == 2){
					list($num,$type) = explode('+', $drugDose, 2);
					$drugDose =  "$num + 0 + $type";
				}
		
				$yAxis =  $this->GetY();
				$this->SetXY($xInnerAxis - 10, $yAxis);
		
				if($drugDoseInitial == ""){
		
					$this->MultiCell($doseeCell,5,"($drugDose)");
				}else{
					$this->MultiCell($doseeCell,5,"($drugDose) $drugDoseInitial");
				}
		
				$xInnerAxis = $xInnerAxis + $doseeCell + 5;
				$this->SetXY($xInnerAxis, $yAxis);
				$this->MultiCell(20,5," $drugNoDay $drugNoDayType |");
		
				$xInnerAxis = $xInnerAxis - $doseeCell - 5;
			}
				
			$restOftheString = "$drugWhen $drugAdvice";
			$xInnerAxis = $xInnerAxis + $doseeCell ;
			$this->SetXY($xInnerAxis, $realY);
			$this->MultiCell($durationCell,5,"$restOftheString |");
			
			$this->SetY($yAxis + 5);
		}
		//$yAxis += 8;
	}
	
	return $this->GetY();

}

function ShowPatInfo($patientCode,$yAxis, $appointmentID){
	
	$resultData = getPatientInformaition($patientCode);
	
	$visitData = getPdfDetail($patientCode, 16);
	
	$rec1 = mysql_fetch_assoc($visitData);
	
	$rec = mysql_fetch_assoc($resultData);
	
	$name = $rec['name'];
	
	$age = $rec['age'];
	
	$sex = $rec['sex'];
	
	$date = date('d-m-Y');
	
	$visit =  $rec1['visitNo'];
	
	$phone =  $rec1['phone'];
	
	$address =  $rec1['address'];
	
	$patientCode = $rec['patientCode'];
	
	
	$this->SetXY(80,$yAxis);
	$this->Write(5, "ID No: $patientCode");
	
	$this->SetXY(160,$yAxis);
	//$this->Write(5, "Visit No: $visit");
	
	$this->SetXY(10,$yAxis );
	$this->Write(5, "Name: $name");
	
	$this->SetXY(130, $yAxis);
	$this->Write(5, "Age: $age Yrs");
	
		
	$this->SetXY(160, $yAxis );
	$this->Write(5, "Date: $date");
	
		
	return $rec['patientImage'];
	
}

function Show_Complain($appointmentID,$xAxis,$yAxis, $maxX , $size) {

	$resultData = getPrescribedComplain($appointmentID);




	if(mysql_num_rows($resultData) > 0){
		$this->SetFont('Times','B',$size + 1);
		$this->SetXY($xAxis, $yAxis);
		$this->MultiCell($maxX,5,"Chief Complaints");
		$yAxis += 6;

	}if(mysql_num_rows($resultData) == 0){
		return $yAxis - 5;
	}
	$this->SetFont('Times','',$size);
	$var = 1;
	while($row=  mysql_fetch_array($resultData)){

		$symptomName = $row['symptomName'];
		$durationNum = $row['durationNum'];
		$durationType = $row['durationType'];
		$durationID = $row['durationID'];

		$yAxis =  $this->GetY();
		$this->SetXY($xAxis, $yAxis);
		if($durationID < 5){
			$this->MultiCell($maxX,5,". $symptomName - $durationNum - $durationType");
		}elseif ($durationID == 7){
			$this->MultiCell($maxX,5,". $symptomName - $durationType");
		}else{
			$this->MultiCell($maxX,5,". $symptomName");
		}
			
		$var++;
	}
	
	$yAxis = $this->GetY() + 2 ;
	$linestyle = array('width' => 20, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(255, 0, 0));
	$this->Line($xAxis, $yAxis, 90 , $yAxis, $linestyle);
	return $this->GetY();

}

function Show_vital($appointmentID,$xAxis, $yAxis, $maxX, $size){




	$resultData = getPrescribedVital($appointmentID);

	$xAxis = $xAxis - 5;
	$baseX =  $xAxis;
	$this->SetXY($xAxis, $yAxis);
	$this->SetFont('Times','u',$size);
	$yAxis += 6;
	while($row=  mysql_fetch_array($resultData)){

		$vitalResult = $row['vitalResult'];
		$vitalDisplayName = $row['vitalDisplayName'];

		$yAxis =  $this->GetY();
		$xAxis =  $this->GetX();
	
		
		$this->SetXY($xAxis + 8, $yAxis);
		$this->Write(5,"$vitalDisplayName: $vitalResult");

	}
	return $this->GetY();
}

function Show_History($appointmentID,$xAxis,$yAxis, $maxX , $size, $typeCode, $headerText){


	$resultData = getPrescribedHistory($appointmentID, $typeCode);




	if(mysql_num_rows($resultData) > 0){
		$this->SetFont('Times','B',$size);
		$this->SetXY($xAxis, $yAxis);
		$this->MultiCell(40,5,$headerText);
		$yAxis += 6;

	}if(mysql_num_rows($resultData) == 0){
		return $yAxis - 5;
	}
	$this->SetFont('Times','',$size);
	while($row=  mysql_fetch_array($resultData)){

		$historyResult = $row['historyResult'];
		$historylDisplayName = $row['historyName'];

		$yAxis =  $this->GetY();
		$this->SetXY($xAxis, $yAxis);
		$this->MultiCell($maxX,5,".$historylDisplayName:  $historyResult");

	}
	
	$yAxis = $this->GetY() + 2 ;
	$linestyle = array('width' => 20, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(255, 0, 0));
	$this->Line($xAxis, $yAxis, 90 , $yAxis, $linestyle);

	return $this->GetY();

}

function Show_Past_History($appointmentID,$xAxis,$yAxis, $maxX , $size, $status , $hedearText){


	$resultData = getPrescribedPastDisease2($appointmentID, $status);




	if(mysql_num_rows($resultData) > 0){
		$this->SetFont('Times','B',$size + 1);
		$this->SetXY($xAxis, $yAxis);
		$this->MultiCell($maxX,5,$hedearText);
		$yAxis += 6;

	}if(mysql_num_rows($resultData) == 0){
		return $yAxis - 5;
	}

	$this->SetFont('Times','',$size);

	while($row=  mysql_fetch_array($resultData)){

		$diseaseName = $row['diseaseName'];

		$yAxis =  $this->GetY();
		$this->SetXY($xAxis, $yAxis);
		$this->MultiCell($maxX,5,".$diseaseName");

	}
	if($status == 1){
		$yAxis = $this->GetY() + 2 ;
		$linestyle = array('width' => 20, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(255, 0, 0));
		$this->Line($xAxis, $yAxis, 90 , $yAxis, $linestyle);
	}
	

	return $this->GetY();
}

function Show_Family_History($appointmentID,$xAxis,$yAxis, $maxX , $size){

	$resultData = getPrescribedFamilyDisease($appointmentID);

	$this->SetFont('Times','',$size);


	if(mysql_num_rows($resultData) > 0){

		$this->SetFont('Times','B',$size + 1);
		$this->SetXY($xAxis, $yAxis);
		$this->MultiCell($maxX,5,"Family Disease");
		$yAxis += 6;

	}if(mysql_num_rows($resultData) == 0){
		return $yAxis - 5;
	}

	$this->SetFont('Times','',$size);
	while($row=  mysql_fetch_array($resultData)){

		$diseaseName = $row['diseaseName'];
		$relationName = $row['relationName'];

		$yAxis =  $this->GetY();
		$this->SetXY($xAxis, $yAxis);
		$this->MultiCell($maxX,5,".$diseaseName - $relationName");

	}
	
	$yAxis = $this->GetY() + 2 ;
	$linestyle = array('width' => 20, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(255, 0, 0));
	$this->Line($xAxis, $yAxis, 90 , $yAxis, $linestyle);

	return $this->GetY();
}

function Show_inv($appointmentID, $xAxis,$yAxis,$maxX,$size) {

	$this->SetFont('Times','',$size);

	$resultData = getPrescribedInv($appointmentID);

	if(mysql_num_rows($resultData) > 0){

		$this->SetFont('Times','B',$size + 1);
		$this->SetXY($xAxis, $yAxis);
		$this->MultiCell($maxX,5,"Test Advised");
		$yAxis += 6;

	}if(mysql_num_rows($resultData) == 0){
		return $yAxis - 5;
	}

	$var = 1;
	$this->SetFont('Times','',$size);
	while($row=  mysql_fetch_array($resultData)){

		$invName = $row['invName'];

		$yAxis =  $this->GetY();
		$this->SetXY($xAxis, $yAxis);
		$this->MultiCell($maxX,5,". $invName");
		$var++;
	}
	
	$yAxis = $this->GetY() + 2 ;
	$linestyle = array('width' => 20, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(255, 0, 0));
	$this->Line($xAxis, $yAxis, 90 , $yAxis, $linestyle);

	return $this->GetY();

}

function Show_Drug_History($appointmentID,$xAxis,$yAxis, $maxX , $size, $conentType , $hedearText){


	$contentData = getContentDetail($appointmentID, $conentType);

	if(mysql_num_rows($contentData) > 0){
		$this->SetFont('Times','B',$size + 1);
		$this->SetXY($xAxis, $yAxis);
		$this->MultiCell($maxX,5,$hedearText);
		$yAxis += 6;

	}if(mysql_num_rows($contentData) == 0){
		return $yAxis - 5;
	}

	$this->SetFont('Times','',$size);

	while($row=  mysql_fetch_array($contentData)){

		$data = $row['detail'];

		$yAxis =  $this->GetY();
		$this->SetXY($xAxis, $yAxis);
		$this->MultiCell($maxX,5,".$data");

	}
	
	if($conentType != 'OLDDRUGS'){
		$yAxis = $this->GetY() + 2 ;
		$linestyle = array('width' => 20, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(255, 0, 0));
		$this->Line($xAxis, $yAxis, 90 , $yAxis, $linestyle);
		
	}
	

	return $this->GetY();
}

function showComment($appointmentID,$leftXaxis,$leftYaxis, $maxX, $size){

	$contentData = getContentDetail($appointmentID, "COMMENT");

	$con = mysql_fetch_assoc($contentData);
	if($con){
		$this->SetFont('Times','B',$size + 1);
		$this->SetXY($leftXaxis, $leftYaxis);
		$this->MultiCell($maxX,5,"Comment");
		$leftYaxis += 5;
		$data = $con['detail'];
		$this->SetXY($leftXaxis, $leftYaxis);
		$this->SetFont('Times','',$size + 1);
		$this->MultiCell($maxX,5, "$data", 0);
			
		
	}
	
	
	
	
	return $this->GetY();
}

}

$pdf = new PDF('','A4',10,'nikosh');
$pdf->WriteHTML('');

//$pdf->SetAutoPageBreak(true, 12);

$res = getAppointmentInfo($appointmentID);
$appData = mysql_fetch_assoc($res);
$appType = $appData['appointmentType'];


$size = 11;
$maxX = 65;

$linestyle = array('width' => 20, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(255, 0, 0));
$pdf->Line(5, 53, 205, 53, $linestyle);
//$pdf->Line(5, 40, 205, 40, $linestyle);

$leftYaxis=$pdf->Show_vital($appointmentID,15,55, $maxX , $size);
$vitalEnds = $leftYaxis + 10;
$pdf->Line(5, $leftYaxis + 8, 205, $leftYaxis + 8, $linestyle);

$rightYaxis = $vitalEnds + 5;

$leftXaxis = 25;
$rightXaxis = 100;
$maxXForRight = 105;

$gap = 5;
$photoSize = 30;



//$pdf->Line($rightXaxis - 10 , $leftYaxis + 5, $rightXaxis - 10, $rightYaxis, $linestyle);






$rightYaxis = $pdf->Show_med($appointmentID,$rightXaxis, $rightYaxis,$size );
$rightYaxis = $pdf->Show_advice($appointmentID,$rightXaxis,$rightYaxis + 10,$size,$maxXForRight);

$rightYaxis = $pdf-> show_nextVisit($appointmentID,$rightXaxis,$rightYaxis + 10 ,$size +2);
$rightYaxis = $pdf-> show_ref_doc($appointmentID,$rightXaxis, $rightYaxis + 10, $size);
if($appType != 4){
	
	if($patientImage != null){
		$pdf->displayImage($doctorID, $patientImage,$leftXaxis,$vitalEnds,$photoSize);
		$gap = $gap + $photoSize;
	}
}

$leftYaxis=$pdf->Show_Complain($appointmentID,$leftXaxis,$vitalEnds + $gap, 60 , $size);
$leftYaxis=$pdf->Show_Drug_History($appointmentID,$leftXaxis,$leftYaxis + 5, 65 , $size , "OLDDRUGS" , "Old Drugs");
$leftYaxis=$pdf->Show_Drug_History($appointmentID,$leftXaxis,$leftYaxis + 5, 65 , $size , "CURRDRUGS" , "Current Drugs");

$leftYaxis=$pdf->Show_Past_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size , 0 , "Past Medical History");
$leftYaxis=$pdf->Show_Past_History($appointmentID,$leftXaxis,$leftYaxis + 5, 65, $size , 1 , "Associated Illness");

$leftYaxis=$pdf->Show_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX , $size, "OBS", "OBS");
$leftYaxis=$pdf->Show_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX , $size, "MH", "MH");
$leftYaxis=$pdf->Show_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX , $size, "Gynae", "Gynae");
$leftYaxis=$pdf->Show_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX , $size, "SUB-FERTILITY", "SUB-FERTILITY History");
$leftYaxis=$pdf->Show_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX , $size, "Immunization", "Immunization History");
$leftYaxis=$pdf->Show_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX , $size, "Others", "Others History");

$leftYaxis=$pdf->Show_Family_History($appointmentID,$leftXaxis,$leftYaxis + 3, $maxX, $size);
$leftYaxis=$pdf->Show_inv($appointmentID,$leftXaxis,$leftYaxis + 5, 65 , $size);


$leftYaxis = $pdf->Show_diagnosis($appointmentID, $leftXaxis ,$leftYaxis + 5,$size , 50);

$leftYaxis=$pdf->showComment($appointmentID,$leftXaxis,$leftYaxis + 5, 60, $size);


if($leftYaxis > $rightYaxis){
	$rightYaxis = $leftYaxis;
}
$pdf->Line($rightXaxis - 10 , $vitalEnds - 2, $rightXaxis - 10, $rightYaxis, $linestyle);
//$pdf->Line(5, $rightYaxis , 205, $rightYaxis , $linestyle);

$pdf->Output('');
exit;
?>
