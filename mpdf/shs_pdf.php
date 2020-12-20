<?php
include("BasicFunction.php");
session_start();
include('../phpServices/config.inc');
$username=$_SESSION['username'];
$appointmentID = $_SESSION['printAppointmentID'];
$patientCode = $_SESSION['printPatientCode'];
$doctorID = $_SESSION['doctorID'];
$date = date('d M, Y');
include('../phpServices/commonServices/appointmentService.php');
include('../phpServices/commonServices/prescriptionService.php');

class PDF extends mPDF {
	
	
	public function Header(){
    //$this->Image('',15,10,180,40,'png','',true, false);
}

function Footer() {
	//$this -> image ('',10,260,185,25,'png','',true, false);
}


function ShowPatInfo($patientCode,$yAxis, $appointmentID, $appData){

        $resultData = getPatientInformaition($patientCode);
        $weight = 0;

        $vitalResultData = getPrescribedVital($appointmentID);

        while($row=  mysql_fetch_array($vitalResultData)){


            $vitalID = $row['vitalID'];

            if($vitalID == 81){
                $vitalResult = $row['vitalResult'];
                //$vitalDisplayName = $row['vitalDisplayName'];
                $weight = $vitalResult;
            }

        }

        $rec = mysql_fetch_assoc($resultData);

        $patientCode = $rec['patientCode'];

        $patientCode = substr($patientCode, - 10);

        $name = $rec['name'];

        $age = $rec['age'];

        $date = $appData['date'];

        $formattedDate = date("d M, Y", strtotime($date));
        $this->SetXY(100,$yAxis + 11);
        $this->MultiCell(65,5, "ID No: $patientCode");


        $this->SetXY(15,$yAxis + 11);
        $this->MultiCell(65,5, "$name");

        $this->SetXY(15, $yAxis - 8);
        //$this->MultiCell(65, 5, "$phone");

        $this->SetXY(80, $yAxis + 11);
        $this->MultiCell(50, 5, "$age Yrs");


        $this->SetXY(130, $yAxis + 5);
        //$this->MultiCell(30, 5, "$sex");

        $this->SetXY(160, $yAxis + 11);
        $this->MultiCell(50,5, "$formattedDate");


        $this->SetXY(100, $yAxis + 5);
        //$this->MultiCell(50, 5, "$address");




        return $rec['patientImage'];

    }

function Show_Drug_History($appointmentID,$xAxis,$yAxis, $maxX , $size, $conentType , $hedearText){


        $contentData = getContentDetail($appointmentID, $conentType);

        if(mysql_num_rows($contentData) > 0){
            $this->SetFont('nikosh','B',$size );
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,$hedearText);
            $yAxis += 6;

        }if(mysql_num_rows($contentData) == 0){
            return $yAxis - 5;
        }

        $this->SetFont('nikosh','',$size);

