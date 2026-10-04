<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'db.php'; // <-- this must return a $conn = new mysqli(...)

header('Content-Type: application/json');

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$results = $_POST['results'] ?? [];

if (!$name || !$email || !$password || empty($results)) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

// Hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

// ✅ INSERT USER
$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: '.$conn->error]);
    exit;
}

$stmt->bind_param("sss", $name, $email, $hash);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Email already exists or DB error: '.$stmt->error]);
    exit;
}

$user_id = $conn->insert_id;
$_SESSION['user_id'] = $user_id;
$_SESSION['name'] = $name;

// ✅ INSERT RESULTS
$stmt = $conn->prepare("INSERT INTO quiz_results (user_id, program_id, raw_score, max_score, percentage) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: '.$conn->error]);
    exit;
}

foreach ($results as $r) {
    $programId = intval($r['programId']);
    $raw = floatval($r['raw']);
    $max = floatval($r['max']);
    $percentage = ($max > 0) ? ($raw / $max * 100) : 0;
    $stmt->bind_param("iidds", $user_id, $programId, $raw, $max, $percentage);
    $stmt->execute();
}

echo json_encode(['success' => true]);
exit;
?>
