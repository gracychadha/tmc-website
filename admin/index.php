<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("db/config.php");

$stmt = $db->prepare("SELECT * FROM captcha WHERE status = '1'");
$stmt->execute();
$result_captcha = $stmt->get_result();
if (!$result_captcha) {
    die("Error fetching Cloudflare Turnstile configuration: " . $db->error);
}
$data_captcha = $result_captcha->fetch_assoc();

$site_key = $data_captcha['sitekey'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $login_identifier = mysqli_real_escape_string($db, $_POST['login_identifier']);
        $password = $_POST['password'];
        $captchaResponse = $_POST['cf-turnstile-response'] ?? '';

        $secret_key = $data_captcha['secretkey'];
        $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
        $data = [
            'secret' => $secret_key,
            'response' => $captchaResponse,
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $response = json_decode($result, true);

        if (!$response['success']) {
            $_SESSION['msg'] = "Error in Cloudflare Turnstile verification.";
            $_SESSION['msg_type'] = 'error';
            header("location: index.php");
            exit();
        }

        $stmt = $db->prepare("SELECT * FROM admin WHERE (username = ? OR email = ?)");
        if (!$stmt) {
            die("Prepare statement failed: " . $db->error);
        }
        $stmt->bind_param("ss", $login_identifier, $login_identifier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $rowadmin = $result->fetch_assoc();

            if ($rowadmin['status'] === 'Disabled') {
                $_SESSION['msg'] = "Account access denied. Please contact support for more information.";
                $_SESSION['msg_type'] = 'error';
                header("location: index.php");
                exit();
            }

            if (password_verify($password, $rowadmin['password'])) {
                $_SESSION['adminId'] = base64_encode($rowadmin['admin_id']);
                $_SESSION['userName'] = $rowadmin['username'];
                header("location: dashboard.php");
                exit();
            } else {
                $_SESSION['msg'] = "Invalid password. Please try again.";
                $_SESSION['msg_type'] = 'error';
                header("location: index.php");
                exit();
            }
        } else {
            $_SESSION['msg'] = "Access denied: Invalid username or email.";
            $_SESSION['msg_type'] = 'error';
            header("location: index.php");
            exit();
        }
    }
}

$sqlfav = "SELECT favicon, backpanel_logo, black_image, helpdesk FROM system_setting LIMIT 1";

