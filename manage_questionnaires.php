<?php
require_once 'db.php';
$fieldsArr = [];
$fieldsRes = $conn->query("SELECT id, name FROM fields");
while($f = $fieldsRes->fetch_assoc()) {
    $fieldsArr[$f['id']] = $f['name'];
}

// Fetch all programs grouped by field_id
$programsArr = [];
$programsRes = $conn->query("SELECT id, title, field_id FROM programs");
while($p = $programsRes->fetch_assoc()) {
    $programsArr[$p['field_id']][] = [
        'id' => $p['id'],
        'title' => $p['title']
    ];
}
?>
<div data-page-title="Manage Questions">
    <div class="d-flex justify-content-between mb-3">
        <h4>Questions</h4>
        <button class="btn btn-success" id="createQuestionBtn">Create Question</button>
    </div>

    <table class="datatable display table-striped" style="width:100%">
        <thead>
            <tr>
                <th>Question</th>
                <th>Field</th>
                <th>Program</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Join fields and programs to show names (assuming you have tables `fields` and `programs`)
        $sql = "SELECT 
                    q.id, 
                    q.question, 
                    f.name AS field_name, 
                    p.title AS program_name
                FROM questions q
                LEFT JOIN fields f ON q.field_id = f.id
                LEFT JOIN programs p ON q.program_id = p.id
                ORDER BY q.id DESC
";
        $result = $conn->query($sql);

        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".htmlspecialchars($row['question'])."</td>
                    <td>".htmlspecialchars($row['field_name'])."</td>
                    <td>".htmlspecialchars($row['program_name'])."</td>
                    <td>
                        <button class='btn btn-sm btn-primary editQuestionBtn' data-id='{$row['id']}'>Edit</button>
                        <button class='btn btn-sm btn-danger deleteQuestionBtn' data-id='{$row['id']}'>Delete</button>
                    </td>
                </tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<!-- Modal for Create/Edit -->
<div class="modal fade" id="questionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="questionForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="questionModalTitle">Create Question</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="questionId">
          <input type="hidden" name="action" value="save">

          <div class="mb-3">
            <label>Question</label>
            <textarea name="question" id="question" class="form-control" required></textarea>
          </div>

          <div class="mb-3">
            <label>Field</label>
            <select name="field_id" id="field_id" class="form-control" required>
                <option value="">Select Field</option>
                <?php foreach($fieldsArr as $fid => $fname): ?>
                    <option value="<?= $fid ?>"><?= htmlspecialchars($fname) ?></option>
                <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label>Program</label>
            <select name="program_id" id="program_id" class="form-control" required>
                <option value="">Select Program</option>
                <!-- Programs will be populated dynamically based on selected field -->
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
  var fields = <?php echo json_encode($fieldsArr); ?>;
var programsByField = <?php echo json_encode($programsArr); ?>;
$(document).ready(function() {

    function populatePrograms(fieldId, selectedProgramId = null) {
        $('#program_id').empty().append('<option value="">Select Program</option>');
        if (programsByField[fieldId]) {
            programsByField[fieldId].forEach(function(prog){
                let selected = prog.id == selectedProgramId ? 'selected' : '';
                $('#program_id').append('<option value="'+prog.id+'" '+selected+'>'+prog.title+'</option>');
            });
        }
    }

    // When field changes
    $('#field_id').change(function(){
        let fieldId = $(this).val();
        populatePrograms(fieldId);
    });

    // When program changes, auto-set field
    $('#program_id').change(function(){
        let programId = $(this).val();
        if(!programId) return;
        let fieldId = null;
        $.each(programsByField, function(fid, progs){
            if(progs.some(p => p.id == programId)){
                fieldId = fid;
                return false; // break loop
            }
        });
        if(fieldId) $('#field_id').val(fieldId);
    });

    // Create question
    $('#createQuestionBtn').click(function() {
        $('#questionForm')[0].reset();
        $('#questionId').val('');
        $('#questionModalTitle').text('Create Question');
        $('#program_id').empty().append('<option value="">Select Program</option>');
        $('#questionModal').modal('show');
    });

    // Edit question
    $(document).off('click', '.editQuestionBtn').on('click', '.editQuestionBtn', function() {
        let id = $(this).data('id');
        $.get('questionnaires_action.php', { action: 'get', id }, function(data) {
            $('#questionId').val(data.id);
            $('#question').val(data.question);
            $('#field_id').val(data.field_id);
            populatePrograms(data.field_id, data.program_id);
            $('#questionModalTitle').text('Edit Question');
            $('#questionModal').modal('show');
        }, 'json');
    });

    // Delete question
    $(document).off('click', '.deleteQuestionBtn').on('click', '.deleteQuestionBtn', function() {
        let id = $(this).data('id');
        if(!confirm('Are you sure you want to delete this question?')) return;
        $.post('questionnaires_action.php', { action: 'delete', id }, function(res) {
            if(res.success) location.reload();
            else alert(res.message);
        }, 'json');
    });

    // Submit form
    $('#questionForm').submit(function(e){
        e.preventDefault();
        $.post('questionnaires_action.php', $(this).serialize(), function(res) {
            if(res.success) location.reload();
            else alert(res.message);
        }, 'json');
    });

});

</script>
