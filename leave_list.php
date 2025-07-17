<?php
require_once 'config/database.php';
include 'includes/header.php';

$stmt = $pdo->query("
    SELECT lr.*, u.name as user_name, u.email as user_email 
    FROM leave_requests lr 
    JOIN users u ON lr.user_id = u.id 
    ORDER BY lr.created_at DESC
");
$leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

$users_stmt = $pdo->query("SELECT id, name FROM users ORDER BY name");
$users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-md-8">
        <h2>Leave Requests</h2>
    </div>
    <div class="col-md-4 text-right">
        <button class="btn btn-primary" data-toggle="modal" data-target="#addLeaveModal">
            <i class="fas fa-plus"></i> Add Leave Request
        </button>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Employee</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Reason</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leaves as $leave): ?>
                <tr>
                    <td><?php echo $leave['id']; ?></td>
                    <td><?php echo htmlspecialchars($leave['user_name']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($leave['date_start'])); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($leave['date_end'])); ?></td>
                    <td>
                        <span class="badge badge-<?php 
                            switch($leave['status']) {
                                case 'approved': echo 'success'; break;
                                case 'rejected': echo 'danger'; break;
                                default: echo 'warning';
                            }
                        ?>">
                            <?php echo ucfirst($leave['status']); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars(substr($leave['reason'], 0, 50)) . (strlen($leave['reason']) > 50 ? '...' : ''); ?></td>
                    <td>
                        <button class="btn btn-sm btn-info edit-leave" 
                                data-id="<?php echo $leave['id']; ?>"
                                data-user="<?php echo $leave['user_id']; ?>"
                                data-start="<?php echo $leave['date_start']; ?>"
                                data-end="<?php echo $leave['date_end']; ?>"
                                data-status="<?php echo $leave['status']; ?>"
                                data-reason="<?php echo htmlspecialchars($leave['reason']); ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="delete_leave.php?id=<?php echo $leave['id']; ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Leave Modal -->
<div class="modal fade" id="addLeaveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Leave Request</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="save_leave.php" method="POST" id="addLeaveForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Employee</label>
                        <select name="user_id" class="form-control" required>
                            <option value="">Select Employee</option>
                            <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="date_start" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="date_end" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Reason</label>
                        <textarea name="reason" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveLeaveBtn">
                        <span class="btn-text">Save</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Processing...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Leave Modal -->
<div class="modal fade" id="editLeaveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Leave Request</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="save_leave.php" method="POST" id="editLeaveForm">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Employee</label>
                        <select name="user_id" id="edit_user_id" class="form-control" required>
                            <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="date_start" id="edit_date_start" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="date_end" id="edit_date_end" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit_status" class="form-control">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Reason</label>
                        <textarea name="reason" id="edit_reason" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="updateLeaveBtn">
                        <span class="btn-text">Update</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Processing...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit leave button functionality
    const editButtons = document.querySelectorAll('.edit-leave');
    editButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.getAttribute('data-id');
            document.getElementById('edit_user_id').value = this.getAttribute('data-user');
            document.getElementById('edit_date_start').value = this.getAttribute('data-start');
            document.getElementById('edit_date_end').value = this.getAttribute('data-end');
            document.getElementById('edit_status').value = this.getAttribute('data-status');
            document.getElementById('edit_reason').value = this.getAttribute('data-reason');
            
            // Show modal (assuming Bootstrap is available)
            const modal = new bootstrap.Modal(document.getElementById('editLeaveModal'));
            modal.show();
        });
    });
    
    // Function to show loading state
    function showLoadingState(button) {
        button.disabled = true;
        button.querySelector('.btn-text').style.display = 'none';
        button.querySelector('.btn-loading').style.display = 'inline';
    }
    
    // Function to hide loading state
    function hideLoadingState(button) {
        button.disabled = false;
        button.querySelector('.btn-text').style.display = 'inline';
        button.querySelector('.btn-loading').style.display = 'none';
    }
    
    // Handle Add Leave Form Submit
    const addLeaveForm = document.getElementById('addLeaveForm');
    if (addLeaveForm) {
        addLeaveForm.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('saveLeaveBtn');
            showLoadingState(submitBtn);
            
            // Optional: Add form validation
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                hideLoadingState(submitBtn);
                alert('Please fill all required fields');
            }
        });
    }
    
    // Handle Edit Leave Form Submit
    const editLeaveForm = document.getElementById('editLeaveForm');
    if (editLeaveForm) {
        editLeaveForm.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('updateLeaveBtn');
            showLoadingState(submitBtn);
            
            // Optional: Add form validation
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                hideLoadingState(submitBtn);
                alert('Please fill all required fields');
            }
        });
    }
    
    // Reset button state when modals are closed
    const addModal = document.getElementById('addLeaveModal');
    if (addModal) {
        addModal.addEventListener('hidden.bs.modal', function() {
            const submitBtn = document.getElementById('saveLeaveBtn');
            hideLoadingState(submitBtn);
            document.getElementById('addLeaveForm').reset();
        });
        
        addModal.addEventListener('shown.bs.modal', function() {
            const submitBtn = document.getElementById('saveLeaveBtn');
            hideLoadingState(submitBtn);
        });
    }
    
    const editModal = document.getElementById('editLeaveModal');
    if (editModal) {
        editModal.addEventListener('hidden.bs.modal', function() {
            const submitBtn = document.getElementById('updateLeaveBtn');
            hideLoadingState(submitBtn);
        });
        
        editModal.addEventListener('shown.bs.modal', function() {
            const submitBtn = document.getElementById('updateLeaveBtn');
            hideLoadingState(submitBtn);
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>