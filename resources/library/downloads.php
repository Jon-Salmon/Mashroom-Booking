<?php

require_once(LIBRARY_PATH . "/fpdf/fpdf.php");

class PDF extends FPDF
{
// Page header
function Header()
{
    // Logo
    #$this->Image('logo.png',10,6,30);
    // Arial bold 15
    $this->SetFont('Arial','',8);
    $this->SetTextColor(150);
    // Title
    $this->Cell(30,0,date("d/m/Y"),0,0,'L');
    // Line break
    $this->Ln();
}

// Page footer
function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-22);
    // Arial italic 8
    $this->SetFont('Arial','',12);
    $this->MultiCell(0,5,'Please ask people who are not on this list, and therefore need to be inducted, to request an induction by visiting mash.trevsjcr.com', 0, 'C');
    $this->Ln(2);
    $this->SetFont('Arial','',16);
    $this->MultiCell(0,5,'The MASH room can also be booked by visiting mash.trevsjcr.com', 0, 'C');

}

// Better table
function ImprovedTable($header, $data)
{
    // Column widths
    #$w = array(42, 55);
    $w = 97;
    // Header
    #$this->SetFont('Arial','B',15);
    #for($i=0;$i<count($header);$i++)
    #    $this->Cell($w[$i],7,$header[$i],0,0,'C');
    #$this->Cell(10,6,'');
    #for($i=0;$i<count($header);$i++)
    #    $this->Cell($w[$i],7,$header[$i],0,0,'C');
    #$this->Ln();
    // Data
    $this->SetFont('Arial','',8);
    $newRow = FALSE;
        
    foreach($data as $row)
    {
        $this->Cell($w,6,$row['name'] . " (" . $row['email'] . ")", 0, 0, 'L');
        if ($newRow){
            $this->Ln(5);
            $this->Cell($w,0,'','T');
            $this->Cell(6,5,'');
            $this->Cell($w,0,'','T');
            $this->Ln(0);
            $newRow = FALSE;
        } else {
            $this->Cell(6,5,'',0,0);
            $newRow = TRUE;
        }
    }
}

}

function downloadUserCSV(){
    global $PDO;
    
    // output headers so that the file is downloaded rather than displayed
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=mash_inducted.csv');

    // create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    // output the column headings
    fputcsv($output, array('Name', 'Email'));

    // fetch the data
    $stmt = $PDO->query('SELECT name, email FROM users ORDER BY name');
    $rows = $stmt->fetchAll();

    // loop over the rows, outputting them
    foreach ($rows as $row){
        fputcsv($output, $row);
    }
    die();
}

function downloadUserPDF(){

    global $USER, $ADMINS;
    $users = $USER->getAll();
    $header = array('Name', 'Email');

    $pdf = new PDF();
    $pdf-> SetMargins(5, 8);
    $pdf->SetDrawColor(200);
    $pdf->AliasNbPages();
    $pdf->AddPage();

    $pdf->Ln(5);
    $pdf->SetFont('Arial','BU',35);
    $pdf->Cell(0,0,'MASH Room Inductees',0,0,'C');
    $pdf->Ln(12);

    $pdf->SetFont('Arial','B',15);
    $pdf->MultiCell(0,8,'ONLY students who have been inducted and are on this list may sign out the keys to the MASH Room, surrendering their campus card as a deposit.',0,'C');
    $pdf->Ln(2);

    $pdf->SetFont('Arial','',10);

    $pdf->Cell(10,0,'');
    $pdf->Cell(180,0,'','T');
    $pdf->Ln(4);

    $pdf->Cell(70, 0, "Technical Manager:", 0, 0, 'R');
    $pdf->Cell(100, 0, $ADMINS->tech->name . " (" . $ADMINS->tech->email . ")", 0, 0, 'L');
    $pdf->Ln(5);
    $pdf->Cell(70, 0, "MASH Room Manager:", 0, 0, 'R');
    $pdf->Cell(100, 0, $ADMINS->mash->name . " (" . $ADMINS->mash->email . ")", 0, 0, 'L');
    $pdf->Ln(4);

    $pdf->Cell(10,0,'');
    $pdf->Cell(180,0,'','T');
    
    $pdf->Ln(5);
    
    $pdf->SetFont('Arial','',10);

    $pdf->ImprovedTable($header,$users);
    
    $pdf->Output();

    die();
}


?>