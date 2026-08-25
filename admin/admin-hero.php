<?php
session_start();
require_once('db/config.php');

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}

date_default_timezone_set('Asia/Kolkata');

// Fetch the single hero record
$hero = null;
$stmt = $db->prepare("SELECT * FROM hero LIMIT 1");
$stmt->execute();
$result_hero = $stmt->get_result();
if ($result_hero->num_rows > 0) {
    $hero = $result_hero->fetch_assoc();
}
$stmt->close();

// Fetch favicon
$faviconPath = "logo/favicon.png";
$sqlfav = "SELECT favicon FROM system_setting LIMIT 1";
if ($stmt = $db->prepare($sqlfav)) {
    $stmt->execute();
    $stmt->bind_result($favicon);
    if ($stmt->fetch()) {
        $faviconPath = "logo/" . $favicon;
    }
    $stmt->close();
}

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['hero_submit'])) {
    $mainTitle = htmlspecialchars($_POST['main_title']);
    $subTitle = htmlspecialchars($_POST['sub_title']);
    $description = $_POST['description'];
    $status = isset($_POST['status']) ? 1 : 0;

    // Handle image upload
    $image = isset($_POST['existing_image']) ? $_POST['existing_image'] : '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "hero/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $targetFile = $targetDir . basename($_FILES["image"]["name"]);
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $image = $targetFile;
            } else {
                $_SESSION['error'] = "Error uploading image.";
                header("Location: admin-hero.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "File is not an image.";
            header("Location: admin-hero.php");
            exit();
        }
    }

    if ($hero) {
        // Update existing record
        $stmt = $db->prepare('UPDATE hero SET main_title = ?, sub_title = ?, description = ?, image = ?, status = ? WHERE idhero = ?');
        $stmt->bind_param('ssssii', $mainTitle, $subTitle, $description, $image, $status, $hero['idhero']);
    } else {
        // Insert new record
        $stmt = $db->prepare('INSERT INTO hero (main_title, sub_title, description, image, status) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssi', $mainTitle, $subTitle, $description, $image, $status);
    }

    if ($stmt->execute()) {
        $_SESSION['message'] = "Hero banner updated successfully.";
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
    $stmt->close();

    header("Location: admin-hero.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="robots" content="noindex, nofollow">
    <title>Hero Banner - Tee Mac Corporation</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">
    <script src="assets/js/theme-script.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">
    <link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .tmc-hero-current-img {
            max-width: 320px;
            max-height: 180px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e5e5e5;
        }
    </style>
</head>

<body>
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
                        <h3 class="page-title mb-1">Hero Banner</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Hero Banner</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                        <div class="pe-1 mb-2">
                            <a href="#" class="btn btn-outline-light bg-white btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="container mt-5">
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="about-container container p-4 bg-light rounded shadow-sm mt-5">
                                    <h5 class="mb-3">Hero Banner Settings</h5>
                                    <div class="row col-12">
                                        <div class="col-md-6 mb-3">
                                            <label for="main_title" class="form-label">Main Title *</label>
                                            <input type="text" class="form-control" id="main_title" name="main_title" placeholder="e.g. We Create Events That People Remember" value="<?php echo htmlspecialchars($hero['main_title'] ?? ''); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="sub_title" class="form-label">Sub Title</label>
                                            <input type="text" class="form-control" id="sub_title" name="sub_title" placeholder="e.g. EVENTS • EXPERIENCES • CONNECTIONS" value="<?php echo htmlspecialchars($hero['sub_title'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="image" class="form-label">Background Image</label>
                                            <?php if (!empty($hero['image'])): ?>
                                                <div class="mb-2">
                                                    <img src="<?php echo htmlspecialchars($hero['image']); ?>" alt="Current Hero Image" class="tmc-hero-current-img">
                                                </div>
                                            <?php endif; ?>
                                            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($hero['image'] ?? ''); ?>">
                                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                            <small class="text-muted">Leave empty to keep current image. Recommended: 1920x1080px</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="1" <?php echo ($hero['status'] ?? '') == '1' ? 'selected' : ''; ?>>Active</option>
                                                <option value="0" <?php echo ($hero['status'] ?? '') == '0' ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter hero description text"><?php echo htmlspecialchars($hero['description'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="text-left">
                                        <button type="submit" class="btn btn-primary px-5" name="hero_submit"><?php echo $hero ? 'Update' : 'Add'; ?></button>
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

    <script src="assets/js/jquery-3.7.1.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/js/bootstrap.bundle.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/js/feather.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/js/jquery.slimscroll.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/js/jquery.dataTables.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/js/dataTables.bootstrap5.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="assets/js/script.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['message'])): ?>
                Swal.fire({
                    toast: true, position: 'bottom-end', icon: 'success',
                    title: "<?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>",
                    showConfirmButton: false, timer: 8000, timerProgressBar: true
                });
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                Swal.fire({
                    toast: true, position: 'bottom-end', icon: 'error',
                    title: "<?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); ?>",
                    showConfirmButton: false, timer: 8000, timerProgressBar: true
                });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });
    </script>
</body>

</html>
