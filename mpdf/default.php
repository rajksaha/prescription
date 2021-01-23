<?php
$date = date('d M, Y');
include_once("BasicFunction.php");
include_once('docFeed/appointment.php');
include_once('docFeed/doctor.php');
include_once('docFeed/patient.php');
include_once('docFeed/complain.php');
include_once('docFeed/diagnosis.php');
include_once('docFeed/diet.php');
include_once('docFeed/drug.php');
include_once('docFeed/inv.php');
include_once('docFeed/advice.php');
include_once('docFeed/vital.php');
include_once('docFeed/history.php');
include_once('docFeed/nextVisit.php');
include_once('docFeed/contentDetail.php');
include_once('docFeed/refDoctor.php');
include_once('docFeed/history.php');

class PDF extends mPDF {
	private $conn;
	private $appointmentID;
	
 function Header()
{
    
}

function Footer() {
	
}



function Show_med($conn, $appointmentID, $xAxis, $yAxis, $size, $pageNum,$pdf){

        $resultData = getPresCribedDrugs($conn, $appointmentID);


        if(mysqli_num_rows($resultData) > 0){
            $this->SetFont('nikosh','B',$size);
            $this->SetXY($xAxis , $yAxis);
            $this->MultiCell(40,5,"Rx");
            $yAxis += 6;

        }else{
            return $yAxis - 5;
        }

        $this->SetFont('nikosh','',$size);

        $nameCell = 100;
        $doseCell = 40;
        $durationCell = 70;
        $whenCell = 15;
        $var = 1;
        while($row=mysqli_fetch_array($resultData)){

            //$this->SetFont('Times','',$size + 2);

            $drugType = $row['drugTypeInitial'];
            $drugName = $row['drugName'];
            $drugStr = $row['drugStrength'];
            $drugPrescribeID = $row['presDrugID'];
            $doseTypeID = $row['doseTypeID'];
            $doseTypeName = $row['doseTypeName'];
            $drugDoseInitial = $row['drugDoseUnit'];
            $drugWhen = $row['drugWhenName'];
            $drugWhenID = $row['drugWhenID'];
            $drugAdvice = $row['drugAdviceName'];
            $drugAdviceID = $row['drugAdviceName'];


            $yAxis =  $this->GetY() + 2;
            if(($yAxis + 10)  > 260 ){
                $this->AddPage();
                $yAxis = 60;
            }


            $this->SetXY($xAxis, $yAxis);
            if($drugStr  == ""){
                $this->MultiCell($nameCell,5,"$var. $drugType. $drugName");
            }else{
                $this->MultiCell($nameCell,5,"$var. $drugType. $drugName- $drugStr");
            }
            $var = $var + 1;


            $this->SetXY($xAxis + 5, $yAxis + 6);
            $doseData = getPreiodicList($conn, $drugPrescribeID);



            if($doseTypeID != -1){
                $dose = mysqli_fetch_assoc($doseData);
                $drugDose = $dose['dose'];
                $drugNoDay = $dose['numOfDay'];
                $drugNoDayType = $dose['bngDurationName'];

                $drugDose = str_replace("-","+", $drugDose);
                if($doseTypeID == 1){
                    if($drugAdviceID == 14 || $drugWhenID == 11 || $drugAdviceID == 54 || $drugAdviceID == 62 || $drugWhenID == 23){
                        $drugDose =  "$drugDose + 0 + 0";
                    }else if ($drugAdviceID == 15 || $drugWhenID == 13){
                        $drugDose =  "0 + 0 + $drugDose";
                    }else if($drugAdviceID == 16 || $drugWhenID == 12){
                        $drugDose =  "0 + $drugDose + 0";
                    }else{
                        $drugDose =  "0 + 0 + $drugDose";
                    }

                }else if($doseTypeID == 2){
                    list($num,$type) = explode('+', $drugDose, 2);
                    $drugDose =  "$num + 0 + $type";
                }


                if($drugNoDay == 0){
                    $drugNoDay = "";
                }
                $restOftheString = "- $drugWhen - $drugAdvice - $drugNoDay $drugNoDayType";

            }else{
                $index= 0;
                $periodText = "";

                while ($dose = mysqli_fetch_array($doseData)){
                    $drugDose = $dose['dose'];
                    $drugNoDay = $dose['numOfDay'];
                    $drugNoDayType = $dose['bangla'];

                    $drugDose = str_replace("-","+", $drugDose);
                    if($doseTypeID == 1){
                        if($drugAdviceID == 14){
                            $drugDose =  "$drugDose + 0 + 0";
                        }else if ($drugAdviceID == 15){
                            $drugDose =  "0 + 0 + $drugDose";
                        }else{
                            $drugDose =  "0 + $drugDose + 0";
                        }

                    }else if($doseTypeID == 2){
                        list($num,$type) = explode('+', $drugDose, 2);
                        $drugDose =  "$num + 0 + $type";
                    }

                    $text = "";
                    if($drugDoseInitial == ""){
                        $text = "($drugDose)";
                    }else{
                        $text = "($drugDose) $drugDoseInitial $drugNoDay $drugNoDayType";
                    }

	                if($index == 0){
                        $periodText = 'প্রথম'. " $drugNoDay $drugNoDayType $text";
                    }else{
                        $periodText = "$periodText". " তারপর " . " $text $drugNoDay $drugNoDayType";
                    }
                    $index++;

                }

                $restOftheString = "$periodText $drugWhen $drugAdvice";
                $drugDose = "";
            }

            $full_str = "";
            if($drugDoseInitial == "" || $drugDose == ''){
                $full_str = "$drugDose $restOftheString|";
            }else{
                $full_str ="($drugDose)$drugDoseInitial $restOftheString।";
            }

            $full_str = $this->convertNumberToBangla($full_str);

            $this->MultiCell(110,5,"$full_str");
            //$yAxis += 8;
        }

        return $this->GetY();

    }

}

