<?php
date_default_timezone_set('Asia/Manila');
include 'db.php';
header('Content-Type: application/json');

// Catch unexpected errors as JSON
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(["status" => "error", "message" => "Invalid request method."]);
        exit;
    }

    if (empty($_POST['email'])) {
        echo json_encode(["status" => "error", "message" => "Email is required."]);
        exit;
    }

    $email = trim($_POST['email']);

    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "No account found with that email."]);
        exit;
    }

    // Generate reset token & expiration
    $token = bin2hex(random_bytes(16));
    $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Save to database
    $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
    $stmt->bind_param("sss", $token, $expires, $email);
    if (!$stmt->execute()) {
        echo json_encode(["status" => "error", "message" => "Database update failed."]);
        exit;
    }

    // Reset link (change to your actual local URL)
    $resetLink = "http://localhost/NextStep/reset_password.php?token=$token";
    $subject = "NextStep Password Reset";
    $message = "Click the link below to reset your password:\n\n$resetLink\n\nThis link will expire in 1 hour.";
    $headers = "From: no-reply@nextstep.com";

    // Send the email
    include 'mailer.php';
    if (!sendResetEmail($email, $resetLink)) {
        echo json_encode(["status" => "error", "message" => "Failed to send email via SMTP."]);
        exit;
    }

    echo json_encode(["status" => "success", "message" => "Password reset link sent to your email."]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Unexpected error: " . $e->getMessage()]);
}
?>
