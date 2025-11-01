<?php
// Database connection details
$servername = "localhost"; // Usually "localhost" for local servers
$username = "root";        // Default username for XAMPP is "root"
$password = "";            // Default password for XAMPP is empty
$dbname = "voucher_system"; // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>


