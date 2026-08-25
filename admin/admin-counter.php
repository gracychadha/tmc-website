<?php
session_start();
require_once('db/config.php');

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}

// Function to handle file upload
function uploadIcon($file, $oldIcon) {
    $targetDir = "uploads/icons/";
    $default = ""; // fallback if no file

    // Create directory if not exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // If no new file uploaded, keep old
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return $oldIcon;
    }

    // Handle error
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return $oldIcon;
    }

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/x-icon'];
    $mimeType = mime_content_type($file['tmp_name']);
    if (!in_array($mimeType, $allowedTypes)) {
        return $oldIcon;
    }

    // Sanitize filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $targetPath = $targetDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Delete old file if exists
        if ($oldIcon && file_exists($targetDir . $oldIcon)) {
            @unlink($targetDir . $oldIcon);
        }
        return $fileName;
    }

    return $oldIcon;
}

// Fetch existing data for Counters section
$sql_check_counters = "SELECT * FROM counters LIMIT 1";
$result_counters = $db->query($sql_check_counters);

// Initialize variables
$counter_title1 = $counter1 = $icon1 = '';
$counter_title2 = $counter2 = $icon2 = '';
$counter_title3 = $counter3 = $icon3 = '';
$counter_title4 = $counter4 = $icon4 = '';

if ($result_counters->num_rows > 0) {
    $row = $result_counters->fetch_assoc();
    $counter_title1 = $row['title1'] ?? '';
    $counter1 = (int)$row['counter1'];
    $icon1 = $row['icon1'] ?? '';
    $counter_title2 = $row['title2'] ?? '';
    $counter2 = (int)$row['counter2'];
    $icon2 = $row['icon2'] ?? '';
    $counter_title3 = $row['title3'] ?? '';
    $counter3 = (int)$row['counter3'];
    $icon3 = $row['icon3'] ?? '';
    $counter_title4 = $row['title4'] ?? '';
    $counter4 = (int)$row['counter4'];
    $icon4 = $row['icon4'] ?? '';
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['counter_submit'])) {
    // Get form data
    $counter_title1 = trim($_POST['counter_title1'] ?? '');
    $counter1 = max(0, (int)($_POST['counter1'] ?? 0));
    $counter_title2 = trim($_POST['counter_title2'] ?? '');
    $counter2 = max(0, (int)($_POST['counter2'] ?? 0));
    $counter_title3 = trim($_POST['counter_title3'] ?? '');
    $counter3 = max(0, (int)($_POST['counter3'] ?? 0));
    $counter_title4 = trim($_POST['counter_title4'] ?? '');
    $counter4 = max(0, (int)($_POST['counter4'] ?? 0));

    // Handle file uploads
    $icon1 = uploadIcon($_FILES['icon1'] ?? [], $icon1);
    $icon2 = uploadIcon($_FILES['icon2'] ?? [], $icon2);
    $icon3 = uploadIcon($_FILES['icon3'] ?? [], $icon3);
    $icon4 = uploadIcon($_FILES['icon4'] ?? [], $icon4);

    // Set date
    date_default_timezone_set('Asia/Kolkata');
    $date = date("Y-m-d");

    if ($result_counters->num_rows > 0) {
        // Update existing record
        $sql_update = "UPDATE counters SET 
            title1 = ?, counter1 = ?, 
            title2 = ?, counter2 = ?, 
            title3 = ?, counter3 = ?, 
            title4 = ?, counter4 = ?, 
            icon1 = ?, icon2 = ?, icon3 = ?, icon4 = ?, 
            date = ? 
            WHERE idcounters = ?";
        $stmt = $db->prepare($sql_update);
        $stmt->bind_param(
            "sisisisisssssi",
            $counter_title1, $counter1,
            $counter_title2, $counter2,
            $counter_title3, $counter3,
            $counter_title4, $counter4,
            $icon1, $icon2, $icon3, $icon4,
            $date,
            $row['idcounters']
        );
        $stmt->execute();
        $_SESSION['message'] = "Counters updated successfully.";
    } else {
        // Insert new record
        $sql_insert = "INSERT INTO counters 
            (title1, counter1, title2, counter2, title3, counter3, title4, counter4, icon1, icon2, icon3, icon4, date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql_insert);
        $stmt->bind_param(
            "sisisisisssss",
            $counter_title1, $counter1,
            $counter_title2, $counter2,
            $counter_title3, $counter3,
            $counter_title4, $counter4,
            $icon1, $icon2, $icon3, $icon4,
            $date
        );
        $stmt->execute();
        $_SESSION['message'] = "Counters added successfully.";
    }

    header("Location: admin-counter.php");
    exit();
}

