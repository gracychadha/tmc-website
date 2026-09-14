<?php
session_start();
require_once('db/config.php');

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}


// Fetch existing data for SMTP Settings section
$sql_check_smtp = "SELECT * FROM email_config LIMIT 1";
$result_smtp = $db->query($sql_check_smtp);

$host = '';
$port = '';
$username = '';
$password = '';
$status = '';
$idemail_config = '';

if ($result_smtp->num_rows > 0) {
    $row_smtp = $result_smtp->fetch_assoc();
    $host = $row_smtp['hosts'];
    $port = $row_smtp['port'];
    $username = $row_smtp['user_email'];
    $password = $row_smtp['password'];
    $status = $row_smtp['status'];
    $idemail_config = $row_smtp['idemail_config'];
}

// Fetch existing data for CAPTCHA Settings section
$sql_check_captcha = "SELECT * FROM captcha LIMIT 1";
$result_captcha = $db->query($sql_check_captcha);

$site_key = '';
$secret_key = '';
$captcha_status = '';
$idcaptcha = '';

if ($result_captcha->num_rows > 0) {
    $row_captcha = $result_captcha->fetch_assoc();
    $site_key = $row_captcha['sitekey'];
    $secret_key = $row_captcha['secretkey'];
    $captcha_status = $row_captcha['status'];
    $idcaptcha = $row_captcha['idcaptcha'];
}

// Fetch existing data for Google CAPTCHA Settings section
$sql_check_google_captcha = "SELECT * FROM google_captcha LIMIT 1";
$result_google_captcha = $db->query($sql_check_google_captcha);

$google_site_key = '';
$google_secret_key = '';
$google_captcha_status = '';
$idgoogle_captcha = '';

