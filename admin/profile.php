<?php
session_start();
require_once('db/config.php');

// Ensure the user is logged in
if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit();
}

$admin_id = base64_decode($_SESSION['adminId']);

// Fetch existing data for the profile section
$sql_check_profile = "SELECT * FROM admin WHERE admin_id = ?";
$stmt = $db->prepare($sql_check_profile);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result_profile = $stmt->get_result();
$row_result = $result_profile->fetch_assoc();

// Set default values
$image = $row_result['image'] ?? '';
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['profile_submit'])) {
        // Profile Image Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_name = $_FILES['image']['name'];
            $image_tmp = $_FILES['image']['tmp_name'];
            $upload_dir = "profile/";
            $allowed_types = ['image/jpeg', 'image/png'];

            // Validate file type
            $file_type = mime_content_type($image_tmp);
            if (!in_array($file_type, $allowed_types)) {
                $message = "Invalid file type. Only JPG and PNG are allowed.";
            } else {
                // Move the uploaded file
                $file_path = $upload_dir . basename($image_name);
                if (move_uploaded_file($image_tmp, $file_path)) {
                    $sql_update = "UPDATE admin SET image = ? WHERE admin_id = ?";
                    $stmt = $db->prepare($sql_update);
                    $stmt->bind_param("si", $image_name, $admin_id);
                    $stmt->execute();
                    $message = "Profile image updated successfully.";
                } else {
                    $message = "Error uploading the file.";
                }
            }
        }
    } elseif (isset($_POST['password_submit'])) {
        // Password Update
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        $sql_check_password = "SELECT password FROM admin WHERE admin_id = ?";
        $stmt = $db->prepare($sql_check_password);
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result_password = $stmt->get_result();
        $row_password = $result_password->fetch_assoc();

        if (password_verify($current_password, $row_password['password'])) {
            if ($new_password === $confirm_password) {
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql_update_password = "UPDATE admin SET password = ? WHERE admin_id = ?";
                $stmt = $db->prepare($sql_update_password);
                $stmt->bind_param("si", $hashed_new_password, $admin_id);
                $stmt->execute();
                $message = "Password updated successfully.";
            } else {
                $message = "New password and confirm password do not match.";
            }
        } else {
            $message = "Current password is incorrect.";
        }
    } elseif (isset($_POST['personal_submit'])) {
        // Personal Information Update
        $username = $_POST['user_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone_number'] ?? '';

        $sql_update_personal = "UPDATE admin SET username = ?, email = ?, phone = ? WHERE admin_id = ?";
        $stmt = $db->prepare($sql_update_personal);
        $stmt->bind_param("sssi", $username, $email, $phone, $admin_id);
        $stmt->execute();
        $message = "Personal information updated successfully.";
    }

    $_SESSION['message'] = $message;
    header("Location: profile.php");
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
    <title>Profile - Tee Mac Corporation</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">
    <link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">
    <link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .profile-card {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-image img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }

        .form-section {
            margin-top: 20px;
        }

        .form-section h3 {
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }

        .form-section form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-section button {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <!-- Header -->
        <div class="header">
            <?php require_once('header.php'); ?>
        </div>
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <?php require_once('admin-sidebar.php'); ?>
        </div>
        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content">
                <div class="d-md-flex align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">User Profile</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="dashboard.php">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">User Profile</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="profile-card">
                            <div class="card-header">
                                <h4>Profile Management</h4>
                            </div>
                            <div class="card-body">
                                <div class="profile-image text-center mb-4">
                                    <img src="<?php echo !empty($image) ? 'profile/' . htmlspecialchars($image) : 'assets/img/default-avatar.png'; ?>" alt="Profile Image" class="img-thumbnail">
                                </div>
                                <form action="profile.php" method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Profile Image</label>
                                        <input type="file" class="form-control" id="image" name="image">
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="profile_submit">Update Profile</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-6">
                        <div class="form-section">
                            <h3>Update Personal Information</h3>
                            <form action="profile.php" method="POST" class="mt-2">
                                <div class="mb-3">
                                    <label for="user_name" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="user_name" name="user_name" value="<?php echo htmlspecialchars($row_result['username']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($row_result['email']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($row_result['phone']); ?>">
                                </div>
                                <button type="submit" class="btn btn-primary" name="personal_submit">Update Information</button>
                            </form>
                        </div>
                        <div class="form-section">
                            <h3>Change Password</h3>
                            <form action="profile.php" method="POST" class="mt-2">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <button type="submit" class="btn btn-primary" name="password_submit">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="footer">
                <div class="mt-5 text-center">
                    <?php require_once('copyright.php'); ?>
                </div>
            </footer>
        </div>
    </div>

    <!-- jQuery -->
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/email-decode.min.js"></script>
    <script src="assets/js/moment.js"></script>
    <script src="assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script src="assets/js/feather.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/plugins/select2/js/select2.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="assets/js/rocket-loader.min.js" data-cf-settings="=FEB024E4D970C7C806EF5348-|49" defer=""></script>
    <script>
        // Display SweetAlert2 for session messages
        <?php if (isset($_SESSION['message'])): ?>
            Swal.fire({
                title: '<?php echo strpos($_SESSION['message'], 'successfully') !== false ? "Success" : "Error"; ?>',
                text: '<?php echo htmlspecialchars($_SESSION['message']); ?>',
                icon: '<?php echo strpos($_SESSION['message'], 'successfully') !== false ? "success" : "error"; ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
    </script>
</body>

</html>