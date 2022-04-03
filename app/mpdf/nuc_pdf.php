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
        //$this->Image('doc.png',15,5,170,40,'GIF','',true, false);
        $this->Image('images.png',15,75,50,8,'png','',true, false);
    }

    function Footer() {
        //$this -> image ('doc1.png',30,260,150,30,'png','',true, false);



    }

    function showDocInfo($username, $yAxis, $size){
        $resultData = getDoctorInfo($username);
        if($resultData['prescriptionStyle'] == 2){
            $size =$size;
            $yAxis = $yAxis + 10;
            $this->SetXY(85, 250);
            $this->SetFont('nikosh','',$size + 1);
            $this->MultiCell(50,4, "à¦œà¦°à§�à¦°à§€ à¦¯à§‹à¦—à¦¾à¦¯à§‹à¦— - à§¦à§§à§¬à§­à§« à§¬à§§à§ª à§«à§¯à§¨ à¦¸à¦•à¦¾à¦² à§§à§¦à¦Ÿà¦¾ à¦¥à§‡à¦•à§‡ à¦¦à§�à¦ªà§�à¦° à§§ à¦Ÿà¦¾ à¦�à¦¬à¦‚ à¦¬à¦¿à¦•à¦¾à¦² à§«à¦Ÿà¦¾ à¦¥à§‡à¦•à§‡ à¦°à¦¾à¦¤ à§®à¦Ÿà¦¾ ", 0);

        }

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

        $this->SetFont('nikosh','',9);

        $name = $rec['name'];

        $age = $rec['age'];

        $sex = $rec['sex'];

        $address = $rec['address'];

        $phone = $rec['phone'];

        $date = date('d M Y');


        $this->SetXY(110,$yAxis + 20);
        $this->MultiCell(65,5, "ID No: $patientCode");


        $this->SetXY(15,$yAxis + 20);
        $this->MultiCell(65,5, "$name");

        $this->SetXY(15, $yAxis - 8);
        //$this->MultiCell(65, 5, "$phone");

        $this->SetXY(95, $yAxis + 20);
        $this->MultiCell(50, 5, "$age Yrs");


        $this->SetXY(130, $yAxis);
        //$this->MultiCell(30, 5, "$sex");

        $this->SetXY(165, $yAxis + 20);
        $this->MultiCell(80,5, "$date");


        $this->SetXY(100, $yAxis + 5);
        //$this->MultiCell(50, 5, "$address");




        return $rec['patientImage'];

    }



    function show_med($appointmentID, $xAxis, $yAxis, $size, $pageNum,$pdf){

        $resultData = getPresCribedDrugs($appointmentID);

        if(mysql_num_rows($resultData) > 20){
            $size = $size ;
        }

        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('Times','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(70,5,"Medications Prescribed");
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


            $yAxis =  $this->GetY();
            if(($yAxis + 10)  > 270 ){
                $this->AddPage();
                $yAxis = 60;
            }


            $printItem = "";
            $this->SetXY($xAxis, $yAxis);
            if($drugStr  == ""){
                $printItem = "$printItem" . "$var. $drugType.  $drugName";
            }else{
                $printItem = "$printItem" . "$var. $drugType.  $drugName - $drugStr";
            }

            $var = $var + 1;



            if($drugDoseInitial != ""){
                if($drugTypeID == 3 || $drugTypeID == 15 || $drugTypeID == 41){
                    $drugDoseInitial = str_replace("s","à¦šà¦¾ à¦šà¦¾à¦®à¦š", $drugDoseInitial);
                }else if($drugTypeID == 4){
                    $drugDoseInitial = str_replace("ampl","à¦�à¦®à§�à¦ªà¦²", $drugDoseInitial);
                    $drugDoseInitial = str_replace("vial","à¦­à¦¾à§Ÿà¦¾à¦²", $drugDoseInitial);
                }else if($drugTypeID == 10 || $drugTypeID == 14 || $drugTypeID == 21){
                    $drugDoseInitial = str_replace("puff","à¦šà¦¾à¦ª", $drugDoseInitial);
                }else if($drugTypeID == 7){
                    $drugDoseInitial = str_replace("d","à¦¡à§�à¦°à¦ª", $drugDoseInitial);
                }else if($drugTypeID == 6){
                    $drugDoseInitial = str_replace("u","(+/-) 2 à¦‡à¦‰à¦¨à¦¿à¦Ÿ", $drugDoseInitial);
                }else if($drugTypeID == 11){
                    $drugDoseInitial = str_replace("drp/min","à¦¦à§�à¦°à¦ª/à¦®à¦¿à¦¨à¦¿à¦Ÿ", $drugDoseInitial);
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
                        $text = "($drugDose) $drugDoseInitial ";
                    }

                    if($index == 0){
                        $periodText = 'à¦ªà§�à¦°à¦¥à¦®'. " $drugNoDay $drugNoDayType $text";
                    }else{
                        $periodText = "$periodText". " à¦¤à¦¾à¦°à¦ªà¦° " . " $text $drugNoDay $drugNoDayType";
                    }
                    $index++;

                }

                $restOftheString = "$periodText $drugWhen $drugAdvice";
                $drugDose = "";
            }

            $full_str = "";
            if($drugDoseInitial == "" || $drugDose == ''){
                $full_str = "$drugDose $restOftheString |";
            }else{
                $full_str ="($drugDose) $drugDoseInitial $restOftheString |";
            }

            $full_str = $this->convertNumberToBangla($full_str);

            $this->MultiCell(175,5,"$printItem   - - -   $full_str");
            //$yAxis += 8;
        }

        return $this->GetY()- 5;

    }



    function showComment($appointmentID,$xAxis,$yAxis, $maxX, $size){

        $contentData = getContentDetail($appointmentID, "COMMENT");

        $con = mysql_fetch_assoc($contentData);
        if($con){
            $this->SetFont('Times','B',$size);
            $this->SetXY($xAxis,$yAxis);
            $this->MultiCell($maxX,5,"Clinical Note");
            $yAxis += 5;
            $data = $con['detail'];
            $this->SetXY($xAxis,$yAxis);
            $this->SetFont('Times','',$size );
            $this->MultiCell($maxX,5, "$data", 0);
        }

        return $this->GetY()- 5;
    }

    function show_Complain($appointmentID,$xAxis,$yAxis, $maxX , $size) {

        $resultData = getPrescribedComplain($appointmentID);




        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('Times','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,10,"Chief Complaints");
            $yAxis += 6;

        }if(mysql_num_rows($resultData) == 0){
            return $yAxis - 5;
        }
        $this->SetFont('Times','',$size);
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
            $this->MultiCell($maxX,5,"The patient presented today with". rtrim($printItem,", "));
        }


        return $this->GetY()- 5;

    }
    function show_vital($appointmentID,$xAxis, $yAxis, $maxX, $size){




        $resultData = getPrescribedVital($appointmentID);

        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('Times','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(70,5,"Physical examination reveals: ");
            $yAxis += 6;

        }if(mysql_num_rows($resultData) == 0){
            return $yAxis - 5;
        }
        $this->SetFont('Times','',$size);
        $printItem = "";
        while($row=  mysql_fetch_array($resultData)){

            $vitalResult = $row['vitalResult'];
            $vitalDisplayName = $row['vitalDisplayName'];
            $printItem = "$printItem" .  " $vitalDisplayName: $vitalResult,";
        }

        $yAxis =  $this->GetY();
        $this->SetXY($xAxis , $yAxis);
        $this->MultiCell($maxX,5,rtrim($printItem,", "));
        return $this->GetY() - 5;
    }
    function show_inv($appointmentID, $xAxis,$yAxis,$maxX,$size) {

        $this->SetFont('Times','',$size);

        $resultData = getPrescribedInv($appointmentID);

        if(mysql_num_rows($resultData) > 0){

            $this->SetFont('Times','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(70,5,"Investigations Advised");
            $yAxis += 6;

        }if(mysql_num_rows($resultData) == 0){
            return $yAxis - 5;
        }

        $printItem = "";
        $this->SetFont('Times','',$size);
        while($row=  mysql_fetch_array($resultData)){
            $invName = $row['invName'];
            $printItem = "$printItem" . " .$invName";
        }
        $yAxis =  $this->GetY();
        $this->SetXY($xAxis, $yAxis);
        $this->MultiCell($maxX,5,rtrim($printItem,", "));
        return $this->GetY()- 5;

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
        return $this->GetY()- 5;
    }
    function show_History($appointmentID,$xAxis,$yAxis, $maxX , $size, $typeCode, $headerText){


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
            $yAxis = $this->checkForPageChange($yAxis, $this->page);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"$historylDisplayName:  $historyResult");

        }

        return $this->GetY()- 5;

    }
    function show_Family_History($appointmentID,$xAxis,$yAxis, $maxX , $size){

        $resultData = getPrescribedFamilyDisease($appointmentID);

        $this->SetFont('Times','',$size);


        if(mysql_num_rows($resultData) > 0){

            $this->SetFont('Times','B',$size );
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
        return $this->GetY()- 5;
    }
    function show_Past_History($appointmentID,$xAxis,$yAxis, $maxX , $size, $status , $hedearText){
        $resultData = getPrescribedPastDisease2($appointmentID, $status);

        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('Times','B',$size );
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
        return $this->GetY()- 5;
    }

    function show_diagnosis($appointmentID,$xAxis,$yAxis, $size ){

        $resultData = getPrescribedDiagnosis($appointmentID);



        $con = mysql_fetch_assoc($resultData);
        if($con){
            $this->SetFont('Times','B',$size );
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(60,5,"Diagnosis");
            $yAxis += 5;
            $this->SetFont('Times','',$size );
            $this->SetXY($xAxis, $yAxis);
            $diseaseName = $con['diseaseName'];
            $this->MultiCell($maxX, 5,"$diseaseName");
        }

        return $this->GetY()- 5;

    }

    function show_ref_doc($appointmentID,$xAxis,$yAxis,$size){

        $this->SetFont('nikosh','',$size);

        $resultData = getPrescribedReffredDoctor($appointmentID);

        $rec = mysql_fetch_assoc($resultData);

        if($rec['doctorName'] != ""){
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(160,5, "REFD. TO: " . $rec['doctorName']);
            $yAxis =  $this->GetY();
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(160,5, $rec['doctorAdress']);
        }

        return $this->GetY();

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

    function show_advice($appointmentID,$xAxis,$yAxis,$size,$maxX){
        $resultData = getPrescribedAdvice($appointmentID);
        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('nikosh','B',$size + 2);
            $this->SetXY($xAxis , $yAxis);
            $this->MultiCell(20,5,"à¦‰à¦ªà¦¦à§‡à¦¶");

        }
        $printItem = "";
        while($row=  mysql_fetch_array($resultData)){
            $advice = $row['advice'];
            $printItem = "$printItem" .  " $advice";
        }
        $this->SetFont('nikosh','',$size + 2);
        $yAxis =  $this->GetY();
        $this->SetXY($xAxis, $yAxis);
        $this->MultiCell($maxX,5,rtrim($printItem,", "));
        return $this->GetY();
    }
    function showClinicalRecord($appointmentID, $xAxis,$yAxis,$maxX,$size) {


        $resultData = getClinicalDate($appointmentID, 'CLINICAL_RECORD');

        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('Times','B',$size);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"Clinical Record");
            $yAxis += 6;

        }if(mysql_num_rows($resultData) == 0){
            return $yAxis - 5;
        }
        $this->SetFont('Times','',$size);
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

        return $this->GetY()- 5;

    }

}

