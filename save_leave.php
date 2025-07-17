<?php
require_once 'config/database.php';
require_once 'includes/email_functions.php';

if ($_POST) {
    $user_id = $_POST['user_id'];
    $date_start = $_POST['date_start'];
    $date_end = $_POST['date_end'];
    $status = $_POST['status'];
    $reason = $_POST['reason'];
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    
    try {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE leave_requests SET user_id = ?, date_start = ?, date_end = ?, status = ?, reason = ? WHERE id = ?");
            $stmt->execute([$user_id, $date_start, $date_end, $status, $reason, $id]);
            $message = "Leave request updated successfully!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO leave_requests (user_id, date_start, date_end, status, reason) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $date_start, $date_end, $status, $reason]);
            $message = "Leave request created successfully!";
            
            sendLeaveNotification($pdo, $user_id, $date_start, $date_end, $reason);
        }
        
        header("Location: leave_list.php?message=" . urlencode($message));
        exit;
        
    } catch (Exception $e) {
        header("Location: leave_list.php?error=" . urlencode("Error: " . $e->getMessage()));
        exit;
    }
}
?>