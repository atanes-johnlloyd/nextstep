<?php
include 'db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ---------- AJAX HANDLERS ----------

// Save (Create or Update)
if (isset($_POST['action']) && $_POST['action'] === 'save') {
    $id = $_POST['id'] ?? '';
    $field_id = isset($_POST['field_id']) ? intval($_POST['field_id']) : 0;
    $category = $conn->real_escape_string($_POST['category']);
    $title = $conn->real_escape_string($_POST['title']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $cert = $conn->real_escape_string($_POST['certification']);
    $desc = $conn->real_escape_string($_POST['description']);
    $careers = $conn->real_escape_string($_POST['careers']);
    $color = $conn->real_escape_string($_POST['color']);

    if ($id) {
        $conn->query("UPDATE programs 
                      SET field_id='$field_id', category='$category', title='$title', duration='$duration', 
                          certification='$cert', description='$desc', careers='$careers', colorClass='$color' 
                      WHERE id=$id");
    } else {
        $conn->query("INSERT INTO programs (field_id, category, title, duration, certification, description, careers, colorClass)
                      VALUES ('$field_id', '$category', '$title', '$duration', '$cert', '$desc', '$careers', '$color')");
        $id = $conn->insert_id;
    }

    $p = $conn->query("SELECT * FROM programs WHERE id=$id")->fetch_assoc();
    echo json_encode(['status' => 'success', 'program' => $p]);
    exit;
}

// Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($conn->query("DELETE FROM programs WHERE id=$id")) {
        echo "success";
    } else {
        echo "error: " . $conn->error;
    }
    exit;
}

// Fetch programs
$programs = $conn->query("SELECT * FROM programs ORDER BY id DESC");
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div data-page-title="Manage Programs">
  <div class="row">
    <div class="col-md-12">
      <div class="card p-3">
        <h5>Programs</h5>

        <table id="datatable" class="table table-bordered align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Field ID</th>
              <th>Category</th>
              <th>Title</th>
              <th>Duration</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while($p = $programs->fetch_assoc()): ?>
              <tr id="row-<?= $p['id'] ?>">
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['field_id']) ?></td>
                <td><?= htmlspecialchars($p['category']) ?></td>
                <td><?= htmlspecialchars($p['title']) ?></td>
                <td><?= htmlspecialchars($p['duration']) ?></td>
                <td>
                  <button class="btn btn-sm btn-primary editBtn"
                      data-id="<?= $p['id'] ?>"
                      data-field="<?= $p['field_id'] ?>"
                      data-category="<?= htmlspecialchars($p['category'], ENT_QUOTES) ?>"
                      data-title="<?= htmlspecialchars($p['title'], ENT_QUOTES) ?>"
                      data-duration="<?= htmlspecialchars($p['duration'], ENT_QUOTES) ?>"
                      data-certification="<?= htmlspecialchars($p['certification'], ENT_QUOTES) ?>"
                      data-description="<?= htmlspecialchars($p['description'], ENT_QUOTES) ?>"
                      data-careers="<?= htmlspecialchars($p['careers'], ENT_QUOTES) ?>"
                      data-color="<?= htmlspecialchars($p['colorClass'], ENT_QUOTES) ?>"
                  >Edit</button>
                  <button class="btn btn-sm btn-danger deleteBtn" data-id="<?= $p['id'] ?>">Delete</button>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

        <button id="newProgram" class="btn btn-sm btn-success mt-3">Create Program</button>

        <!-- Edit / Create Form -->
        <div id="editForm" style="display:none; margin-top:20px;">
          <h5 id="formTitle">Create / Edit Program</h5>
          <input type="hidden" id="program_id">

          <!-- Title Input -->
          <label>Title</label>
          <input type="text" id="title_input" class="form-control mb-2" placeholder="Enter program title">

          <!-- Category Dropdown -->
          <label>Category</label>
          <select id="category_input" class="form-control mb-2">
            <option value="">-- Select Category --</option>
            <?php
            $categories = $conn->query("SELECT DISTINCT category FROM programs ORDER BY category ASC");
            while ($c = $categories->fetch_assoc()):
            ?>
              <option value="<?= htmlspecialchars($c['category']) ?>"><?= htmlspecialchars($c['category']) ?></option>
            <?php endwhile; ?>
          </select>

          <!-- Duration Input -->
          <label>Duration</label>
          <input type="text" id="duration_input" class="form-control mb-2" placeholder="Enter program duration (e.g., 4 years)">

          <!-- Certification -->
          <label>Certification</label>
          <textarea id="cert_input" class="form-control mb-2"></textarea>

          <!-- Description -->
          <label>Description</label>
          <textarea id="desc_input" class="form-control mb-2"></textarea>

          <!-- Careers -->
          <label>Careers</label>
          <textarea id="careers_input" class="form-control mb-2"></textarea>

          <!-- Color Class -->
          <label>Color Class</label>
          <select id="color_input" class="form-control mb-2">
            <option value="">-- Select Color --</option>
            <?php
            $colors = $conn->query("SELECT DISTINCT colorClass FROM programs WHERE colorClass != '' ORDER BY colorClass ASC");
            while ($col = $colors->fetch_assoc()):
            ?>
              <option value="<?= htmlspecialchars($col['colorClass']) ?>"><?= htmlspecialchars($col['colorClass']) ?></option>
            <?php endwhile; ?>
          </select>

          <!-- Form Buttons -->
          <div class="d-flex justify-content-between">
            <button type="button" id="cancelForm" class="btn btn-secondary btn-sm">Cancel</button>
            <button type="button" id="saveProgram" class="btn btn-primary btn-sm">Save Program</button>
          </div>
        </div>

