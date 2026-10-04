<?php
session_start();
require_once 'db.php';

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in']!==true){
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit;
}

$action = $_REQUEST['action'] ?? '';

if($action==='get'){
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_assoc());
    exit;
}

if($action==='delete'){
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode(['success'=>true]);
    exit;
}

if($action==='save'){
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if($id>0){
        // Edit user
        if($password){
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password=? WHERE id=?");
            $stmt->bind_param("sssi", $name, $email, $hash, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
            $stmt->bind_param("ssi", $name, $email, $id);
        }
        $stmt->execute();
        echo json_encode(['success'=>true]);
        exit;
    } else {
        // Create new user
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name,email,password,created_at) VALUES (?,?,?,NOW())");
        $stmt->bind_param("sss", $name, $email, $hash);
        $stmt->execute();
        echo json_encode(['success'=>true]);
        exit;
    }
}

echo json_encode(['success'=>false,'message'=>'Invalid action']);
