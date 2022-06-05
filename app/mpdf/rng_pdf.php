<?php
$date = date('d M, Y');
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
include_once('mpdf.php');

class RNG extends mPDF
{

    function ShowPatInfo($conn, $patientID, $yAxis, $appointmentID, $appData)
    {
        $rec = getPatientInfo($conn, $patientID);
        $this->SetFont('nikosh','B',10);
        $patientCode = $rec['patientcode'];
        $name = $rec['firstname'];
        $age = $rec['age'];
        $date=date_create($appData['appdate']);

        $formattedDate = date_format($date,"d M, Y");
        $this->SetXY(120, $yAxis);
        $this->MultiCell(60, 5, "ID: $patientCode");

        $this->SetXY(15, $yAxis);
        $this->MultiCell(80, 5, "$name");

        $this->SetXY(85, $yAxis);
        $this->MultiCell(35, 5, "$age Yrs");

        $this->SetXY(180, $yAxis);
        $this->MultiCell(50, 5, "$formattedDate");

        return $rec['patientImage'];

    }

    function show_med($conn, $appointmentID, $xAxis, $yAxis, $size, $pageNum,$pdf){
        $resultData = getPresCribedDrugs($conn, $appointmentID);

        if($resultData->rowCount() > 0){
            $this->SetFont('nikosh','B',$size);
            $this->SetXY($xAxis , $yAxis);
            $this->MultiCell(40,5,"Rx");
            $yAxis += 6;

        }else{
            return $yAxis - 5;
        }

        $this->SetFont('nikosh','',($size + 1));

        $nameCell = 110;
        $doseCell = 40;
        $durationCell = 70;
        $whenCell = 15;
        $var = 1;

        while($row = $resultData->fetch(PDO::FETCH_ASSOC)){
            $drugType = $row['drugtypeinitial'];
            $drugTypeID = $row['drugtypeid'];
            $drugName = $row['drugname'];
            $drugStr = $row['drugstrength'];
            $drugPrescribeID = $row['presdrugid'];
            $doseTypeID = $row['dosetypecode'];
            $doseTypeName = $row['dosetypename'];
            $drugDoseInitial = $row['drugdoseunit'];
            $drugWhen = $row['drugwhenname'];
            $drugWhenID = $row['drugwhenid'];
            $drugAdvice = $row['drugadvicename'];
            $drugAdviceID = $row['drugadvicename'];
            $doseString = $row['dosestring'];

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
                    $drugDoseInitial = str_replace("u","ইউনিট", $drugDoseInitial);
                }else if($drugTypeID == 11){
                    $drugDoseInitial = str_replace("drp/min","ড্রপ/মিনিট", $drugDoseInitial);
                }
            }


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
            $this->SetXY($xAxis + 5, $this->GetY());
            $doseData = json_decode($doseString, TRUE);