if ($result_google_captcha->num_rows > 0) {
    $row_google_captcha = $result_google_captcha->fetch_assoc();
    $google_site_key = $row_google_captcha['sitekey'];
    $google_secret_key = $row_google_captcha['secretkey'];
    $google_captcha_status = $row_google_captcha['status'];
    $idgoogle_captcha = $row_google_captcha['idgoogle_captcha'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['smtp_submit'])) {
        // Process SMTP Settings section
        $new_host = isset($_POST['host']) ? $_POST['host'] : '';
        $new_port = isset($_POST['port']) ? $_POST['port'] : '';
        $new_username = isset($_POST['user_email']) ? $_POST['user_email'] : '';
        $new_password = isset($_POST['password']) ? $_POST['password'] : '';
        $new_status = isset($_POST['status']) ? $_POST['status'] : '';

        // Re-fetch the existing data to ensure the result is accurate
        $result_smtp = $db->query($sql_check_smtp);

        if ($result_smtp->num_rows > 0) {
            // Update record
            $row_smtp = $result_smtp->fetch_assoc();
            $update_fields = [];
            $update_values = [];

            if ($new_host != $host) {
                $update_fields[] = "hosts = ?";
                $update_values[] = $new_host;
            }
            if ($new_port != $port) {
                $update_fields[] = "port = ?";
                $update_values[] = $new_port;
            }
            if ($new_username != $username) {
                $update_fields[] = "user_email = ?";
                $update_values[] = $new_username;
            }
            if ($new_password != $password) {
                $update_fields[] = "password = ?";
                $update_values[] = $new_password;
            }
            if ($new_status != $status) {
                $update_fields[] = "status = ?";
                $update_values[] = $new_status;
            }

            if (!empty($update_fields)) {
                $update_fields[] = "idemail_config = ?";
                $update_values[] = $idemail_config;
                $sql_update = "UPDATE email_config SET " . implode(", ", $update_fields);
                $stmt = $db->prepare($sql_update);
                $stmt->bind_param(str_repeat("s", count($update_values)), ...$update_values);
                $stmt->execute();
                $_SESSION['message'] = "SMTP settings record updated successfully.";
            }
        } else {
            // Insert record
            $sql_insert = "INSERT INTO email_config (hosts, port, user_email, password, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql_insert);
            $stmt->bind_param("sisss", $new_host, $new_port, $new_username, $new_password, $new_status);
            $stmt->execute();
            $_SESSION['message'] = "SMTP settings record added successfully.";
        }
    }

    if (isset($_POST['captcha_submit'])) {
        // Process CAPTCHA Settings section
        $new_site_key = isset($_POST['site_key']) ? $_POST['site_key'] : '';
        $new_secret_key = isset($_POST['secret_key']) ? $_POST['secret_key'] : '';
        $new_captcha_status = isset($_POST['captcha_status']) ? $_POST['captcha_status'] : '';

        // Re-fetch the existing data to ensure the result is accurate
        $result_captcha = $db->query($sql_check_captcha);

        if ($result_captcha->num_rows > 0) {
            // Update record
            $row_captcha = $result_captcha->fetch_assoc();
            $update_fields = [];
            $update_values = [];

            if ($new_site_key != $site_key) {
                $update_fields[] = "sitekey = ?";
                $update_values[] = $new_site_key;
            }
            if ($new_secret_key != $secret_key) {
                $update_fields[] = "secretkey = ?";
                $update_values[] = $new_secret_key;
            }
            if ($new_captcha_status != $captcha_status) {
                $update_fields[] = "status = ?";
                $update_values[] = $new_captcha_status;
            }

            if (!empty($update_fields)) {
                $update_fields[] = "idcaptcha = ?";
                $update_values[] = $idcaptcha;
                $sql_update = "UPDATE captcha SET " . implode(", ", $update_fields);
                $stmt = $db->prepare($sql_update);
                $stmt->bind_param(str_repeat("s", count($update_values)), ...$update_values);
                $stmt->execute();
                $_SESSION['message'] = "CAPTCHA settings record updated successfully.";
            }
        } else {
            // Insert record
            $sql_insert = "INSERT INTO captcha (sitekey, secretkey, status) VALUES (?, ?, ?)";
            $stmt = $db->prepare($sql_insert);
            $stmt->bind_param("sss", $new_site_key, $new_secret_key, $new_captcha_status);
            $stmt->execute();
            $_SESSION['message'] = "CAPTCHA settings record added successfully.";
        }
    }

    if (isset($_POST['google_captcha_submit'])) {
        // Process Google CAPTCHA Settings section
        $new_google_site_key = isset($_POST['google_site_key']) ? $_POST['google_site_key'] : '';
        $new_google_secret_key = isset($_POST['google_secret_key']) ? $_POST['google_secret_key'] : '';
        $new_google_captcha_status = isset($_POST['google_captcha_status']) ? $_POST['google_captcha_status'] : '';

        // Re-fetch the existing data to ensure the result is accurate
        $result_google_captcha = $db->query($sql_check_google_captcha);

        if ($result_google_captcha->num_rows > 0) {
            // Update record
            $row_google_captcha = $result_google_captcha->fetch_assoc();
            $update_fields = [];
            $update_values = [];

            if ($new_google_site_key != $google_site_key) {
                $update_fields[] = "sitekey = ?";
                $update_values[] = $new_google_site_key;
            }
            if ($new_google_secret_key != $google_secret_key) {
                $update_fields[] = "secretkey = ?";
                $update_values[] = $new_google_secret_key;
            }
            if ($new_google_captcha_status != $google_captcha_status) {
                $update_fields[] = "status = ?";
                $update_values[] = $new_google_captcha_status;
            }

            if (!empty($update_fields)) {
                $update_fields[] = "idgoogle_captcha = ?";
                $update_values[] = $idgoogle_captcha;
                $sql_update = "UPDATE google_captcha SET " . implode(", ", $update_fields);
                $stmt = $db->prepare($sql_update);
                $stmt->bind_param(str_repeat("s", count($update_values)), ...$update_values);
                $stmt->execute();
                $_SESSION['message'] = "Google CAPTCHA settings record updated successfully.";
            }
        } else {
            // Insert record
            $sql_insert = "INSERT INTO google_captcha (sitekey, secretkey, status) VALUES (?, ?, ?)";
            $stmt = $db->prepare($sql_insert);
            $stmt->bind_param("sss", $new_google_site_key, $new_google_secret_key, $new_google_captcha_status);
            $stmt->execute();
            $_SESSION['message'] = "Google CAPTCHA settings record added successfully.";
        }
    }

    header("Location: admin-website-setting.php");
    exit();
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
    <meta name="robots" content="noindex, nofollow">
    <title>Website Setting - Tee Mac Corporation</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">

    <!-- Theme Script js -->
    <script src="assets/js/theme-script.js"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- Feather CSS -->
    <link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin-custom.css">
