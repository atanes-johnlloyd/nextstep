<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id) {
    echo json_encode(['success'=>false,'hasResults'=>false]);
    exit;
}

// Check if the user has results
$stmt = $conn->prepare("SELECT COUNT(*) FROM quiz_results WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

echo json_encode([
    'success' => true,
    'hasResults' => $count > 0
]);
?>
