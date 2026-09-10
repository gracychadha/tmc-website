<?php
session_start();
require_once('db/config.php');

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}

date_default_timezone_set('Asia/Kolkata');

// Fetch existing data
$about = null;
$stmt = $db->prepare("SELECT * FROM achievements LIMIT 1");
$stmt->execute();
$result_about = $stmt->get_result();
if ($result_about->num_rows > 0) {
    $about = $result_about->fetch_assoc();
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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['about_submit'])) {
    $title = isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '';
    $subTitle = isset($_POST['sub_title']) ? htmlspecialchars($_POST['sub_title']) : '';
    $content = isset($_POST['content']) ? $_POST['content'] : '';
    $benefits = isset($_POST['benefits']) ? $_POST['benefits'] : '';

    $upload_dir = "achievements/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Handle image upload
    $image = isset($_POST['existing_image']) ? $_POST['existing_image'] : '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageName = basename($_FILES["image"]["name"]);
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $upload_dir . $imageName)) {
                $image = $imageName;
            }
        }
    }

  

    if ($about) {
        // Update
        $stmt = $db->prepare('UPDATE achievements SET title = ?, sub_title = ?, content = ?, benefits = ?, image = ? WHERE id = ?');
        $stmt->bind_param('sssssi', $title, $subTitle, $content, $benefits, $image, $about['id']);
    } else {
        // Insert
        $stmt = $db->prepare('INSERT INTO achievements (title, sub_title, content, benefits, image ) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $title, $subTitle, $content, $benefits, $image);
    }

    if ($stmt->execute()) {
        $_SESSION['message'] = "Achievement section updated successfully.";
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
    $stmt->close();

    header("Location: admin-acheivements.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="robots" content="noindex, nofollow">
    <title>About Page - Tee Mac Corporation</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">
    <script src="assets/js/theme-script.js" ></script>
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
                        <h3 class="page-title mb-1">About Page</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Achievement Section</li>
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
                                <div class="about-container container p-4 bg-light rounded shadow-sm">
                                    <h5 class="mb-3">Achievement Section Settings</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="sub_title" class="form-label">Sub Title</label>
                                            <input type="text" class="form-control" id="sub_title" name="sub_title" placeholder="e.g. About Us" value="<?php echo htmlspecialchars($about['sub_title'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="title" class="form-label">Title</label>
                                            <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" value="<?php echo htmlspecialchars($about['title'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="image" class="form-label">Image</label>
                                            <?php if (!empty($about['image'])): ?>
                                                <div class="mb-2">
                                                    <img src="<?php echo htmlspecialchars(strpos($about['image'], '/') !== false ? $about['image'] : 'achievements/' . $about['image']); ?>" alt="Current Image" class="tmc-about-img">
                                                </div>
                                            <?php endif; ?>
                                            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($about['image'] ?? ''); ?>">
                                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                            <small class="text-muted">Leave empty to keep current image</small>
                                        </div>
                                       
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Content</label>
                                            <div id="summernote"></div>
                                            <input type="hidden" name="content" id="about-content" value="<?php echo htmlspecialchars($about['content'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Benefits</label>
                                            <div id="summernote1"></div>
                                            <input type="hidden" name="benefits" id="about-benefits" value="<?php echo htmlspecialchars($about['benefits'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="text-left">
                                        <button type="submit" class="btn btn-primary px-5" name="about_submit"><?php echo $about ? 'Update' : 'Add'; ?></button>
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

    <script src="assets/js/jquery-3.7.1.min.js" ></script>
    <script src="assets/js/bootstrap.bundle.min.js" ></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js" ></script>
    <script src="assets/js/feather.min.js" ></script>
    <script src="assets/js/jquery.slimscroll.min.js" ></script>
    <script src="assets/js/jquery.dataTables.min.js" ></script>
    <script src="assets/js/dataTables.bootstrap5.min.js" ></script>
    <script src="assets/plugins/summernote/summernote-lite.min.js" ></script>
    <script src="assets/js/script.js" ></script>
    <script src="assets/js/admin-custom.js"></script>
    <script src="assets/js/rocket-loader.min.js" data-cf-settings="094c2cc781cee01c60adaad3-|49" defer=""></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {

            var existingContent = $('#about-content').val();
            var existingBenefits = $('#about-benefits').val();

            // Initialize Content Summernote
            $('#summernote').summernote({
                height: 200,
                placeholder: 'Enter Content here...',
                tabsize: 2
            });

            // Initialize Benefits Summernote
            $('#summernote1').summernote({
                height: 200,
                placeholder: 'Enter Benefits here...',
                tabsize: 2
            });

            // Load existing content
            if (existingContent) {
                $('#summernote').summernote('code', existingContent);
            }

            // Load existing benefits
            if (existingBenefits) {
                $('#summernote1').summernote('code', existingBenefits);
            }

            // Before submit, copy Summernote HTML into hidden inputs
            $('form').on('submit', function() {

                $('#about-content').val(
                    $('#summernote').summernote('code')
                );

                $('#about-benefits').val(
                    $('#summernote1').summernote('code')
                );

            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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