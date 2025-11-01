<?php
include('db.php');
session_start();
require 'vendor/autoload.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// ----------------------------
//  Create Voucher
// ----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_voucher'])) {
    $date = $_POST['date'];
    $payer = $_POST['payer'];
    $amount = $_POST['amount'];
    $expense_title = $_POST['expense_title'];
    $description = $_POST['description'];

    $sql = "INSERT INTO vouchers (date, payer, amount, expense_title, description, otp_status) 
            VALUES ('$date', '$payer', '$amount', '$expense_title', '$description', 'Verified')";

    if ($conn->query($sql) === TRUE) {
        $success = "Voucher submitted successfully!";
    } else {
        $error = "Database error: " . $conn->error;
    }
}

// ----------------------------
//  Update Voucher
// ----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_voucher'])) {
    $id = intval($_POST['voucher_id']);
    $date = $_POST['date'];
    $payer = $_POST['payer'];
    $amount = $_POST['amount'];
    $expense_title = $_POST['expense_title'];
    $description = $_POST['description'];

    $sql = "UPDATE vouchers 
            SET date='$date', payer='$payer', amount='$amount', expense_title='$expense_title', description='$description' 
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        $success = "Voucher #$id updated successfully!";
    } else {
        $error = "Error updating voucher: " . $conn->error;
    }
}

// ----------------------------
//  Delete Voucher
// ----------------------------
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    if ($conn->query("DELETE FROM vouchers WHERE id=$delete_id") === TRUE) {
        $success = "Voucher #$delete_id deleted successfully!";
    } else {
        $error = "Error deleting voucher: " . $conn->error;
    }
}

// ----------------------------
//  Load Voucher for Editing
// ----------------------------
$editData = null;
if (isset($_GET['voucher_id']) && $_GET['voucher_id'] !== '') {
    $vid = intval($_GET['voucher_id']);
    $res = $conn->query("SELECT * FROM vouchers WHERE id=$vid");
    if ($res->num_rows > 0) {
        $editData = $res->fetch_assoc();
    }
}

// ----------------------------
//  Get Next Voucher Number
// ----------------------------
$result = $conn->query("SELECT id FROM vouchers ORDER BY id DESC LIMIT 1");
$nextVoucherNo = ($result->num_rows > 0) ? $result->fetch_assoc()['id'] + 1 : 1;

// ----------------------------
//  Reset Form
// ----------------------------
if (isset($_GET['reset'])) {
    header("Location: create_voucher.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Create Voucher</title>
    <link rel="stylesheet" href="navbar.css" />
    <link rel="stylesheet" href="create_voucher.css" />
    <style>
        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .cancel-button {
            background-color: red;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .cancel-button:hover { background-color: #cc0000; }
        .form-row textarea {
            width: 100%;
            min-height: 80px;
            padding: 8px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: vertical;
        }
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            text-align: center;
        }
        .message.success { background: #d4edda; color: #155724; }
        .message.error { background: #f8d7da; color: #721c24; }
        .voucher-selector {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .voucher-selector select {
            flex: 1;
            padding: 8px;
            border-radius: 5px;
        }
        button, .cancel-button {
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 12px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button[name="submit_voucher"] {
            background-color: #28a745;
            color: white;
        }
        button[name="submit_voucher"]:hover {
            background-color: #218838;
        }
        button[name="update_voucher"] {
            background-color: #007bff;
            color: white;
        }
        button[name="update_voucher"]:hover {
            background-color: #0069d9;
        }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container">
    <h2><?php echo $editData ? "Edit Voucher #{$editData['id']}" : "Create a New Voucher"; ?></h2>

    <p><strong>Current Voucher No:</strong> <?php echo $editData ? $editData['id'] : $nextVoucherNo; ?></p>

    <?php if (!empty($error)): ?>
        <p class="message error"><?php echo $error; ?></p>
    <?php elseif (!empty($success)): ?>
        <p class="message success"><?php echo $success; ?></p>
    <?php endif; ?>

    <!-- Select Previous Voucher -->
    <form method="get" class="voucher-selector">
        <label for="voucher_id">Select Previous Voucher:</label>
        <select name="voucher_id" id="voucher_id">
            <option value="">-- None (Create New) --</option>
            <?php
            $vouchers = $conn->query("SELECT id, payer, amount FROM vouchers ORDER BY id DESC");
            while ($row = $vouchers->fetch_assoc()) {
                $selected = ($editData && $editData['id'] == $row['id']) ? 'selected' : '';
                echo "<option value='{$row['id']}' $selected>Voucher #{$row['id']} - {$row['payer']} ({$row['amount']})</option>";
            }
            ?>
        </select>
        <button type="submit">Load</button>
    </form>

    <!-- Voucher Form -->
    <form method="post">
        <?php if ($editData): ?>
            <input type="hidden" name="voucher_id" value="<?php echo $editData['id']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <label>Date</label>
            <input type="date" name="date" value="<?php echo $editData['date'] ?? ''; ?>" required>
        </div>

        <div class="form-row">
            <label>Payer</label>
            <input type="text" name="payer" value="<?php echo $editData['payer'] ?? ''; ?>" required>
        </div>

        <div class="form-row">
            <label>Amount</label>
            <input type="number" name="amount" step="0.01" value="<?php echo $editData['amount'] ?? ''; ?>" required>
        </div>

        <div class="form-row">
            <label>Expense Title</label>
            <select name="expense_title" required>
                <option value="">-- Select --</option>
                <?php
                $titles = ["Fuel","Food","Purchasing","Salary","Salary Advance","Office upkeeping",
                           "Postal charges","Travelling","Technical repairs","Vehicle maintain","Drinking water",
                           "Mobitel bill","Dialog bill","Building rent","Electricity bill","Water bill","Transport",
                           "Donation","Promotion","Rent","Advertising","Commission","Loan","Car Finance","Insurance"];
                foreach ($titles as $title) {
                    $selected = ($editData && $editData['expense_title'] === $title) ? 'selected' : '';
                    echo "<option value='$title' $selected>$title</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-row">
            <label>Description</label>
            <textarea name="description" placeholder="Add any additional details here..."><?php echo $editData['description'] ?? ''; ?></textarea>
        </div>
        
        <div class="form-actions">
            <?php if ($editData): ?>
                <button type="submit" name="update_voucher">Update Voucher</button>
                <a href="create_voucher.php?delete_id=<?php echo $editData['id']; ?>" 
                   class="cancel-button"
                   onclick="return confirm('Are you sure you want to delete this voucher?');">
                   Delete Voucher
                </a>
            <?php else: ?>
                <button type="submit" name="submit_voucher">Submit Voucher</button>
                <a href="create_voucher.php?reset=true" class="cancel-button">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

</body>
</html>
