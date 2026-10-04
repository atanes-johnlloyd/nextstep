<?php
require_once 'db.php'; // existing mysqli connection
?>
<div data-page-title="Manage Users">
    <div class="d-flex justify-content-between mb-3">
        <h4>Users</h4>
        <button class="btn btn-success" id="createUserBtn">Create User</button>
    </div>

    <table class="datatable display table-striped" style="width:100%">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $result = $conn->query("SELECT id, name, email FROM users ORDER BY id DESC");
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".htmlspecialchars($row['name'])."</td>
                    <td>".htmlspecialchars($row['email'])."</td>
                    <td>
                        <button class='btn btn-sm btn-primary editUserBtn' data-id='{$row['id']}'>Edit</button>
                        <button class='btn btn-sm btn-danger deleteUserBtn' data-id='{$row['id']}'>Delete</button>
                    </td>
                </tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<!-- Modal for Create/Edit -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="userForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="userModalTitle">Create User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="userId">
          <input type="hidden" name="action" value="save">
          <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" id="password" class="form-control">
            <small class="text-muted">Leave blank if not changing password</small>
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

    // Create user
    $('#createUserBtn').click(function() {
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('#userModalTitle').text('Create User');
        $('#userModal').modal('show');
    });

    // Edit user
    $(document).on('click', '.editUserBtn', function() {
        let id = $(this).data('id');
        $.get('users_action.php', { action: 'get', id }, function(data) {
            $('#userId').val(data.id);
            $('#name').val(data.name);
            $('#email').val(data.email);
            $('#password').val('');
            $('#userModalTitle').text('Edit User');
            $('#userModal').modal('show');
        }, 'json');
    });

    // Delete user
    $(document).on('click', '.deleteUserBtn', function() {
        if(!confirm('Are you sure you want to delete this user?')) return;
        let id = $(this).data('id');
        $.post('users_action.php', { action: 'delete', id }, function(res) {
            if(res.success) location.reload();
            else alert(res.message);
        }, 'json');
    });

    // Submit form
    $('#userForm').submit(function(e){
        e.preventDefault();
        $.post('users_action.php', $(this).serialize(), function(res) {
            if(res.success) location.reload();
            else alert(res.message);
        }, 'json');
    });
});
</script>
