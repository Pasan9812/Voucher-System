<?php
require('fpdf/fpdf.php');
session_start();

if (isset($_SESSION['pdf_data']) && isset($_SESSION['pdf_filename'])) {
    $pdf_data = $_SESSION['pdf_data'];
    $filename = $_SESSION['pdf_filename'];

    // Get filters
    $month = $_SESSION['report_month'] ?? '';
    $payer = $_SESSION['report_payer'] ?? '';
    $expense_title = $_SESSION['report_expense'] ?? '';

    // Format month nicely
    $month_name = !empty($month) ? date('F Y', strtotime($month . "-01")) : '';

    // Build dynamic PDF title
    $pdf_title = "Expense Report";
    if (!empty($month_name)) {
        $pdf_title .= " - " . $month_name;
    }
    if (!empty($payer)) {
        $pdf_title .= " (Payer: " . $payer . ")";
    }

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Dynamic title
    $pdf->Cell(190, 10, $pdf_title, 0, 1, 'C');
    $pdf->Ln(10);

    // Table header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(95, 10, 'Expense Title', 1, 0, 'C');
    $pdf->Cell(95, 10, 'Total Amount', 1, 1, 'C');

    // Table data
    $pdf->SetFont('Arial', '', 12);
    $total_expenses = 0;
    foreach (array_slice($pdf_data, 1) as $row) {
        $pdf->Cell(95, 10, $row[0], 1, 0, 'C');
        $pdf->Cell(95, 10, $row[1], 1, 1, 'C');
        $total_expenses += floatval(str_replace(',', '', $row[1]));
    }

    // Total row
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(95, 10, 'Total Expenses', 1, 0, 'C');
    $pdf->Cell(95, 10, number_format($total_expenses, 2), 1, 1, 'C');

    // Output
    $pdf->Output('D', $filename);

    // Clear session
    unset($_SESSION['pdf_data'], $_SESSION['pdf_filename'], $_SESSION['report_month'], $_SESSION['report_payer'], $_SESSION['report_expense']);
    exit();
} else {
    echo "No data available to generate the PDF.";
}

?>
