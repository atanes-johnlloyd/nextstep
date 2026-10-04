<?php
header('Content-Type: application/json');

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "nextstep_db";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// ✅ Correct column names based on your table structure
$sql = "SELECT id, field_id, category, title, duration, description, colorClass FROM programs ORDER BY category, title";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["error" => "SQL Error: " . $conn->error]);
    exit;
}

$programs = [];

while ($row = $result->fetch_assoc()) {
    $programs[] = [
        "id" => $row["id"],
        "field_id" => $row["field_id"],
        "category" => $row["category"],
        "title" => $row["title"],
        "duration" => $row["duration"],
        "description" => $row["description"],
        "colorClass" => $row["colorClass"]
    ];
}

// Output JSON
echo json_encode($programs);
$conn->close();
?>