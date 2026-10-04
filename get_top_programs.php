<?php
include 'db.php';
header('Content-Type: application/json');

// Query: calculate the average score for each program
$query = "
SELECT 
    p.title AS program_name,
    AVG(qr.percentage) AS avg_percentage,
    COUNT(qr.id) AS total_attempts
FROM quiz_results qr
JOIN programs p ON qr.program_id = p.id
GROUP BY p.id
ORDER BY avg_percentage DESC
LIMIT 3
";

$result = $conn->query($query);

if (!$result) {
    echo json_encode(['error' => $conn->error]);
    exit;
}

$top_programs = [];
while ($row = $result->fetch_assoc()) {
    $top_programs[] = [
        'program_name' => $row['program_name'],
        'avg_percentage' => round($row['avg_percentage'], 2),
        'total_attempts' => (int)$row['total_attempts']
    ];
}

echo json_encode($top_programs);
?>