        while($row=  mysql_fetch_array($contentData)){

            $data = $row['detail'];

            $yAxis =  $this->GetY();
            $yAxis = $this->checkForPageChange($yAxis, $this->page);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"$data");

        }

        return $this->GetY();
    }
    function showComment($appointmentID,$leftXaxis,$leftYaxis, $maxX, $size){

        $contentData = getContentDetail($appointmentID, "COMMENT");

        $con = mysql_fetch_assoc($contentData);
        if($con){
            $this->SetFont('nikosh','B',$size);
            $this->SetXY($leftXaxis, $leftYaxis);
            $this->MultiCell($maxX,5,"Clinical Note");
            $leftYaxis += 5;
            $data = $con['detail'];
            $this->SetXY($leftXaxis, $leftYaxis);
            $this->SetFont('nikosh','',$size );
            $this->MultiCell($maxX,5, "$data", 0);
        }

        return $this->GetY();
    }
    function Show_diagnosis($appointmentID,$xAxis,$yAxis, $size ){

        $resultData = getPrescribedDiagnosis($appointmentID);



        $con = mysql_fetch_assoc($resultData);
        if($con){
            $this->SetFont('nikosh','B',$size );
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(60,5,"Diagnosis");
            $yAxis += 5;
            $this->SetFont('nikosh','',$size );
            $this->SetXY($xAxis, $yAxis);
            $diseaseName = $con['diseaseName'];
			$note = $con['note'];
            $this->MultiCell(60, 5,"$diseaseName, $note");
        }

        return $this->GetY();

    }
    function Show_Complain($appointmentID,$xAxis,$yAxis, $maxX , $size) {

        $resultData = getPrescribedComplain($appointmentID);




        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('nikosh','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"Chief Complaints");
            $yAxis += 6;

        }if(mysql_num_rows($resultData) == 0){
            return $yAxis - 5;
        }
        $this->SetFont('nikosh','',$size);
        $var = 1;
        while($row=  mysql_fetch_array($resultData)){

            $symptomName = $row['symptomName'];
            $durationNum = $row['durationNum'];
            $durationType = $row['durationType'];
            $durationID = $row['durationID'];

            $yAxis =  $this->GetY();
            $yAxis = $this->checkForPageChange($yAxis, $this->page);
            $this->SetXY($xAxis, $yAxis);
            if($durationID < 5){
                $this->MultiCell($maxX,5," $symptomName - $durationNum - $durationType");
            }elseif ($durationID == 7){
                $this->MultiCell($maxX,5," $symptomName - $durationType");
            }else{
                $this->MultiCell($maxX,5," $symptomName");
            }

            $var++;
        }

        return $this->GetY();

    }
    function Show_Family_History($appointmentID,$xAxis,$yAxis, $maxX , $size){

        $resultData = getPrescribedFamilyDisease($appointmentID);



        if(mysql_num_rows($resultData) > 0){

            $this->SetFont('nikosh','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"Family Disease");
            $yAxis += 6;

        }if(mysql_num_rows($resultData) == 0){
            return $yAxis - 5;
        }

        $this->SetFont('nikosh','',$size);
        while($row=  mysql_fetch_array($resultData)){

            $diseaseName = $row['diseaseName'];
            $relationName = $row['relationName'];

            $yAxis =  $this->GetY();
            $yAxis = $this->checkForPageChange($yAxis, $this->page);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"$diseaseName - $relationName");

        }

        return $this->GetY();
    }
    function Show_Past_History($appointmentID,$xAxis,$yAxis, $maxX , $size, $status , $hedearText){


        $resultData = getPrescribedPastDisease2($appointmentID, $status);




        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('nikosh','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(80,5,$hedearText);
            $yAxis += 6;

        }if(mysql_num_rows($resultData) == 0){
            return $yAxis - 5;
        }

        $this->SetFont('nikosh','',$size);

        while($row=  mysql_fetch_array($resultData)){

            $diseaseName = $row['diseaseName'];

            $yAxis =  $this->GetY();
            $yAxis = $this->checkForPageChange($yAxis, $this->page);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"$diseaseName");

        }

        return $this->GetY();
    }
    function Show_History($appointmentID,$xAxis,$yAxis, $maxX , $size, $typeCode, $headerText){


        $resultData = getPrescribedHistory($appointmentID, $typeCode);




        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('nikosh','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(80,5,$headerText);
            $yAxis += 6;

        }if(mysql_num_rows($resultData) == 0){
            return $yAxis - 5;
        }
        $this->SetFont('nikosh','',$size);
        while($row=  mysql_fetch_array($resultData)){

            $historyResult = $row['historyResult'];
            $historylDisplayName = $row['historyName'];

            $yAxis =  $this->GetY();
            $yAxis = $this->checkForPageChange($yAxis, $this->page);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"$historylDisplayName:  $historyResult");

        }

        return $this->GetY();

    }
    function show_ref_doc($appointmentID,$xAxis,$yAxis,$size){

        $this->SetFont('nikosh','',$size);

        $resultData = getPrescribedReffredDoctor($appointmentID);

        $rec = mysql_fetch_assoc($resultData);

        if($rec['doctorName'] != ""){
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(110,5, "Refd. to: " . $rec['doctorName']);
            $yAxis =  $this->GetY();
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(110,5, $rec['doctorAdress']);
        }

        return $this->GetY();

    }

    function show_diet($appointmentID,$xAxis,$yAxis,$size){


        $resultData = getContentDetail($appointmentID, "DIET");
        $rec = mysql_fetch_assoc($resultData);

        if($rec['detail'] != ""){
            $this->SetFont('nikosh','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(110,5, "Diet");
            $this->SetFont('nikosh','',$size);
            $this->SetXY($xAxis, $yAxis +5 );
            $this->MultiCell(110,5, $rec['detail']);
            return $this->GetY();
        }

        return $yAxis;

    }
    function show_nextVisit($appointmentID,$xAxis,$yAxis,$size){


        $resultData = getPrescribedNextVisit($appointmentID);

        $rec = mysql_fetch_assoc($resultData);

        $nextVisitType = $rec['nextVisitType'];

        $this->SetXY($xAxis, $yAxis);



        if($nextVisitType == 2){

            $contentData = getContentDetail($nextVisitType, "NEXTVISIT");
            $con = mysql_fetch_assoc($contentData);
            $data = $con['detail'];

            $numOfday = $rec['numOfDay'];
            $numOfday = $this->convertNumberToBangla($numOfday);
            $dayType = $rec['bangla'];
            $this->MultiCell(60,5, "$numOfday - $dayType $data", 1);

        }else if($nextVisitType == 1){

            $contentData = getContentDetail($nextVisitType, "NEXTVISIT");
            $con = mysql_fetch_assoc($contentData);
            $data = $con['detail'];

            $date = $rec['date'];
            $newDate = date("d-m-Y", strtotime($date));
            $newDate = $this->convertNumberToBangla($newDate);
            $this->MultiCell(60,5, "$newDate $data", 1);
        }

        return $this->GetY();

    }

    function Show_advice($appointmentID,$xAxis,$yAxis,$size,$maxX){

        $resultData = getPrescribedAdvice($appointmentID);

        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('nikosh','B',$size );
            $this->SetXY($xAxis , $yAxis);
            $this->MultiCell(20,5,"উপদেশ");
        }

        $this->SetFont('nikosh','',$size );
		$var = 1;
        while($row=  mysql_fetch_array($resultData)){
            $advice = $row['advice'];

            $yAxis =  $this->GetY();
            $yAxis = $this->checkForPageChange($yAxis, $this->page);
            $this->SetXY($xAxis, $yAxis );
            $this->MultiCell($maxX,5,"* $advice");
			$var++;
        }
        return $this->GetY();
    }
    function Show_vital($appointmentID,$xAxis, $yAxis, $maxX, $size){




        $resultData = getPrescribedVital($appointmentID);

        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('nikosh','B',$size );
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(40,5,"On Examination");
            $yAxis += 6;

        }if(mysql_num_rows($resultData) == 0){
            return $yAxis - 5;
        }
        $this->SetFont('nikosh','',$size);
        while($row=  mysql_fetch_array($resultData)){

            $vitalResult = $row['vitalResult'];
            $vitalDisplayName = $row['vitalDisplayName'];

            $yAxis =  $this->GetY();
            $yAxis = $this->checkForPageChange($yAxis, $this->page);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"$vitalDisplayName:  $vitalResult");

        }

        return $this->GetY();
    }
    function Show_inv($appointmentID, $xAxis,$yAxis,$maxX,$size) {


        $resultData = getPrescribedInv($appointmentID);

        if(mysql_num_rows($resultData) > 0){

            $this->SetFont('nikosh','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(40,5,"Test Advised");
            $yAxis += 6;

        }if(mysql_num_rows($resultData) == 0){
            return $yAxis - 5;
        }

        $var = 1;
        $this->SetFont('nikosh','',$size);
        while($row=  mysql_fetch_array($resultData)){

            $invName = $row['invName'];

            $yAxis =  $this->GetY();
            $yAxis = $this->checkForPageChange($yAxis, $this->page);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5," .$invName");
            $var++;
        }

        return $this->GetY();

    }

    function Show_med($appointmentID, $xAxis, $yAxis, $size, $pageNum,$pdf){

        $resultData = getPresCribedDrugs($appointmentID);


        if(mysql_num_rows($resultData) > 0){
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
        while($row=  mysql_fetch_array($resultData)){

            //$this->SetFont('Times','',$size + 2);

            $drugType = $row['typeInitial'];
            $drugTypeID = $row['drugTypeID'];
            $drugName = $row['drugName'];
            $drugStr = $row['drugStrength'];
            $drugPrescribeID = $row['id'];
            $drugTime = $row['drugTimeID'];
            $drugDoseInitial = $row['drugDoseUnit'];
            $drugWhen = $row['whenTypeName'];
            $drugWhenID = $row['drugWhenID'];
            $drugAdvice = $row['adviceTypeName'];
            $drugAdviceID = $row['drugAdviceID'];


            $yAxis =  $this->GetY()+ 6;
            if(($yAxis + 10)  > 240 ){
                $this->AddPage();
                $yAxis = 60;
            }


            $this->SetXY($xAxis, $yAxis);
            if($drugStr  == ""){
                $this->MultiCell($nameCell,5,"$var. $drugType. $drugName");
            }else{
                $this->MultiCell($nameCell,5,"$var. $drugType. $drugName - $drugStr");
            }
            $var = $var + 1;
			
			
			$this->SetXY($xInnerAxis, $yAxis);
		$realY =  $this->GetY();
	if($drugDoseInitial != ""){
			if($drugTypeID == 3 || $drugTypeID == 15 || $drugTypeID == 41){
				$drugDoseInitial = str_replace("s","চা চামচ", $drugDoseInitial);
			}else if($drugTypeID == 4){
				$drugDoseInitial = str_replace("ampl","এম্পল", $drugDoseInitial);
				$drugDoseInitial = str_replace("vial","ভায়াল", $drugDoseInitial);
			}else if($drugTypeID == 10 || $drugTypeID == 14 || $drugTypeID == 21){
				$drugDoseInitial = str_replace("puff","চাপ", $drugDoseInitial);
			}else if($drugTypeID == 7){
				$drugDoseInitial = str_replace("d","ড্রপ", $drugDoseInitial);
			}else if($drugTypeID == 6){
				$drugDoseInitial = str_replace("u","(+/-) 2 ইউনিট", $drugDoseInitial);
			}else if($drugTypeID == 11){
				$drugDoseInitial = str_replace("drp/min","দ্রপ/মিনিট", $drugDoseInitial);	
			}
		}


            $this->SetXY($xAxis + 5, $yAxis + 6);
            //$this->MultiCell($nameCell,5,"$xAxis - $yAxis");

            $doseData = getPreiodicListforPdf($drugPrescribeID);



            if($drugTime != -1){

                $dose = mysql_fetch_assoc($doseData);
                $drugDose = $dose['dose'];
                $drugNoDay = $dose['numOfDay'];
                $drugNoDayType = $dose['bangla'];

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
                }else if($drugTime == -4){
                    $drugDose =  "সপ্তাহে ১ বার";
                }else if($drugTime == -5){
                    $drugDose =  "মাসে ১ বার";
                }else if($drugTime == -6){
                    $drugDose =  "বছরে ১ বার";
                }


                if($drugNoDay == 0){
                    $drugNoDay = "";
                }
                $restOfTheString = "";
                if($drugWhen != ""){
                    $restOfTheString = $restOfTheString . " - " . $drugWhen;
                }
                if($drugAdvice != ""){
                    $restOfTheString = $restOfTheString . " - " . $drugAdvice;
                }
                if($drugNoDay == null || $drugNoDay != "" || $drugNoDay != 0){
                    $restOfTheString = $restOfTheString . " - " . $drugNoDay . $drugNoDayType;
                }

            }else{
                $index= 0;
                $periodText = "";

                while ($dose = mysql_fetch_array($doseData)){
                    $drugDose = $dose['dose'];
                    $drugNoDay = $dose['numOfDay'];
                    $drugNoDayType = $dose['bangla'];

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

                    $text = "";
                    if($drugTime == -4 || $drugTime == -5 || $drugTime == -6){
                        $text = "$drugDose";
                        $drugDoseInitial = "";
                    }else if($drugDoseInitial == ""){
                        $text = "($drugDose)";
                    }else{
                        $text = "($drugDose) $drugDoseInitial";
                    }

                    if($index = 0){
                        $periodText = " $drugNoDay $drugNoDayType $text";
                    }else{
                        $periodText = "$periodText". "\n" . " $text $drugNoDay $drugNoDayType";
                    }
                    $index++;

                }

                $restOfTheString = " $drugWhen $drugAdvice $periodText ";
                $drugDose = "";
            }

            $full_str = "";
            if($drugDoseInitial == "" || $drugDose == ''){
                $full_str = "$drugDose $restOfTheString|";
            }else if($drugTime == -4 || $drugTime == -5 || $drugTime == -6){
                $full_str ="$drugDose $restOfTheString|";
            }else{
                $full_str ="($drugDose) $drugDoseInitial $restOfTheString|";
            }

            $full_str = $this->convertNumberToBangla($full_str);

            $this->MultiCell(90,5,"$full_str");
            //$yAxis += 8;
        }

        return $this->GetY();

    }

}





