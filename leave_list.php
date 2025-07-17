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
            <form action="save_leave.php" method="POST">
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
                    <button type="submit" class="btn btn-primary">Save</button>
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
            <form action="save_leave.php" method="POST">
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
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.edit-leave').click(function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_user_id').val($(this).data('user'));
        $('#edit_date_start').val($(this).data('start'));
        $('#edit_date_end').val($(this).data('end'));
        $('#edit_status').val($(this).data('status'));
        $('#edit_reason').val($(this).data('reason'));
        $('#editLeaveModal').modal('show');
    });
});
</script>

<?php include 'includes/footer.php'; ?>