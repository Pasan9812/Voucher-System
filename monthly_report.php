<?php
session_start();

if (!isset($_SESSION['username'])) {
    // User is not logged in → redirect to login page
    header("Location: login.php");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monthly Report</title>
    <link rel="stylesheet" href="report.css">
</head>


<?php
include('db.php');
include('navbar.php');

// Fetch all distinct payers
$payers_sql = "SELECT DISTINCT payer FROM vouchers ORDER BY payer ASC";
$payers_result = $conn->query($payers_sql);

// Fetch all distinct expense titles
$expense_sql = "SELECT DISTINCT expense_title FROM vouchers ORDER BY expense_title ASC";
$expense_result = $conn->query($expense_sql);
?>

<div class="report-container">
    <h1>Monthly Expense Report</h1>

    <form method="GET" action="monthly_report.php" class="report-form">
        <div class="form-group">
            <label for="month">Select Month:</label>
            <input type="month" id="month" name="month" required>
        </div>

        <div class="form-group">
            <label for="payer">Select Payer (Optional):</label>
            <select id="payer" name="payer">
                <option value="">-- All Payers --</option>
                <?php while ($row = $payers_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['payer']) ?>"><?= htmlspecialchars($row['payer']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="expense_title">Select Expense Title (Optional):</label>
            <select id="expense_title" name="expense_title">
                <option value="">-- All Expense Titles --</option>
                <?php while ($row = $expense_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['expense_title']) ?>"><?= htmlspecialchars($row['expense_title']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group full-width">
            <button type="submit" class="generate-btn">Generate Report</button>
        </div>
    </form>

<?php
if (isset($_GET['month'])) {
    $month = trim($_GET['month']);
    $payer = isset($_GET['payer']) ? trim($_GET['payer']) : null;
    $expense_title = isset($_GET['expense_title']) ? trim($_GET['expense_title']) : null;

    if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
        die("<div class='error'>Invalid month format. Please use YYYY-MM.</div>");
    }

    $pdf_data = [["Expense Title", "Total Amount"]];
    $params = [$month];
    $sql = "SELECT expense_title, SUM(amount - cashback_amount) AS total_amount 
            FROM vouchers 
            WHERE DATE_FORMAT(date, '%Y-%m') = ? 
              AND cashback_status = 'Completed'";

    if (!empty($payer)) {
        $sql .= " AND payer = ?";
        $params[] = $payer;
    }

    if (!empty($expense_title)) {
        $sql .= " AND expense_title = ?";
        $params[] = $expense_title;
    }

    $sql .= " GROUP BY expense_title ORDER BY total_amount DESC";

    $stmt = $conn->prepare($sql);
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $total_expenses = 0;
    $top_expense = null;
    $top_amount = 0;

    echo "<div class='report-section'>";
    echo "<h2>Report for <span class='highlight'>" . ($payer ?: "All Payers") . "</span>"
       . " | Expense: <span class='highlight'>" . ($expense_title ?: "All Expenses") . "</span>"
       . " | Month: <span class='highlight'>$month</span></h2>";

    if ($result->num_rows > 0) {
        echo "<table class='expense-table'>
                <thead>
                    <tr><th>Expense Title</th><th>Total Amount</th></tr>
                </thead><tbody>";

        while ($row = $result->fetch_assoc()) {
            $amount = $row['total_amount'];
            if ($amount > $top_amount) {
                $top_amount = $amount;
                $top_expense = $row['expense_title'];
            }
            echo "<tr>
                    <td>" . htmlspecialchars($row['expense_title']) . "</td>
                    <td>Rs. " . number_format($amount, 2) . "</td>
                  </tr>";
            $total_expenses += $amount;
            $pdf_data[] = [$row['expense_title'], number_format($amount, 2)];
        }

        echo "<tr class='total-row'>
                <td><strong>Total Expenses</strong></td>
                <td><strong>Rs. " . number_format($total_expenses, 2) . "</strong></td>
              </tr>";
        echo "</tbody></table>";
    } else {
        echo "<p class='info'>No vouchers found for this selection.</p>";
    }

    echo "</div>";

   
    $_SESSION['pdf_data'] = $pdf_data;
    $_SESSION['pdf_filename'] = "report_$month"
                               . (!empty($payer) ? "_$payer" : "")
                               . (!empty($expense_title) ? "_$expense_title" : "")
                               . ".pdf";

    echo "<div class='download-form-wrapper'>
            <form method='POST' action='download_pdf.php' class='download-form'>
                <button type='submit' class='download-btn'>Download Report as PDF</button>
            </form>
          </div>";
}
$conn->close();
?>
</div>

