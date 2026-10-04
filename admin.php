<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>NextStep — Admin</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- DataTables CSS -->
   <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Crimson+Text:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Freeman&display=swap" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="admin.css" rel="stylesheet">
</head>
<body>
  <div class="admin-wrap d-flex">
    <!-- left nav -->
    <aside class="sidebar bg-deep px-3 py-4">
      <div class="brand mb-4 d-flex">
        <div>
          <img class="logo" src="assets/images/Logo2.png">
        </div>
        
        <a class="navbar-brand" href="index.php">NextStep</a>
      </div>
      <nav class="nav flex-column" id="sideNav">
        <a href="#" class="nav-link nav-item active" data-page="dashboard.php">Dashboard</a>
        <a href="#" class="nav-link nav-item" data-page="manage_questionnaires.php">Manage Questionnaires</a>
        <a href="#" class="nav-link nav-item" data-page="manage_programs.php">Manage Programs</a>
        <a href="#" class="nav-link nav-item" data-page="manage_users.php">Manage Users</a>
      </nav>
    </aside>

    <!-- main content -->
    <main class="content-area p-4">
      <header class="d-flex justify-content-between align-items-center mb-3">
        <h2 id="pageTitle">Admin Dashboard</h2>
        <div><a id="logoutBtn" href="#" class="text-decoration-none">Log out</a></div>
      </header>

      <div id="contentArea">
        <!-- initial content (dashboard) will be loaded by JS via AJAX -->
        <div class="loading-placeholder">Loading...</div>
      </div>
    </main>
  </div>

  <!-- dependencies -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/app.js"></script>
</body>
<script>
$(function(){
  // load default page
  loadPage('dashboard.php');

  // nav click
  $('#sideNav .nav-link').on('click', function(e){
    e.preventDefault();
    $('#sideNav .nav-link').removeClass('active');
    $(this).addClass('active');
    const page = $(this).data('page');
    loadPage(page);
  });

  // logout
  $('#logoutBtn').on('click', function(e){
    e.preventDefault();
    $.post('admin_logout.php', function(){
        window.location.href = 'admin_login.php';
    });
  });

  // quick links inside loaded pages
    // quick links inside loaded pages
  $(document).on('click', '.quick-link', function(e){
    e.preventDefault();
    const page = $(this).data('page');
    loadPage(page);

    // update sidebar highlight if the link corresponds to a sidebar page
    $('#sideNav .nav-link').removeClass('active');
    $('#sideNav .nav-link').each(function(){
      if ($(this).data('page') === page) {
        $(this).addClass('active');
      }
    });
  });
});

// function to dynamically load pages
function loadPage(url){
  $('#contentArea').html('<div class="loading-placeholder">Loading...</div>');

  $.get(url).done(function(html){
      $('#contentArea').html(html);

      const title = $('#contentArea').find('[data-page-title]').data('page-title');
      $('#pageTitle').text(title || 'Admin');

      // Initialize DataTables
      $('#contentArea').find('.datatable').each(function(){
          if ($.fn.DataTable.isDataTable(this)) {
              $(this).DataTable().destroy();
          }
          $(this).DataTable({
              "pageLength": 10,
              "lengthChange": true,
              "searching": true,
              "ordering": true
          });
      });
  }).fail(function(){
      $('#contentArea').html('<div class="alert alert-danger">Failed to load page.</div>');
  });
}
</script>

</html>