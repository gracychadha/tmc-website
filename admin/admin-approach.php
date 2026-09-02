<?php
session_start();
require_once('db/config.php');

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}

date_default_timezone_set('Asia/Kolkata');

// Fetch existing data
$approach = null;
$stmt = $db->prepare("SELECT * FROM approach LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $approach = $result->fetch_assoc();
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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approach_submit'])) {
    $title = htmlspecialchars($_POST['title'] ?? '');
    $subTitle = htmlspecialchars($_POST['sub_title'] ?? '');
    $step1 = htmlspecialchars($_POST['step1'] ?? '');
    $step2 = htmlspecialchars($_POST['step2'] ?? '');
    $step3 = htmlspecialchars($_POST['step3'] ?? '');
    $description1 = $_POST['description1'] ?? '';
    $description2 = $_POST['description2'] ?? '';
    $description3 = $_POST['description3'] ?? '';

    $upload_dir = "approach/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Handle image1 upload
    $image1 = $_POST['existing_image1'] ?? '';
    if (isset($_FILES['image1']) && $_FILES['image1']['error'] == 0) {
        $check = getimagesize($_FILES["image1"]["tmp_name"]);
        if ($check !== false) {
            $imageName1 = basename($_FILES["image1"]["name"]);
            if (move_uploaded_file($_FILES["image1"]["tmp_name"], $upload_dir . $imageName1)) {
                $image1 = $imageName1;
            }
        }
    }

    // Handle image2 upload
    $image2 = $_POST['existing_image2'] ?? '';
    if (isset($_FILES['image2']) && $_FILES['image2']['error'] == 0) {
        $check = getimagesize($_FILES["image2"]["tmp_name"]);
        if ($check !== false) {
            $imageName2 = basename($_FILES["image2"]["name"]);
            if (move_uploaded_file($_FILES["image2"]["tmp_name"], $upload_dir . $imageName2)) {
                $image2 = $imageName2;
            }
        }
    }

    // Handle image3 upload
    $image3 = $_POST['existing_image3'] ?? '';
    if (isset($_FILES['image3']) && $_FILES['image3']['error'] == 0) {
        $check = getimagesize($_FILES["image3"]["tmp_name"]);
        if ($check !== false) {
            $imageName3 = basename($_FILES["image3"]["name"]);
            if (move_uploaded_file($_FILES["image3"]["tmp_name"], $upload_dir . $imageName3)) {
                $image3 = $imageName3;
            }
        }
    }

    if ($approach) {
        $stmt = $db->prepare('UPDATE approach SET title = ?, sub_title = ?, step1 = ?, step2 = ?, step3 = ?, description1 = ?, description2 = ?, description3 = ?, image1 = ?, image2 = ?, image3 = ? WHERE id = ?');
        $stmt->bind_param('sssssssssssi', $title, $subTitle, $step1, $step2, $step3, $description1, $description2, $description3, $image1, $image2, $image3, $approach['id']);
    } else {
        $stmt = $db->prepare('INSERT INTO approach (title, sub_title, step1, step2, step3, description1, description2, description3, image1, image2, image3) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssssssss', $title, $subTitle, $step1, $step2, $step3, $description1, $description2, $description3, $image1, $image2, $image3);
    }

    if ($stmt->execute()) {
        $_SESSION['message'] = "Approach section updated successfully.";
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
    $stmt->close();

    header("Location: admin-approach.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="robots" content="noindex, nofollow">
    <title>Approach Section - Tee Mac Corporation</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">
    <script src="assets/js/theme-script.js"></script>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">
    <link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="assets/plugins/summernote/summernote-lite.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin-custom.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
                        <h3 class="page-title mb-1">Approach Section</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Approach Section</li>
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
                        <div class="container mt-4">
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="p-4 bg-light rounded shadow-sm">
                                    <h5 class="mb-4"><i class="fa-solid fa-seedling me-2"></i>Approach Section Settings</h5>

                                    <!-- Title & Sub Title -->
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3">
                                            <label for="sub_title" class="form-label fw-semibold">Sub Title</label>
                                            <input type="text" class="form-control" id="sub_title" name="sub_title" placeholder="e.g. Our Approach" value="<?php echo htmlspecialchars($approach['sub_title'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="title" class="form-label fw-semibold">Title</label>
                                            <input type="text" class="form-control" id="title" name="title" placeholder="Enter main title" value="<?php echo htmlspecialchars($approach['title'] ?? ''); ?>">
                                        </div>
                                    </div>

                                    <!-- Step 1 -->
                                    <div class="tmc-step-card">
                                        <h6><i class="fa-solid fa-1 me-1"></i> Step 1</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Step 1 Title</label>
                                                <input type="text" class="form-control" name="step1" placeholder="e.g. Consultation" value="<?php echo htmlspecialchars($approach['step1'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Step 1 Image</label>
                                                <?php if (!empty($approach['image1'])): ?>
                                                    <div class="mb-2">
                                                        <img src="approach/<?php echo htmlspecialchars($approach['image1']); ?>" alt="Step 1 Image" class="tmc-approach-img">
                                                    </div>
                                                <?php endif; ?>
                                                <input type="hidden" name="existing_image1" value="<?php echo htmlspecialchars($approach['image1'] ?? ''); ?>">
                                                <input type="file" class="form-control" name="image1" accept="image/*">
                                                <small class="text-muted">Leave empty to keep current image</small>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label fw-semibold">Step 1 Description</label>
                                                <div id="summernote-desc1"></div>
                                                <input type="hidden" name="description1" id="approach-desc1" value="<?php echo htmlspecialchars($approach['description1'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 2 -->
                                    <div class="tmc-step-card">
                                        <h6><i class="fa-solid fa-2 me-1"></i> Step 2</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Step 2 Title</label>
                                                <input type="text" class="form-control" name="step2" placeholder="e.g. Planning" value="<?php echo htmlspecialchars($approach['step2'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Step 2 Image</label>
                                                <?php if (!empty($approach['image2'])): ?>
                                                    <div class="mb-2">
                                                        <img src="approach/<?php echo htmlspecialchars($approach['image2']); ?>" alt="Step 2 Image" class="tmc-approach-img">
                                                    </div>
                                                <?php endif; ?>
                                                <input type="hidden" name="existing_image2" value="<?php echo htmlspecialchars($approach['image2'] ?? ''); ?>">
                                                <input type="file" class="form-control" name="image2" accept="image/*">
                                                <small class="text-muted">Leave empty to keep current image</small>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label fw-semibold">Step 2 Description</label>
                                                <div id="summernote-desc2"></div>
                                                <input type="hidden" name="description2" id="approach-desc2" value="<?php echo htmlspecialchars($approach['description2'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 3 -->
                                    <div class="tmc-step-card">
                                        <h6><i class="fa-solid fa-3 me-1"></i> Step 3</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Step 3 Title</label>
                                                <input type="text" class="form-control" name="step3" placeholder="e.g. Execution" value="<?php echo htmlspecialchars($approach['step3'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Step 3 Image</label>
                                                <?php if (!empty($approach['image3'])): ?>
                                                    <div class="mb-2">
                                                        <img src="approach/<?php echo htmlspecialchars($approach['image3']); ?>" alt="Step 3 Image" class="tmc-approach-img">
                                                    </div>
                                                <?php endif; ?>
                                                <input type="hidden" name="existing_image3" value="<?php echo htmlspecialchars($approach['image3'] ?? ''); ?>">
                                                <input type="file" class="form-control" name="image3" accept="image/*">
                                                <small class="text-muted">Leave empty to keep current image</small>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label fw-semibold">Step 3 Description</label>
                                                <div id="summernote-desc3"></div>
                                                <input type="hidden" name="description3" id="approach-desc3" value="<?php echo htmlspecialchars($approach['description3'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-start mt-3">
                                        <button type="submit" class="btn btn-primary px-5" name="approach_submit">
                                           <?php echo $approach ? 'Update' : 'Add'; ?>
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
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="assets/js/feather.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/plugins/summernote/summernote-lite.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="assets/js/admin-custom.js"></script>
    <script src="assets/js/rocket-loader.min.js" data-cf-settings="094c2cc781cee01c60adaad3-|49" defer=""></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            var desc1 = $('#approach-desc1').val();
            var desc2 = $('#approach-desc2').val();
            var desc3 = $('#approach-desc3').val();

            $('#summernote-desc1').summernote({ height: 150, placeholder: 'Enter Step 1 description...', tabsize: 2 });
            $('#summernote-desc2').summernote({ height: 150, placeholder: 'Enter Step 2 description...', tabsize: 2 });
            $('#summernote-desc3').summernote({ height: 150, placeholder: 'Enter Step 3 description...', tabsize: 2 });

            if (desc1) $('#summernote-desc1').summernote('code', desc1);
            if (desc2) $('#summernote-desc2').summernote('code', desc2);
            if (desc3) $('#summernote-desc3').summernote('code', desc3);

            $('form').on('submit', function() {
                $('#approach-desc1').val($('#summernote-desc1').summernote('code'));
                $('#approach-desc2').val($('#summernote-desc2').summernote('code'));
                $('#approach-desc3').val($('#summernote-desc3').summernote('code'));
            });
        });
    </script>

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