function showDocInfo($username, $yAxis, $size){

	$resultData = getDoctorInfo($doctorId);
	if($resultData['prescriptionStyle'] == 2){
		$this->SetXY(130, $yAxis);
		$this->SetFont('nikosh','B',$size + 3);
		$this->MultiCell(100,5, "Prof. Md. Ali Hossain", 0);
		
		$this->SetXY(130, $yAxis);
		$this->SetFont('nikosh','',$size);
		$this->MultiCell(100,5, "MBBS FCPS(Med), MD(Chest)", 0);

		
		$this->SetXY(130, $yAxis);
		$this->SetFont('nikosh','',$size);
		$this->MultiCell(100,5, "Medicine Specialist and Pulmonologist", 0);

		$yAxis = $yAxis + 5;
		$this->SetXY(130, $yAxis);
		$this->SetFont('nikosh','',$size);
		$this->MultiCell(100,5, "BMDC Reg. No. A-15979", 0);

		

	}

}

function preparePrescription($conn, $appointmentID){
	$pdf = new PDF('','A4',10,'nikosh');
	$pdf->WriteHTML('');
	echo "inside";
	//$pdf->SetAutoPageBreak(true, 12);
	
	$appData = getAppointmentInfo($conn,$appointmentID);
	$appType = $appData['appointmentType'];
	$patientID = $appData['patientID'];
	$doctorID = $appData['doctorID'];
	$lineStyle = array('width' => 20, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(255, 0, 0));
	$pdf->Line(10, 53, 195, 53, $linestyle);
	$pdf->Line(10, 60, 195, 60, $linestyle);
	//$pdf->Line(10, 260, 195, 260, $linestyle);
	
	$leftYaxis = 65;
	$rightYaxis = 70;
	$size = 10;
	
	$leftXaxis = 15;
	$rightXaxis = 90;
	$maxX = 60;
	$maxXForRight = 100;
	
	$gap = 5;
	$photoSize = 25;
	
	$pageNum = 1;
	$pdf->page = $pageNum;
	
	
	$pdf->Line($rightXaxis - 10 , 53, $rightXaxis - 10, 260, $lineStyle);
	
	
	$rightYaxis = $pdf->Show_med($conn, $appointmentID,$rightXaxis, $rightYaxis,$size + 2, $pageNum, $pdf);
	$rightYaxis = $pdf->checkForPageChange($rightYaxis, $pdf->page);
	$rightYaxis = $pdf->Show_advice($conn, $appointmentID,$rightXaxis,$rightYaxis + 10,$size + 1,$maxXForRight);
	$rightYaxis = $pdf->checkForPageChange($rightYaxis, $pdf->page);
	$rightYaxis = $pdf-> show_nextVisit($conn, $appointmentID,$rightXaxis,$rightYaxis + 10 ,$size +2);
	
	
	$yPageNo = $pdf->page;
	$pageNum = 1;
	$pdf->page = $pageNum;
	
	if($appType != 4){
		$patientImage = $pdf->ShowPatInfo($conn, $patientID, 45, $appointmentID);
		if($patientImage != null){
			$pdf->displayImage($conn, $doctorID, $patientImage,$leftXaxis,$leftYaxis,$photoSize);
			$gap = $gap + $photoSize;
		}
	}
	
	$leftYaxis=$pdf->Show_Complain($conn, $appointmentID,$leftXaxis,$leftYaxis + $gap, $maxX , $size);
	$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
 	$leftYaxis=$pdf->Show_vital($conn, $appointmentID,$leftXaxis,$leftYaxis + 5, $maxX , $size);
 	$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
	$leftYaxis=$pdf->Show_History($conn, $appointmentID, $doctorID, $leftXaxis,$leftYaxis +5, $maxX , $size);
	$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
	$leftYaxis=$pdf->Show_Past_History($conn, $appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size , 0 , "Past Disease");
	$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
	$leftYaxis=$pdf->Show_Past_History($conn, $appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size , 1 , "Associated Illness");
	$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
	$leftYaxis=$pdf->Show_Family_History($conn, $appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size);
	$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
	$leftYaxis=$pdf->Show_Drug_History($conn, $appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size , "OLD_DRUG" , "Old Drug(s)");
	$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
	$leftYaxis=$pdf->Show_Drug_History($conn, $appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size , "CURRENT_DRUG" , "Current Drug(s)");
	$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
	/* $leftYaxis=$pdf->showClinicalRecord($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size); */
	$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
 	$leftYaxis= $pdf->Show_inv($conn, $appointmentID,$leftXaxis,$leftYaxis + 5 , $maxX , $size);
 	$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
 	$leftYaxis = $pdf->Show_diagnosis($conn, $appointmentID, $leftXaxis ,$leftYaxis + 5 ,$size , $maxX);
 	$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
	$leftYaxis=$pdf->showComment($conn, $appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size);
	$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
	$leftYaxis=$pdf-> show_ref_doc($conn, $appointmentID,$leftXaxis,$leftYaxis + 5,$size);
	$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
	
	if($yPageNo > $pdf->page){
		$pdf->page = $yPageNo;
		$pdf->Line($rightXaxis - 10 , 60, $rightXaxis - 10, 260, $lineStyle);
	}
	
	//$pdf->showDocInfo($username, 15, $size + 2);
	
	$pdf->Output('');
	return $pdf;
}




?>
