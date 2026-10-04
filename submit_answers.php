<?php
include 'db.php'; // your database connection

// Example scoring (you’ll replace this with your actual scoring logic)
$query = "SELECT id, title, category, description, colorClass FROM programs ORDER BY RAND() LIMIT 3";
$result = $conn->query($query);

$topPrograms = [];
while ($row = $result->fetch_assoc()) {
    $topPrograms[] = $row;
}

echo json_encode([
    "status" => "success",
    "topPrograms" => $topPrograms
]);
?>