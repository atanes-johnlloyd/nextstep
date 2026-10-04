<?php
session_start();
$logged_in = isset($_SESSION['user_id']);
$user_id = $logged_in ? $_SESSION['user_id'] : 0;
$user_name = $logged_in ? $_SESSION['name'] : '';

require_once("db.php");

$has_results = false;

if ($logged_in) {
  $check = $conn->query("SELECT * FROM quiz_results WHERE user_id = $user_id LIMIT 1");
  $has_results = $check && $check->num_rows > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>NextStep</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Crimson+Text:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Freeman&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg">
  <div class="container">
    <img class="logo" src="assets/images/Logo.png">
    <a class="navbar-brand" href="index.php">NextStep</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- ✅ Centered Navigation Links -->
      <div class="navbar-center mx-auto">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="programs.php">Programs</a></li>
          <li class="nav-item"><a class="nav-link" href="#" id="result">Result</a></li>
          <li class="nav-item"><a class="nav-link" href="about_us.php">About Us</a></li>
        </ul>
      </div>

      <!-- ✅ Mobile view -->
      <ul class="navbar-nav d-md-none d-flex">
        <li class="nav-item">
          <?php if ($logged_in): ?>
            <div class="dropdown">
              <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdownMobile" data-bs-toggle="dropdown" aria-expanded="false">
                Hello, <?php echo htmlspecialchars($user_name); ?>!
              </button>
              <ul class="dropdown-menu" aria-labelledby="userDropdownMobile">
                <?php if (isset($has_results) && $has_results): ?>
                  <li><a class="dropdown-item" href="#" id="takeTheTest1">Retake Test</a></li>
                  <li><a class="dropdown-item delete-results" href="delete_results.php">Delete Results</a></li>
                <?php else: ?>
                  <li><a class="dropdown-item" href="#" id="takeTheTest1">Take Test</a></li>
                <?php endif; ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
              </ul>
            </div>
          <?php else: ?>
            <a href="#" class="btn-login open-login">Log In</a>
            <a href="#" class="btn-taketest" id="takeTheTest1">Take the Test</a>
          <?php endif; ?>
        </li>
      </ul>
    </div>

    <!-- ✅ Desktop view (right side) -->
    <div class="d-md-flex d-none align-items-center">
      <?php if ($logged_in): ?>
        <div class="dropdown">
          <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdownDesktop" data-bs-toggle="dropdown" aria-expanded="false">
            Hello, <?php echo htmlspecialchars($user_name); ?>!
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownDesktop">
            <?php if (isset($has_results) && $has_results): ?>
              <li><a class="dropdown-item" href="#" id="takeTheTest2">Retake Test</a></li>
              <li><a class="dropdown-item delete-results" href="delete_results.php">Delete Results</a></li>
            <?php else: ?>
              <li><a class="dropdown-item" href="#" id="takeTheTest2">Take Test</a></li>
            <?php endif; ?>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
          </ul>
        </div>
      <?php else: ?>
        <a href="#" class="btn-login open-login">Log In</a>
        <a href="#" class="btn-taketest" id="takeTheTest2">Take the Test</a>
      <?php endif; ?>
    </div>
  </div>
</nav>



  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-text container">
      <h1>Find The Next Step For You</h1>
      <p>
        Are you still undecided of what programs you should take when you became a college student? <br>
        Let us help you by answering our Future Program Test and find the best match for you.
      </p>
      <button class="btn btn-hero" id="startTestBtn">Start the Test</button>
    </div>
  </section>

  <!-- Steps Section -->
  <section class="steps">
    <div class="container">
      <h2>Start with simple steps</h2>
      <div class="row justify-content-center">
        <div class="col-md-3 step-box">
          <h5>Step 1</h5>
          <h4>Log In</h4>
          <p>Sign in to start your journey</p>
        </div>
        <div class="col-md-3 step-box">
          <h5>Step 2</h5>
          <h4>Answer</h4>
          <p>Complete a quick Program Test</p>
        </div>
        <div class="col-md-3 step-box">
          <h5>Step 3</h5>
          <h4>Receive</h4>
          <p>See tailored program suggestions for you</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta">
    <div class="container d-flex align-items-center justify-content-center">
      <h3>Do you want to know what programs<br>are suited for you?</h3>
      <button class="btn btn-cta" id="startTestBtn2">Start the Test</button>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div>© 2025 NextStep | Terms | Privacy Policy | Accessibility</div>
  </footer>

  <!-- Login/Register Modal -->
  <div id="loginModal" class="modal-overlay">
    <div class="modal-content" id="loginContent">
      <h2>Log In</h2>
      <p>New here? <a href="#" id="switchToRegister">Register instead</a></p>
      <form id="loginForm">
        <input type="email" name="email" class="form-control modal-input" id="loginEmail" placeholder="Email" required>
        <input type="password" name="password" class="form-control modal-input" id="loginPassword" placeholder="Password" required>
        <button type="submit" class="btn-modal-primary">Log In</button>
      </form>
      <p class="small-text"><a href="#" id="forgotPasswordLink">Forgot your password?</a></p>
    </div>

    <div class="modal-content d-none" id="registerContent">
      <h2>Register</h2>
      <p>Already have an account? <a href="#" id="switchToLogin">Login instead</a></p>
      <form id="registerForm">
        <input type="text" name="name" class="form-control modal-input" id="registerName" placeholder="Full Name" required>
        <input type="email" name="email" class="form-control modal-input" id="registerEmail" placeholder="Email" required>
        <input type="password" name="password" class="form-control modal-input" id="registerPassword" placeholder="Password" minlength="8" required>
        <small id="passwordError" class="text-danger d-none">Password must be at least 8 characters long.</small>
        <button type="submit" class="btn-modal-primary">Register</button>
      </form>
    </div>
  </div>

  <!-- Forgot Password Modal -->
<div id="forgotPasswordModal" class="modal-overlay">
  <div class="modal-content">
    <h2>Reset Password</h2>
    <p>Enter your email to receive a password reset link.</p>
    <form id="forgotPasswordForm">
      <input type="email" name="email" class="form-control modal-input" placeholder="Email" required>
      <button type="submit" class="btn-modal-primary">Send Reset Link</button>
    </form>
    <p class="small-text"><a href="#" id="backToLogin">Back to Login</a></p>
  </div>
</div>

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    const userId = <?php echo $user_id; ?>;
    
    $(document).ready(function() {
        const loggedIn = <?php echo $logged_in ? 'true' : 'false'; ?>;

        if (!loggedIn) {
            // Not logged in → keep default text
            return;
        }

        // If logged in → check result status
        $.getJSON("has_results.php", { user_id: <?php echo $user_id; ?> }, function(data) {
            if (data.success) {
                if (data.hasResults) {
                    // ✅ Already has results → Retake the Test
                    $("#startTestBtn, #startTestBtn2, #takeTheTest1, #takeTheTest2").text("Retake the Test");
                }
            }
        });
    });

    $(".delete-results").click(function(e){
        e.preventDefault(); // prevent immediate navigation
        const url = $(this).attr("href"); // get the link URL

        Swal.fire({
            title: "Are you sure?",
            text: "This will permanently delete your test results.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if(result.isConfirmed){
                // Redirect to delete_results.php
                window.location.href = url;
            }
        });
    });

    $(".open-login").click(function(e){
      e.preventDefault();
      $("#registerContent").addClass("d-none");
      $("#loginContent").removeClass("d-none");
      $("#loginModal").fadeIn(200).css("display","flex");
    });

    $(window).click(function(e){
      if ($(e.target).is("#loginModal")) $("#loginModal").fadeOut(200);
    });

    $("#switchToRegister").click(function(e){
      e.preventDefault();
      $("#loginContent").addClass("d-none");
      $("#registerContent").removeClass("d-none");
    });
    $("#switchToLogin").click(function(e){
      e.preventDefault();
      $("#registerContent").addClass("d-none");
      $("#loginContent").removeClass("d-none");
    });

    $("#startTestBtn, #startTestBtn2, #takeTheTest1, #takeTheTest2").click(function () {
    const loggedIn = <?php echo $logged_in ? 'true' : 'false'; ?>;
    if (!loggedIn) {
        Swal.fire({
            title: "Login Required",
            text: "You need to login/register before taking the test.",
            icon: "warning",
            confirmButtonText: "Log In / Register",
        }).then((result) => {
            if(result.isConfirmed){$("#loginModal").fadeIn(200).css("display","flex");}
        });
        return;
    }

    // AJAX check if user already has quiz results
    $.getJSON("has_results.php", function(data) {
        if (data.success) {
            if (data.hasResults) {
                // User already has results
                Swal.fire({
                    title: "Retake the Test",
                    text: "Are you sure you want to retake the test?",
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonText: "Yes",
                    cancelButtonText: "No"
                }).then((result) => {
                    if(result.isConfirmed){
                        // Retake: go to test
                        window.location.href = "test.php"
                    }
                });
            } else {
                // No results yet — start quiz
                Swal.fire({
                    title: "",
                    text: "You will start the Future Program Test.",
                    icon: "info",
                    confirmButtonText: "Start Now"
                }).then((result) => {
                    if(result.isConfirmed) window.location.href = "test.php";
                });
            }
        } else {
            Swal.fire("Error", "Failed to check your quiz results.", "error");
        }
    });
});


    // ✅ AJAX Login (fixed)
    $("#loginForm").submit(function(e){
      e.preventDefault();
      $.ajax({
        url: "login.php",
        type: "POST",
        dataType: "json",
        data: $(this).serialize(),
        success: function(res){
          if(res.status === "success"){
            Swal.fire("Success", res.message, "success").then(()=>{
              $("#loginModal").fadeOut(200);
              location.reload();
            });
          } else Swal.fire("Error", res.message, "error");
        },
        error: function(xhr, status, error){
          console.error("AJAX Error:", xhr.responseText);
          Swal.fire("Error", "Server error. Please check your PHP connection.", "error");
        }
      });
    });

    // ✅ AJAX Register
    $("#registerForm").submit(function(e){
      e.preventDefault();

      const password = $("#registerPassword").val();
      if (password.length < 8) {
        $("#passwordError").removeClass("d-none");
        return;
      } else {
        $("#passwordError").addClass("d-none");
      }

      $.ajax({
        url: "register.php",
        type: "POST", 
        dataType: "json",
        data: $(this).serialize(),
        success: function(res){
          if(res.status === "success"){
            Swal.fire("Success", res.message, "success").then(()=>{
              $("#registerContent").addClass("d-none");
              $("#loginContent").removeClass("d-none");
            });
          } else Swal.fire("Error", res.message, "error");
        },
        error: function(xhr, status, error){
          console.error("AJAX Error:", xhr.responseText);
          Swal.fire("Error", "Server error. Please check your PHP connection.", "error");
        }
      });
    });

    $("#forgotPasswordLink").click(function(e){
      e.preventDefault();
      $("#loginModal").fadeOut(200, function(){
        $("#forgotPasswordModal").fadeIn(200).css("display", "flex");
      });
    });

    // Go back to login
    $("#backToLogin").click(function(e){
      e.preventDefault();
      $("#forgotPasswordModal").fadeOut(200, function(){
        $("#loginModal").fadeIn(200).css("display", "flex");
      });
    });

    // Submit "Forgot Password" form via AJAX
    $("#forgotPasswordForm").submit(function(e){
      e.preventDefault();
      $.ajax({
        url: "forgot_password.php",
        type: "POST",
        dataType: "json",
        data: $(this).serialize(),
        success: function(res){
          if(res.status === "success"){
            Swal.fire("Success", res.message, "success").then(()=>{
              $("#forgotPasswordModal").fadeOut(200);
            });
          } else Swal.fire("Error", res.message, "error");
        },
        error: function(){
          Swal.fire("Error", "Something went wrong. Please try again.", "error");
        }
      });
    });
    $(window).click(function(e){
      if ($(e.target).is("#forgotPasswordModal")) $("#forgotPasswordModal").fadeOut(200);
    });

    $("#result").click(function(e){
    e.preventDefault();

    const loggedIn = <?php echo $logged_in ? 'true' : 'false'; ?>;
    const userId = <?php echo $user_id; ?>;

    if(!loggedIn){
        // Case 1: User not logged in
        Swal.fire({
            title: "Login Required",
            text: "You need to login/register to view your results.",
            icon: "warning",
            confirmButtonText: "Log In / Register",
        }).then((result) => {
            if(result.isConfirmed){$("#loginModal").fadeIn(200).css("display","flex");}
        });
        return;
    }

    // Case 2 & 3: Check if user has results
    $.getJSON("has_results.php", { user_id: userId }, function(data){
        if(data.success){
            if(data.hasResults){
                // Case 3: User has results → go to results page
                window.location.href = "test.php";
            } else {
                // Case 2: Logged in but no results yet
                Swal.fire("No Results", "You have not completed the quiz yet. Please take the quiz first.", "info");
            }
        } else {
            Swal.fire("Error", "Failed to check your quiz results. Please try again.", "error");
        }
    }).fail(function(){
        Swal.fire("Error", "Server error. Please try again later.", "error");
    });
});
  </script>
</body>
</html>
