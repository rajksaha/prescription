<?php
include("BasicFunction.php");
session_start();
include('../phpServices/config.inc');
$username=$_SESSION['username'];
$appointmentID = $_SESSION['printAppointmentID'];
$patientCode = $_SESSION['printPatientCode'];
$date = date('d M, Y');
include('../phpServices/commonServices/appointmentService.php');
include('../phpServices/commonServices/prescriptionService.php');

class PDF extends mPDF {
	
		public function Header()
{
    $this->Image('header.png',30,5,140,40,'png','',true, false);
	$this->Image('images (1).png',30,53,60,7,'png','',true, false);
}

function Footer() {
	$this -> image ('footer.png',30,260,140,20,'png','',true, false);
	
	
	
}

    function show_med($appointmentID, $xAxis, $yAxis, $size, $pageNum,$pdf){

        $resultData = getPresCribedDrugs($appointmentID);

        if(mysql_num_rows($resultData) > 10){
            $size = $size - 2;
        }

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


            $yAxis =  $this->GetY() + 2;
            if(($yAxis + 10)  > 260 ){
                $this->AddPage();
                $yAxis = 70;
            }


            $printItem = "";
            $this->SetXY($xAxis, $yAxis);
            if($drugStr  == ""){
                $printItem = "$printItem" . " $var. $drugType. $drugName";
            }else{
                $printItem = "$printItem" . " $var. $drugType. $drugName- $drugStr";
            }
            $var = $var + 1;


            
		
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
                }


