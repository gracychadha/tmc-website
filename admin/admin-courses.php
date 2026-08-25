<?php
session_start();
require_once('db/config.php');

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}


// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set default timezone
date_default_timezone_set('Asia/Kolkata');

// Handle form submissions for adding, editing, or deleting courses
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new course
    if (isset($_POST['add-form']) && $_SERVER['REQUEST_METHOD'] == "POST") {
        // Capture and sanitize form inputs
        $title = htmlspecialchars(strip_tags(trim($_POST['title'])));
        $assessment = htmlspecialchars(strip_tags(trim($_POST['assessment'])));
        $duration = htmlspecialchars(strip_tags(trim($_POST['duration'])));
        $certification = htmlspecialchars(strip_tags(trim($_POST['certification'])));
        $content = $_POST['content']; // Content may contain HTML
        $date = date("Y-m-d");

        // Handle file upload (Featured image)
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
            $targetDir = "courses/";
            $targetFile = $targetDir . basename($_FILES["featured_image"]["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Validate file type
            $check = getimagesize($_FILES["featured_image"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["featured_image"]["tmp_name"], $targetFile)) {
                    $featuredImage = $targetFile;
                } else {
                    $_SESSION['error'] = "Sorry, there was an error uploading your file.";
                    header('location: admin-courses.php');
                    exit();
                }
            } else {
                $_SESSION['error'] = "File is not an image.";
                header('location: admin-courses.php');
                exit();
            }
        } else {
            $featuredImage = '';
        }

        // Insert course data into the database
        $stmt = $db->prepare("INSERT INTO courses (title, duration, assessment, certification, image, content, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $title, $duration, $assessment, $certification, $featuredImage, $content, $date);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Course added successfully!";
        } else {
            error_log("Add query failed: " . $stmt->error);
            $_SESSION['error'] = "Error: " . $stmt->error;
        }

        $stmt->close();
        header('location: admin-courses.php');
        exit();
    }

    // Edit existing course
    if (isset($_POST['edit-courses']) && $_SERVER['REQUEST_METHOD'] == "POST") {
        // Capture and sanitize form inputs
        $idcourses = intval($_POST['idcourses']);
        $title = htmlspecialchars(strip_tags(trim($_POST['title'])));
        $duration = htmlspecialchars(strip_tags(trim($_POST['duration'])));
        $assessment = htmlspecialchars(strip_tags(trim($_POST['assessment'])));
        $certification = htmlspecialchars(strip_tags(trim($_POST['certification'])));
        $status = isset($_POST['status']) ? 1 : 0;
        $content = $_POST['content']; // Content may contain HTML

        // Log input data for debugging
        error_log("POST data: " . print_r($_POST, true));
        error_log("FILES data: " . print_r($_FILES, true));

        // Handle image upload
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
            $targetDir = "courses/";
            $targetFile = $targetDir . basename($_FILES["featured_image"]["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Validate file type
            $check = getimagesize($_FILES["featured_image"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["featured_image"]["tmp_name"], $targetFile)) {
                    $image_path = $targetFile;
                } else {
                    $_SESSION['error'] = "Error uploading image.";
                    header('location: admin-courses.php');
                    exit();
                }
            } else {
                $_SESSION['error'] = "File is not an image.";
                header('location: admin-courses.php');
                exit();
            }
        } else {
            $image_path = $_POST['existing_image'];
        }

        // Update course data in the database
        $stmt = $db->prepare("UPDATE courses SET title = ?, duration = ?, assessment = ?, certification = ?, status = ?, content = ?, image = ? WHERE idcourses = ?");
        $stmt->bind_param("sssssssi", $title, $duration, $assessment, $certification, $status, $content, $image_path, $idcourses);

        // Log the query for debugging
        error_log("SQL Query: UPDATE courses SET title = '$title', duration = '$duration', assessment = '$assessment', certification = '$certification', status = $status, content = '$content', image = '$image_path' WHERE idcourses = $idcourses");

        if ($stmt->execute()) {
            $_SESSION['message'] = "Course details updated successfully!";
        } else {
            error_log("Update query failed: " . $stmt->error);
            $_SESSION['error'] = "Error updating course: " . $stmt->error;
        }

        $stmt->close();
        header('location: admin-courses.php');
        exit();
    }

    // Delete course(s)
    if (isset($_POST['delete-form']) && $_SERVER['REQUEST_METHOD'] == "POST") {
        if (!empty($_POST['ids'])) {
            $ids = $_POST['ids'];
            $idsArray = explode(',', $ids);

            // Prepare the SQL statement with placeholders
            $placeholders = implode(',', array_fill(0, count($idsArray), '?'));
            $stmt = $db->prepare("DELETE FROM courses WHERE idcourses IN ($placeholders)");

            // Bind the parameters dynamically
            $types = str_repeat('i', count($idsArray));
            $stmt->bind_param($types, ...$idsArray);

            if ($stmt->execute()) {
                $_SESSION['message'] = ($stmt->affected_rows > 0)
                    ? "Course(s) deleted successfully!"
                    : "No course found with those IDs.";
            } else {
                error_log("Delete query failed: " . $stmt->error);
                $_SESSION['error'] = "Error deleting course(s): " . $stmt->error;
            }

            $stmt->close();
        } else {
            $_SESSION['error'] = "Invalid course IDs.";
        }

        header('location: admin-courses.php');
        exit();
    }

    // Redirect after processing
    header('location: admin-courses.php');
    exit();
}

