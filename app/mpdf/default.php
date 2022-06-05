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

class DefTemplate extends mPDF
{
    function preparePrescription($conn, $appointmentID): PDF
    {
        $pdf = new DefTemplate('', 'A4', 10, 'nikosh');
        $pdf->WriteHTML('');
        //$pdf->SetAutoPageBreak(true, 12);

        $appData = getAppointmentInfo($conn, $appointmentID);
        $appType = $appData['appointmenttype'];
        $patientID = $appData['patientid'];
        $doctorID = $appData['doctorid'];
        $linestyle = null;
        $lineStyle = array('width' => 20, 'cap' => 'butt', 'join' => 'miter', 'dash' => '', 'phase' => 0, 'color' => array(255, 0, 0));
        $pdf->Line(10, 53, 195, 53, $lineStyle);
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


        $pdf->Line($rightXaxis - 10, 62, $rightXaxis - 10, 260, $lineStyle);


        $rightYaxis = $pdf->show_med($conn, $appointmentID, $rightXaxis, $rightYaxis, $size + 2, $pageNum, $pdf);
        $rightYaxis = $pdf->checkForPageChange($rightYaxis, $pdf->page);
        $rightYaxis = $pdf->Show_advice($conn, $appointmentID, $rightXaxis, $rightYaxis + 10, $size + 1, $maxXForRight);
        $rightYaxis = $pdf->checkForPageChange($rightYaxis, $pdf->page);
        $rightYaxis = $pdf->show_nextVisit($conn, $appointmentID, $rightXaxis, $rightYaxis + 10, $size + 2);

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
        $leftYaxis = $pdf->Show_Complain($conn, $appointmentID, $leftXaxis, $leftYaxis + $gap, $maxX, $size);
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->Show_vital($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size);
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->Show_History($conn, $appointmentID, $doctorID, $leftXaxis, $leftYaxis + 5, $maxX, $size);
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->Show_Past_History($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size, 0, "Past Disease");
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->Show_Past_History($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size, 1, "Associated Illness");
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->Show_Family_History($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size);
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->Show_Drug_History($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size, "OLD_DRUG", "Old Drug(s)");
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->Show_Drug_History($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size, "CURRENT_DRUG", "Current Drug(s)");
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->showClinicalRecord($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size);
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->Show_inv($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size);
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->Show_diagnosis($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $size, $maxX);
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->showComment($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $maxX, $size);
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        $leftYaxis = $pdf->show_ref_doc($conn, $appointmentID, $leftXaxis, $leftYaxis + 5, $size, $maxX);
        $leftYaxis = $pdf->checkForPageChange($leftYaxis, $pdf->page);
        if ($yPageNo > $pdf->page) {
            $pdf->page = $yPageNo;
            $pdf->Line($rightXaxis - 10, 60, $rightXaxis - 10, 260, $lineStyle);
        }

        //$leftYaxis=$pdf-> show_advice_temp($conn, $appointmentID,$leftXaxis,$leftYaxis + 5,$size);

        $pdf->Output('');
        return $pdf;
    }
}
?>