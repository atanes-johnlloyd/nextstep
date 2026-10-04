<?php
require_once 'db.php'; // existing mysqli connection
// Get unique categories
$categories = $conn->query("SELECT DISTINCT category FROM programs ORDER BY category");

// Get unique field IDs with their category
$fields = $conn->query("SELECT DISTINCT field_id, category FROM programs ORDER BY field_id");

// Get unique color classes
$colors = $conn->query("SELECT DISTINCT colorClass FROM programs ORDER BY colorClass");

$categoryFieldMap = [];
$result = $conn->query("SELECT DISTINCT category, field_id FROM programs");
while($row = $result->fetch_assoc()){
    $categoryFieldMap[$row['category']] = $row['field_id'];
}
?>
<div data-page-title="Manage Programs">
    <div class="d-flex justify-content-between mb-3">
        <h4>Programs</h4>
        <button class="btn btn-success" id="createProgramBtn">Create Program</button>
    </div>

    <table class="datatable display table-striped" style="width:100%">
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Field ID</th>
                <th>Duration</th>
                <th>Certification</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $result = $conn->query("SELECT * FROM programs ORDER BY id DESC");
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".htmlspecialchars($row['title'])."</td>
                    <td>".htmlspecialchars($row['category'])."</td>
                    <td>".htmlspecialchars($row['field_id'])."</td>
                    <td>".htmlspecialchars($row['duration'])."</td>
                    <td>".htmlspecialchars($row['certification'])."</td>
                    <td>
                        <button class='btn btn-sm btn-primary editProgramBtn' data-id='{$row['id']}'>Edit</button>
                        <button class='btn btn-sm btn-danger deleteProgramBtn' data-id='{$row['id']}'>Delete</button>
                    </td>
                </tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<!-- Modal for Create/Edit -->
<div class="modal fade" id="programModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="programForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="programModalTitle">Create Program</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="programId">
          <input type="hidden" name="action" value="save">

          <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Category</label>
              <select name="category" id="category" class="form-control" required>
                  <option value="">Select Category</option>
                  <?php foreach($categoryFieldMap as $cat => $fid): ?>
                      <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                  <?php endforeach; ?>
              </select>
          </div>

          <div class="mb-3">
            <label>Field ID</label>
            <input type="number" name="field_id" id="field_id" class="form-control" readonly>
          </div>

          <div class="mb-3">
            <label>Duration</label>
            <input type="text" name="duration" id="duration" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Certification</label>
            <textarea name="certification" id="certification" class="form-control"></textarea>
          </div>

          <div class="mb-3">
            <label>Description</label>
            <textarea name="description" id="description" class="form-control"></textarea>
          </div>

          <div class="mb-3">
            <label>Careers</label>
            <textarea name="careers" id="careers" class="form-control"></textarea>
          </div>

          <div class="mb-3">
            <label>Color Class</label>
            <select name="colorClass" id="colorClass" class="form-control" required>
                <option value="">Select Color Class</option>
                <?php while($row = $colors->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['colorClass']) ?>"><?= htmlspecialchars($row['colorClass']) ?></option>
                <?php endwhile; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
$(document).ready(function() {

    // Create program
    $('#createProgramBtn').click(function() {
        $('#programForm')[0].reset();
        $('#programId').val('');
        $('#programModalTitle').text('Create Program');
        $('#programModal').modal('show');
    });

    // Edit program
    $(document).on('click', '.editProgramBtn', function() {
        let id = $(this).data('id');
        $.get('programs_action.php', { action: 'get', id }, function(data) {
            $('#programId').val(data.id);
            $('#title').val(data.title);
            $('#category').val(data.category);
            $('#field_id').val(data.field_id);
            $('#duration').val(data.duration);
            $('#certification').val(data.certification);
            $('#description').val(data.description);
            $('#careers').val(data.careers);
            $('#colorClass').val(data.colorClass);
            $('#programModalTitle').text('Edit Program');
            $('#programModal').modal('show');
        }, 'json');
    });

    // Delete program
    $(document).on('click', '.deleteProgramBtn', function() {
        if(!confirm('Are you sure you want to delete this program?')) return;
        let id = $(this).data('id');
        $.post('programs_action.php', { action: 'delete', id }, function(res) {
            if(res.success) location.reload();
            else alert(res.message);
        }, 'json');
    });

    // Submit form
    $('#programForm').submit(function(e){
        e.preventDefault();
        $.post('programs_action.php', $(this).serialize(), function(res) {
            if(res.success) location.reload();
            else alert(res.message);
        }, 'json');
    });
});
const categoryToFields = {};
<?php
// Build JS mapping of category → list of field_ids
$fields = $conn->query("SELECT DISTINCT field_id, category FROM programs");
while($row = $fields->fetch_assoc()) {
    echo "categoryToFields['" . addslashes($row['category']) . "'] = categoryToFields['" . addslashes($row['category']) . "'] || [];\n";
    echo "categoryToFields['" . addslashes($row['category']) . "'].push(" . $row['field_id'] . ");\n";
}
?>

$('#category').change(function(){
    const cat = $(this).val();
    const fieldSelect = $('#field_id');
    fieldSelect.empty();

    if(categoryToFields[cat]){
        categoryToFields[cat].forEach(function(fid){
            fieldSelect.append('<option value="'+fid+'">'+fid+'</option>');
        });
    }
});
const categoryToField = <?php echo json_encode($categoryFieldMap); ?>;

$('#category').change(function(){
    const cat = $(this).val();
    $('#field_id').val(categoryToField[cat] || '');
});


</script>
