<?php
require_once 'config/database.php';
include 'includes/header.php';

if ($_POST) {
    $host = $_POST['host'];
    $port = $_POST['port'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $encryption = $_POST['encryption'];
    $from_email = $_POST['from_email'];
    $from_name = $_POST['from_name'];
    
    try {
        $stmt = $pdo->prepare("UPDATE smtp_settings SET host = ?, port = ?, username = ?, password = ?, encryption = ?, from_email = ?, from_name = ? WHERE id = 1");
        $stmt->execute([$host, $port, $username, $password, $encryption, $from_email, $from_name]);
        $message = "SMTP settings updated successfully!";
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

$stmt = $pdo->query("SELECT * FROM smtp_settings WHERE id = 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

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

<h2>SMTP Settings</h2>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>SMTP Host</label>
                        <input type="text" name="host" class="form-control" value="<?php echo htmlspecialchars($settings['host'] ?? ''); ?>" required>
                        <small class="form-text text-muted">e.g., smtp.gmail.com</small>
                    </div>
                    
                    <div class="form-group">
                        <label>SMTP Port</label>
                        <input type="number" name="port" class="form-control" value="<?php echo htmlspecialchars($settings['port'] ?? '587'); ?>" required>
                        <small class="form-text text-muted">Usually 587 for TLS or 465 for SSL</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($settings['username'] ?? ''); ?>" required>
                        <small class="form-text text-muted">Your email address</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" value="<?php echo htmlspecialchars($settings['password'] ?? ''); ?>" required>
                        <small class="form-text text-muted">Your email password or app password</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Encryption</label>
                        <select name="encryption" class="form-control" required>
                            <option value="tls" <?php echo ($settings['encryption'] ?? '') == 'tls' ? 'selected' : ''; ?>>TLS</option>
                            <option value="ssl" <?php echo ($settings['encryption'] ?? '') == 'ssl' ? 'selected' : ''; ?>>SSL</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>From Email</label>
                        <input type="email" name="from_email" class="form-control" value="<?php echo htmlspecialchars($settings['from_email'] ?? ''); ?>" required>
                        <small class="form-text text-muted">Email address that appears as sender</small>
                    </div>
                    
                    <div class="form-group">
                        <label>From Name</label>
                        <input type="text" name="from_name" class="form-control" value="<?php echo htmlspecialchars($settings['from_name'] ?? ''); ?>" required>
                        <small class="form-text text-muted">Name that appears as sender</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Help & Instructions</h5>
            </div>
            <div class="card-body">
                <h6>Gmail Setup:</h6>
                <ul>
                    <li>Host: smtp.gmail.com</li>
                    <li>Port: 587</li>
                    <li>Encryption: TLS</li>
                    <li>Use App Password instead of regular password</li>
                </ul>
                
                <h6>Outlook/Hotmail:</h6>
                <ul>
                    <li>Host: smtp-mail.outlook.com</li>
                    <li>Port: 587</li>
                    <li>Encryption: TLS</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>