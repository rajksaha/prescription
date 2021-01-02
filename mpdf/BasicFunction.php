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


}