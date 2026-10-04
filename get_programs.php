<?php
include 'db.php'; // adjust path if needed

if (!isset($_POST['ids']) || empty($_POST['ids'])) {
    echo json_encode([]);
    exit;
}

$ids = $_POST['ids'];
if (!is_array($ids)) {
    $ids = explode(',', $ids);
}
$ids = array_map('intval', $ids); // convert to integers
$ids_list = implode(',', $ids);  // "1,2,3"

$sql = "SELECT id, title, category, colorClass 
        FROM programs 
        WHERE id IN ($ids_list)";

$result = $conn->query($sql);
$programs = [];
while($row = $result->fetch_assoc()) {
    $programs[] = $row;
}
echo json_encode($programs);