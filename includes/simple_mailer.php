<?php
class SimpleMailer {
    private $host;
    private $port;
    private $username;
    private $password;
    private $encryption;
    private $fromEmail;
    private $fromName;
    
    public function __construct($config) {
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->encryption = $config['encryption'];
        $this->fromEmail = $config['from_email'];
        $this->fromName = $config['from_name'];
    }
    
    public function send($to, $subject, $body, $isHtml = true) {
        try {
            $headers = "From: {$this->fromName} <{$this->fromEmail}>\r\n";
            $headers .= "Reply-To: {$this->fromEmail}\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
            
            if ($isHtml) {
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            }
            
            if (is_array($to)) {
                $success = true;
                foreach ($to as $email => $name) {
                    $toHeader = is_numeric($email) ? $name : "$name <$email>";
                    if (!mail($toHeader, $subject, $body, $headers)) {
                        $success = false;
                    }
                }
                return $success;
            } else {
                return mail($to, $subject, $body, $headers);
            }
            
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }
}
?>