<script>
$(function() {
  // Create new program
  $('#newProgram').on('click', function() {
    $('#editForm').show()[0].scrollIntoView({ behavior: "smooth" });
    $('#formTitle').text('Create Program');
    $('#program_id').val('');
    $('#field_input, #category_input, #title_input, #duration_input, #cert_input, #desc_input, #careers_input, #color_input').val('');
  });

  // ✅ Edit Program (event delegation)
  $(document).on('click', '.editBtn', function() {
    $('#editForm').show()[0].scrollIntoView({ behavior: "smooth" });
    $('#formTitle').text('Edit Program');
    $('#program_id').val($(this).data('id'));
    $('#field_input').val($(this).data('field'));
    $('#category_input').val($(this).data('category'));
    $('#title_input').val($(this).data('title'));
    $('#duration_input').val($(this).data('duration'));
    $('#cert_input').val($(this).data('certification'));
    $('#desc_input').val($(this).data('description'));
    $('#careers_input').val($(this).data('careers'));
    $('#color_input').val($(this).data('color'));
  });

  // ✅ Delete Program (event delegation)
  $(document).on('click', '.deleteBtn', function() {
    const id = $(this).data('id');
    Swal.fire({
      title: "Delete this program?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!"
    }).then(result => {
      if (result.isConfirmed) {
        $.get('manage_programs.php?delete=' + id, function(response) {
          if (response.trim() === "success") {
            $('#row-' + id).fadeOut(400, function() { $(this).remove(); });
            Swal.fire("Deleted!", "Program removed.", "success");
          } else {
            Swal.fire("Error", response, "error");
          }
        });
      }
    });
  });

  // Cancel form
  $('#cancelForm').on('click', function() {
    $('#editForm').hide();
  });

  // Save (Create or Update)
  $('#saveProgram').on('click', function() {
    const data = {
      action: 'save',
      id: $('#program_id').val(),
      field_id: $('#field_input').val(),
      category: $('#category_input').val(),
      title: $('#title_input').val(),
      duration: $('#duration_input').val(),
      certification: $('#cert_input').val(),
      description: $('#desc_input').val(),
      careers: $('#careers_input').val(),
      color: $('#color_input').val()
    };

    $.post('manage_programs.php', data, function(response) {
      try {
        const res = JSON.parse(response);
        if (res.status === 'success') {
          Swal.fire('Saved!', 'Program saved successfully.', 'success').then(() => location.reload());
        } else {
          Swal.fire('Error', 'Failed to save program.', 'error');
        }
      } catch (e) {
        Swal.fire('Error', 'Invalid response from server.', 'error');
      }
    });
  });
});
</script>

<?php $conn->close(); ?>
