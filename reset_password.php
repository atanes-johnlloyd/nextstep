<?php
date_default_timezone_set('Asia/Manila');
include 'db.php';

$token = $_GET['token'] ?? '';
$stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<h3>Invalid or expired token.</h3>";
    exit;
}

$showSuccess = false;

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (strlen($_POST['password']) < 8) {
        echo "<script>
          Swal.fire({
            title: 'Weak Password',
            text: 'Password must be at least 8 characters long.',
            icon: 'error',
            confirmButtonColor: '#070069'
          });
        </script>";
        exit;
    }
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?");
    $stmt->bind_param("ss", $password, $token);
    $stmt->execute();

    $showSuccess = true;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Freeman&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #F6F6F6;
      font-family: "Inter", sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .modal-content {
      background: #fff;
      padding: 2.5rem 3rem;
      border-radius: 2rem;
      box-shadow: 0 4px 25px rgba(0, 0, 0, 0.25);
      text-align: center;
      max-width: 30rem;
      width: 90%;
      color: #070069;
    }

    .modal-content h2 {
      font-family: "Freeman", sans-serif;
      margin-bottom: 1rem;
    }

    .modal-input {
      margin: 0.8rem 0;
      padding: 0.7rem 1rem;
      border-radius: 0.6rem;
      border: 1px solid #ccc;
      font-size: 1rem;
      width: 100%;
      transition: 0.5s;
    }

    .modal-input:focus {
      border-color: #06B6D4;
      outline: none;
    }

    .btn-modal-primary {
      margin-top: 1rem;
      background-color: #070069;
      color: white;
      border: none;
      border-radius: 25px;
      padding: 0.6rem 1.5rem;
      cursor: pointer;
      font-weight: 600;
      transition: 0.3s;
      width: 100%;
    }

    .btn-modal-primary:hover {
      background-color: #06B6D4;
    }

    .btn-cancel {
      margin-top: 0.8rem;
      background-color: transparent;
      color: #06B6D4;
      border: none;
      font-weight: bold;
      cursor: pointer;
      text-decoration: underline;
    }

    .btn-cancel:hover {
      color: #070069;
    }
  </style>
</head>

<body>
  <div class="modal-content">
    <h2>Reset Password</h2>
    <p>Enter your new password below to complete the reset.</p>

    <form method="POST" onsubmit="return validatePassword()">
      <input type="password" id="password" name="password" class="modal-input" placeholder="New Password" minlength="8" required>
      <small id="passwordError" style="color:red; display:none;">Password must be at least 8 characters long.</small>
      <button type="submit" class="btn-modal-primary">Reset Password</button>
    </form>

    <button class="btn-cancel" onclick="window.location.href='index.php'">Cancel</button>
  </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if ($showSuccess): ?>
  <script>
    function validatePassword() {
      const password = document.getElementById('password').value;
      const error = document.getElementById('passwordError');
      if (password.length < 8) {
        error.style.display = 'block';
        return false;
      }
      error.style.display = 'none';
      return true;
    }
    Swal.fire({
      title: 'Password Updated!',
      text: 'Your password has been reset successfully.',
      icon: 'success',
      confirmButtonText: 'OK',
      confirmButtonColor: '#070069'
    }).then(() => {
      window.location.href = 'index.php';
    });
  </script>
  <?php endif; ?>
</html>