            if($doseTypeID != -1){
                $dose = $doseData[0];
                $drugDose = $dose['dose'];
                $drugNoDay = $dose['numOfDay'];
                $drugNoDayType = $dose['bngDurationName'];
                $drugDurType = $dose['durationType'];
                $drugDose = str_replace("-","+", $drugDose);
                if($doseTypeID == 1){
                    if($drugAdviceID == '9edb35c5-c12f-11eb-a1cb-0668c7450820' ||
                        $drugWhenID == 'fb64eeb1-c130-11eb-a1cb-0668c7450820' ||
                        $drugAdviceID == 54 ||
                        $drugAdviceID == 62 ||
                        $drugWhenID == 23){
                        $drugDose =  "$drugDose + 0 + 0";
                    }else if($drugAdviceID == '9edb3706-c12f-11eb-a1cb-0668c7450820' || $drugWhenID == 'fb64ef4f-c130-11eb-a1cb-0668c7450820'){
                        $drugDose =  "0 + $drugDose + 0";
                    }else{
                        $drugDose =  "0 + 0 + $drugDose";
                    }

                }else if($doseTypeID == 2){
                    list($num,$type) = explode('+', $drugDose, 2);
                    $drugDose =  "$num + 0 + $type";
                }else if($doseTypeID == -4){
                    $drugDose =  "সপ্তাহে ১ বার";
                }else if($doseTypeID == -5){
                    $drugDose =  "মাসে ১ বার";
                }else if($doseTypeID == -6){
                    $drugDose =  "বছরে ১ বার";
                }

                if($drugDurType >  4){
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

                foreach ($doseData as $dose){
                    $drugDose = $dose['dose'];
                    $drugNoDay = $dose['numOfDay'];
                    $drugDurType = $dose['durationType'];
                    $drugNoDayType = $dose['bngDurationName'];

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
                    if($doseTypeID == -4 || $doseTypeID == -5 || $doseTypeID == -6){
                        $text = "$drugDose";
                        $drugDoseInitial = "";
                    }else if($drugDoseInitial == ""){
                        $text = "($drugDose)";
                    }else{
                        $text = "($drugDose) $drugDoseInitial ";
                    }

                    if($drugDurType >  4){
                        $drugNoDay = "";
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
                $full_str = "$drugDose $restOfTheString ";
            }else if($doseTypeID == -4 || $doseTypeID == -5 || $doseTypeID == -6){
                $full_str ="$drugDose $restOfTheString";
            }else{
                $full_str ="($drugDose)$drugDoseInitial $restOfTheString";
            }
            $full_str = $this->convertNumberToBangla($full_str);
            $this->MultiCell($nameCell,5,"$full_str");
        }
        return $this->GetY();
    }

    function show_advice($conn, $appointmentID,$xAxis,$yAxis,$size,$maxX){
        $resultData = getPrescribedAdvice($conn, $appointmentID);
        if($resultData->rowCount() > 0){
            $this->SetFont('nikosh','B',$size );
            $this->SetXY($xAxis , $yAxis);
            $this->MultiCell(20,5,"উপদেশ");
        }
        $this->SetFont('nikosh','',$size );
        while($row = $resultData->fetch(PDO::FETCH_ASSOC)){
            $advice = $row['advice'];
            $yAxis =  $this->GetY();
            $yAxis = $this->checkForPageChange($yAxis, $this->page);
            $this->SetXY($xAxis, $yAxis );
            $this->MultiCell($maxX,5,"* $advice");
        }
        return $this->GetY();
    }

    function show_diet($conn, $appointmentID,$xAxis,$yAxis,$size){
        $result = getContentDetail($conn, $appointmentID, "DIET");
        $rec = $result->fetch(PDO::FETCH_ASSOC);
        if($rec['shortname'] != ""){
            $this->SetFont('nikosh','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(110,5, "Diet");
            $this->SetFont('nikosh','',$size);
            $this->SetXY($xAxis, $yAxis +5 );
            $this->MultiCell(110,5, $rec['shortname']);
            return $this->GetY();
        }
        return $yAxis;
    }

    function show_nextVisit($conn, $appointmentID,$xAxis,$yAxis,$size){

        $rec = getPrescribedNextVisit($conn, $appointmentID);
        if($rec){
            $nextVisitType = $rec['nextvisittype'];
            $this->SetXY($xAxis, $yAxis);
            if($nextVisitType == 2){
                $data = " পর রিপোর্ট সহ দেখা করবেন।";
                $numOfday = $rec['numofday'];
                $numOfday = $this->convertNumberToBangla($numOfday);
                $dayType = $rec['durationtypename'];
                $this->MultiCell(60,5, "$numOfday - $dayType $data", 1);
            }else if($nextVisitType == 1){
                $data = " তারিখে দেখা করবেন।";
                $date=date_create($rec['visitdate']);
                $formattedDate = date_format($date,"d-m-Y");
                $newDate = $this->convertNumberToBangla($formattedDate);
                $this->MultiCell(60,5, "$newDate $data", 1);
            }
        }
        return $this->GetY();
    }

    function show_diagnosis($conn, $appointmentID,$xAxis,$yAxis, $size, $maxX){

        $rec = getPrescribedDiagnosis($conn, $appointmentID);
        if($rec){
            $this->SetFont('nikosh','B',$size );
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(60,5,"Diagnosis");
            $yAxis += 5;
            $this->SetFont('nikosh','',$size );
            $this->SetXY($xAxis, $yAxis);
            $diseaseName = $rec['diseasename'];
            $note = $rec['note'];
            $this->MultiCell($maxX, 5,"$diseaseName $note");
        }
        return $this->GetY();
    }

    function show_Complain($conn, $appointmentID,$xAxis,$yAxis, $maxX , $size) {

        $resultData = getPrescribedComplain($conn, $appointmentID);

        if($resultData->rowCount() > 0){
            $this->SetFont('nikosh','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"Problems:");
            $yAxis += 6;

        }else{
            return $yAxis - 5;
        }
        $this->SetFont('nikosh','',$size);
        $var = 1;
        while($row = $resultData->fetch(PDO::FETCH_ASSOC)){

            $symptomName = $row['symptomname'];
            $durationNum = $row['durationnum'];
            $durationType = $row['durationtype'];
            $durationTypeName = $row['durationtypename'];

            $yAxis =  $this->GetY();
            $yAxis = $this->checkForPageChange($yAxis, $this->page);
            $this->SetXY($xAxis, $yAxis);
            if($durationType == null || $durationType == 6){
                $this->MultiCell($maxX,5," *$symptomName");
            }else if ($durationType >= 5){
                $this->MultiCell($maxX,5," *$symptomName - $durationTypeName");
            }else if($durationType < 5){
                $this->MultiCell($maxX,5," *$symptomName - $durationNum - $durationTypeName");
            }
            $var++;
        }

        return $this->GetY();

    }

    function show_vital($conn, $appointmentID,$xAxis, $yAxis, $maxX, $size){

        $resultData = getPrescribedVital($conn, $appointmentID);

        if($resultData->rowCount() > 0){
            $this->SetFont('nikosh','B',$size );
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(40,5,"On Examination:");
            $yAxis += 6;

        }else{
            return $yAxis - 5;
        }

        $this->SetFont('nikosh','',$size);
        while($row = $resultData->fetch(PDO::FETCH_ASSOC)){

            $vitalResult = $row['vitalresult'];
            $vitalDisplayName = $row['vitalname'];

            $yAxis =  $this->GetY();
            $yAxis = $this->checkForPageChange($yAxis, $this->page);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"*$vitalDisplayName:  $vitalResult");
        }
        return $this->GetY();
    }

    function show_History($conn, $appointmentID,$doctorID, $xAxis,$yAxis, $maxX, $size){

        $customHistoryList = getDotorHistory($conn, $doctorID);

        if(!$customHistoryList){
            return $yAxis;
        }
        if($customHistoryList->rowCount() > 0){
            while($cus = $customHistoryList->fetch(PDO::FETCH_ASSOC)){
                $typeCode = $cus['defaultname'];
                $headerText = $cus['menuheader'];
                $resultData = getCustomHistory($conn, $appointmentID, $typeCode);
                if($resultData->rowCount() > 0){
                    $this->SetFont('nikosh','B',$size);
                    $this->SetXY($xAxis, $yAxis);
                    $this->MultiCell(40,5,$headerText);
                    $yAxis = $yAxis + 6;
                    $this->SetFont('nikosh','',$size);
                    while($row = $resultData->fetch(PDO::FETCH_ASSOC)){
                        $historyResult = $row['historyresult'];
                        $historylDisplayName = $row['historyname'];
                        $yAxis = $this->checkForPageChange($yAxis, $this->page);
                        $this->SetXY($xAxis, $yAxis);
                        $this->MultiCell($maxX,5,"$historylDisplayName:  $historyResult");
                        $yAxis = $yAxis + 5;
                    }
                }

            }
        }
        return $this->GetY();
    }

    function show_Past_History($conn, $appointmentID,$xAxis,$yAxis, $maxX , $size, $status , $hedearText){

        $resultData = getPresPastDisease($conn, $appointmentID, $status);
        if(!$resultData){
            return $yAxis;
        }else if($resultData && $resultData->rowCount() > 0){
            $this->SetFont('nikosh','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5, $hedearText);
            $yAxis += 6;
            $this->SetFont('nikosh','',$size);
            while($row = $resultData->fetch(PDO::FETCH_ASSOC)){
                $diseaseName = $row['diseasename'];
                $yAxis =  $this->GetY();
                $yAxis = $this->checkForPageChange($yAxis, $this->page);
                $this->SetXY($xAxis, $yAxis);
                $this->MultiCell($maxX,5,"$diseaseName");
            }
        }
        return $this->GetY();
    }

    function show_Family_History($conn, $appointmentID,$xAxis,$yAxis, $maxX , $size){
        $resultData = getPresFamilyDisease($conn, $appointmentID);
        if(!$resultData){
            return $yAxis;
        }else if($resultData->rowCount() > 0){
            $this->SetFont('nikosh','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"Family Disease");
            $yAxis += 6;
            $this->SetFont('nikosh','',$size);
            while($row = $resultData->fetch(PDO::FETCH_ASSOC)){
                $diseaseName = $row['diseasename'];
                $relationName = $row['relationname'];
                $yAxis =  $this->GetY();
                $yAxis = $this->checkForPageChange($yAxis, $this->page);
                $this->SetXY($xAxis, $yAxis);
                $this->MultiCell($maxX,5,".$diseaseName - $relationName");
            }
        }
        return $this->GetY();
    }

    function show_inv($conn, $appointmentID, $xAxis,$yAxis,$maxX,$size) {
        $resultData = getPrescribedInv($conn, $appointmentID);

        if($resultData->rowCount() > 0){
            $this->SetFont('nikosh','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(40,5,"Test Advised");
            $yAxis += 6;

        }else{
            return $yAxis - 5;
        }

        $var = 1;
        $this->SetFont('nikosh','',$size);
        while($row = $resultData->fetch(PDO::FETCH_ASSOC)){

            $invName = $row['invname'];

            $yAxis =  $this->GetY();
            $yAxis = $this->checkForPageChange($yAxis, $this->page);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5," *$invName");
            $var++;
        }
        return $this->GetY();
    }

    function preparePrescription($conn, $appointmentID): PDF
    {
        $pdf = new RNG('', 'A4', 10, 'nikosh');
        $pdf->WriteHTML('');

        $pdf->SetAutoPageBreak(true, 35);

        $appData = getAppointmentInfo($conn, $appointmentID);
        $appType = $appData['appointmenttype'];
        $patientID = $appData['patientid'];
        $doctorID = $appData['doctorid'];

        $linestyle = null;
        $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => '',
            'phase' => 0, 'color' => array(255, 0, 0));

        $leftYaxis = 60;
        $rightYaxis = 60;
        $size = 10;

        $leftXaxis = 15;
        $rightXaxis = 90;
        $maxX = 75;
        $maxXForRight = 100;

        $gap = 0;
        $photoSize = 0;

        $pageNum = 1;
        $pdf->page = $pageNum;

        $rightYaxis = $pdf->show_diet($conn, $appointmentID, $rightXaxis, $rightYaxis, $size);
        $rightYaxis = $pdf->show_med($conn, $appointmentID, $rightXaxis, $rightYaxis, $size + 1, $pageNum, $pdf);
        $rightYaxis = $pdf->checkForPageChange($rightYaxis, $pdf->page);
        $rightYaxis = $pdf->Show_advice($conn, $appointmentID, $rightXaxis, $rightYaxis + 5, ($size+2), $maxXForRight);
        $rightYaxis = $pdf->checkForPageChange($rightYaxis, $pdf->page);
        $rightYaxis = $pdf->show_nextVisit($conn, $appointmentID, $rightXaxis, $rightYaxis + 5, $size);
        $rightYaxis = $pdf->checkForPageChange($rightYaxis, $pdf->page);
        $rightYaxis = $pdf->show_ref_doc($conn, $appointmentID, $rightXaxis, $rightYaxis + 5, $size, $maxXForRight);

        $yPageNo = $pdf->page;
        $pageNum = 1;
        $pdf->page = $pageNum;

        if ($appType != 4) {
            $patientImage = $pdf->ShowPatInfo($conn, $patientID, 45, $appointmentID, $appData);
            if ($patientImage != null) {
                $pdf->displayImage($conn, $doctorID, $patientImage, $leftXaxis, $leftYaxis, $photoSize);
                $gap = $gap + $photoSize;
            }
        }
        $leftYaxis = $pdf->Show_diagnosis($conn, $appointmentID, $leftXaxis, $leftYaxis + $gap, $size, $maxX);
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->Show_Complain($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size);
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->Show_vital($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size);
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->Show_History($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size, "RISK", "Risk Factors");
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->Show_Past_History($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size, 0, "Past Illness");
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->Show_Past_History($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size, 1, "Co Morbidities");
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->Show_Family_History($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size);
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->Show_Drug_History($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size, "OLDDRUGS", "Old Drugs");
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->Show_Drug_History($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size, "CURRDRUGS", "Current Drugs");
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->showClinicalRecord($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size);
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->Show_inv($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size);
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->showComment($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size);
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);

        if ($yPageNo > $pdf->page) {
            $pdf->page = $yPageNo;
        }
        $pdf->Output('');
        exit;
    }
}


?>
