<?php
header('Content-Type: application/json');
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "No user session found"]);
    exit;
}

$user_id = intval($_SESSION['user_id']);

// Query the quiz_results table
$sql = "SELECT program_id, raw_score, max_score, percentage FROM quiz_results WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$results = [];
while ($row = $result->fetch_assoc()) {
    $results[] = [
        "programId" => intval($row['program_id']),
        "raw" => floatval($row['raw_score']),
        "max" => floatval($row['max_score']),
        "percentage" => floatval($row['percentage'])
    ];
}

if (empty($results)) {
    echo json_encode(["error" => "No results found for this user"]);
} else {
    echo json_encode(["success" => true, "results" => $results]);
}
?>