// Fetch courses data from the database
$courses_query = "SELECT * FROM courses ORDER BY idcourses DESC";
$resultcourses = $db->query($courses_query);

if (!$resultcourses) {
    error_log("Fetch query failed: " . $db->error);
    $_SESSION['error'] = "Error fetching data: " . $db->error;
    header('location: admin-courses.php');
    exit();
}

// Fetch favicon from system settings
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
    <title>Courses - Tee Mac Corporation</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">

    <!-- Theme Script -->
    <script src="assets/js/theme-script.js"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- Feather CSS -->
    <link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">

    <!-- Daterangepicker CSS -->
    <link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">

    <!-- Summernote CSS -->
    <link rel="stylesheet" href="assets/plugins/summernote/summernote-lite.min.css">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Notyf CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css" />

    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.1/dist/summernote.min.css" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

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
                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Courses</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="dashboard.php">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Courses</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                        <div class="pe-1 mb-2">
                            <a href="#" class="btn btn-outline-light bg-white btn-icon me-1" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                        <div class="pe-1 mb-2">
                            <button type="button" class="btn btn-outline-light bg-white btn-icon me-1" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Print" data-bs-original-title="Print">
                                <i class="ti ti-printer"></i>
                            </button>
                        </div>
                        <div class="dropdown me-2 mb-2">
                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-light fw-medium d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                <i class="ti ti-file-export me-2"></i>Export
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-3">
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item rounded-1" id="export-pdf"><i class="ti ti-file-type-pdf me-1"></i>Export as PDF</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item rounded-1" id="export-word"><i class="ti ti-file-type-doc me-1"></i>Export as Word</a>
                                </li>
                            </ul>
                        </div>
                        <div class="mb-2">
                            <a href="#" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_courses"><i class="ti ti-square-rounded-plus me-2"></i>Add Courses</a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Filter Section -->
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                        <h4 class="mb-3">Courses List</h4>
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="dropdown mb-3 me-2">
                                <a href="javascript:void(0);" class="btn btn-outline-light bg-white dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-filter me-2"></i>Filter</a>
                                <div class="dropdown-menu drop-width"></div>
                            </div>
                            <div class="dropdown mb-3">
                                <a href="javascript:void(0);" class="btn btn-outline-light bg-white dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-sort-ascending-2 me-2"></i>Sort by A-Z</a>
                                <ul class="dropdown-menu p-3">
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1 active">Ascending</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Descending</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Recently Viewed</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item rounded-1">Recently Added</a></li>
                                </ul>
                            </div>
                            <div class="dropdown mb-3 me-2">
                                <a href="javascript:void(0);" class="btn btn-outline-light bg-white delete-btn" id="delete-selected" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-trash me-2"></i>Delete Selected</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0 py-3">
                        <!-- Courses List -->
                        <div class="custom-datatable-filter table-responsive">
                            <table class="table datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort">
                                            <div class="form-check form-check-md">
                                                <input class="form-check-input" type="checkbox" id="select-all">
                                            </div>
                                        </th>
                                        <th>Title</th>
                                        <th>Photo</th>
                                        <th>Assessment</th>
                                        <th>Duration</th>
                                        <th>Certification</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($resultcourses->num_rows > 0) {
                                        while ($rowcourses = $resultcourses->fetch_assoc()) {
                                            $imagePath = htmlspecialchars($rowcourses['image']);
                                    ?>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input delete-checkbox" type="checkbox" value="<?php echo $rowcourses['idcourses']; ?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="ms-2">
                                                            <p class="text-dark mb-0"><?php echo htmlspecialchars($rowcourses['title']); ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><img src="<?php echo $imagePath; ?>" style="width: 100%;" alt=""></td>
                                                <td><?php echo htmlspecialchars($rowcourses['assessment']); ?></td>
                                                <td><?php echo htmlspecialchars($rowcourses['duration']); ?></td>
                                                <td><?php echo htmlspecialchars($rowcourses['certification']); ?></td>
                                                <td>
                                                    <?php if ($rowcourses['status'] == 1) { ?>
                                                        <span class="badge badge-soft-success d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Active</span>
                                                    <?php } else { ?>
                                                        <span class="badge badge-soft-danger d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Inactive</span>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($rowcourses['created_at']); ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="#" class="btn btn-outline-light bg-white btn-icon edit-btn d-flex align-items-center justify-content-center rounded-circle p-0 me-2"
                                                           data-id="<?php echo $rowcourses['idcourses']; ?>"
                                                           data-title="<?php echo htmlspecialchars($rowcourses['title']); ?>"
                                                           data-duration="<?php echo htmlspecialchars($rowcourses['duration']); ?>"
                                                           data-assessment="<?php echo htmlspecialchars($rowcourses['assessment']); ?>"
                                                           data-certification="<?php echo htmlspecialchars($rowcourses['certification']); ?>"
                                                           data-image="<?php echo htmlspecialchars($rowcourses['image']); ?>"
                                                           data-status="<?php echo $rowcourses['status']; ?>"
                                                           data-content="<?php echo htmlspecialchars($rowcourses['content']); ?>"
                                                           data-bs-toggle="modal" data-bs-target="#edit_role"><i class="ti ti-edit-circle text-primary"></i></a>
                                                        <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center delete-btn rounded-circle p-0 me-3" data-id="<?php echo $rowcourses['idcourses']; ?>" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash-x text-danger"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /Courses List -->
                    </div>
                </div>
                <!-- /Filter Section -->
            </div>
        </div>
        <!-- /Page Wrapper -->

        <!-- Add Course Modal -->
        <div class="modal fade" id="add_courses">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">New Course</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <form action="admin-courses.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <!-- Title input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Course Title</label>
                                    <input type="text" name="title" placeholder="Enter course title" class="form-control" required>
                                </div>
                                <!-- Duration input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Duration</label>
                                    <input type="text" name="duration" class="form-control" placeholder="Enter Duration" required>
                                </div>
                                <!-- Assessment input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Assessment</label>
                                    <input type="text" name="assessment" placeholder="Enter Assessment" class="form-control" required>
                                </div>
                                <!-- Certification input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Certification</label>
                                    <input type="text" name="certification" class="form-control" placeholder="Enter Certification" required>
                                </div>
                                <!-- Image upload -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Image</label>
                                    <div class="d-flex align-items-center upload-pic flex-wrap row-gap-3">
                                        <div class="d-flex align-items-center justify-content-center avatar avatar-xxl border border-dashed me-2 flex-shrink-0 text-dark frames">
                                            <i class="ti ti-photo-plus fs-16"></i>
                                        </div>
                                        <div class="profile-upload">
                                            <div class="profile-uploader d-flex align-items-center">
                                                <div class="drag-upload-btn mb-3">
                                                    Upload
                                                    <input type="file" class="form-control image-sign" name="featured_image">
                                                </div>
                                            </div>
                                            <p>Upload image size 4MB, Format JPG, PNG, SVG</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Content input -->
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <label for="title" class="form-label">Content *</label>
                                    <div id="summernote"></div>
                                    <input type="hidden" name="content" id="courses-content">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" name="add-form" class="btn btn-primary">Add Course</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Add Course Modal -->

        <!-- Edit Course Modal -->
        <div class="modal fade" id="edit_role" tabindex="-1" aria-labelledby="edit_serviceLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="edit_serviceLabel">Edit Course</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <form id="serviceForm" action="admin-courses.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <!-- Hidden input for course ID -->
                                <input type="hidden" name="idcourses" id="edit-courses-id">
                                <!-- Title input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Course Title</label>
                                    <input type="text" name="title" id="edit-title" placeholder="Enter course title" class="form-control" required>
                                </div>
                                <!-- Duration input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Duration</label>
                                    <input type="text" name="duration" id="edit-duration" class="form-control" placeholder="Enter Duration" required>
                                </div>
                                <!-- Assessment input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Assessment</label>
                                    <input type="text" name="assessment" id="edit-assessment" placeholder="Enter Assessment" class="form-control" required>
                                </div>
                                <!-- Certification input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Certification</label>
                                    <input type="text" name="certification" id="edit-certification" class="form-control" placeholder="Enter Certification" required>
                                </div>
                                <!-- Image upload -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Image</label>
                                    <div class="d-flex align-items-center upload-pic flex-wrap row-gap-3">
                                        <div class="d-flex align-items-center justify-content-center avatar avatar-xxl border border-dashed me-2 flex-shrink-0 text-dark frames">
                                            <img id="edit-image-preview" src="" alt="Course Image" class="img-fluid">
                                        </div>
                                        <div class="profile-upload">
                                            <div class="profile-uploader d-flex align-items-center">
                                                <div class="drag-upload-btn mb-3">
                                                    Upload
                                                    <input type="hidden" name="existing_image" id="existing-image">
                                                    <input type="file" class="form-control image-sign" name="featured_image">
                                                </div>
                                            </div>
                                            <p>Upload image size 4MB, Format JPG, PNG, SVG</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Status input -->
                                <div class="col-md-6 modal-status-toggle d-flex align-items-center justify-content-between mb-4">
                                    <div class="status-title">
                                        <label for="title" class="form-label">Status *</label>
                                        <p>Change the Status by toggle</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="edit-status" name="status">
                                    </div>
                                </div>
                                <!-- Content input -->
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <label for="title" class="form-label">Content *</label>
                                    <div id="summernote1"></div>
                                    <input type="hidden" name="content" id="edit-contents">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" name="edit-courses" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Edit Course Modal -->

        <!-- Delete Course Modal -->
        <div class="modal fade" id="delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="admin-courses.php">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="ids" id="delete-courses-ids" value="">
                        <div class="modal-body text-center">
                            <span class="delete-icon">
                                <i class="ti ti-trash-x"></i>
                            </span>
                            <h4>Confirm Deletion</h4>
                            <p>You want to delete all the marked items, this cannot be undone once you delete.</p>
                            <div class="d-flex justify-content-center">
                                <a href="#" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                                <button type="submit" name="delete-form" class="btn btn-danger">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Delete Course Modal -->

        <footer class="footer">
            <div class="mt-5 text-center">
                <?php require_once('copyright.php'); ?>
            </div>
        </footer>
    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Core JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <!-- Daterangepicker JS -->
    <script src="assets/js/moment.js"></script>
    <script src="assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>

    <!-- Feather Icon JS -->
    <script src="assets/js/feather.min.js"></script>

    <!-- Slimscroll JS -->
    <script src="assets/js/jquery.slimscroll.min.js"></script>

    <!-- Datatable JS -->
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap5.min.js"></script>

    <!-- Select2 JS -->
    <script src="assets/plugins/select2/js/select2.min.js"></script>

    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.1/dist/summernote.min.js"></script>

   
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/vfs_fonts.js"></script>
    <script src="assets/js/export.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Summernote for add form
            $('#summernote').summernote({
                height: 200,
                placeholder: 'Enter content here...'
            });

            // Initialize Summernote for edit form
            $('#summernote1').summernote({
                height: 200,
                placeholder: 'Enter content here...'
            });

            // Handle add form submission
            $('form').on('submit', function(e) {
                let content = $('#summernote').summernote('code');
                $('#courses-content').val(content);
            });

            // Handle edit button click
            $('.edit-btn').on('click', function() {
                console.log("Edit button clicked for ID:", $(this).data('id')); // Debug output
                var coursesId = $(this).data('id');
                var title = $(this).data('title');
                var duration = $(this).data('duration');
                var assessment = $(this).data('assessment');
                var certification = $(this).data('certification');
                var image = $(this).data('image');
                var status = $(this).data('status');
                var content = $(this).data('content');

                // Populate edit form
                $('#edit-courses-id').val(coursesId);
                $('#edit-title').val(title);
                $('#edit-duration').val(duration);
                $('#edit-assessment').val(assessment);
                $('#edit-certification').val(certification);
                $('#edit-status').prop('checked', status == 1);
                $('#edit-contents').val(content);
                $('#edit-image-preview').attr('src', image);
                $('#existing-image').val(image);
                $('#summernote1').summernote('code', content);
            });

            // Handle edit form submission
            $('#serviceForm').on('submit', function() {
                var content = $('#summernote1').summernote('code');
                console.log("Summernote content:", content); // Debug output
                $('#edit-contents').val(content);
            });

            // Handle delete button click
            $('.delete-btn').on('click', function() {
                var coursesId = $(this).data('id');
                console.log("Clicked Delete for ID:", coursesId); // Debug output
                $('#delete-courses-ids').val(coursesId);
                $('#delete-modal').modal('show');
            });

            // Handle delete selected button click
            $('#delete-selected').click(function() {
                var selectedIds = [];
                $('.delete-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length > 0) {
                    $('#delete-courses-ids').val(selectedIds.join(','));
                    $('#delete-modal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Selection',
                        text: 'Please select at least one course to delete.',
                        toast: true,
                        position: 'bottom-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });

            // Handle select all checkbox
            $('#select-all').on('click', function() {
                $('.delete-checkbox').prop('checked', this.checked);
            });
        });
    </script>
</body>

</html>
