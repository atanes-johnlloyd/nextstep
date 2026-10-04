<?php
include 'db.php';
header('Content-Type: application/json');

$query = "SELECT COUNT(*) AS total_users FROM users";
$result = $conn->query($query);

if ($result && $row = $result->fetch_assoc()) {
    echo json_encode(['total_users' => (int)$row['total_users']]);
} else {
    echo json_encode(['total_users' => 0]);
}
?>
