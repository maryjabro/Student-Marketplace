<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Local MySQL using root, no password
$DB_HOST = "127.0.0.1";
$DB_USER = "root";
$DB_PASS = "";  // empty, since you logged in without a password
$DB_NAME = "studentMarketplace";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
