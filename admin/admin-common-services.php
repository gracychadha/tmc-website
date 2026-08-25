<?php
session_start();
require_once('db/config.php');

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}





// Fetch existing data for Counters section
$sql_check_common_services = "SELECT * FROM commonservices LIMIT 1";
$result_common_services = $db->query($sql_check_common_services);

$title1 = '';
$title2 = '';
$title3 = '';
$title4 = '';

if ($result_common_services->num_rows > 0) {
    $row_common_services = $result_common_services->fetch_assoc();
    $title1 = $row_common_services['title1'];
    $title2 = $row_common_services['title2'];
    $title3 = $row_common_services['title3'];
    $title4 = $row_common_services['title4'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    if (isset($_POST['counter_submit'])) {
        // Process Counters section
        $title1 = isset($_POST['title1']) ? $_POST['title1'] : '';
        $title2 = isset($_POST['title2']) ? $_POST['title2'] : '';
        $title3 = isset($_POST['title3']) ? $_POST['title3'] : '';
        $title4 = isset($_POST['title4']) ? $_POST['title4'] : '';
       

        $result_common_services = $db->query($sql_check_common_services);

        if ($result_common_services->num_rows > 0) {
            $row_common_services = $result_common_services->fetch_assoc();
            $sql_update = "UPDATE commonservices SET title1 = ?,  title2 = ?, title3 = ?,  title4 = ?  WHERE id = ?";
            $stmt = $db->prepare($sql_update);
            $stmt->bind_param("ssssi", $title1, $title2, $title3, $title4 , $row_common_services['id']);
            $stmt->execute();
            $_SESSION['message'] = "Common services updated successfully.";
        } else {
            $sql_insert = "INSERT INTO commonservices (title1,  title2,  title3,  title4 ) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($sql_insert);
            $stmt->bind_param("ssss", $title1,$title2, $title3, $title4, );
            $stmt->execute();
            $_SESSION['message'] = "Common Services added successfully.";
        }
    }

    header("Location: admin-common-services.php");
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
    <title>Common Services - Tee Mac Corporation</title>

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
        a:hover { text-decoration: none; }
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
                    <h3 class="page-title mb-1">Common Services</h3>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="index.php">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Common Services</li>
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
            
           

         

            <!-- Counters Section -->
            <div class="row">
                <div class="col-xxl-12 col-xl-12">
                    <div class="container mt-5">
                        <form action="" method="POST">
                            <div class="about-container container p-4 bg-light rounded shadow-sm">
                               
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="title1" class="form-label">Common Service 1 Title</label>
                                        <input type="text" class="form-control" id="title1" name="title1" placeholder="Enter common services 1 title" value="<?php echo htmlspecialchars($title1); ?>">
                                    </div>
                                   
                                    <div class="col-md-6 mb-3">
                                        <label for="title2" class="form-label">Common Service 2 Title</label>
                                        <input type="text" class="form-control" id="title2" name="title2" placeholder="Enter common services 2 title" value="<?php echo htmlspecialchars($title2); ?>">
                                    </div>
                                   
                                    <div class="col-md-6 mb-3">
                                        <label for="title3" class="form-label">Common Service 3 Title</label>
                                        <input type="text" class="form-control" id="title3" name="title3" placeholder="Enter common services 3 title" value="<?php echo htmlspecialchars($title3); ?>">
                                    </div>
                                   
                                    <div class="col-md-6 mb-3">
                                        <label for="title4" class="form-label">Common Service 4 Title</label>
                                        <input type="text" class="form-control" id="title4" name="title4" placeholder="Enter common services 4 title" value="<?php echo htmlspecialchars($title4); ?>">
                                    </div>
                                   
                                </div>
                                <div class="text-left">
                                    <button type="submit" class="btn btn-primary px-5" name="counter_submit"><?php echo $result_common_services->num_rows > 0 ? 'Update' : 'Add'; ?></button>
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
            height: 200,
            placeholder: 'Enter Description here...',
            tabsize: 2
        });

        $('#summernote_choose').summernote({
            height: 200,
            placeholder: 'Enter Description here...',
            tabsize: 2
        });
    });
</script>
</body>
</html>