// Fetch favicon
$faviconPath = "assets/img/favicon.ico"; // fallback
$sqlfav = "SELECT favicon FROM system_setting LIMIT 1";
if ($stmt = $db->prepare($sqlfav)) {
    $stmt->execute();
    $stmt->bind_result($favicon);
    if ($stmt->fetch()) {
        if (!empty($favicon)) {
            $faviconPath = "logo/" . htmlspecialchars($favicon);
        }
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
    <title>Counter - Tee Mac Corporation</title>

    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $faviconPath; ?>">
    <script src="assets/js/theme-script.js"></script>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">
    <link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">
    <link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="assets/plugins/summernote/summernote-lite.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        a:hover { text-decoration: none; }
        .icon-preview img { height: 24px; margin-right: 6px; }
    </style>
</head>
<body>
<script>
document.addEventListener('DOMContentLoaded', function () {
    <?php if (isset($_SESSION['message'])): ?>
        Swal.fire({
            toast: true,
            position: 'bottom-end',
            icon: 'success',
            title: "<?php echo addslashes($_SESSION['message']); ?>",
            showConfirmButton: false,
            timer: 8000,
            timerProgressBar: true
        });
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
});
</script>

<div class="main-wrapper">
    <div class="header">
        <?php require_once('header.php'); ?>
    </div>

    <div class="sidebar" id="sidebar">
        <?php require_once('admin-sidebar.php') ?>
    </div>

    <div class="page-wrapper">
        <div class="content">
            <div class="d-md-flex d-block align-items-center justify-content-between border-bottom pb-3">
                <div class="my-auto mb-2">
                    <h3 class="page-title mb-1">Counter</h3>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Counter</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                    <div class="pe-1 mb-2">
                        <a href="admin-counter.php" class="btn btn-outline-light bg-white btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh">
                            <i class="ti ti-refresh"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Counters Section -->
            <div class="row mt-4">
                <div class="col-xxl-12 col-xl-12">
                    <div class="container">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="p-4 bg-light rounded shadow-sm">

                                <div class="row">
                                    <!-- Counter 1 -->
                                    <div class="col-md-4 mb-3">
                                        <label for="counter_title1" class="form-label">Counter 1 Title</label>
                                        <input type="text" class="form-control" id="counter_title1" name="counter_title1" placeholder="e.g., Students Enrolled" value="<?php echo htmlspecialchars($counter_title1); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="counter1" class="form-label">Counter 1 Value</label>
                                        <input type="number" class="form-control" id="counter1" name="counter1" placeholder="e.g., 5000" value="<?php echo $counter1; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="icon1" class="form-label">Icon 1 (Image)</label>
                                       
                                        <input type="file" class="form-control" name="icon1" accept="image/*">
                                         <p class="text-danger">icon should be 34 x 34</p>
                                        <?php if ($icon1): ?>
                                            <div class="mt-2 icon-preview">
                                                <img src="uploads/icons/<?php echo $icon1; ?>" alt="Icon 1">
                                                <small>Current file</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Counter 2 -->
                                    <div class="col-md-4 mb-3">
                                        <label for="counter_title2" class="form-label">Counter 2 Title</label>
                                        <input type="text" class="form-control" id="counter_title2" name="counter_title2" placeholder="e.g., Courses Offered" value="<?php echo htmlspecialchars($counter_title2); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="counter2" class="form-label">Counter 2 Value</label>
                                        <input type="number" class="form-control" id="counter2" name="counter2" placeholder="e.g., 120" value="<?php echo $counter2; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="icon2" class="form-label">Icon 2 (Image)</label>
                                       
                                        <input type="file" class="form-control" name="icon2" accept="image/*">
                                         <p class="text-danger">icon should be 34 x 34</p>
                                        <?php if ($icon2): ?>
                                            <div class="mt-2 icon-preview">
                                                <img src="uploads/icons/<?php echo $icon2; ?>" alt="Icon 2">
                                                <small>Current file</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Counter 3 -->
                                    <div class="col-md-4 mb-3">
                                        <label for="counter_title3" class="form-label">Counter 3 Title</label>
                                        <input type="text" class="form-control" id="counter_title3" name="counter_title3" placeholder="e.g., Instructors" value="<?php echo htmlspecialchars($counter_title3); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="counter3" class="form-label">Counter 3 Value</label>
                                        <input type="number" class="form-control" id="counter3" name="counter3" placeholder="e.g., 45" value="<?php echo $counter3; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="icon3" class="form-label">Icon 3 (Image)</label>
                                       
                                        <input type="file" class="form-control" name="icon3" accept="image/*">
                                         <p class="text-danger">icon should be 34 x 34</p>
                                        <?php if ($icon3): ?>
                                            <div class="mt-2 icon-preview">
                                                <img src="uploads/icons/<?php echo $icon3; ?>" alt="Icon 3">
                                                <small>Current file</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Counter 4 -->
                                    <div class="col-md-4 mb-3">
                                        <label for="counter_title4" class="form-label">Counter 4 Title</label>
                                        <input type="text" class="form-control" id="counter_title4" name="counter_title4" placeholder="e.g., Completion Rate" value="<?php echo htmlspecialchars($counter_title4); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="counter4" class="form-label">Counter 4 Value</label>
                                        <input type="number" class="form-control" id="counter4" name="counter4" placeholder="e.g., 98" value="<?php echo $counter4; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="icon4" class="form-label">Icon 4 (Image)</label>
                                        <input type="file" class="form-control" name="icon4" accept="image/*">
                                        
                                        <p class="text-danger">icon should be 34 x 34</p>
                                        <?php if ($icon4): ?>
                                            <div class="mt-2 icon-preview">
                                                <img src="uploads/icons/<?php echo $icon4; ?>" alt="Icon 4">
                                                <small>Current file</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="text-start mt-3">
                                    <button type="submit" class="btn btn-primary px-5" name="counter_submit">
                                        <?php echo $result_counters->num_rows > 0 ? 'Update' : 'Add'; ?> Counters
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="mt-5 text-center">
            <?php require_once('copyright.php'); ?>
        </div>
    </footer>
</div>

<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/moment.js"></script>
<script src="assets/plugins/daterangepicker/daterangepicker.js"></script>
<script src="assets/js/bootstrap-datetimepicker.min.js"></script>
<script src="assets/js/feather.min.js"></script>
<script src="assets/js/jquery.slimscroll.min.js"></script>
<script src="assets/plugins/select2/js/select2.min.js"></script>
<script src="assets/plugins/summernote/summernote-lite.min.js"></script>
<script src="assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
$(document).ready(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
</body>
</html>