<?php
require_once 'db.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if($action == 'get') {
    $id = intval($_GET['id']);
    $res = $conn->query("SELECT * FROM programs WHERE id=$id")->fetch_assoc();
    echo json_encode($res);
    exit;
}

if($action == 'save') {
    $id = intval($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $category = $conn->real_escape_string($_POST['category']);
    $field_id = intval($_POST['field_id']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $certification = $conn->real_escape_string($_POST['certification']);
    $description = $conn->real_escape_string($_POST['description']);
    $careers = $conn->real_escape_string($_POST['careers']);
    $colorClass = $conn->real_escape_string($_POST['colorClass']);

    if($id > 0){
        // Update
        $sql = "UPDATE programs SET 
                title='$title',
                category='$category',
                field_id=$field_id,
                duration='$duration',
                certification='$certification',
                description='$description',
                careers='$careers',
                colorClass='$colorClass'
                WHERE id=$id";
    } else {
        // Insert
        $sql = "INSERT INTO programs 
                (title, category, field_id, duration, certification, description, careers, colorClass)
                VALUES
                ('$title','$category',$field_id,'$duration','$certification','$description','$careers','$colorClass')";
    }

    if($conn->query($sql)){
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false, 'message'=>$conn->error]);
    }
    exit;
}

if($action == 'delete'){
    $id = intval($_POST['id']);
    if($conn->query("DELETE FROM programs WHERE id=$id")){
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false,'message'=>$conn->error]);
    }
    exit;
}

echo json_encode(['success'=>false,'message'=>'Invalid action']);
?>
