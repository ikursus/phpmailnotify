# Installation Guide

## Method 1: Using Built-in Simple Mailer (Current Setup)

The system is currently configured to use a simple mailer class that works with PHP's built-in `mail()` function.

### Requirements:
- PHP 7.4+ with mail() function enabled
- MySQL 5.7+
- Web server (Apache/Nginx)

### Setup Steps:
1. **Import Database:**
   ```bash
   mysql -u root -p < database.sql
   ```

2. **Configure Database:**
   Edit `config/database.php` with your credentials

3. **Configure Web Server:**
   Point your web server to the mailnotify directory

4. **Configure SMTP:**
   - Access the system via browser
   - Go to "SMTP Settings"
   - Enter your email settings

## Method 2: Using Full PHPMailer (Recommended for Production)

For better email delivery and advanced features, you can install the full PHPMailer.

### Installation:
```bash
# Make sure PHP and Composer are installed
composer install
```

### Switch to PHPMailer:
1. **Replace the email functions file:**
   ```php
   // In includes/email_functions.php, replace the first few lines:
   <?php
   use PHPMailer\PHPMailer\PHPMailer;
   use PHPMailer\PHPMailer\SMTP;
   use PHPMailer\PHPMailer\Exception;
   
   require_once __DIR__ . '/../vendor/autoload.php';
   ```

2. **Update the sendLeaveNotification function:**
   ```php
   // Replace SimpleMailer code with PHPMailer code:
   $mail = new PHPMailer(true);
   $mail->isSMTP();
   $mail->Host = $smtpSettings['host'];
   $mail->SMTPAuth = true;
   $mail->Username = $smtpSettings['username'];
   $mail->Password = $smtpSettings['password'];
   $mail->SMTPSecure = $smtpSettings['encryption'];
   $mail->Port = $smtpSettings['port'];
   $mail->setFrom($smtpSettings['from_email'], $smtpSettings['from_name']);
   
   foreach ($groupLevel1Users as $user) {
       $mail->addAddress($user['email'], $user['name']);
   }
   
   $mail->Subject = $subject;
   $mail->isHTML(true);
   $mail->Body = $content;
   $mail->send();
   ```

## Email Configuration Examples:

### Gmail:
- Host: smtp.gmail.com
- Port: 587
- Encryption: TLS
- Use App Password (not regular password)

### Outlook/Hotmail:
- Host: smtp-mail.outlook.com
- Port: 587
- Encryption: TLS

### Other Providers:
- Check your email provider's SMTP settings
- Most use port 587 with TLS encryption

## Troubleshooting:

### Email Not Sending:
1. Check SMTP settings are correct
2. Verify PHP mail() function is enabled
3. Check server logs for errors
4. Test with a simple email first

### Database Connection Issues:
1. Verify MySQL credentials in config/database.php
2. Ensure database exists and tables are created
3. Check MySQL service is running

### Permission Issues:
1. Ensure web server has read access to files
2. Check file permissions (644 for files, 755 for directories)