$pdf = new PDF('','A4',10,'nikosh');
$pdf->WriteHTML('');

$pdf->SetAutoPageBreak(true, 35);

$res = getAppointmentInfo($appointmentID);
$appData = mysql_fetch_assoc($res);
$appType = $appData['appointmentType'];

$lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(255, 0, 0));
$pdf->Line(15, 55, 195, 55, $linestyle);
$pdf->Line(15, 62, 195, 62, $linestyle);
$pdf->Line(15, 260, 195, 260, $linestyle);

$leftYaxis = 65;
$rightYaxis = 70;
$size = 10;

$leftXaxis = 15;
$rightXaxis = 90;
$maxX = 60;
$maxXForRight = 100;

$gap = 15;
$photoSize = 30;

$pageNum = 1;
$pdf->page = $pageNum;


$pdf->Line($rightXaxis - 10 , 62, $rightXaxis - 10, 260, $lineStyle);

$rightYaxis = $pdf->show_diet($appointmentID,$rightXaxis, $rightYaxis,$size, $pageNum, $pdf);
$rightYaxis = $pdf->Show_med($appointmentID,$rightXaxis, $rightYaxis,$size, $pageNum, $pdf);
$rightYaxis = $pdf->checkForPageChange($rightYaxis, $pdf->page);
$rightYaxis = $pdf->Show_advice($appointmentID,$rightXaxis,$rightYaxis + 5,$size ,$maxXForRight);
$rightYaxis = $pdf->checkForPageChange($rightYaxis, $pdf->page);
$rightYaxis = $pdf-> show_nextVisit($appointmentID,$rightXaxis,$rightYaxis + 5 ,$size);
$rightYaxis = $pdf->checkForPageChange($rightYaxis, $pdf->page);
$rightYaxis=$pdf-> show_ref_doc($appointmentID,$rightXaxis,$rightYaxis + 5,$size );

