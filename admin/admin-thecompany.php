<?php
session_start();
require_once('db/config.php');

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}



// Fetch existing data for The company section
$sql_check_choose = "SELECT * FROM why_choose_us LIMIT 1";
$result_choose = $db->query($sql_check_choose);

$choose_title = '';
$choose_description = '';
$image1 = '';
$image2 = '';
$image3 = '';
$image4 = '';

if ($result_choose->num_rows > 0) {
    $row_choose = $result_choose->fetch_assoc();
    $choose_title = $row_choose['title'];
    $choose_description = $row_choose['content'];
    $image1 = $row_choose['image1'];
    $image2 = $row_choose['image2'];
    $image3 = $row_choose['image3'];
    $image4 = $row_choose['image4'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  

    if (isset($_POST['choose_submit'])) {
        // Process The company section
        $choose_title = isset($_POST['choose_title']) ? $_POST['choose_title'] : '';
        $choose_description = isset($_POST['choose_description']) ? $_POST['choose_description'] : '';
        $image1 = isset($_FILES['image1']['name']) ? $_FILES['image1']['name'] : '';
        $image2 = isset($_FILES['image2']['name']) ? $_FILES['image2']['name'] : '';
        $image3 = isset($_FILES['image3']['name']) ? $_FILES['image3']['name'] : '';
        $image4 = isset($_FILES['image4']['name']) ? $_FILES['image4']['name'] : '';
        $image1_tmp = isset($_FILES['image1']['tmp_name']) ? $_FILES['image1']['tmp_name'] : '';
        $image2_tmp = isset($_FILES['image2']['tmp_name']) ? $_FILES['image2']['tmp_name'] : '';
        $image3_tmp = isset($_FILES['image3']['tmp_name']) ? $_FILES['image3']['tmp_name'] : '';
        $image4_tmp = isset($_FILES['image4']['tmp_name']) ? $_FILES['image4']['tmp_name'] : '';


        $upload_dir = "choose_us_icon/";
        date_default_timezone_set('Asia/Kolkata');
        $date = date("Y-m-d");

        $result_choose = $db->query($sql_check_choose);

        if ($result_choose->num_rows > 0) {
            $row_choose = $result_choose->fetch_assoc();
            $image1 = !empty($image1) ? $image1 : $row_choose['image1'];
            $image2 = !empty($image2) ? $image2 : $row_choose['image2'];
            $image3 = !empty($image3) ? $image3 : $row_choose['image3'];
            $image4 = !empty($image4) ? $image4 : $row_choose['image4'];
            
            if (!empty($image1_tmp)) move_uploaded_file($image1_tmp, $upload_dir . $image1);
            if (!empty($image2_tmp)) move_uploaded_file($image2_tmp, $upload_dir . $image2);
            if (!empty($image3_tmp)) move_uploaded_file($image3_tmp, $upload_dir . $image3);
            if (!empty($image4_tmp)) move_uploaded_file($image4_tmp, $upload_dir . $image4);
            
            $sql_update = "UPDATE why_choose_us SET title = ?, content = ?, image1 = ?, image2 = ?, image3 = ?, image4 = ? , date = ? WHERE id = ?";
            $stmt = $db->prepare($sql_update);
            $stmt->bind_param("sssssssi", $choose_title, $choose_description, $image1, $image2, $image3, $image4 , $date, $row_choose['id']);
            $stmt->execute();
            $_SESSION['message'] = "The company record updated successfully.";
        } else {
            if (!empty($image1_tmp)) move_uploaded_file($image1_tmp, $upload_dir . $image1);
            if (!empty($image2_tmp)) move_uploaded_file($image2_tmp, $upload_dir . $image2);
            if (!empty($image3_tmp)) move_uploaded_file($image3_tmp, $upload_dir . $image3);
            if (!empty($image4_tmp)) move_uploaded_file($image4_tmp, $upload_dir . $image4);
            
            $sql_insert = "INSERT INTO why_choose_us (title, content, image1, image2, image3, image4 , date) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql_insert);
            $stmt->bind_param("sssssss", $choose_title, $choose_description, $image1, $image2, $image3, $image4 , $date);
            $stmt->execute();
            $_SESSION['message'] = "The company record added successfully.";
        }
    }

    header("Location: admin-thecompany.php");
    exit();
}

// Fetch favicon
$sqlfav = "SELECT favicon FROM system_setting LIMIT 1";
if ($stmt = $db->prepare($sqlfav)) {
    $stmt->execute();
    $stmt->bind_result($favicon);
    if ($stmt->fetch()) {
        $faviconPath = "logo/" . $favicon;
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
    <title>About The Company - Tee Mac Corporation</title>

    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">
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
    a:hover {
        text-decoration: none;
    }
    </style>
</head>

<body>
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
                        <h3 class="page-title mb-1">About The Company</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="index.php">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">About The Company</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                        <div class="pe-1 mb-2">
                            <a href="#" class="btn btn-outline-light bg-white btn-icon" data-bs-toggle="tooltip"
                                data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                    </div>
                </div>



                <!-- The company Section -->
                <div class="row">
                    <div class="col-xxl-12 col-xl-12">
                        <div class="container mt-5">
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="about-container container p-4 bg-light rounded shadow-sm">

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="choose_title" class="form-label">Title</label>
                                            <input type="text" class="form-control" id="choose_title"
                                                name="choose_title" placeholder="Enter title"
                                                value="<?php echo htmlspecialchars($choose_title); ?>">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="summernote_choose" class="form-label">Description</label>
                                            <textarea class="form-control" id="summernote_choose"
                                                name="choose_description"
                                                placeholder="Enter description"><?php echo $choose_description; ?></textarea>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="image1" class="form-label">Image 1</label>
                                            <input type="file" class="form-control" id="image1" name="image1">
                                            <?php if (!empty($image1)): ?>
                                            <img src="choose_us_icon/<?php echo htmlspecialchars($image1); ?>"
                                                alt="Current Image 1" style="max-width: 100px; margin-top: 10px;">
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="image2" class="form-label">Image 2</label>
                                            <input type="file" class="form-control" id="image2" name="image2">
                                            <?php if (!empty($image2)): ?>
                                            <img src="choose_us_icon/<?php echo htmlspecialchars($image2); ?>"
                                                alt="Current Image 2" style="max-width: 100px; margin-top: 10px;">
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="image3" class="form-label">Image 3</label>
                                            <input type="file" class="form-control" id="image3" name="image3">
                                            <?php if (!empty($image3)): ?>
                                            <img src="choose_us_icon/<?php echo htmlspecialchars($image3); ?>"
                                                alt="Current Image 3" style="max-width: 100px; margin-top: 10px;">
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="image4" class="form-label">Image 4</label>
                                            <input type="file" class="form-control" id="image4" name="image4">
                                            <?php if (!empty($image4)): ?>
                                            <img src="choose_us_icon/<?php echo htmlspecialchars($image4); ?>"
                                                alt="Current Image 4" style="max-width: 100px; margin-top: 10px;">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="text-left">
                                        <button type="submit" class="btn btn-primary px-5"
                                            name="choose_submit"><?php echo $result_choose->num_rows > 0 ? 'Update' : 'Add'; ?></button>
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
    $(document).ready(function() {
        $('#summernote').summernote({
            height: 500,
            placeholder: 'Enter Description here...',
            tabsize: 2
        });

        $('#summernote_choose').summernote({
            height: 350,
            placeholder: 'Enter Description here...',
            tabsize: 2
        });
    });
    </script>
</body>

</html>