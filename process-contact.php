<?php
header('Content-Type: application/json');

require_once('admin/db/config.php');

$response = ['status' => 'error', 'message' => 'Something went wrong.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize inputs
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $message = trim($_POST['message'] ?? '');

    // Combine first and last name
    $fullName = $fname . ' ' . $lname;

    // Basic validation
    if (!empty($fullName) && !empty($email) && !empty($phone)) {
        
        // Prepare SQL statement
        $stmt = $db->prepare("INSERT INTO contact (name, email, phone, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fullName, $email, $phone, $message);
        
        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Message sent successfully! We will contact you soon.'];
        } else {
            $response = ['status' => 'error', 'message' => 'Failed to save message. Please try again.'];
        }
        $stmt->close();
    } else {
        $response = ['status' => 'error', 'message' => 'Please fill all required fields.'];
    }
}

echo json_encode($response);
?>