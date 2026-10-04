<?php
session_start();
require_once 'db.php'; // make sure this points to your DB connection file

if (!isset($_SESSION['user_id'])) {
  // User is not logged in
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Delete results belonging to the user
$sql = "DELETE FROM quiz_results WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    header("Location: $redirect?deleted=1");
exit();
}

$stmt->close();
$conn->close();
?>