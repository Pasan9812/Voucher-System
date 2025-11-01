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
    <title>Complete Voucher</title>
    <link rel="stylesheet" href="complete_voucher.css">
</head>

<?php
// Include database connection
include('db.php');

$voucher = null;
$message = "";

// Handle search submission
if (isset($_POST['search_voucher'])) {
    $voucher_id = intval($_POST['voucher_id']);

    // Fetch voucher details
    $sql = "SELECT * FROM vouchers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $voucher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $voucher = $result->fetch_assoc();
    } else {
        $message = "Voucher not found!";
    }
}

// Handle cashback update submission
if (isset($_POST['complete_voucher'])) {
    $voucher_id = intval($_POST['voucher_id']);
    $cashback_amount = floatval($_POST['cashback_amount']);

    // Fetch the original voucher amount to validate
    $sql = "SELECT amount FROM vouchers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $voucher_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $original_amount = floatval($row['amount']);

        if ($cashback_amount > $original_amount) {
            $message = "Cashback amount cannot exceed the original voucher amount of Rs. " . number_format($original_amount, 2);
        } else {
            $sql = "UPDATE vouchers SET cashback_amount = ?, cashback_status = 'Completed' WHERE id = ? AND cashback_status = 'Pending'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("di", $cashback_amount, $voucher_id);

            if ($stmt->execute()) {
                $message = "Voucher updated successfully!";
            } else {
                $message = "Error updating voucher: " . $conn->error;
            }
        }
    } else {
        $message = "Voucher not found during update.";
    }
}
?>

<body>

<?php include('navbar.php'); ?>

<div class="form-container">
    <h2>Complete Pending Voucher</h2>

    <?php 
    if (!empty($message)) {
        echo "<p class='message'>$message</p>";
    }
    ?>

    <!-- First form: Search Voucher -->
    <form method="POST">
        <label for="voucher_id">Enter Voucher No:</label>
        <input type="number" id="voucher_id" name="voucher_id" value="<?php echo isset($voucher['id']) ? $voucher['id'] : ''; ?>" required>

        <div class="button-container">
            <input type="submit" name="search_voucher" value="Search">
        </div>
    </form>

    <?php if ($voucher): ?>
        <hr>
        <h3>Voucher Details:</h3>
        <p><strong>Payer:</strong> <?php echo htmlspecialchars($voucher['payer']); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($voucher['date']); ?></p>
        <p><strong>Amount:</strong> Rs. <?php echo number_format($voucher['amount'], 2); ?></p>
        <p><strong>Expense Title:</strong> <?php echo htmlspecialchars($voucher['expense_title']); ?></p>
        <p><strong>Description :</strong> <?php echo htmlspecialchars($voucher['description']); ?></p>

        <?php
            $status = htmlspecialchars($voucher['cashback_status']);
            $statusClass = strtolower($status) === 'pending' ? 'status-pending' : 'status-completed';
        ?>
        <p><strong>Cashback Status:</strong> <span class="<?php echo $statusClass; ?>"><?php echo $status; ?></span></p>

        <?php if ($voucher['cashback_status'] === 'Pending'): ?>
            <!-- Second form: Complete Voucher -->
            <form method="POST">
                <input type="hidden" name="voucher_id" value="<?php echo $voucher['id']; ?>">

                <label for="cashback_amount">
                    Enter Cashback Amount (max Rs. <?php echo number_format($voucher['amount'], 2); ?>):
                </label>
                <input type="number"
                       id="cashback_amount"
                       name="cashback_amount"
                       required
                       min="0"
                       step="0.01"
                       max="<?php echo htmlspecialchars($voucher['amount']); ?>">

                <div class="button-container">
                    <input type="submit" name="complete_voucher" value="Complete Voucher">
                </div>
            </form>
        <?php else: ?>
            <p class="error">This voucher is already completed.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
