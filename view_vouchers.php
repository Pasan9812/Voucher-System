<?php
session_start();

if (!isset($_SESSION['username'])) {
    // User is not logged in → redirect to login page
    header("Location: login.php");
    exit();
}
?>


<?php
// Include the database connection
include('db.php');

// Initialize default filter values
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'id';
$payer_filter = isset($_GET['payer']) ? $_GET['payer'] : '';

// Validate sort options to prevent SQL injection
$allowed_sort = ['id', 'date', 'cashback_status'];
if (!in_array($sort_by, $allowed_sort)) {
    $sort_by = 'id';
}

// Build query dynamically
$sql = "SELECT id, date, payer, amount, expense_title, description, cashback_amount, cashback_status FROM vouchers WHERE 1=1";

if (!empty($from_date) && !empty($to_date)) {
    $sql .= " AND date BETWEEN '$from_date' AND '$to_date'";
}

if (!empty($payer_filter)) {
    $sql .= " AND payer = '$payer_filter'";
}

$sql .= " ORDER BY $sort_by";

$result = $conn->query($sql);

// Fetch distinct payers for dropdown
$payer_query = "SELECT DISTINCT payer FROM vouchers";
$payer_result = $conn->query($payer_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="view_vouchers.css" />
    <title>View Vouchers</title>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="content">
    <h2>All Vouchers</h2>

    <div class="top-bar">
        <!-- Date Range Filter -->
        <form method="GET" class="filter-form">
            <label>From: <input type="date" name="from_date" value="<?php echo $from_date; ?>"></label>
            <label>To: <input type="date" name="to_date" value="<?php echo $to_date; ?>"></label>

            <button type="submit">Search</button>
            
            <!-- Payer Filter -->
            <label>Payer:</label>
            <select name="payer" onchange="this.form.submit()">
                <option value="">All</option>
                <?php 
                while($payer_row = $payer_result->fetch_assoc()) {
                    $selected = ($payer_filter == $payer_row['payer']) ? "selected" : "";
                    echo "<option value='{$payer_row['payer']}' $selected>{$payer_row['payer']}</option>";
                }
                ?>
            </select>
        </form>

        <!-- Sort Option -->
        <form method="GET" class="sort-form">
            <!-- Retain filters when sorting -->
            <input type="hidden" name="from_date" value="<?php echo $from_date; ?>">
            <input type="hidden" name="to_date" value="<?php echo $to_date; ?>">
            <input type="hidden" name="payer" value="<?php echo $payer_filter; ?>">

            <label>Sort By:</label>
            <select name="sort_by" onchange="this.form.submit()">
                <option value="id" <?php if($sort_by=='id') echo 'selected'; ?>>Voucher No</option>
                <option value="date" <?php if($sort_by=='date') echo 'selected'; ?>>Date</option>
                <option value="cashback_status" <?php if($sort_by=='cashback_status') echo 'selected'; ?>>Status</option>
            </select>
        </form>
    </div>

    <?php
    if ($result->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>Voucher No</th>
                    <th>Date</th>
                    <th>Payer</th>
                    <th>Amount</th>
                    <th>Expense Title</th>
                    <th>Description</th>
                    <th>Cashback Amount</th>
                    <th>Status</th>
                </tr>";
        while($row = $result->fetch_assoc()) {
            $cashbackAmount = ($row['cashback_amount'] !== null) ? $row['cashback_amount'] : '-';
            $cashbackStatusClass = ($row['cashback_status'] == 'Completed') ? 'completed' : 'pending';

            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['date']}</td>
                    <td>{$row['payer']}</td>
                    <td>{$row['amount']}</td>
                    <td>{$row['expense_title']}</td>
                    <td>{$row['description']}</td>
                    <td>{$cashbackAmount}</td>
                    <td class='{$cashbackStatusClass}'>{$row['cashback_status']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No vouchers found.</p>";
    }

    $conn->close();
    ?>
</div>

</body>
</html>
