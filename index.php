<?php
require_once 'config/database.php';
include 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM users ORDER BY group_level, name");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Users List</h2>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Group Level</th>
                            <th>Department ID</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge badge-<?php 
                                    switch($user['group_level']) {
                                        case 1: echo 'danger'; break;
                                        case 2: echo 'warning'; break;
                                        case 3: echo 'info'; break;
                                        case 4: echo 'success'; break;
                                        default: echo 'secondary';
                                    }
                                ?>">
                                    Level <?php echo $user['group_level']; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($user['department_id']); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>