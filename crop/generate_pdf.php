<?php 
require_once('tcpdf/tcpdf.php'); // Include the TCPDF library
include('dbconnection.php'); // Include database connection
error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_pdf'])) {
    // Fetch data for tbl_data
    $query1 = mysqli_query($con, "SELECT * FROM tbl_data");
    $tableData = [];
    while ($row = mysqli_fetch_assoc($query1)) {
        $tableData[] = $row;
    }

    // Fetch data for recommended crops
    $query2 = mysqli_query($con, "SELECT * FROM tbl_crop");
    $cropData = [];
    while ($row = mysqli_fetch_assoc($query2)) {
        $cropData[] = $row;
    }

    // Create a new PDF document
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Sahyadri');
    $pdf->SetTitle('Crop Recommendation Report');
    $pdf->SetHeaderData('', '', 'AI Based Crop Recommendation', '');
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(TRUE, 10);
    $pdf->AddPage();

    // Add tbl_data table
    $html = '<h3 style="text-align:center;">Crop Recommendation Table</h3>';
    $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; text-align:center;">';
    $html .= '<thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Nitrogen (N)</th>
                    <th>Phosphorus (P)</th>
                    <th>Potassium (K)</th>
                    <th>Soil Moisture</th>
                    <th>Ph</th>
                </tr>
              </thead>';
    $html .= '<tbody>';

    foreach ($tableData as $row) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($row['date']) . '</td>
                    <td>' . htmlspecialchars($row['time']) . '</td>
                    <td>' . htmlspecialchars($row['n']) . '</td>
                    <td>' . htmlspecialchars($row['p']) . '</td>
                    <td>' . htmlspecialchars($row['k']) . '</td>
                    <td>' . htmlspecialchars($row['soil']) . '</td>
                    <td>' . htmlspecialchars($row['ph']) . '</td>
                  </tr>';
    }

    $html .= '</tbody>';
    $html .= '</table>';

    // Write tbl_data content to the PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Add a new page for recommended crops
    $pdf->AddPage();

    // Add recommended crops table
    $html = '<h3 style="text-align:center;">Recommended Crops</h3>';
    $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; text-align:center;">';
    $html .= '<thead>
                <tr>
                    <th>Crop</th>
                    <th>Percentage</th>
                </tr>
              </thead>';
    $html .= '<tbody>';

    foreach ($cropData as $row) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($row['crop']) . '</td>
                    <td>' . htmlspecialchars($row['percentage']) . '</td>
                  </tr>';
    }

    $html .= '</tbody>';
    $html .= '</table>';

    // Write recommended crops content to the PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Output PDF
    $pdf->Output('crop_recommendation_report.pdf', 'D'); // 'D' forces download
    exit;
}
?>
