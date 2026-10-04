<?php
// pages/dashboard.php
// -----------------------------
// BACKEND NOTE:
// When backend integration is ready, replace static values below with database queries.
// Example:
// $total_users = getTotalUsers();
// $avg_time = getAverageTime();
// $top_programs = getTopPrograms(); 
// etc.
// -----------------------------
?>
<div data-page-title="Admin Dashboard">

  <div class="row g-3 mb-3">

    <!-- Total Users Card -->
    <div class="col-md-3">
      <div class="kpi h-100 card p-3">
        <h6>Total Users</h6>
        <div class="h3">123456</div>
        <small class="text-muted">Completed / Pending</small>
      </div>
    </div>

    <!-- Average Time Card -->
    <div class="col-md-3">
      <div class="kpi h-100 card p-3">
        <h6>Average Time</h6>
        <div class="h3">123 mins</div>
        <small class="text-muted">Based on last 7 days</small>
      </div>
    </div>

    <!-- Top Programs Card -->
    <div class="col-md-3">
      <div class="kpi h-100 card p-3">
        <h6>Top Programs</h6>
        <ul id="top-programs-list" class="mb-0">
          <li>Loading...</li>
        </ul>
      </div>
    </div>

    <!-- Quick Links Card -->
    <div class="col-md-3">
    <div class="kpi h-100 card p-3 quick-links">
      <h6>Quick Links</h6>
      <a class="btn btn-outline-primary d-block mb-2 quick-link" data-page="manage_questionnaires.php">Questionnaires</a>
      <a class="btn btn-outline-primary d-block mb-2 quick-link" data-page="manage_programs.php">Programs</a>
      <a class="btn btn-outline-primary d-block quick-link" data-page="manage_users.php">Users</a>
    </div>
  </div>


  <div class="row g-3">
    <div class="col-md-8">
      <div class="card p-3 h-100">
        <h5>Recent Activity</h5>
        <ul>
          <li>New user <b>John Lloyd Atanes</b> completed questionnaire: <i>BS Information Technology</i></li>
          <li>New user <b>Jonnysins</b> completed questionnaire: <i>BS Nursing</i></li>
        </ul>
        <!-- BACKEND NOTE:
             Fetch recent activity dynamically from DB.
             Example query: SELECT * FROM user_activity ORDER BY date DESC LIMIT 5;
        -->
      </div>
    </div>

    <div class="col-md-4">
      <div class="card p-3 h-100">
        <h5>System Summary</h5>
        <p>Useful summary or charts can be placed here.</p>
        <!-- BACKEND NOTE:
             This section can include summary stats, charts (via Chart.js), or KPIs.
        -->
      </div>
    </div>
  </div>
</div>

<!-- jQuery: Fix Quick Links functionality -->
<script>
$(document).ready(function() {

  // Equalize KPI card heights
  function equalizeCardHeights() {
    let maxHeight = 0;
    $('.kpi').each(function() {
      $(this).css('height', 'auto');
      maxHeight = Math.max(maxHeight, $(this).outerHeight());
    });
    $('.kpi').height(maxHeight);
  }
  equalizeCardHeights();
  $(window).resize(equalizeCardHeights);

  // ✅ Quick Links click handler (uses loadPage() from admin.php)
  $(document).on('click', '.quick-link', function(e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (typeof loadPage === 'function') {
      loadPage(page);
    } else {
      console.error('loadPage() not found. Make sure admin.php is loaded first.');
    }
  });

});
// ✅ Dynamically load Top Programs from quiz_results
function loadTopPrograms() {
  $.ajax({
    url: 'get_top_programs.php',
    method: 'GET',
    dataType: 'json',
    success: function(data) {
      const list = $('#top-programs-list');
      list.empty();

      if (data.length > 0) {
        data.forEach(item => {
          list.append(`
            <li>
              ${item.program_name} 
              <small class="text-muted">(${item.total_taken} users, avg ${item.avg_percentage}%)</small>
            </li>
          `);
        });
      } else {
        list.append('<li>No data available</li>');
      }
    },
    error: function(err) {
      console.error('Error fetching top programs:', err);
      $('#top-programs-list').html('<li>Error loading data</li>');
    }
  });
}

// Run it once the page is ready
$(document).ready(function() {
  loadTopPrograms();
});

$(document).ready(function () {
  // ✅ Load Total Users
  $.ajax({
    url: 'get_user_count.php',
    method: 'GET',
    dataType: 'json',
    success: function (data) {
      $('.kpi:contains("Total Users") .h3').text(data.total_users);
    },
    error: function (err) {
      console.error('Error fetching total users:', err);
      $('.kpi:contains("Total Users") .h3').text('Error');
    }
  });

  // ✅ Load Top Programs
  $.ajax({
    url: 'get_top_programs.php',
    method: 'GET',
    dataType: 'json',
    success: function (data) {
      let list = '';
      if (Array.isArray(data) && data.length > 0) {
        data.forEach(function (p) {
          list += `<li>${p.program_name} <small class="text-muted">(${p.avg_percentage}%)</small></li>`;
        });
      } else {
        list = '<li>No data available</li>';
      }
      $('.kpi:contains("Top Programs") ul').html(list);
    },
    error: function (err) {
      console.error('Error fetching top programs:', err);
      $('.kpi:contains("Top Programs") ul').html('<li>Error loading data</li>');
    }
  });
});

</script>

<style>
.kpi {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.1);
}
.quick-links .btn {
  font-size: 0.9rem;
  text-align: left;
}
</style>