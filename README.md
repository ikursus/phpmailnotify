# Mail Notify System

A simple PHP system for managing leave requests with email notifications.

## Features

- **User Management**: View users with different group levels
- **Leave Management**: Full CRUD operations for leave requests
- **Email Notifications**: Automatic email notifications to group level 1 users when new leave is submitted
- **SMTP Configuration**: Editable SMTP settings through web interface
- **Email Templates**: Customizable email templates with WYSIWYG editor
- **Bootstrap 4**: Clean and responsive design

## Setup Instructions

### 1. Database Setup
```sql
# Import the database schema
mysql -u root -p < database.sql
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Configure Database
Edit `config/database.php` with your database credentials:
```php
$host = 'localhost';
$dbname = 'mailnotify';
$username = 'your_username';
$password = 'your_password';
```

### 4. Configure SMTP
1. Access the system via web browser
2. Go to "SMTP Settings" page
3. Configure your SMTP server details

## File Structure

```
mailnotify/
├── config/
│   └── database.php          # Database configuration
├── includes/
│   ├── header.php           # Common header with navigation
│   ├── footer.php           # Common footer with scripts
│   └── email_functions.php  # Email sending functions
├── index.php                # Users list page
├── leave_list.php           # Leave requests management
├── save_leave.php           # Leave request save handler
├── delete_leave.php         # Leave request delete handler
├── smtp_settings.php        # SMTP configuration page
├── email_templates.php      # Email template management
├── database.sql             # Database schema and sample data
├── composer.json            # PHP dependencies
└── README.md               # This file
```

## Sample Data

The system includes 5 sample users:
- 1 user with group level 1 (Admin - receives notifications)
- 1 user with group level 2 (Supervisor)
- 1 user with group level 3 (Lead)
- 2 users with group level 4 (Managers)

## Usage

1. **View Users**: Access the main page to see all users
2. **Manage Leave**: Go to "Leave Requests" to create, edit, or delete leave requests
3. **Email Notifications**: When a new leave request is created, group level 1 users automatically receive email notifications
4. **SMTP Settings**: Configure email server settings in "SMTP Settings"
5. **Email Templates**: Customize email content in "Email Templates" with the built-in WYSIWYG editor

## Email Template Placeholders

Available placeholders for email templates:
- `{employee_name}` - Employee's full name
- `{employee_email}` - Employee's email address
- `{date_start}` - Leave start date
- `{date_end}` - Leave end date
- `{reason}` - Leave reason

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer
- Web server (Apache/Nginx)

## Technologies Used

- **Backend**: PHP with PDO
- **Frontend**: Bootstrap 4, jQuery
- **Email**: PHPMailer
- **Editor**: CKEditor 4
- **Database**: MySQL