if ($stmt = $db->prepare($sqlfav)) {
    $stmt->execute();
    $stmt->bind_result($favicon, $backpanel_logo, $black_image, $helpdesk);
    if ($stmt->fetch()) {
        $faviconPath = "logo/" . $favicon;
        $backpanelLogoPath = "logo/" . $backpanel_logo;
        $blackImagePath = "logo/" . $black_image;
    }
    $stmt->close();
} else {
    echo "Failed to prepare the statement.";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Tee Mac Corporation - Admin Login</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">
    <link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body.account-page {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: #0a0a1a;
            overflow-x: hidden;
        }

        /* === BACKGROUND === */
        .bg-scene {
            position: fixed;
            inset: 0;
            z-index: 0;
        }

        .bg-scene::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('<?php echo htmlspecialchars($blackImagePath); ?>') center/cover no-repeat;
            filter: blur(4px) brightness(0.35);
            transform: scale(1.1);
        }

        .bg-scene::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(212, 175, 55, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(99, 66, 255, 0.06) 0%, transparent 50%),
                linear-gradient(180deg, rgba(10, 10, 26, 0.6) 0%, rgba(10, 10, 26, 0.85) 100%);
        }

        /* Animated orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: floatOrb 20s ease-in-out infinite;
            z-index: 0;
            pointer-events: none;
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            background: rgba(212, 175, 55, 0.12);
            top: -10%;
            left: -5%;
        }

        .orb-2 {
            width: 300px;
            height: 300px;
            background: rgba(99, 66, 255, 0.1);
            bottom: -10%;
            right: -5%;
            animation-delay: -7s;
        }

        .orb-3 {
            width: 200px;
            height: 200px;
            background: rgba(212, 175, 55, 0.08);
            top: 50%;
            right: 20%;
            animation-delay: -14s;
        }

        @keyframes floatOrb {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            25% {
                transform: translate(30px, -40px) scale(1.05);
            }

            50% {
                transform: translate(-20px, 20px) scale(0.95);
            }

            75% {
                transform: translate(15px, 35px) scale(1.02);
            }
        }

        /* Floating particles */
        .particles {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: rgba(212, 175, 55, 0.4);
            border-radius: 50%;
            animation: rise linear infinite;
        }

        .particle:nth-child(1) {
            left: 10%;
            animation-duration: 18s;
            width: 2px;
            height: 2px;
        }

        .particle:nth-child(2) {
            left: 22%;
            animation-duration: 22s;
            animation-delay: 3s;
        }

        .particle:nth-child(3) {
            left: 38%;
            animation-duration: 16s;
            animation-delay: 6s;
            width: 4px;
            height: 4px;
            opacity: 0.6;
        }

        .particle:nth-child(4) {
            left: 52%;
            animation-duration: 20s;
            animation-delay: 2s;
        }

        .particle:nth-child(5) {
            left: 68%;
            animation-duration: 24s;
            animation-delay: 8s;
            width: 2px;
            height: 2px;
        }

        .particle:nth-child(6) {
            left: 78%;
            animation-duration: 17s;
            animation-delay: 5s;
        }

        .particle:nth-child(7) {
            left: 90%;
            animation-duration: 21s;
            animation-delay: 1s;
            width: 4px;
            height: 4px;
            opacity: 0.5;
        }

        .particle:nth-child(8) {
            left: 45%;
            animation-duration: 19s;
            animation-delay: 7s;
        }

        @keyframes rise {
            0% {
                bottom: -5%;
                opacity: 0;
                transform: translateX(0);
            }

            10% {
                opacity: 0.8;
            }

            90% {
                opacity: 0.8;
            }

            100% {
                bottom: 105%;
                opacity: 0;
                transform: translateX(40px);
            }
        }

        /* === MAIN LAYOUT === */
        .main-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
        }

        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            width: 100%;
            max-width: 1000px;
            min-height: 580px;
            border-radius: 24px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.5);
            animation: cardReveal 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        @keyframes cardReveal {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* === LEFT PANEL === */
        .left-panel {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px 40px;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.05) 0%, rgba(99, 66, 255, 0.03) 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.06);
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at center, rgba(212, 175, 55, 0.14) 0%, transparent 50%);
            animation: rotateGlow 30s linear infinite;
        }

        @keyframes rotateGlow {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .left-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .left-content .brand-icon {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            background: linear-gradient(135deg, #d4af37, #b8962e);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            box-shadow: 0 12px 35px rgba(212, 175, 55, 0.3);
            animation: iconPulse 3s ease-in-out infinite;
        }

        @keyframes iconPulse {

            0%,
            100% {
                box-shadow: 0 12px 35px rgba(212, 175, 55, 0.3);
            }

            50% {
                box-shadow: 0 12px 50px rgba(212, 175, 55, 0.5);
            }
        }

        .left-content .brand-icon i {
            font-size: 28px;
            color: #0a0a1a;
        }

        .left-content .brand-logo {
            max-width: 180px;
            margin-bottom: 20px;
            filter: drop-shadow(0 4px 15px rgba(0, 0, 0, 0.3));
        }

        .left-content h2 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .left-content h2 span {
            background: linear-gradient(135deg, #d4af37, #f0d060);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .left-content .tagline {
            color: rgba(255, 255, 255, 0.45);
            font-size: 0.85rem;
            line-height: 1.6;
            max-width: 280px;
            margin: 0 auto 28px;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            text-align: left;
            max-width: 260px;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.82rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }

        .feature-list li:last-child {
            border-bottom: none;
        }

        .feature-list li i {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(212, 175, 55, 0.1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #d4af37;
            font-size: 12px;
            flex-shrink: 0;
        }

        /* === RIGHT PANEL === */
        .right-panel {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px 40px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
            width: 100%;
        }

        .form-header .avatar {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.15), rgba(212, 175, 55, 0.05));
            border: 1px solid rgba(212, 175, 55, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .form-header .avatar i {
            font-size: 22px;
            color: #d4af37;
        }

        .form-header h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 4px;
        }

        .form-header p {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.82rem;
            margin: 0;
        }

        /* Alert */
        .alert {
            border-radius: 12px;
            font-size: 0.8rem;
            border: none;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            animation: shake 0.4s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        /* Form fields */
        .field-group {
            margin-bottom: 18px;
        }

        .field-group label {
            display: block;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.78rem;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .field-input {
            position: relative;
        }

        .field-input>i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.2);
            font-size: 14px;
            transition: color 0.3s ease;
            z-index: 3;
        }

        .field-input input {
            width: 100%;
            height: 50px;
            padding: 0 46px 0 46px;
            background: rgba(255, 255, 255, 0.04);
            border: 1.5px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            color: #fff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.88rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .field-input input::placeholder {
            color: rgba(255, 255, 255, 0.2);
        }

        .field-input input:focus {
            background: rgba(255, 255, 255, 0.07);
            border-color: rgba(212, 175, 55, 0.5);
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.08), 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .field-input:focus-within>i {
            color: #d4af37;
        }

        .field-input .toggle-pass {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.2);
            cursor: pointer;
            z-index: 3;
            font-size: 15px;
            transition: color 0.3s;
            background: none;
            border: none;
            padding: 0;
        }

        .field-input .toggle-pass:hover {
            color: #d4af37;
        }

        .captcha-wrap {
            margin-bottom: 18px;
        }

        .forgot-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 22px;
        }

        .forgot-row a {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.8rem;
            text-decoration: none;
            transition: color 0.3s;
        }

        .forgot-row a:hover {
            color: #d4af37;
        }

        /* Submit button */
        .btn-signin {
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            background: linear-gradient(135deg, #d4af37 0%, #b8962e 50%, #d4af37 100%);
            background-size: 200% 100%;
            color: #0a0a1a;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-signin::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.2) 50%, transparent 100%);
            transform: translateX(-100%);
            transition: transform 0.5s ease;
        }

        .btn-signin:hover:not(:disabled)::before {
            transform: translateX(100%);
        }

        .btn-signin:hover:not(:disabled) {
            transform: translateY(-2px);
            background-position: 100% 0;
            box-shadow: 0 12px 35px rgba(212, 175, 55, 0.35);
        }

        .btn-signin:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-signin:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        /* Helpdesk */
        .helpdesk-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 22px;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            width: 100%;
        }

        .helpdesk-bar i {
            color: #d4af37;
            font-size: 13px;
        }

        .helpdesk-bar span {
            color: rgba(255, 255, 255, 0.45);
            font-size: 0.8rem;
        }

        .helpdesk-bar strong {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 600;
        }

        /* Copyright */
        .copyright-bar {
            text-align: center;
            color: white;
            font-size: 0.72rem;
        }

        .copyright-bar a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }

        .copyright-bar a:hover {
            color: #d4af37;
        }

        /* === RESPONSIVE === */
        @media (max-width: 991px) {
            .login-container {
                grid-template-columns: 1fr;
                max-width: 440px;
            }

            .left-panel {
                display: none;
            }

            .right-panel {
                padding: 40px 30px;
            }
        }

        @media (max-width: 480px) {
            .login-page {
                padding: 15px;
            }

            .right-panel {
                padding: 32px 22px;
            }

            .login-container {
                min-height: auto;
                border-radius: 20px;
            }
        }
    </style>
