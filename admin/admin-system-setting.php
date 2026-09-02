<?php
session_start();
require_once('db/config.php');

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}


// Fetch existing data for System Settings section
$sql_check_settings = "SELECT * FROM system_setting LIMIT 1";
$result_settings = $db->query($sql_check_settings);

$logo = '';
$logo_white = '';
$backpanel_image = '';
$helpdesk_number = '';
$favicon = '';

if ($result_settings->num_rows > 0) {
    $row_settings = $result_settings->fetch_assoc();
    $logo = $row_settings['black_image'];
    $logo_white = $row_settings['white_image'];
    $backpanel_image = $row_settings['backpanel_logo'];
    $helpdesk_number = $row_settings['helpdesk'];
    $favicon = $row_settings['favicon'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['settings_submit'])) {
        // Process System Settings section
        $new_logo = isset($_FILES['logo']['name']) ? $_FILES['logo']['name'] : '';
        $new_logo_white = isset($_FILES['logo_white']['name']) ? $_FILES['logo_white']['name'] : '';
        $new_backpanel_image = isset($_FILES['backpanel_image']['name']) ? $_FILES['backpanel_image']['name'] : '';
        $new_helpdesk_number = isset($_POST['helpdesk_number']) ? $_POST['helpdesk_number'] : '';
        $new_favicon = isset($_FILES['favicon']['name']) ? $_FILES['favicon']['name'] : '';

        // Directory to store uploaded files
        $upload_dir = 'logo/';

        // Handle file uploads
        if (!empty($new_logo)) {
            move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $new_logo);
            $logo = $new_logo;
        }
        if (!empty($new_logo_white)) {
            move_uploaded_file($_FILES['logo_white']['tmp_name'], $upload_dir . $new_logo_white);
            $logo_white = $new_logo_white;
        }
        if (!empty($new_backpanel_image)) {
            move_uploaded_file($_FILES['backpanel_image']['tmp_name'], $upload_dir . $new_backpanel_image);
            $backpanel_image = $new_backpanel_image;
        }
        if (!empty($new_favicon)) {
            move_uploaded_file($_FILES['favicon']['tmp_name'], $upload_dir . $new_favicon);
            $favicon = $new_favicon;
        }

        // Re-fetch the existing data to ensure the result is accurate
        $result_settings = $db->query($sql_check_settings);

        if ($result_settings->num_rows > 0) {
            // Update record
            $row_settings = $result_settings->fetch_assoc();
            $sql_update = "UPDATE system_setting SET ";
            $update_fields = [];
            $update_params = [];

            if (!empty($logo)) {
                $update_fields[] = "black_image = ?";
                $update_params[] = $logo;
            }
            if (!empty($logo_white)) {
                $update_fields[] = "white_image = ?";
                $update_params[] = $logo_white;
            }
            if (!empty($backpanel_image)) {
                $update_fields[] = "backpanel_logo = ?";
                $update_params[] = $backpanel_image;
            }
            if (!empty($new_helpdesk_number)) {
                $update_fields[] = "helpdesk = ?";
                $update_params[] = $new_helpdesk_number;
            }
            if (!empty($favicon)) {
                $update_fields[] = "favicon = ?";
                $update_params[] = $favicon;
            }

            $sql_update .= implode(", ", $update_fields) . " WHERE id = ?";
            $update_params[] = $row_settings['id'];

            if (!empty($update_fields)) {
                $stmt = $db->prepare($sql_update);
                $stmt->bind_param(str_repeat("s", count($update_params)), ...$update_params);
                $stmt->execute();
                $_SESSION['message'] = "System settings record updated successfully.";
            }
        } else {
            // Insert record
            $sql_insert = "INSERT INTO system_setting (black_image, white_image, backpanel_logo, helpdesk, favicon) VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql_insert);
            $stmt->bind_param("sssss", $logo, $logo_white, $backpanel_image, $new_helpdesk_number, $favicon);
            $stmt->execute();
            $_SESSION['message'] = "System settings record added successfully.";
        }
    }

    header("Location: admin-system-setting.php");
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
    <title>System Setting - Tee Mac Corporation</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">

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
            <?php require_once('admin-sidebar.php'); ?>
        </div>
        <!-- /Sidebar -->

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content">
                <div class="d-md-flex d-block align-items-center justify-content-between border-bottom pb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">System Setting</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="dashboard.php">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">System Setting</li>
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
                            <!-- System Settings Section -->
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="about-container container p-4 bg-light rounded shadow-sm mt-5">
                                    <h3 class="mb-3">System Settings</h3>
                                    <div class="row col-12">
                                        <div class="col-md-6 mb-3">
                                            <label for="logo" class="form-label">Logo</label>
                                            <input type="file" class="form-control" id="logo" name="logo" onchange="previewImage(event, 'logoPreview')">
                                            <img id="logoPreview" src="<?php echo !empty($logo) ? 'logo/' . htmlspecialchars($logo) : ''; ?>" alt="Logo Preview" class="tmc-settings-thumb mt-2<?php echo empty($logo) ? ' tmc-hidden' : ''; ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="logo_white" class="form-label">Logo White</label>
                                            <input type="file" class="form-control" id="logo_white" name="logo_white" onchange="previewImage(event, 'logoWhitePreview')">
                                            <img id="logoWhitePreview" src="<?php echo !empty($logo_white) ? 'logo/' . htmlspecialchars($logo_white) : ''; ?>" alt="Logo White Preview" class="tmc-settings-thumb mt-2<?php echo empty($logo_white) ? ' tmc-hidden' : ''; ?>">
                                        </div>
                                        <!-- <div class="col-md-6 mb-3">
                                            <label for="backpanel_image" class="form-label">Backpanel Image</label>
                                            <input type="file" class="form-control" id="backpanel_image" name="backpanel_image" onchange="previewImage(event, 'backpanelImagePreview')">
                                            <img id="backpanelImagePreview" src="<?php echo !empty($backpanel_image) ? 'logo/' . htmlspecialchars($backpanel_image) : ''; ?>" alt="Backpanel Image Preview" class="tmc-settings-thumb mt-2<?php echo empty($backpanel_image) ? ' tmc-hidden' : ''; ?>">
                                        </div> -->
                                        <div class="col-md-6 mb-3">
                                            <label for="favicon" class="form-label">Favicon</label>
                                            <input type="file" class="form-control" id="favicon" name="favicon" onchange="previewImage(event, 'faviconPreview')">
                                            <img id="faviconPreview" src="<?php echo !empty($favicon) ? 'logo/' . htmlspecialchars($favicon) : ''; ?>" alt="Favicon Preview" class="tmc-settings-thumb mt-2<?php echo empty($favicon) ? ' tmc-hidden' : ''; ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="helpdesk_number" class="form-label">Helpdesk Number</label>
                                            <input type="text" class="form-control" id="helpdesk_number" name="helpdesk_number" placeholder="Enter helpdesk number" value="<?php echo htmlspecialchars($helpdesk_number); ?>">
                                        </div>
                                    </div>
                                    <div class="text-left">
                                        <button type="submit" class="btn btn-primary px-5" name="settings_submit"><?php echo $result_settings->num_rows > 0 ? 'Update' : 'Add'; ?></button>
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

    <!-- Theme Script JS -->
    <script src="assets/js/theme-script.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image preview function
            function previewImage(event, previewId) {
                const input = event.target;
                const preview = document.getElementById(previewId);
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('tmc-hidden');
                    };
                    reader.readAsDataURL(input.files[0]);
                } else {
                    preview.classList.add('tmc-hidden');
                }
            }

            // Expose previewImage to global scope for onchange events
            window.previewImage = previewImage;

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