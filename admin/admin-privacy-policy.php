<?php
session_start();
require_once('db/config.php');

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}



// Fetch existing data for The company section
$sql_check_choose = "SELECT * FROM policy LIMIT 1";
$result_choose = $db->query($sql_check_choose);
$description = '';


if ($result_choose->num_rows > 0) {
    $row_choose = $result_choose->fetch_assoc();
    $description = $row_choose['content'];

}

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    if (isset($_POST['choose_submit'])) {
        // Process The company section
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $result_choose = $db->query($sql_check_choose);
        $sql_update = "UPDATE policy SET  content = ? WHERE id = ?";
        $stmt = $db->prepare($sql_update);
        $stmt->bind_param("si", $choose_description, $row_choose['idpolicy']);
        $stmt->execute();
        $_SESSION['message'] = "The company record updated successfully.";
        // if ($result_choose->num_rows > 0) {
        //     $row_choose = $result_choose->fetch_assoc();
        //     $sql_update = "UPDATE policy SET  content = ? WHERE id = ?";
        //     $stmt = $db->prepare($sql_update);
        //     $stmt->bind_param("si", $choose_description, $row_choose['idpolicy']);
        //     $stmt->execute();
        //     $_SESSION['message'] = "The company record updated successfully.";
        // } else {


        //     $sql_insert = "INSERT INTO policy ( content) VALUES (?)";
        //     $stmt = $db->prepare($sql_insert);
        //     $stmt->bind_param("s", $choose_title, $choose_description, $image1, $image2, $image3, $image4, $date);
        //     $stmt->execute();
        //     $_SESSION['message'] = "The company record added successfully.";
        // }
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
    <title>Privacy Policy - Tee Mac Corporation</title>

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
        document.addEventListener('DOMContentLoaded', function () {
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
                        <h3 class="page-title mb-1">Privacy policy</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="index.php">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Privacy Policy</li>
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
                                       
                                        <div class="col-md-12 mb-3">
                                            <label for="summernote_choose" class="form-label">Description</label>
                                            <textarea class="form-control" id="summernote_choose"
                                                name="choose_description"
                                                placeholder="Enter description"><?php echo $description; ?></textarea>
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
        $(document).ready(function () {
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