<?php
require_once 'config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM email_templates WHERE id = ?");
        $stmt->execute([$id]);
        
        header("Location: email_templates.php?message=" . urlencode("Email template deleted successfully!"));
        exit;
        
    } catch (Exception $e) {
        header("Location: email_templates.php?error=" . urlencode("Error: " . $e->getMessage()));
        exit;
    }
} else {
    header("Location: email_templates.php");
    exit;
}
?>