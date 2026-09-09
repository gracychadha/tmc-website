<?php
header('Content-Type: application/json');
require_once('admin/db/config.php');


$response = ['status' => 'error', 'message' => 'Something went wrong.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize the email input (note: form uses name="mail")
    $email = filter_var(trim($_POST['mail'] ?? ''), FILTER_SANITIZE_EMAIL);

    // Validate email format
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        
        // Optional: Check if email already exists
        $checkStmt = $db->prepare("SELECT idsubscribers FROM subscribers WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            $response = ['status' => 'error', 'message' => 'This email is already subscribed!'];
        } else {
            // Insert new subscriber
            $stmt = $db->prepare("INSERT INTO subscribers (email) VALUES (?)");
            $stmt->bind_param("s", $email);
            
            if ($stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'Thank you for subscribing!'];
            } else {
                $response = ['status' => 'error', 'message' => 'Failed to subscribe. Please try again.'];
            }
            $stmt->close();
        }
        $checkStmt->close();
    } else {
        $response = ['status' => 'error', 'message' => 'Please enter a valid email address.'];
    }
}

echo json_encode($response);
?>