<?php
session_start();
error_reporting(E_ALL); // Enable error reporting for debugging
ini_set('display_errors', 1);

require("db/config.php");

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}


// Function to sanitize input data
function sanitize_input($data)
{
    $data = trim($data); // Remove leading and trailing whitespaces
    $data = stripslashes($data); // Remove backslashes
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); // Convert special characters to HTML entities
    return $data;
}

// Function to validate email address
function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize user inputs
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $mobile = mysqli_real_escape_string($db, $_POST['mobile']);
    $user_type = mysqli_real_escape_string($db, $_POST['user_type']);
    $activationToken = bin2hex(random_bytes(16)); // Generate activation token

    $captchaResponse = $_POST['cf-turnstile-response'];
    $secretKey = "0x4AAAAAAA4eC24IkKM0feSRZyV3pLFyz4w"; // Replace with your Secret Key

    // Validate CAPTCHA response with Cloudflare
    $verifyUrl = "https://challenges.cloudflare.com/turnstile/v0/siteverify";
    $data = [
        'secret' => $secretKey,
        'response' => $captchaResponse,
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($verifyUrl, false, $context);
    $response = json_decode($result, true);

    if (!$response['success']) {
        $_SESSION['msg'] = "CAPTCHA verification failed.";
        $_SESSION['msg_type'] = 'error'; // Message type as error
        header("location:admin-register.php");
        exit();
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['msg'] = "Passwords do not match.";
        $_SESSION['msg_type'] = 'error'; // Message type as error
        header("location:admin-register.php");
        exit();
    }

    date_default_timezone_set("Asia/Kolkata"); // Set the timezone
    $current_time = time();
    $current_datetime = date('Y-m-d H:i:s', $current_time);
    $expire_time = $current_time + 15 * 60; // Token expiration time

    // Check if the email already exists
    $stmt = $db->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = mysqli_num_rows($result);
    if ($row > 0) {
        $_SESSION['msg'] = "Email already exists. Please use a different email.";
        $_SESSION['msg_type'] = 'error'; // Message type as error
        header("location:admin-register.php");
        exit();
    }

    $hash = password_hash($password, PASSWORD_BCRYPT); // Hash the password
    $status = "Enable";

    // Insert the data into the database
    $stmt = $db->prepare("INSERT INTO admin (username, email, phone, password, date, type, activation, status, expire_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssssss', $username, $email, $mobile, $hash, $current_datetime, $user_type, $activationToken, $status, $expire_time);
    if ($stmt->execute()) {
        $_SESSION['msg'] = "Registration successful! You can now log in.";
        $_SESSION['msg_type'] = 'success'; // Message type as success
        header("location:admin-index.php");
    } else {
        $_SESSION['msg'] = "Error during registration: " . $stmt->error; // Specific error message
        $_SESSION['msg_type'] = 'error'; // Message type as error
        header("location:admin-register.php");
    }
    exit;
}

// SQL query with a prepared statement
$sqlfav = "SELECT favicon FROM system_setting LIMIT 1";

if ($stmt = $db->prepare($sqlfav)) {
    // Execute the statement
    $stmt->execute();

    // Bind the result to a variable
    $stmt->bind_result($favicon);

    // Fetch the result
    if ($stmt->fetch()) {
        $faviconPath = "logo/" . $favicon; // Build the full path
        
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="keywords" content="admin, estimates, bootstrap, business, html5, responsive, Projects">
    <meta name="author" content="Dreams technologies - Bootstrap Admin Template">
    <meta name="robots" content="noindex, nofollow">
    <title>Register - Tee Mac Corporation</title>
     <!-- Favicon -->
     <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Feather CSS -->
    <link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">
    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Cloudflare Turnstile CSS -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js" integrity="sha512-ykZ1QQr0Jy/4ZkvKuqWn4iF3lqPZyij9iRv6sGqLRdTPkY69YX6+7wvVGmsdBbiIfN/8OdsI7HABjvEok6ZopQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body class="account-page">
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div class="container-fuild">
            <div class="login-wrapper w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="d-lg-flex align-items-center justify-content-center bg-light-300 d-lg-block d-none flex-wrap vh-100 overflowy-auto">
                           
                                <img src="assets/img/login-page.png" alt="Img">
                            
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-12 col-sm-12">
                        <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap ">
                            <div class="col-md-8 mx-auto p-4">
                                <form action="admin-register.php" method="POST" id="signupForm">
                                    <div>
                                        <div class=" mx-auto mb-3 text-center">
                                            <img src="assets/img/authentication/authentication-logo.svg" class="img-fluid " style="width: 80%;" alt="Logo">
                                        </div>
                                        <div class="card">
                                            <div class="card-body p-4">
                                                <div class=" mb-4">
                                                    <h2 class="mb-2">Register</h2>
                                                    <p class="mb-0">Please enter your details to sign up</p>
                                                </div>

                                                <?php
                                                if (isset($_SESSION['msg'])) {
                                                    $message = $_SESSION['msg'];
                                                    $message_type = $_SESSION['msg_type']; // Get the message type (success/error)

                                                    // Display the message
                                                    echo "<div class='alert alert-$message_type alert-dismissible fade show' id='alert-message'>
                                                        $message
                                                    </div>";

                                                    // Unset session message after displaying
                                                    unset($_SESSION['msg']);
                                                    unset($_SESSION['msg_type']);
                                                }
                                                ?>

                                                <div class="mt-4">
                                                    <div class="mb-3 ">
                                                        <label class="form-label">Name</label>
                                                        <div class="input-icon mb-3 position-relative">
                                                            <span class="input-icon-addon">
                                                                <i class="ti ti-user"></i>
                                                            </span>
                                                            <input type="text" name="username" class="form-control" required>
                                                        </div>
                                                        <label class="form-label">Email Address</label>
                                                        <div class="input-icon mb-3 position-relative">
                                                            <span class="input-icon-addon">
                                                                <i class="ti ti-mail"></i>
                                                            </span>
                                                            <input type="email" name="email" class="form-control" required>
                                                        </div>
                                                        <label class="form-label">Phone</label>
                                                        <div class="input-icon mb-3 position-relative">
                                                            <span class="input-icon-addon">
                                                                <i class="ti ti-phone"></i>
                                                            </span>
                                                            <input type="text" name="mobile" class="form-control" required>
                                                        </div>
                                                        <label class="form-label">Password</label>
                                                        <div class="pass-group mb-3">
                                                            <input type="password" name="password" class="pass-input form-control" required>
                                                            <span class="ti toggle-password ti-eye-off"></span>
                                                        </div>
                                                        <label class="form-label">Confirm Password</label>
                                                        <div class="pass-group mb-3">
                                                            <input type="password" name="confirm_password" class="pass-input form-control" required>
                                                            <span class="ti toggle-password ti-eye-off"></span>
                                                        </div>
                                                    </div>
                                                    <!-- Cloudflare Turnstile CAPTCHA -->
                                                    <div class="form-group">
                                                        <div class="cf-turnstile" data-sitekey="0x4AAAAAAA4eC__h6eOYAIky" data-callback="enableSubmitButton"></div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <button type="submit" id="submitButton" class="btn btn-primary w-100" disabled>Sign Up</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="copyright">
                                            <p>&copy; <?php echo date("Y"); ?> <a href="admin-index.php">Visa Options</a></p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Main Wrapper -->
    <!-- jQuery -->
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap Core JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <!-- Feather Icon JS -->
    <script src="assets/js/feather.min.js"></script>
    <!-- Slimscroll JS -->
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <!-- Select2 JS -->
    <script src="assets/plugins/select2/js/select2.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
    <script>
        function enableSubmitButton(token) {
            document.getElementById('submitButton').disabled = false;
        }

        // Automatically close the alert after 5 seconds
        setTimeout(function() {
            var alertMessage = document.getElementById('alert-message');
            if (alertMessage) {
                alertMessage.style.display = 'none';
            }
        }, 5000);
    </script>
</body>

</html>
