<?php
require_once 'config/database.php';
include 'includes/header.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;
$template = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM email_templates WHERE id = ?");
    $stmt->execute([$id]);
    $template = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$template) {
        header("Location: email_templates.php?error=" . urlencode("Template not found"));
        exit;
    }
}

if ($_POST) {
    $name = $_POST['name'];
    $subject = $_POST['subject'];
    $content = $_POST['content'];
    
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
        
        header("Location: email_templates.php?message=" . urlencode($message));
        exit;
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
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
        <h2><?php echo $id ? 'Edit' : 'Add'; ?> Email Template</h2>
    </div>
    <div class="col-md-4 text-right">
        <a href="email_templates.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Templates
        </a>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Template Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" 
                               value="<?php echo htmlspecialchars($template['name'] ?? ''); ?>" required>
                        <small class="form-text text-muted">Internal name for the template</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Email Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" 
                               value="<?php echo htmlspecialchars($template['subject'] ?? ''); ?>" required>
                        <small class="form-text text-muted">You can use placeholders: {employee_name}, {employee_email}, {date_start}, {date_end}, {reason}</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Email Content <span class="text-danger">*</span></label>
                        <textarea name="content" id="template_content" class="form-control" rows="15" required><?php echo htmlspecialchars($template['content'] ?? ''); ?></textarea>
                        <small class="form-text text-muted">HTML content with placeholders: {employee_name}, {employee_email}, {date_start}, {date_end}, {reason}</small>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $id ? 'Update' : 'Add'; ?> Template
                        </button>
                        <a href="email_templates.php" class="btn btn-secondary ml-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h5>Available Placeholders</h5>
            </div>
            <div class="card-body">
                <p><strong>Use these placeholders in your subject and content:</strong></p>
                <ul class="list-unstyled">
                    <li><code>{employee_name}</code><br><small class="text-muted">Employee's full name</small></li>
                    <li class="mt-2"><code>{employee_email}</code><br><small class="text-muted">Employee's email address</small></li>
                    <li class="mt-2"><code>{date_start}</code><br><small class="text-muted">Leave start date</small></li>
                    <li class="mt-2"><code>{date_end}</code><br><small class="text-muted">Leave end date</small></li>
                    <li class="mt-2"><code>{reason}</code><br><small class="text-muted">Leave reason</small></li>
                </ul>
                
                <hr>
                <h6>Example Subject:</h6>
                <code>New Leave Request from {employee_name}</code>
                
                <h6 class="mt-3">Example Content:</h6>
                <code>&lt;p&gt;Hello,&lt;/p&gt;
&lt;p&gt;{employee_name} has submitted a leave request.&lt;/p&gt;
&lt;p&gt;&lt;strong&gt;Period:&lt;/strong&gt; {date_start} to {date_end}&lt;/p&gt;</code>
            </div>
        </div>
    </div>
</div>

<script>
// Wait for CKEditor to be fully loaded
window.addEventListener('load', function() {
    console.log('Page loaded, initializing CKEditor...');
    
    // Check if CKEDITOR is available
    if (typeof CKEDITOR !== 'undefined') {
        console.log('CKEditor is available');
        
        // Initialize CKEditor with a simpler configuration
        CKEDITOR.replace('template_content', {
            height: 400,
            removePlugins: 'elementspath',
            resize_enabled: false,
            toolbar: 'Full',
            toolbarGroups: [
                { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
                { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
                { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ] },
                { name: 'forms' },
                '/',
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
                { name: 'links' },
                { name: 'insert' },
                '/',
                { name: 'styles' },
                { name: 'colors' },
                { name: 'tools' },
                { name: 'others' }
            ]
        });
        
        console.log('CKEditor initialized');
    } else {
        console.error('CKEditor not loaded!');
        alert('CKEditor failed to load. Please refresh the page.');
    }
});

// Update CKEditor content before form submission
$(document).ready(function() {
    $('form').submit(function(e) {
        console.log('Form submitting...');
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.template_content) {
            CKEDITOR.instances.template_content.updateElement();
            console.log('CKEditor content updated');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>