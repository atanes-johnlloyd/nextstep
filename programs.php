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
<title>Explore Programs</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Crimson+Text:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Freeman&display=swap" rel="stylesheet">
<link rel="stylesheet" href="programs.css">
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
          <li class="nav-item"><a class="nav-link active" href="#">Programs</a></li>
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


    <section class="header">
        <div class="container">
            <h2>Explore Programs</h2>
        </div>
    </section>

  <!-- Technology & Engineering Section -->
    <section class="tech-section">
        <div class="container px-0">
            <h2 class="title"><img src="assets/images/foot.png" class="me-2 rounded-full" alt="Icon"> Technology & Engineering</h2>
            <div id="tech-cards" class="card-scroll-container">
                <!-- Cards will be injected here by JavaScript -->
            </div>
        </div>
    </section>

    <!-- Business & Management Section -->
    <section class="business-section">
        <div class="container">
            <h2 class="title"><img src="assets/images/foot.png" class="me-2 rounded-full" alt="Icon"> Business & Management</h2>
            <div id="business-cards" class="card-scroll-container">
                <!-- Cards will be injected here by JavaScript -->
            </div>
        </div>
    </section>

    <section class="health-section">
        <div class="container">
            <h2 class="title"><img src="assets/images/foot.png" class="me-2 rounded-full" alt="Icon"> Health & Medical Sciences</h2>
            <div id="health-cards" class="card-scroll-container">
                <!-- Cards will be injected here by JavaScript -->
            </div>
        </div>
    </section>

    <section class="art-section">
        <div class="container">
            <h2 class="title"><img src="assets/images/foot.png" class="me-2 rounded-full" alt="Icon"> Arts, Design & Humanities</h2>
            <div id="art-cards" class="card-scroll-container">
                <!-- Cards will be injected here by JavaScript -->
            </div>
        </div>
    </section>

    <section class="educ-section">
        <div class="container">
            <h2 class="title"><img src="assets/images/foot.png" class="me-2 rounded-full" alt="Icon"> Education & Teacher Training</h2>
            <div id="educ-cards" class="card-scroll-container">
                <!-- Cards will be injected here by JavaScript -->
            </div>
        </div>
    </section>
    <section class="social-section">
        <div class="container">
            <h2 class="title"><img src="assets/images/foot.png" class="me-2 rounded-full" alt="Icon"> Social Sciences & Psychology</h2>
            <div id="social-cards" class="card-scroll-container">
                <!-- Cards will be injected here by JavaScript -->
            </div>
        </div>
    </section>
    <section class="law-section">
        <div class="container">
            <h2 class="title"><img src="assets/images/foot.png" class="me-2 rounded-full" alt="Icon"> Law, Governance & Public Service</h2>
            <div id="law-cards" class="card-scroll-container">
                <!-- Cards will be injected here by JavaScript -->
            </div>
        </div>
    </section>
    <section class="agri-section">
        <div class="container">
            <h2 class="title"><img src="assets/images/foot.png" class="me-2 rounded-full" alt="Icon"> Agriculture, Fisheries & Environment</h2>
            <div id="agri-cards" class="card-scroll-container">
                <!-- Cards will be injected here by JavaScript -->
            </div>
        </div>
    </section>
    <section class="aviation-section">
        <div class="container">
            <h2 class="title"><img src="assets/images/foot.png" class="me-2 rounded-full" alt="Icon"> Aviation, Maritime & Transportation</h2>
            <div id="aviation-cards" class="card-scroll-container">
                <!-- Cards will be injected here by JavaScript -->
            </div>
        </div>
    </section>
    <section class="cta">
        <div class="container d-flex align-items-center justify-content-center">
            <h3>Do you want to know what programs<br>are suited for you?</h3>
            <button class="btn btn-cta" id="startTestBtn2">Start the Test</button>
        </div>
    </section>
    <footer>
        <div>© 2025 NextStep | Terms | Privacy Policy | Accessibility</div>
    </footer>

    <!-- Log In / Account Recovery Modal -->
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
</body>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    // === FETCH DATA FROM DATABASE ===
    $.getJSON("fetch_programs.php", function (programsData) {

        // === GROUP BY CATEGORY ===
        const groupedPrograms = programsData.reduce((acc, program) => {
            const { category } = program;
            if (!acc[category]) acc[category] = [];
            acc[category].push(program);
            return acc;
        }, {});

        // === CARD TEMPLATE FUNCTION ===
        function createCardHTML(program) {
            return `
                <div class="program-card ${program.colorClass}">
                    <div class="program-title">${program.title}</div>
                    <p class="program-description">${program.description}</p>
                    <p class="program-duration">${program.duration}</p>
                    <button class="btn btn-sm btn-custom" data-program-id="${program.id}">
                        View Details
                    </button>
                </div>
            `;
        }

        // === FUNCTION TO RENDER EACH CATEGORY ===
        const renderCategory = (category, containerId) => {
            const programs = groupedPrograms[category] || [];
            const html = programs.map(createCardHTML).join("");
            $(containerId).html(html);
        };

        // === RENDER ALL CATEGORIES ===
        renderCategory("Technology & Engineering", "#tech-cards");
        renderCategory("Business & Management", "#business-cards");
        renderCategory("Health & Medical Sciences", "#health-cards");
        renderCategory("Arts, Design & Humanities", "#art-cards");
        renderCategory("Education & Teacher Training", "#educ-cards");
        renderCategory("Social Sciences & Psychology", "#social-cards");
        renderCategory("Law, Governance & Public Service", "#law-cards");
        renderCategory("Agriculture, Fisheries & Environment", "#agri-cards");
        renderCategory("Aviation, Maritime & Transportation", "#aviation-cards");

        // === VIEW DETAILS HANDLER ===
        $(document).on("click", ".btn-custom", function () {
            const programId = $(this).data("program-id");
            // Redirect to ProgramInfo.php with the program ID as a GET parameter
            window.location.href = `program_info.php?id=${programId}`;
        });
    }).fail(function () {
        console.error("Failed to load data from database.");
        Swal.fire("Error", "Unable to load programs from the database.", "error");
    });

    const userId = <?php echo $user_id; ?>;

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
});

$(document).ready(function () {

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
</script>
</html>
