<?php
session_start();
include 'db.php';

$user_id = $_POST['user_id'] ?? 0;
$results = $_POST['results'] ?? [];

if (!$user_id || !is_array($results)) {
    echo json_encode(['success'=>false,'message'=>'Invalid data']);
    exit;
}

foreach ($results as $r) {
    $programId = intval($r['programId']);
    $raw = floatval($r['raw']);
    $max = floatval($r['max']);
    $pct = floatval($r['pct'] ?? 0);

    // INSERT new result or UPDATE if user/program combination exists
    $stmt = $conn->prepare("
        INSERT INTO quiz_results (user_id, program_id, raw_score, max_score, percentage, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE 
            raw_score = VALUES(raw_score),
            max_score = VALUES(max_score),
            percentage = VALUES(percentage),
            created_at = NOW()
    ");
    $stmt->bind_param("iiddd", $user_id, $programId, $raw, $max, $pct);
    $stmt->execute();
    $stmt->close();
}

echo json_encode(['success'=>true,'message'=>'Results saved/updated successfully']);
?>