</head>

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <!-- Header -->
        <div class="header">
            <?php require_once('header.php'); ?>
        </div>
        <!-- /Header -->

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <?php require_once('admin-sidebar.php') ?>
        </div>
        <!-- /Sidebar -->

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content">
                <div class="d-md-flex d-block align-items-center justify-content-between border-bottom pb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Company Information</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="index.php">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Company Information</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                        <div class="pe-1 mb-2">
                            <a href="#" class="btn btn-outline-light bg-white btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="container mt-5">
                            <!-- SMTP Settings Section -->
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="about-container container p-4 bg-light rounded shadow-sm mt-5">
                                    <h5 class="mb-3">SMTP Settings</h5>
                                    <div class="row col-12">
                                        <div class="col-md-6 mb-3">
                                            <label for="host" class="form-label">Host</label>
                                            <input type="text" class="form-control" id="host" name="host" placeholder="Enter SMTP host" value="<?php echo htmlspecialchars($host); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="port" class="form-label">Port</label>
                                            <input type="number" class="form-control" id="port" name="port" placeholder="Enter SMTP port" value="<?php echo htmlspecialchars($port); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" name="user_email" placeholder="Enter SMTP username" value="<?php echo htmlspecialchars($username); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <div class="pass-group">
                                                <input type="password" class="pass-input form-control" id="password" name="password" placeholder="Enter SMTP password" value="<?php echo htmlspecialchars($password); ?>">
                                                <span class="ti toggle-password ti-eye-off" id="togglePassword"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="1" <?php echo $status == 1 ? 'selected' : ''; ?>>Active</option>
                                                <option value="0" <?php echo $status == 0 ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="text-left">
                                        <button type="submit" class="btn btn-primary px-5" name="smtp_submit"><?php echo $result_smtp->num_rows > 0 ? 'Update' : 'Add'; ?></button>
                                    </div>
                                </div>
                            </form>

                            <!-- CAPTCHA Settings Section -->
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="about-container container p-4 bg-light rounded shadow-sm mt-5">
                                    <h5 class="mb-3">Cloudflare Captcha</h5>
                                    <div class="row col-12">
                                        <div class="col-md-6 mb-3">
                                            <label for="site_key" class="form-label">Site Key</label>
                                            <input type="text" class="form-control" id="site_key" name="site_key" placeholder="Enter CAPTCHA site key" value="<?php echo htmlspecialchars($site_key); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="secret_key" class="form-label">Secret Key</label>
                                            <input type="text" class="form-control" id="secret_key" name="secret_key" placeholder="Enter CAPTCHA secret key" value="<?php echo htmlspecialchars($secret_key); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="captcha_status" class="form-label">Status</label>
                                            <select class="form-control" id="captcha_status" name="captcha_status">
                                                <option value="1" <?php echo $captcha_status == 1 ? 'selected' : ''; ?>>Active</option>
                                                <option value="0" <?php echo $captcha_status == 0 ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="text-left">
                                        <button type="submit" class="btn btn-primary px-5" name="captcha_submit"><?php echo $result_captcha->num_rows > 0 ? 'Update' : 'Add'; ?></button>
                                    </div>
                                </div>
                            </form>

                            <!-- Google CAPTCHA Settings Section -->
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="about-container container p-4 bg-light rounded shadow-sm mt-5">
                                    <h5 class="mb-3">Google CAPTCHA Settings</h5>
                                    <div class="row col-12">
                                        <div class="col-md-6 mb-3">
                                            <label for="google_site_key" class="form-label">Site Key</label>
                                            <input type="text" class="form-control" id="google_site_key" name="google_site_key" placeholder="Enter Google CAPTCHA site key" value="<?php echo htmlspecialchars($google_site_key); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="google_secret_key" class="form-label">Secret Key</label>
                                            <input type="text" class="form-control" id="google_secret_key" name="google_secret_key" placeholder="Enter Google CAPTCHA secret key" value="<?php echo htmlspecialchars($google_secret_key); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="google_captcha_status" class="form-label">Status</label>
                                            <select class="form-control" id="google_captcha_status" name="google_captcha_status">
                                                <option value="1" <?php echo $google_captcha_status == 1 ? 'selected' : ''; ?>>Active</option>
                                                <option value="0" <?php echo $google_captcha_status == 0 ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="text-left">
                                        <button type="submit" class="btn btn-primary px-5" name="google_captcha_submit"><?php echo $result_google_captcha->num_rows > 0 ? 'Update' : 'Add'; ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Wrapper -->

        <footer class="footer">
            <div class="mt-5 text-center">
                <?php require_once('copyright.php'); ?>
            </div>
        </footer>

    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="assets/js/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap Core JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <!-- Datetimepicker JS -->
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>

    <!-- Feather Icon JS -->
    <script src="assets/js/feather.min.js"></script>

    <!-- Slimscroll JS -->
    <script src="assets/js/jquery.slimscroll.min.js"></script>

    <!-- Datatable JS -->
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap5.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
    <script src="assets/js/admin-custom.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const togglePassword = document.getElementById('togglePassword');
            const passwordField = document.getElementById('password');

            if (togglePassword && passwordField) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordField.setAttribute('type', type);

                    // Toggle the eye icon
                    this.classList.toggle('ti-eye');
                    this.classList.toggle('ti-eye-off');
                });
            }

            // SweetAlert2 notifications
            <?php if (isset($_SESSION['message'])): ?>
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: 'success',
                    title: "<?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>",
                    showConfirmButton: false,
                    timer: 8000,
                    timerProgressBar: true
                });
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: 'error',
                    title: "<?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); ?>",
                    showConfirmButton: false,
                    timer: 8000,
                    timerProgressBar: true
                });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });
    </script>

</body>

</html>