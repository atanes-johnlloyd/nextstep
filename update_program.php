<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    if ($id === "" || $id === null) {
        // INSERT new program
        $stmt = $conn->prepare("INSERT INTO programs (field_id, category, title, duration, certification, description, careers, colorClass) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $_POST['field'], $_POST['category'], $_POST['title'], $_POST['duration'], $_POST['cert'], $_POST['desc'], $_POST['careers'], $_POST['color']);
    } else {
        // UPDATE existing program
        $stmt = $conn->prepare("UPDATE programs SET field_id=?, category=?, title=?, duration=?, certification=?, description=?, careers=?, colorClass=? WHERE id=?");
        $stmt->bind_param("isssssssi", $_POST['field'], $_POST['category'], $_POST['title'], $_POST['duration'], $_POST['cert'], $_POST['desc'], $_POST['careers'], $_POST['color'], $id);
    }

    echo $stmt->execute() ? "success" : "error";
}
?>
