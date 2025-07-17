<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function getSmtpSettings($pdo) {
    $stmt = $pdo->query("SELECT * FROM smtp_settings WHERE is_active = 1 LIMIT 1");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getEmailTemplate($pdo, $templateName) {
    $stmt = $pdo->prepare("SELECT * FROM email_templates WHERE name = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$templateName]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getGroupLevelUsers($pdo, $groupLevel) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE group_level = ?");
    $stmt->execute([$groupLevel]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function sendLeaveNotification($pdo, $userId, $dateStart, $dateEnd, $reason) {
    try {
        $userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $userStmt->execute([$userId]);
        $employee = $userStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$employee) {
            throw new Exception("Employee not found");
        }
        
        $groupLevel1Users = getGroupLevelUsers($pdo, 1);
        
        if (empty($groupLevel1Users)) {
            throw new Exception("No group level 1 users found for notification");
        }
        
        $smtpSettings = getSmtpSettings($pdo);
        if (!$smtpSettings) {
            throw new Exception("SMTP settings not configured");
        }
        
        $template = getEmailTemplate($pdo, 'leave_notification');
        if (!$template) {
            throw new Exception("Email template not found");
        }
        
        $mail = new PHPMailer(true);
        
        $mail->isSMTP();
        $mail->Host = $smtpSettings['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtpSettings['username'];
        $mail->Password = $smtpSettings['password'];
        $mail->SMTPSecure = $smtpSettings['encryption'];
        $mail->Port = $smtpSettings['port'];
        
        $mail->setFrom($smtpSettings['from_email'], $smtpSettings['from_name']);
        
        $subject = str_replace(
            ['{employee_name}', '{employee_email}', '{date_start}', '{date_end}', '{reason}'],
            [$employee['name'], $employee['email'], $dateStart, $dateEnd, $reason],
            $template['subject']
        );
        
        $content = str_replace(
            ['{employee_name}', '{employee_email}', '{date_start}', '{date_end}', '{reason}'],
            [$employee['name'], $employee['email'], $dateStart, $dateEnd, $reason],
            $template['content']
        );
        
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $content;
        
        foreach ($groupLevel1Users as $user) {
            $mail->addAddress($user['email'], $user['name']);
        }
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}
?>