</head>

<body class="account-page">

    <!-- Background -->
    <div class="bg-scene"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div class="login-page"  >
            <div class="login-container">

                <!-- Left Panel -->
                <div class="left-panel">
                    <div class="left-content">
                        <?php
                        if ($blackImagePath) {
                        ?>
                            <img src="<?php echo htmlspecialchars($blackImagePath); ?>" alt="TMC" class="brand-logo">
                        <?php
                        } else {
                        ?>
                            <h2><span>Tee Mac</span> Corporation</h2>
                        <?php
                        }
                        ?>

                        <p class="tagline">Your trusted partner for world-class event management, planning, and execution.</p>
                        <ul class="feature-list">
                            <li><i class="fas fa-clipboard-list"></i> Event Planning & Coordination</li>
                            <li><i class="fas fa-users"></i> Guest & Vendor Management</li>
                            <li><i class="fas fa-chart-line"></i> Real-time Analytics Dashboard</li>
                            <li><i class="fas fa-headset"></i> 24/7 Dedicated Support</li>
                        </ul>
                    </div>
                </div>

                <!-- Right Panel -->
                <div class="right-panel">
                    <div class="form-header">
                        <div class="avatar"><i class="fas fa-user-shield"></i></div>
                        <h3>Welcome Back</h3>
                        <p>Sign in to your admin dashboard</p>
                    </div>

                    <?php
                    if (isset($_SESSION['msg'])) {
                        $message = $_SESSION['msg'];
                        $message_type = $_SESSION['msg_type'];
                        $alertType = ($message_type == 'error') ? 'danger' : 'success';
                        echo "<div class='alert alert-$alertType alert-dismissible fade show' role='alert'>
                                <i class='fas fa-exclamation-circle'></i>
                                <span>$message</span>
                                <button type='button' class='btn-close btn-close-white' data-bs-dismiss='alert'></button>
                              </div>";
                        unset($_SESSION['msg'], $_SESSION['msg_type']);
                    }
                    ?>

                    <form action="index.php" method="POST" enctype="multipart/form-data" style="width: 100%;">
                        <div class="field-group">
                            <label>Username or Email</label>
                            <div class="field-input">
                                <i class="fas fa-envelope"></i>
                                <input type="text" name="login_identifier" placeholder="Enter your email or username" required>
                            </div>
                        </div>

                        <div class="field-group">
                            <label>Password</label>
                            <div class="field-input">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" id="password" placeholder="Enter your password" required>
                                <button type="button" class="toggle-pass" id="togglePassword"><i class="fas fa-eye-slash"></i></button>
                            </div>
                        </div>

                        <div class="captcha-wrap">
                            <div class="cf-turnstile" data-sitekey="<?php echo $site_key; ?>" data-callback="enableSubmitButton"></div>
                            <input type="hidden" name="cf-turnstile-response" id="cf-turnstile-response">
                        </div>

                        <!-- <div class="forgot-row">
                            <a href="#">Forgot Password?</a>
                        </div> -->

                        <button type="submit" id="submitButton" name="login" class="btn-signin" disabled>
                            <i class="fas fa-right-to-bracket"></i> Sign In
                        </button>
                    </form>

                    <div class="helpdesk-bar">
                        <i class="fas fa-headset"></i>
                        <span>HelpDesk: <strong><?php echo $helpdesk; ?></strong></span>
                    </div>
                </div>

            </div>
        </div>
        <div class="copyright-bar">
            <?php require_once('copyright.php'); ?>
        </div>
    </div>

    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        function enableSubmitButton(token) {
            document.getElementById('cf-turnstile-response').value = token;
            document.getElementById('submitButton').disabled = false;
        }
        document.getElementById('togglePassword').addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>