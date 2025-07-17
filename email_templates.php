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
        <a href="edit_template.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Template
        </a>
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
                                <a href="edit_template.php?id=<?php echo $template['id']; ?>" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
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


<?php include 'includes/footer.php'; ?>