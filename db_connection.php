<?php

$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if needed
$dbname = "carismaleatery"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8mb4");

// Optional: Timezone settings (for accurate timestamps)
date_default_timezone_set('Asia/Manila');
?>