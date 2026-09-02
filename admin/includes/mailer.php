<?php
// Reusable mailer helper using SMTP credentials from the email_config table.
// Requires: admin/db/config.php already loaded ($db available).

require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send an email using the SMTP settings stored in email_config.
 *
 * @param string $to      Recipient email
 * @param string $subject Email subject
 * @param string $html    HTML body
 * @param string $alt     (optional) Plain-text body
 * @return bool|string    true on success, error message string on failure
 */
function send_tmc_email($to, $subject, $html, $alt = '')
{
    global $db;

    $sql = "SELECT hosts, user_email, password, port, status FROM email_config LIMIT 1";
    $result = $db->query($sql);
    if (!$result || $result->num_rows == 0) {
        return "SMTP settings not configured.";
    }
    $smtp = $result->fetch_assoc();

    if (!isset($smtp['status']) || $smtp['status'] != '1') {
        return "SMTP is disabled in settings.";
    }

    $host = $smtp['hosts'];
    $username = $smtp['user_email'];
    $password = $smtp['password'];
    $port = (int)$smtp['port'];

    $secure = 'tls';
    if ($port == 465) {
        $secure = 'ssl';
    }

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
         $mail->SMTPSecure = $secure;
        $mail->Port = $port;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($username, 'Tee Mac Corporation');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html;
        $mail->AltBody = $alt !== '' ? $alt : strip_tags($html);

        $mail->send();
        return true;
    } catch (Exception $e) {
        return isset($mail) ? $mail->ErrorInfo : $e->getMessage();
    }
}

/**
 * Send login credentials to a newly created / reset team member account.
 *
 * @param string $to       Recipient email
 * @param string $name     Team member name
 * @param string $username Admin username
 * @param string $password Plain text password
 * @return bool|string
 */
function send_credentials_email($to, $name, $username, $password)
{
    $loginUrl = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';

    $subject = "Your Tee Mac Corporation Admin Login Credentials";
    $html = "
        <div style='font-family:Arial,Helvetica,sans-serif;max-width:600px;margin:0 auto;border:1px solid #e5e5e5;border-radius:8px;overflow:hidden;'>
            <div style='background:#0a0a1a;padding:20px;text-align:center;'>
                <h2 style='color:#d4af37;margin:0;'>Tee Mac Corporation</h2>
            </div>
            <div style='padding:24px;'>
                <p>Dear " . htmlspecialchars($name) . ",</p>
                <p>An admin account has been created for you. Use the following credentials to login to the admin panel:</p>
                <table style='width:100%;border-collapse:collapse;margin:20px 0;'>
                    <tr><td style='padding:8px;background:#f5f5f5;'>Login URL</td><td style='padding:8px;'><a href='" . $loginUrl . "'>" . $loginUrl . "</a></td></tr>
                    <tr><td style='padding:8px;background:#f5f5f5;'>Email</td><td style='padding:8px;'>" . htmlspecialchars($to) . "</td></tr>
                    <tr><td style='padding:8px;background:#f5f5f5;'>Username</td><td style='padding:8px;'>" . htmlspecialchars($username) . "</td></tr>
                    <tr><td style='padding:8px;background:#f5f5f5;'>Password</td><td style='padding:8px;'><strong>" . htmlspecialchars($password) . "</strong></td></tr>
                </table>
                <p>For security reasons, please change your password after your first login.</p>
                <p>Regards,<br>Admin Team<br>Tee Mac Corporation</p>
            </div>
        </div>";

    $alt = "Dear $name,\n\nYour Tee Mac Corporation admin login credentials:\nLogin URL: $loginUrl\nEmail: $to\nUsername: $username\nPassword: $password\n\nPlease change your password after your first login.\n\nRegards,\nAdmin Team\nTee Mac Corporation";

    return send_tmc_email($to, $subject, $html, $alt);
}

/**
 * Generate a strong random password.
 */
function generate_password($length = 12)
{
    $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lower = 'abcdefghijklmnopqrstuvwxyz';
    $digits = '0123456789';
    $symbols = '@#$%&*!';
    $all = $upper . $lower . $digits . $symbols;
    $password = $upper[random_int(0, strlen($upper) - 1)]
        . $lower[random_int(0, strlen($lower) - 1)]
        . $digits[random_int(0, strlen($digits) - 1)]
        . $symbols[random_int(0, strlen($symbols) - 1)];
    for ($i = 4; $i < $length; $i++) {
        $password .= $all[random_int(0, strlen($all) - 1)];
    }
    return str_shuffle($password);
}