<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nextstep_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_errno) {
    if (php_sapi_name() !== 'cli') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
        exit;
    } else {
        die("Database connection failed: " . $conn->connect_error);
    }
}

$conn->set_charset("utf8mb4");
?>