                if($drugNoDay == 0){
                    $drugNoDay = "";
                }
                $restOftheString = "- $drugWhen - $drugAdvice - $drugNoDay $drugNoDayType";

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
                $full_str ="($drugDose) $drugDoseInitial $restOftheString|";
            }

            $full_str = $this->convertNumberToBangla($full_str);

            $this->MultiCell(160,5,"$printItem - $full_str");
            //$yAxis += 8;
        }

        return $this->GetY();

    }
	
	function ShowPatInfo($patientCode,$yAxis, $appointmentID){
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
		
		$this->SetFont('nikosh','',11);
		
        $name = $rec['name'];

        $age = $rec['age'];

        $sex = $rec['sex'];

        $address = $rec['address'];

        $phone = $rec['phone'];

        $date = date('D d M Y');


        $this->SetXY(30,$yAxis + 15);
        $this->MultiCell(65,5, "Patient ID No: $patientCode");


        $this->SetXY(30,$yAxis + 25);
        $this->MultiCell(65,5, "$name");

        $this->SetXY(15, $yAxis - 8);
        //$this->MultiCell(65, 5, "$phone");

        $this->SetXY(80, $yAxis + 25);
        $this->MultiCell(50, 5, "$age Yrs");


        $this->SetXY(130, $yAxis + 5);
        //$this->MultiCell(30, 5, "$sex");

        $this->SetXY(150, $yAxis + 25);
        $this->MultiCell(50,5, "$date");


        $this->SetXY(30, $yAxis + 30);
        $this->MultiCell(50, 5, "$address");

        return $rec['patientImage'];

    }
	
	
    function show_Complain($appointmentID,$xAxis,$yAxis, $maxX , $size) {

        $resultData = getPrescribedComplain($appointmentID);




        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('nikosh','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"The patient presented today with");
            $yAxis += 6;

        }if(mysql_num_rows($resultData) == 0){
            return $yAxis - 5;
        }
        $this->SetFont('nikosh','',$size);
        $var = 1;
        $printItem = "";
        while($row=  mysql_fetch_array($resultData)){

            $symptomName = $row['symptomName'];
            $durationNum = $row['durationNum'];
            $durationType = $row['durationType'];
            $durationID = $row['durationID'];


            if($durationID < 5){
                $printItem = "$printItem" .  "  $symptomName - $durationNum - $durationType, ";
            }elseif ($durationID == 7){
                $printItem = "$printItem" .  "  $symptomName - $durationType, ";
            }else{
                $printItem = "$printItem" .  "  $symptomName, ";
            }
            $var++;
        }
        if($printItem){
            $yAxis =  $this->GetY();
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,rtrim($printItem,", "));
        }


        return $this->GetY();

    }
    function show_vital($appointmentID,$xAxis, $yAxis, $maxX, $size){




        $resultData = getPrescribedVital($appointmentID);

        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('nikosh','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(70,5,"Physical examination reveals: ");
            $yAxis += 6;

        }if(mysql_num_rows($resultData) == 0){
            return $yAxis - 5;
        }
        $this->SetFont('nikosh','',$size);
        $printItem = "";
        while($row=  mysql_fetch_array($resultData)){

            $vitalResult = $row['vitalResult'];
            $vitalDisplayName = $row['vitalDisplayName'];
            $printItem = "$printItem" .  "  $vitalDisplayName: $vitalResult,";
        }

        $yAxis =  $this->GetY();
        $this->SetXY($xAxis , $yAxis);
        $this->MultiCell($maxX,5,rtrim($printItem,", "));
        return $this->GetY();
    }
    function show_inv($appointmentID, $xAxis,$yAxis,$maxX,$size) {

        $this->SetFont('Times','',$size);

        $resultData = getPrescribedInv($appointmentID);

        if(mysql_num_rows($resultData) > 0){

            $this->SetFont('nikosh','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(70,5,"Investigations Advised");
            $yAxis += 6;

        }if(mysql_num_rows($resultData) == 0){
            return $yAxis - 5;
        }

        $printItem = "";
        $this->SetFont('nikosh','',$size);
        while($row=  mysql_fetch_array($resultData)){
            $invName = $row['invName'];
            $printItem = "$printItem" .  "  $invName,";
        }
        $yAxis =  $this->GetY();
        $this->SetXY($xAxis, $yAxis);
        $this->MultiCell($maxX,5,rtrim($printItem,", "));
        return $this->GetY();

    }
    function show_Drug_History($appointmentID,$xAxis,$yAxis, $maxX , $size, $conentType , $hedearText){


        $contentData = getContentDetail($appointmentID, $conentType);

        if(mysql_num_rows($contentData) > 0){
            $this->SetFont('Times','B',$size + 1 );
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,$hedearText);
            $yAxis += 6;

        }if(mysql_num_rows($contentData) == 0){
            return $yAxis - 5;
        }

        $this->SetFont('Times','',$size);
        $printItem = "";
        while($row=  mysql_fetch_array($contentData)){

            $data = $row['detail'];
            $printItem = "$printItem" .  "  $data,";

        }
        $yAxis =  $this->GetY();
        $this->SetXY($xAxis, $yAxis);
        $this->MultiCell($maxX,5,rtrim($printItem,", "));
        return $this->GetY();
    }
    function show_History($appointmentID,$xAxis,$yAxis, $maxX , $size, $typeCode, $headerText){


        $resultData = getPrescribedHistory($appointmentID, $typeCode);




        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('Times','B',$size + 1);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(40,5,$headerText);
            $yAxis += 6;

        }if(mysql_num_rows($resultData) == 0){
            return $yAxis - 5;
        }
        $this->SetFont('Times','',$size);
        $printItem = "";
        while($row=  mysql_fetch_array($resultData)){
            $historyResult = $row['historyResult'];
            $historylDisplayName = $row['historyName'];
            $printItem = "$printItem" .  "  $historylDisplayName: $historyResult,";

        }
        $yAxis =  $this->GetY();
        $this->SetXY($xAxis, $yAxis);
        $this->MultiCell($maxX,5,rtrim($printItem,", "));
        return $this->GetY();

    }
    function show_Family_History($appointmentID,$xAxis,$yAxis, $maxX , $size){

        $resultData = getPrescribedFamilyDisease($appointmentID);

        $this->SetFont('Times','',$size);


        if(mysql_num_rows($resultData) > 0){

            $this->SetFont('Times','B',$size + 1 );
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"Family Disease");
            $yAxis += 6;

        }if(mysql_num_rows($resultData) == 0){
            return $yAxis - 5;
        }

        $this->SetFont('Times','',$size);
        $printItem = "";
        while($row=  mysql_fetch_array($resultData)){
            $diseaseName = $row['diseaseName'];
            $relationName = $row['relationName'];
            $printItem = "$printItem" .  "  $diseaseName - $relationName,";
        }
        $yAxis =  $this->GetY();
        $this->SetXY($xAxis, $yAxis);
        $this->MultiCell($maxX,5,rtrim($printItem,", "));
        return $this->GetY();
    }
    function show_Past_History($appointmentID,$xAxis,$yAxis, $maxX , $size, $status , $hedearText){
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
        $printItem = "";
        while($row=  mysql_fetch_array($resultData)){
            $diseaseName = $row['diseaseName'];
            $printItem = "$printItem" .  "  $diseaseName,";

        }
        $yAxis =  $this->GetY();
        $this->SetXY($xAxis, $yAxis);
        $this->MultiCell($maxX,5,rtrim($printItem,", "));
        return $this->GetY();
    }
    function show_advice($appointmentID,$xAxis,$yAxis,$size,$maxX){
        $resultData = getPrescribedAdvice($appointmentID);
        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('nikosh','B',$size + 2);
            $this->SetXY($xAxis , $yAxis);
            $this->MultiCell(20,5,"উপদেশ");

        }
        $printItem = "";
        while($row=  mysql_fetch_array($resultData)){
            $advice = $row['advice'];
            $printItem = "$printItem" .  "  $advice";
        }
        $this->SetFont('nikosh','',$size);
        $yAxis =  $this->GetY();
        $this->SetXY($xAxis, $yAxis);
        $this->MultiCell($maxX,5,rtrim($printItem,", "));
        return $this->GetY();
    }
    function showClinicalRecord($appointmentID, $xAxis,$yAxis,$maxX,$size) {


        $resultData = getClinicalDate($appointmentID, 'CLINICAL_RECORD');

        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('nikosh','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"Clinical Record");
            $yAxis += 6;

        }if(mysql_num_rows($resultData) == 0){
            return $yAxis - 5;
        }
        $this->SetFont('nikosh','',$size);
        $var = 1;
        while($row=  mysql_fetch_array($resultData)){
            $code = $row['code'];
            $yAxis =  $this->GetY();
            $yAxis = $this->checkForPageChange($yAxis, $this->page);
            $printItem = "Date: $code";
            $yAxis = $yAxis + 5;
            $innerData = getClinicalDetail($appointmentID, 'CLINICAL_RECORD', $code);
            while($item =  mysql_fetch_array($innerData)){
                $data = $item['detail'];
                $printItem = "$printItem" . " $data,";
            }
            $yAxis =  $this->GetY();
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,rtrim($printItem,", "));

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

$lineStyle = array('width' => 20, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(255, 0, 0));
//$pdf->Line(0, 53, 210, 53, $linestyle);
//$pdf->Line(10, 43, 195, 43, $linestyle);

$leftYaxis = 80;
$rightYaxis = 80;
$size = 10;

$leftXaxis = 30;
$rightXaxis = 90;
$maxX = 160;
$maxXForRight = 160;

$gap = 5;
$photoSize = 5;

$pageNum = 1;
$pdf->page = $pageNum;






$yPageNo = $pdf->page;
$pageNum = 1;
$pdf->page = $pageNum;

if($appType != 4){
    $patientImage = $pdf->ShowPatInfo($patientCode, 45, $username);
    if($patientImage != null){
        $pdf->displayImage($username, $patientImage,$leftXaxis,$leftYaxis,$photoSize);
        $gap = $gap + $photoSize;
    }
}

$leftYaxis=$pdf->show_Complain($appointmentID,$leftXaxis,$leftYaxis + $gap, $maxX , $size);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->show_vital($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX , $size);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->show_History($appointmentID,$leftXaxis,$leftYaxis +5, $maxX , $size, "RISK", "Risk Factors");
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->show_Past_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size , 0 , "Past Disease");
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->show_Past_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size , 1 , "Associated Illness");
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->show_Family_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->show_Drug_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size , "OLDDRUGS" , "Old Drugs");
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->show_Drug_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size , "CURRDRUGS" , "Current Drugs");
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->showClinicalRecord($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->show_inv($appointmentID,$leftXaxis,$leftYaxis + 5 , $maxX , $size);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis = $pdf->show_diagnosis($appointmentID, $leftXaxis ,$leftYaxis + 5 ,$size , $maxX);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->showComment($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf-> show_ref_doc($appointmentID,$leftXaxis,$leftYaxis + 5,$size);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);


$leftYaxis=$pdf-> show_med($appointmentID,$leftXaxis,$leftYaxis + 5,$size + 2, $pageNum, $pdf);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);

$leftYaxis=$pdf-> show_advice($appointmentID,$leftXaxis,$leftYaxis + 5,$size + 2, $maxX);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);

$leftYaxis=$pdf-> show_nextVisit($appointmentID,$leftXaxis,$leftYaxis + 5,$size +2);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);


if($yPageNo > $pdf->page){
    $pdf->page = $yPageNo;
    $pdf->Line($rightXaxis - 10 , 60, $rightXaxis - 10, 260, $lineStyle);
}

//$pdf-> show_diagnosis($appointmentID,15,55,$size);
//$pdf-> show_ref_doc($appointmentID,15,260,$size);
//$pdf->showDocInfo($username, 15, $size + 2);

$pdf->Output('');
exit;
?>
