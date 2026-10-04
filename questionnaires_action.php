<?php
require_once 'db.php';

header('Content-Type: application/json');

// Check action
$action = $_REQUEST['action'] ?? '';

if ($action === 'get') {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM questions WHERE id = $id");
    $data = $result->fetch_assoc();
    echo json_encode($data);
    exit();
}

if ($action === 'save') {
    $id = intval($_POST['id'] ?? 0);
    $question = $conn->real_escape_string($_POST['question']);
    $field_id = intval($_POST['field_id']);
    $program_id = intval($_POST['program_id']);

    if ($id > 0) {
        // Update existing question
        $sql = "UPDATE questions SET question='$question', field_id=$field_id, program_id=$program_id WHERE id=$id";
    } else {
        // Insert new question
        $sql = "INSERT INTO questions (question, field_id, program_id) VALUES ('$question', $field_id, $program_id)";
    }

    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
    exit();
}

if ($action === 'delete') {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM questions WHERE id=$id";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
    exit();
}

// If action not recognized
echo json_encode(['success' => false, 'message' => 'Invalid action']);
