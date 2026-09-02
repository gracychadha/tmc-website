<?php
session_start();
require_once('db/config.php');

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}


// Set default timezone
date_default_timezone_set('Asia/Kolkata');

// Handle form submissions for adding a new team post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form is being submitted for adding a new team
    // Handle file upload (Featured image)
    if (isset($_POST['add-form']) && $_SERVER['REQUEST_METHOD'] == "POST") {
        // Capture form inputs
        $name = htmlspecialchars(strip_tags(trim($_POST['name'])));
        $designation = htmlspecialchars(strip_tags(trim($_POST['designation'])));
        $phone = htmlspecialchars(strip_tags(trim($_POST['phone'])));
        $email = htmlspecialchars(strip_tags(trim($_POST['email'])));
        $facebook = htmlspecialchars(strip_tags(trim($_POST['facebook'])));
        $linkedin = htmlspecialchars(strip_tags(trim($_POST['linkedin'])));
        $twitter = htmlspecialchars(strip_tags(trim($_POST['twitter'])));
        $content = $_POST['content'];
        $status = isset($_POST['status']) ? 0 : 1; // Convert checkbox value to 1 or 0


        date_default_timezone_set('Asia/Kolkata');
        $date = date("Y-m-d");





        // Handle file upload (Featured image)
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
            $targetDir = "team/"; // Define the directory for uploading the image
            $targetFile = $targetDir . basename($_FILES["featured_image"]["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Check if image file is a valid image (validate file type)
            $check = getimagesize($_FILES["featured_image"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["featured_image"]["tmp_name"], $targetFile)) {
                    $featuredImage = $targetFile; // Assign the uploaded file path to the image variable
                } else {
                    $_SESSION['message'] = "Sorry, there was an error uploading your file.";
                    header('Location: admin-team.php');
                    exit();
                }
            } else {
                $_SESSION['message'] = "File is not an image.";
                header('Location: admin-team.php');
                exit();
            }
        } else {
            $featuredImage = ''; // If no image is uploaded, leave it empty
        }

        // Insert the team data into the database
        // Prepare the SQL query
        $stmt = $db->prepare("INSERT INTO team_members (member_name, role, email, phone, linkedin, twitter, facebook, bio, profile_picture, date, status) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Corrected bind_param() with 11 parameters
        $stmt->bind_param("ssssssssssi", $name, $designation, $email, $phone, $linkedin, $twitter, $facebook, $content, $featuredImage, $date, $status);


        if ($stmt->execute()) {
            $_SESSION['message'] = "Team Member added successfully!";
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
        }


        header('Location: admin-team.php');
        exit();
    }


    if (isset($_POST['edit-career']) && $_SERVER['REQUEST_METHOD'] == "POST") {
        $idteam_members = intval($_POST['idteam_members']);
        // Capture form inputs
        $name = htmlspecialchars(strip_tags(trim($_POST['name'])));
        $designation = htmlspecialchars(strip_tags(trim($_POST['designation'])));
        $phone = htmlspecialchars(strip_tags(trim($_POST['phone'])));
        $email = htmlspecialchars(strip_tags(trim($_POST['email'])));
        $facebook = htmlspecialchars(strip_tags(trim($_POST['facebook'])));
        $linkedin = htmlspecialchars(strip_tags(trim($_POST['linkedin'])));
        $twitter = htmlspecialchars(strip_tags(trim($_POST['twitter'])));
        $content = $_POST['content'];
        $status = isset($_POST['status']) ? 1 : 0; // Convert checkbox value to 1 or 0





        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image = $_FILES['image']['name'];
            $target_dir = "team/";
            $target_file = $target_dir . basename($image);
            move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
            $image_path = $target_file; // Save the image path with the folder name
        } else {
            $image_path = $_POST['existing_image']; // Use existing image if no new image is uploaded
        }

        $stmt = $db->prepare("UPDATE team_members SET member_name =?, role=?, email=?, phone=?, linkedin=?, twitter=?, facebook=?, bio=?, profile_picture=?,  status=? WHERE idteam_members = ?");
        $stmt->bind_param("ssssssssssi", $name, $designation, $email, $phone, $linkedin, $twitter, $facebook, $content, $image_path, $status, $idteam_members);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Team post updated successfully!";
        } else {
            $_SESSION['message'] = "Error updating career post: " . $stmt->error;
        }

        $stmt->close();
        header('Location: admin-team.php');
        exit();
    }


    if (isset($_POST['delete-form']) && $_SERVER['REQUEST_METHOD'] == "POST") {
        if (!empty($_POST['ids'])) { // Ensure the IDs exist
            $ids = $_POST['ids'];
            $idsArray = explode(',', $ids);

            // Prepare the SQL statement with placeholders
            $placeholders = implode(',', array_fill(0, count($idsArray), '?'));
            $stmt = $db->prepare("DELETE FROM team_members WHERE idteam_members IN ($placeholders)");

            // Bind the parameters dynamically
            $types = str_repeat('i', count($idsArray));
            $stmt->bind_param($types, ...$idsArray);

            if ($stmt->execute()) {
                $_SESSION['message'] = ($stmt->affected_rows > 0)
                    ? "Team member deleted successfully!"
                    : "No career post found with those IDs.";
            } else {
                $_SESSION['message'] = "Error deleting career post(s): " . $stmt->error;
            }

            $stmt->close();
        } else {
            $_SESSION['message'] = "Invalid career IDs.";
        }

        header('Location: admin-team.php');
        exit();
    }



    // Redirect after processing
    header('Location: admin-team.php');
    exit();
}