$pdf = new PDF('','A4',10,'nikosh');
$pdf->WriteHTML('');

$pdf->SetAutoPageBreak(true, 30);

$res = getAppointmentInfo($appointmentID);
$appData = mysql_fetch_assoc($res);
$appType = $appData['appointmentType'];

$lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(255, 0, 0));
$pdf->Line(15, 70, 190, 70, $linestyle);
//$pdf->Line(10, 235, 95, 235, $linestyle);
//$pdf->Line(30, 263, 183, 263, $linestyle);
//$pdf->Line(30 , 55, 30, 263, $lineStyle);
//$pdf->Line(183 , 55, 183, 263, $lineStyle);
$leftYaxis = 80;
$rightYaxis = 80;
$size = 8;

$leftXaxis = 15;
$rightXaxis = 90;
$maxX = 175;
$maxXForRight = 175;

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

$leftYaxis=$pdf->show_Complain($appointmentID,$leftXaxis,$leftYaxis, $maxX , $size);
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
$leftYaxis=$pdf->show_History($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX , $size, "HOSPITAL", "");
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->showClinicalRecord($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);

$leftYaxis = $pdf->show_diagnosis($appointmentID, $leftXaxis ,$leftYaxis + 5 ,$size , $maxX);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
$leftYaxis=$pdf->showComment($appointmentID,$leftXaxis,$leftYaxis + 5, $maxX, $size);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);

$leftYaxis=$pdf-> show_med($appointmentID,$leftXaxis,$leftYaxis + 5,$size, $pageNum, $pdf);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);

$leftYaxis=$pdf->show_inv($appointmentID,$leftXaxis,$leftYaxis + 5 , $maxX , $size);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);

$leftYaxis=$pdf-> show_advice($appointmentID,$leftXaxis,$leftYaxis + 5,$size + 2, $maxX);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);

$leftYaxis=$pdf-> show_nextVisit($appointmentID,$leftXaxis,$leftYaxis + 5,$size);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);

$leftYaxis=$pdf-> show_ref_doc($appointmentID,$leftXaxis,$leftYaxis + 2,$size);
$leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);


if($yPageNo > $pdf->page){
    $pdf->page = $yPageNo;
    //$pdf->Line($rightXaxis - 10 , 60, $rightXaxis - 10, 260, $lineStyle);
}

//$pdf-> show_diagnosis($appointmentID,15,55,$size);
//$pdf-> show_ref_doc($appointmentID,15,260,$size);
$pdf->showDocInfo($username, 5, $size + 2);

$pdf->Output('');
exit;
?>
