<?php
require_once 'config/database.php';
include 'includes/header.php';

if ($_POST) {
    $name = $_POST['name'];
    $subject = $_POST['subject'];
    $content = $_POST['content'];
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    
    try {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE email_templates SET name = ?, subject = ?, content = ? WHERE id = ?");
            $stmt->execute([$name, $subject, $content, $id]);
            $message = "Email template updated successfully!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO email_templates (name, subject, content) VALUES (?, ?, ?)");
            $stmt->execute([$name, $subject, $content]);
            $message = "Email template added successfully!";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

$stmt = $pdo->query("SELECT * FROM email_templates ORDER BY name");
$templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['message'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['message']) . '</div>';
}
if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
}
if (isset($message)) {
    echo '<div class="alert alert-success">' . $message . '</div>';
}
if (isset($error)) {
    echo '<div class="alert alert-danger">' . $error . '</div>';
}
?>

<div class="row">
    <div class="col-md-8">
        <h2>Email Templates</h2>
    </div>
    <div class="col-md-4 text-right">
        <button class="btn btn-primary" data-toggle="modal" data-target="#addTemplateModal">
            <i class="fas fa-plus"></i> Add New Template
        </button>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($templates as $template): ?>
                        <tr>
                            <td><?php echo $template['id']; ?></td>
                            <td><?php echo htmlspecialchars($template['name']); ?></td>
                            <td><?php echo htmlspecialchars(substr($template['subject'], 0, 50)) . (strlen($template['subject']) > 50 ? '...' : ''); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $template['is_active'] ? 'success' : 'secondary'; ?>">
                                    <?php echo $template['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td><?php echo date('Y-m-d H:i', strtotime($template['updated_at'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-info edit-template" 
                                        data-id="<?php echo $template['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($template['name']); ?>"
                                        data-subject="<?php echo htmlspecialchars($template['subject']); ?>"
                                        data-content="<?php echo htmlspecialchars($template['content']); ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="delete_template.php?id=<?php echo $template['id']; ?>" 
                                   class="btn btn-sm btn-danger ml-1" 
                                   onclick="return confirm('Are you sure you want to delete this template?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Template Modal -->
<div class="modal fade" id="addTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Email Template</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Template Name</label>
                        <input type="text" name="name" id="add_name" class="form-control" required>
                        <small class="form-text text-muted">Internal name for the template</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Email Subject</label>
                        <input type="text" name="subject" id="add_subject" class="form-control" required>
                        <small class="form-text text-muted">You can use placeholders: {employee_name}, {employee_email}, {date_start}, {date_end}, {reason}</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Email Content</label>
                        <textarea name="content" id="add_content" class="form-control" rows="10" required></textarea>
                        <small class="form-text text-muted">HTML content with placeholders: {employee_name}, {employee_email}, {date_start}, {date_end}, {reason}</small>
                    </div>
                    
                    <div class="alert alert-info">
                        <strong>Available Placeholders:</strong><br>
                        <code>{employee_name}</code> - Employee's full name<br>
                        <code>{employee_email}</code> - Employee's email address<br>
                        <code>{date_start}</code> - Leave start date<br>
                        <code>{date_end}</code> - Leave end date<br>
                        <code>{reason}</code> - Leave reason
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Template</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Template Modal -->
<div class="modal fade" id="editTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Email Template</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Template Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                        <small class="form-text text-muted">Internal name for the template</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Email Subject</label>
                        <input type="text" name="subject" id="edit_subject" class="form-control" required>
                        <small class="form-text text-muted">You can use placeholders: {employee_name}, {employee_email}, {date_start}, {date_end}, {reason}</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Email Content</label>
                        <textarea name="content" id="edit_content" class="form-control" rows="10" required></textarea>
                        <small class="form-text text-muted">HTML content with placeholders: {employee_name}, {employee_email}, {date_start}, {date_end}, {reason}</small>
                    </div>
                    
                    <div class="alert alert-info">
                        <strong>Available Placeholders:</strong><br>
                        <code>{employee_name}</code> - Employee's full name<br>
                        <code>{employee_email}</code> - Employee's email address<br>
                        <code>{date_start}</code> - Leave start date<br>
                        <code>{date_end}</code> - Leave end date<br>
                        <code>{reason}</code> - Leave reason
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Template</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // CKEditor configuration
    var editorConfig = {
        height: 300,
        toolbar: [
            { name: 'document', items: [ 'Source', '-', 'Preview' ] },
            { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
            { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
            '/',
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ] },
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            '/',
            { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
        ]
    };
    
    // Initialize CKEditor for both add and edit forms
    CKEDITOR.replace('add_content', editorConfig);
    CKEDITOR.replace('edit_content', editorConfig);
    
    // Edit template button click
    $('.edit-template').click(function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_subject').val($(this).data('subject'));
        
        CKEDITOR.instances.edit_content.setData($(this).data('content'));
        
        $('#editTemplateModal').modal('show');
    });
    
    // Form submissions - update CKEditor content
    $('#addTemplateModal form').submit(function() {
        CKEDITOR.instances.add_content.updateElement();
    });
    
    $('#editTemplateModal form').submit(function() {
        CKEDITOR.instances.edit_content.updateElement();
    });
    
    // Reset add form when modal is closed
    $('#addTemplateModal').on('hidden.bs.modal', function() {
        $('#add_name').val('');
        $('#add_subject').val('');
        CKEDITOR.instances.add_content.setData('');
    });
});
</script>

<?php include 'includes/footer.php'; ?>