// Query to fetch team data from the database
$teamquery = "SELECT * FROM team_members ORDER BY idteam_members DESC ";
$resultteam = $db->query($teamquery);

if (!$resultteam) {
    $_SESSION['message'] = "Error fetching data: " . $db->error;
    header('Location: admin-team.php');
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
    <title>Team Member - Tee Mac Corporation</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">

    <!-- Theme Script -->
    <script src="assets/js/theme-script.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

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
    <link rel="stylesheet" href="assets/css/admin-custom.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->


    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/vfs_fonts.js"></script>
    <script src="assets/js/export.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">



</head>

<body>
    <!-- SweetAlert2 Notifications -->
    <?php if (isset($_SESSION['message'])): ?>
        <script>
            console.log("Session message: <?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>");
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: "<?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>",
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    code: true
                });
            });
        </script>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <script>
            console.log("Session error: <?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); ?>");
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "<?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); ?>",
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>


    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <!-- Header -->
        <div class="header">
            <?php
            require_once('header.php');
            ?>
        </div>
        <!-- /Header -->

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <?php
            require_once('admin-sidebar.php') ?>
        </div>
        <!-- /Sidebar -->

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content">

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Team Member</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="dashboard.php">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Team Member</li>
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
                            <ul class="dropdown-menu  dropdown-menu-end p-3">
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item rounded-1" id="export-pdf"><i class="ti ti-file-type-pdf me-1"></i>Export as PDF</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item rounded-1" id="export-word"><i class="ti ti-file-type-doc me-1"></i>Export as Word</a>
                                </li>
                            </ul>
                        </div>
                        <div class="mb-2">
                            <a href="#" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_career"><i class="ti ti-square-rounded-plus me-2"></i>Add
                                Team Member</a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Filter Section -->
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                        <h4 class="mb-3">Team Member List</h4>
                        <div class="d-flex align-items-center flex-wrap">
                            <?php
                            $filterConfig = [
                                'page_type'    => 'team',
                                'table_id'     => 'datatable',
                                'show_filters' => ['name', 'status'],
                                'sort_enabled' => true,
                            ];
                            include 'includes/filter-bar.php';
                            ?>
                            <div class="dropdown mb-3 me-2">
                                <a href="javascript:void(0);" class="btn btn-outline-light bg-white delete-btn" id="delete-selected" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-trash me-2"></i>Delete Selected</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0 py-3">
                        <!-- Categories List -->

                        <div class="custom-datatable-filter table-responsive">
                            <table class="table datatable" data-name-col="2" data-status-col="5">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort">
                                            <div class="form-check form-check-md">
                                                <input class="form-check-input" type="checkbox" id="select-all">
                                            </div>
                                        </th>

                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Designation</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($resultteam->num_rows > 0) {
                                        while ($rowteam = $resultteam->fetch_assoc()) {
                                            $imagePath = htmlspecialchars($rowteam['profile_picture']);
                                    ?>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input delete-checkbox" type="checkbox" value="<?php echo $rowteam['idteam_members']; ?>">
                                                    </div>
                                                </td>
                                                <td><img src="<?php echo $imagePath; ?>" class="tmc-img-sm" alt=""></td>

                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="ms-2">
                                                            <p class="text-dark mb-0"><?php echo $rowteam['member_name']; ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo $rowteam['role']; ?></td>
                                                <td>
                                                    <?php if ($rowteam['status'] == 1) { ?>
                                                        <span class="badge badge-soft-success d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Active</span>
                                                    <?php } else { ?>
                                                        <span class="badge badge-soft-danger d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Inactive</span>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $rowteam['date']; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="#" class="btn btn-outline-light bg-white btn-icon edit-btn d-flex align-items-center justify-content-center rounded-circle p-0 me-2"
                                                            data-id="<?php echo $rowteam['idteam_members']; ?>"
                                                            data-name="<?php echo $rowteam['member_name']; ?>"
                                                            data-role="<?php echo $rowteam['role']; ?>"
                                                            data-email="<?php echo $rowteam['email']; ?>"
                                                            data-phone="<?php echo $rowteam['phone']; ?>"
                                                            data-linkedin="<?php echo $rowteam['linkedin']; ?>"
                                                            data-twitter="<?php echo $rowteam['twitter']; ?>"
                                                            data-facebook="<?php echo $rowteam['facebook']; ?>"
                                                            data-content="<?php echo htmlspecialchars($rowteam['bio']); ?>"
                                                            data-image="<?php echo $rowteam['profile_picture']; ?>"
                                                            data-status="<?php echo $rowteam['status']; ?>"
                                                            data-bs-toggle="modal" data-bs-target="#edit_role"><i class="ti ti-edit-circle text-primary"></i></a>
                                                        <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center delete-btn rounded-circle p-0 me-3" data-id="<?php echo $rowteam['idteam_members']; ?>" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash-x text-danger"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /Categories List -->
                    </div>
                </div>
                <!-- /Filter Section -->
            </div>
        </div>
        <!-- /Page Wrapper -->

        <!-- Add Team Member -->
        <div class="modal fade" id="add_career">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">New Team Member</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <form action="admin-team.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <!-- Title input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Team Name</label>
                                    <input type="text" name="name" placeholder="Enter Team Name" class="form-control">
                                </div>
                                <!-- Vacancies input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Designtion</label>
                                    <input type="text" name="designation" class="form-control" placeholder="Enter Designtion">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" placeholder="Enter Email Address">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" placeholder="Enter Phone Number">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Facebook Link</label>
                                    <input type="url" name="facebook" class="form-control" placeholder="Enter Facebook Link">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">LinkedIn</label>
                                    <input type="url" name="linkedin" class="form-control" placeholder="Enter LinkedIn URL">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Twitter Link</label>
                                    <input type="url" name="twitter" class="form-control" placeholder="Enter Twitter Link">
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



                                <!-- Description input -->
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <label for="title" class="form-label">Content *</label>
                                    <div id="summernote"></div>
                                    <input type="hidden" name="content" id="team-content">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" name="add-form" class="btn btn-primary">Add Team</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Add Team Member -->


        <!-- Edit Team Member Modal -->
        <div class="modal fade" id="edit_role" tabindex="-1" aria-labelledby="edit_serviceLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="edit_serviceLabel">Edit Team</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <form id="teamForm" action="admin-team.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <!-- Hidden input for career ID -->
                                <input type="hidden" name="idteam_members" id="edit-team-id">
                                <!-- Title input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Team Name</label>
                                    <input type="text" name="name" id="edit-title" placeholder="Enter Team title" class="form-control">
                                </div>
                                <!-- Designation input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Designation</label>
                                    <input type="text" name="designation" id="edit-role" class="form-control" placeholder="Enter Designation">
                                </div>
                                <!-- Phone Number input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" id="edit-phone" placeholder="Enter Phone Number" class="form-control">
                                </div>
                                <!-- Email Address input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" id="edit-email" class="form-control" placeholder="Enter Email Address">
                                </div>
                                <!-- Facebook Link input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Facebook Link</label>
                                    <input type="url" name="facebook" id="edit-facebook" class="form-control" placeholder="Enter Facebook Link">
                                </div>

                                <!-- Twitter Link input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Twitter Link</label>
                                    <input type="url" name="twitter" id="edit-twitter" class="form-control" placeholder="Enter Twitter Link">
                                </div>

                                <!-- LinkedIn input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">LinkedIn</label>
                                    <input type="url" name="linkedin" id="edit-linkedin" class="form-control" placeholder="Enter LinkedIn URL">
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


                                <!-- Image upload -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Image</label>
                                    <div class="d-flex align-items-center upload-pic flex-wrap row-gap-3">
                                        <div class="d-flex align-items-center justify-content-center avatar avatar-xxl border border-dashed me-2 flex-shrink-0 text-dark frames">
                                            <img id="edit-image-preview" src="" alt="Team Image" class="img-fluid tmc-img-preview">
                                        </div>
                                        <div class="profile-upload">
                                            <div class="profile-uploader d-flex align-items-center">
                                                <div class="drag-upload-btn mb-3">
                                                    Upload
                                                    <input type="hidden" name="existing_image" id="existing-image">

                                                    <input type="file" class="form-control image-sign" name="image">
                                                </div>
                                            </div>
                                            <p>Upload image size 4MB, Format JPG, PNG, SVG</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description input -->
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <label for="title" class="form-label">Content *</label>
                                    <div id="summernote1"></div>
                                    <input type="hidden" name="content" id="edit-contents">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" name="edit-career" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Edit Team Member Modal -->



        <!-- Delete Team Member Modal -->
        <div class="modal fade" id="delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="admin-team.php">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="ids" id="delete-career-ids" value=""> <!-- Hidden input for career IDs -->
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


        <!-- /Delete Team Member Modal -->

        <footer class="footer">
            <div class="mt-5 text-center">
                <?php
                require_once('copyright.php');
                ?>
            </div>
        </footer>

    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="assets/js/jquery-3.7.1.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

    <!-- Bootstrap Core JS -->
    <script src="assets/js/bootstrap.bundle.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

    <!-- Daterangepicker JS -->
    <script src="assets/js/moment.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/plugins/daterangepicker/daterangepicker.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

    <!-- Feather Icon JS -->
    <script src="assets/js/feather.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

    <!-- Slimscroll JS -->
    <script src="assets/js/jquery.slimscroll.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

    <script src="assets/js/jquery.dataTables.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/js/dataTables.bootstrap5.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>


    <!-- Select2 JS -->
    <script src="assets/plugins/select2/js/select2.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

    <!-- Summernote JS -->
    <script src="assets/plugins/summernote/summernote-lite.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

    <!-- Custom JS -->
    <script src="assets/js/script.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

    <script src="assets/js/admin-custom.js"></script>

    <script src="assets/js/rocket-loader.min.js" data-cf-settings="094c2cc781cee01c60adaad3-|49" defer=""></script>


<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageBox = document.getElementById('messageBox');
            if (messageBox) {
                setTimeout(() => {
                    messageBox.style.display = 'none';
                }, 5000); // Hide the message after 5 seconds
            }
        });
    </script>


    <script>
        $(document).ready(function() {
            // Handle form submission
            $('form').on('submit', function(e) {
                // e.preventDefault();
                let content = $('#summernote').summernote('code');

                // Get content from each Summernote editor and store it in the hidden input fields
                $('#team-content').val(content);
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.edit-btn').on('click', function() {
                var teamId = $(this).data('id');
                var title = $(this).data('name');
                var phone = $(this).data('phone');
                var role = $(this).data('role');
                var email = $(this).data('email');
                var facebook = $(this).data('facebook');
                var twitter = $(this).data('twitter');
                var linkedin = $(this).data('linkedin');
                var date = $(this).data('date');
                var image = $(this).data('image');
                var status = $(this).data('status');
                var content = $(this).data('content');


                // Set the values in the edit form
                $('#edit-team-id').val(teamId);
                $('#edit-role').val(role);
                $('#edit-title').val(title);
                $('#edit-phone').val(phone);
                $('#edit-email').val(email);
                $('#edit-facebook').val(facebook);
                $('#edit-linkedin').val(linkedin);
                $('#edit-twitter').val(twitter);
                $('#edit-status').prop('checked', status == 1);
                $('#edit-contents').val(content);



                // Set the image preview and existing image path
                $('#edit-image-preview').attr('src', image);
                $('#existing-image').val(image);



                // Initialize the summernote editor with the content
                $('#summernote1').summernote('code', content);
            });
            // Set the content from Summernote editor to the hidden input field before form submission
            $('#teamForm').on('submit', function() {
                var content = $('#summernote1').summernote('code');
                $('#edit-contents').val(content);
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.delete-btn').on('click', function() {
                var careerId = $(this).data('id');
                console.log("Clicked Delete for ID:", careerId); // Debugging output

                // Set the career ID in the hidden input
                $('#delete-career-ids').val(careerId);

                // Show the modal
                $('#delete-modal').modal('show');
            });

            $('#delete-selected').click(function() {
                var selectedIds = [];
                $('.delete-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length > 0) {
                    // Set the selected IDs in the hidden input
                    $('#delete-career-ids').val(selectedIds.join(','));

                    // Show the modal
                    $('#delete-modal').modal('show');
                } else {
                    alert('Please select at least one career post to delete.');
                }
            });
        });
    </script>



</html>