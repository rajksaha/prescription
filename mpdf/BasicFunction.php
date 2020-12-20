<?php

/**
 * Created by PhpStorm.
 * User: raj
 * Date: 4/24/2017
 * Time: 12:12 PM
 */
include("mpdf.php");
class BasicFunction extends mPDF
{


    function displayImage ($doctorId, $patientImage,$xAxis, $yAxis, $size){
        $doctorData = getDoctorInfo($doctorId);

        if($doctorData['photoSupport'] == 1){
            $this->Image('../'.$patientImage, $xAxis, $yAxis, $size);
        }

    }
    function show_med($appointmentID, $xAxis, $yAxis, $size){

        $resultData = getPresCribedDrugs($appointmentID);

        if(mysql_num_rows($resultData) > 10){
            $size = $size - 2;
        }

        if(mysql_num_rows($resultData) > 0){
            //$this->SetFont('Times','B',$size + 3);
            $this->SetXY($xAxis , $yAxis);
            $this->MultiCell(40,5,"Rxx");
            $yAxis += 6;

        }else{
            return $yAxis - 5;
        }

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

            $this->SetXY($xAxis, $yAxis);
            if($drugStr  == ""){
                $this->MultiCell($nameCell,5,"$var. $drugType. $drugName");
            }else{
                $this->MultiCell($nameCell,5,"$var. $drugType. $drugName- $drugStr");
            }
            $var = $var + 1;
            $xInnerAxis = $nameCell + 5;

            //$this->SetFont('prolog','',$size + 1);



            $this->SetXY($xAxis + 5, $yAxis + 6);
            $realY =  $this->GetY();
            /*if($drugDoseInitial != ""){
                if($drugTypeID == 3 || $drugTypeID == 15 || $drugTypeID == 41){
                    $drugDoseInitial = str_replace("s"," Pv PvgP ", $drugDoseInitial);
                }else if($drugTypeID == 4){
                    $drugDoseInitial = str_replace("ampl","GÂ¤cj", $drugDoseInitial);
                    $drugDoseInitial = str_replace("vial","fvqvj", $drugDoseInitial);
                }else if($drugTypeID == 10 || $drugTypeID == 14){
                    $drugDoseInitial = str_replace("puff","cvd", $drugDoseInitial);
                }else if($drugTypeID == 7){
                    $drugDoseInitial = str_replace("d","WÂªc", $drugDoseInitial);
                }else if($drugTypeID == 6){
                    $drugDoseInitial = str_replace("u","BDwbU", $drugDoseInitial);
                }else if($drugTypeID == 11){
                    $drugDoseInitial = str_replace("drp/min","WÂªc/wgwbU", $drugDoseInitial);
                }
            }*/

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
                if($drugDoseInitial == ""){

                    $this->MultiCell(110,5,"$drugDose $restOftheString|");
                }else if($drugDose == ''){

                    $this->MultiCell(110,5,"$drugDose $restOftheString|");
                }else{
                    $this->MultiCell(110,5,"($drugDose)$drugDoseInitial $restOftheString|");
                }
            }else{
                $realY =  $yAxis;
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
                        $periodText = 'à¦ªà§�à¦°à¦¥à¦®'. " $drugNoDay $drugNoDayType $text";
                    }else{
                        $periodText = "$periodText". " à¦¤à¦¾à¦°à¦ªà¦° " . " $text $drugNoDay $drugNoDayType";
                    }
                    $index++;

                }

                $restOftheString = "$periodText $drugWhen $drugAdvice";
                $xInnerAxis = $xInnerAxis + $doseCell ;
                $this->SetXY($xInnerAxis, $realY);
                $this->MultiCell($durationCell,5,"$restOftheString");

                $this->SetY($yAxis + 5);
            }
            //$yAxis += 8;
        }

        return $this->GetY();

    }
    function showClinicalRecord($appointmentID, $xAxis,$yAxis,$maxX,$size) {

        $this->SetFont('Times','',$size);

        $resultData = getContentDetail($appointmentID, 'CLINICAL_RECORD');

        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('Times','',$size +1);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(20,5,"Clinical Record");
            $yAxis += 6;

        }if(mysql_num_rows($resultData) == 0){
            return $yAxis - 5;
        }

        $var = 1;
        $this->SetFont('Times','',$size);
        while($row=  mysql_fetch_array($resultData)){

            $data = $row['data'];
            $yAxis =  $this->GetY();
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,". $data");
            $var++;
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

        $patientCode = substr($patientCode, -5);

        $name = $rec['name'];

        $age = $rec['age'];

        $sex = $rec['sex'];

        $address = $rec['address'];

        $phone = $rec['phone'];

        $date = date('d M,y');


        $this->SetXY(15,$yAxis - 4);
        $this->Write(5, "Patient ID No: $patientCode");


        $this->SetXY(15,$yAxis );
        $this->Write(5, "$name");

        $this->SetXY(70, $yAxis);
        $this->Write(5, "$age Yrs");

        $this->SetXY(160, $yAxis);
        $this->Write(5, "$date");

        $this->SetXY(130, $yAxis);
        $this->Write(5, "$sex");

        $this->SetXY(100, $yAxis);
        $this->Write(5, "$address");

        $this->SetXY(15, $yAxis - 8);
        $this->Write(5, "$phone");


        return $rec['patientImage'];

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

        while($row=  mysql_fetch_array($contentData)){

            $data = $row['detail'];

            $yAxis =  $this->GetY();
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"$data");

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
            $this->SetFont('Times','',$size );
            $this->MultiCell($maxX,5, "$data", 0);
        }

        return $this->GetY();
    }
    function show_diagnosis($appointmentID,$xAxis,$yAxis, $size ){

        $resultData = getPrescribedDiagnosis($appointmentID);



        $con = mysql_fetch_assoc($resultData);
        if($con){
            $this->SetFont('Times','B',$size + 1 );
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(60,5,"Diagnosis");
            $yAxis += 5;
            $this->SetFont('Times','',$size );
            $this->SetXY($xAxis, $yAxis);
            $diseaseName = $con['diseaseName'];
            $this->MultiCell(60, 5,"$diseaseName");
        }

        return $this->GetY();

    }
    function show_Complain($appointmentID,$xAxis,$yAxis, $maxX , $size) {

        $resultData = getPrescribedComplain($appointmentID);




        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('Times','B',$size + 1 );
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
        while($row=  mysql_fetch_array($resultData)){

            $diseaseName = $row['diseaseName'];
            $relationName = $row['relationName'];

            $yAxis =  $this->GetY();
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"$diseaseName - $relationName");

        }

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

        while($row=  mysql_fetch_array($resultData)){

            $diseaseName = $row['diseaseName'];

            $yAxis =  $this->GetY();
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"$diseaseName");

        }

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
        while($row=  mysql_fetch_array($resultData)){

            $historyResult = $row['historyResult'];
            $historylDisplayName = $row['historyName'];

            $yAxis =  $this->GetY();
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"$historylDisplayName:  $historyResult");

        }

        return $this->GetY();

    }
    function show_ref_doc($appointmentID,$xAxis,$yAxis,$size){

        $this->SetFont('Times','',$size);

        $resultData = getPrescribedReffredDoctor($appointmentID);

        $rec = mysql_fetch_assoc($resultData);

        if($rec['doctorName'] != ""){
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(90,5, "Refd. to: " . $rec['doctorName']);
            $yAxis =  $this->GetY();
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(90,5, $rec['doctorAdress']);
        }

        return $this->GetY();

    }
    //TODO : remove static bangla
    function show_nextVisit($appointmentID,$xAxis,$yAxis,$size){


        $resultData = getPrescribedNextVisit($appointmentID);

        $rec = mysql_fetch_assoc($resultData);

        $nextVisitType = $rec['nextVisitType'];

        $this->SetXY($xAxis, $yAxis);

        $this->SetFont('prolog','',$size + 2);



        if($nextVisitType == 2){

            $contentData = getContentDetail($nextVisitType, "NEXTVISIT");
            $con = mysql_fetch_assoc($contentData);
            $data = $con['code'];

            $numOfday = $rec['numOfDay'];
            $dayType = $rec['pdf'];
            $this->SetFont('prolog','',$size + 2);
            $this->MultiCell(60,5, "$numOfday - $dayType $data", 1);

        }else if($nextVisitType == 1){

            $contentData = getContentDetail($nextVisitType, "NEXTVISIT");
            $con = mysql_fetch_assoc($contentData);
            $data = $con['code'];

            $date = $rec['date'];
            $newDate = date("d-m-Y", strtotime($date));
            $this->SetFont('prolog','',14);
            $this->MultiCell(60,5, "$newDate $data", 1);
        }

        return $this->GetY();

    }
    function show_advice($appointmentID,$xAxis,$yAxis,$size,$maxX){
        $resultData = getPrescribedAdvice($appointmentID);
        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('Times','B',$size + 2);
            $this->SetXY($xAxis , $yAxis);
            $this->MultiCell(20,5,"Advice");

        }
        $this->SetFont('nikosh','B',$size);
        while($row=  mysql_fetch_array($resultData)){
            $advice = $row['advice'];
            $yAxis =  $this->GetY();
            $this->SetXY($xAxis, $yAxis );
            $this->MultiCell($maxX,5,".$advice");

        }
        return $this->GetY();
    }
    function show_vital($appointmentID,$xAxis, $yAxis, $maxX, $size){




        $resultData = getPrescribedVital($appointmentID);

        if(mysql_num_rows($resultData) > 0){
            $this->SetFont('Times','B',$size + 1 );
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(40,5,"On Examination");
            $yAxis += 6;

        }if(mysql_num_rows($resultData) == 0){
            return $yAxis - 5;
        }
        $this->SetFont('Times','',$size);
        while($row=  mysql_fetch_array($resultData)){

            $vitalResult = $row['vitalResult'];
            $vitalDisplayName = $row['vitalDisplayName'];

            $yAxis =  $this->GetY();
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell($maxX,5,"$vitalDisplayName:  $vitalResult");

        }

        return $this->GetY();
    }
    function show_inv($appointmentID, $xAxis,$yAxis,$maxX,$size) {

        $this->SetFont('Times','',$size);

        $resultData = getPrescribedInv($appointmentID);

        if(mysql_num_rows($resultData) > 0){

            $this->SetFont('Times','B',$size + 1);
            $this->SetXY($xAxis, $yAxis);
            $this->MultiCell(40,5,"Test Advised");
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
            $this->MultiCell($maxX,5," .$invName");
            $var++;
        }

        return $this->GetY();

    }


}