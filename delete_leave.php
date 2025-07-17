<?php
require_once 'config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM leave_requests WHERE id = ?");
        $stmt->execute([$id]);
        
        header("Location: leave_list.php?message=" . urlencode("Leave request deleted successfully!"));
        exit;
        
    } catch (Exception $e) {
        header("Location: leave_list.php?error=" . urlencode("Error: " . $e->getMessage()));
        exit;
    }
} else {
    header("Location: leave_list.php");
    exit;
}
?>