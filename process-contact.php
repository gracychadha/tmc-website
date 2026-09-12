<?php
header('Content-Type: application/json');
require_once('admin/db/config.php');

$response = ['status' => 'error', 'message' => 'Something went wrong.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Get and sanitize inputs
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    $errors = [];

    // 2. Strict Server-Side Validation
    if (empty($fname) || !preg_match("/^[a-zA-Z\s]{2,50}$/", $fname)) {
        $errors[] = "Please enter a valid first name (2-50 letters and spaces only).";
    }

    if (empty($lname) || !preg_match("/^[a-zA-Z\s]{2,50}$/", $lname)) {
        $errors[] = "Please enter a valid last name (2-50 letters and spaces only).";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (empty($phone) || !preg_match('/^\+?[0-9]{10,15}$/', $phone)) {
        $errors[] = "Please enter a valid phone number (10-15 digits).";
    }

    if (empty($message) || strlen($message) < 10) {
        $errors[] = "Message must be at least 10 characters long.";
    }

    if (empty($recaptcha_response)) {
        $errors[] = "Please complete the reCAPTCHA to prove you're not a robot.";
    }

    if (!empty($errors)) {
        $response = ['status' => 'error', 'message' => implode(" ", $errors)];
        echo json_encode($response);
        exit();
    }

    // 3. Fetch reCAPTCHA Secret Key from Database
    $captcha_query = "SELECT secretkey FROM google_captcha LIMIT 1";
    $captcha_result_db = $db->query($captcha_query);

    if ($captcha_result_db && $captcha_row = $captcha_result_db->fetch_assoc()) {
        $recaptcha_secret = trim($captcha_row['secretkey']);
        
        if (empty($recaptcha_secret)) {
            $response = ['status' => 'error', 'message' => 'reCAPTCHA secret key is missing in the database.'];
            echo json_encode($response);
            exit();
        }
    } else {
        $response = ['status' => 'error', 'message' => 'reCAPTCHA configuration error. Please contact the administrator.'];
        echo json_encode($response);
        exit();
    }

    // 4. Verify reCAPTCHA with Google
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_data = [
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($recaptcha_data)
        ]
    ];
    
    // Use cURL as a more reliable alternative to file_get_contents for server-to-server requests
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $recaptcha_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($recaptcha_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if (!$result) {
        $response = ['status' => 'error', 'message' => 'reCAPTCHA network error: ' . $curl_error];
        echo json_encode($response);
        exit();
    }

    $captcha_result = json_decode($result);

    if (!$captcha_result->success) {
        // DEBUG: Expose Google's specific error codes
        $error_codes = isset($captcha_result->{'error-codes'}) ? implode(', ', $captcha_result->{'error-codes'}) : 'Unknown error';
        $response = [
            'status' => 'error', 
            'message' => 'reCAPTCHA verification failed. Google Error: ' . $error_codes
        ];
        echo json_encode($response);
        exit();
    }

    // 5. Insert into database securely
    $fullName = trim($fname . ' ' . $lname);
    
    $stmt = $db->prepare("INSERT INTO contact (name, email, phone, message) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        $response = ['status' => 'error', 'message' => 'Database preparation error: ' . $db->error];
        echo json_encode($response);
        exit();
    }

    $stmt->bind_param("ssss", $fullName, $email, $phone, $message);

    if ($stmt->execute()) {
        $response = [
            'status' => 'success',
            'message' => "Thank you, $fname! We've received your message and will get back to you soon."
        ];
    } else {
        $response = ['status' => 'error', 'message' => 'Failed to submit. Please try again later.'];
    }

    $stmt->close();
}

echo json_encode($response);
?>