$yPageNo = $pdf->page;
$pageNum = 1;
$pdf->page = $pageNum;

if($appType != 4){
    $patientImage = $pdf->ShowPatInfo($patientCode, 45, $appointmentID, $appData);
    if($patientImage != null){
        $pdf->displayImage($doctorID, $patientImage,$leftXaxis,$leftYaxis,$photoSize);
        $gap = $gap + $photoSize;
    }
}

$leftYaxis=$pdf->Show_Complain($appointmentID,$leftXaxis,$leftYaxis + $gap, $maxX , $size);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->Show_vital($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX , $size);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->Show_History($appointmentID,$leftXaxis,$leftYaxis +5, $maxX , $size, "RISK", "Risk Factors");
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->Show_Past_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size , 0 , "Past Illness");
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->Show_Past_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size , 1 , "Co Morbidities");
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->Show_Family_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->Show_Drug_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size , "OLDDRUGS" , "Old Drugs");
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->Show_Drug_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size , "CURRDRUGS" , "Current Drugs");
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->Show_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX , $size, "OBS", "OBS");
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->Show_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX , $size, "MH", "MH");
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->Show_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX , $size, "Gynae", "Gynae");
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->Show_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX , $size, "SUB-FERTILITY", "SUB-FERTILITY History");
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->Show_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX , $size, "Immunization", "Immunization History");
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->Show_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX , $size, "Others", "Others History");
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->showClinicalRecord($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->Show_inv($appointmentID,$leftXaxis,$leftYaxis + 5 , $maxX , $size);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis = $pdf->Show_diagnosis($appointmentID, $leftXaxis ,$leftYaxis + 5 ,$size , $maxX);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->showComment($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);



if($yPageNo > $pdf->page){
    $pdf->page = $yPageNo;
    $pdf->Line($rightXaxis - 10 , 55, $rightXaxis - 10, 260, $lineStyle);
	$pdf->Line(15, 55, 195, 55, $linestyle);
	//$pdf->Line(15, 62, 195, 62, $linestyle);
	$pdf->Line(15, 260, 195, 260, $linestyle);
}

//$pdf-> show_diagnosis($appointmentID,15,55,$size);
//$pdf-> show_ref_doc($appointmentID,15,260,$size);
//$pdf->showDocInfo($username, 15, $size );


$pdf->Output('');
exit;
?>
