<?php
ob_start();
session_start();
date_default_timezone_set('Europe/Athens');

require('../database/mysqli_con.php'); // Σύνδεση με τη βάση
require_once('../includes/helper_fun.inc.php');
check_session();
require_once('../lib/tcpdf/tcpdf.php'); // Include the TCPDF library

$barcodeget = [];
$sum = 0;
$aa = filter_input(INPUT_POST, 'aa',FILTER_VALIDATE_INT);
$helptable = [] ;
for ($i=1; $i<=$aa; $i++) {
    $help = "barcode_".$i;
    $helptable[$i] = filter_input(INPUT_POST, $help, FILTER_VALIDATE_INT);
    if (!empty($helptable[$i])){
        $sum = $sum + 1;
        $barcodeget[$sum] = $helptable[$i];
    }
}


// Create a new PDF document
$pdf = new TCPDF();

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Barcode PDF');
$pdf->SetSubject('Barcode PDF');

// Set default header data
$pdf->SetHeaderData('', 0, 'Barcodes', 'Generated by TCPDF', array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Set initial position
$x = $pdf->GetX();
$y = $pdf->GetY();

foreach ($barcodeget as $number) {
    // Calculate barcode width
    $barcodeWidth = 70; // Example width, you need to adjust this according to your barcode

    // Check if barcode fits in the remaining width of the page
    if (($x + $barcodeWidth) > ($pdf->GetPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT)) {
        // Move to the next row
        $x = PDF_MARGIN_LEFT;
        $y += 30; // Adjust this value according to your needs for vertical spacing

        // Check if adding barcode will exceed current page height
        if (($y + 30) > ($pdf->GetPageHeight() - PDF_MARGIN_TOP - PDF_MARGIN_BOTTOM)) {
            // Add a new page
            $pdf->AddPage();
            $x = PDF_MARGIN_LEFT;
            $y = PDF_MARGIN_TOP;
        }
        
        $pdf->SetXY($x, $y);
    }

    // Generate and print the barcode
    $pdf->write1DBarcode($number, 'C128', $x, $y + 10, '', 18, 0.4, array(), 'N');

    // Print the number above the barcode
    $pdf->SetXY($x, $y);
    $pdf->Cell($barcodeWidth-40, 10, $number, 0, 0, 'C');

    // Update position for the next barcode
    $x += $barcodeWidth + 10; // Adjust 10 for horizontal spacing between barcodes
}

// Define the output file path
$outputPath = tempnam(sys_get_temp_dir(), 'barcodes_') . '.pdf';

// Output the PDF to a file
$pdf->Output($outputPath, 'F');

// Clear the output buffer
while (ob_get_level()) {
    ob_end_clean();
}

// Serve the PDF file for download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="barcodes.pdf"');
header('Content-Length: ' . filesize($outputPath));
readfile($outputPath);

// Delete the temporary PDF file
unlink($outputPath);

mysqli_close($dbcon);
header('Location: active_product_view.php');
ob_end_flush();
exit();
?>