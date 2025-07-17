CREATE DATABASE IF NOT EXISTS mailnotify;
USE mailnotify;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    group_level INT NOT NULL,
    department_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Leave table
CREATE TABLE leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date_start DATE NOT NULL,
    date_end DATE NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- SMTP settings table
CREATE TABLE smtp_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    host VARCHAR(100) NOT NULL,
    port INT NOT NULL,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    encryption VARCHAR(10) NOT NULL,
    from_email VARCHAR(100) NOT NULL,
    from_name VARCHAR(100) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Email templates table
CREATE TABLE email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample users
INSERT INTO users (name, email, group_level, department_id) VALUES
('John Admin', 'john@company.com', 1, 1),
('Sarah Manager', 'sarah@company.com', 4, 2),
('Mike Manager', 'mike@company.com', 4, 3),
('Lisa Supervisor', 'lisa@company.com', 2, 2),
('Tom Lead', 'tom@company.com', 3, 1);

-- Insert default SMTP settings
INSERT INTO smtp_settings (host, port, username, password, encryption, from_email, from_name) VALUES
('smtp.gmail.com', 587, 'your_email@gmail.com', 'your_password', 'tls', 'your_email@gmail.com', 'Leave Management System');

-- Insert default email template
INSERT INTO email_templates (name, subject, content) VALUES
('leave_notification', 'New Leave Request Notification', 
'<h3>New Leave Request</h3>
<p>A new leave request has been submitted:</p>
<p><strong>Employee:</strong> {employee_name}</p>
<p><strong>Email:</strong> {employee_email}</p>
<p><strong>Leave Period:</strong> {date_start} to {date_end}</p>
<p><strong>Reason:</strong> {reason}</p>
<p>Please review this request in the system.</p>
<br>
<p>Best regards,<br>Leave Management System</p>');