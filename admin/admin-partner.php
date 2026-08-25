<?php
session_start();
require_once('db/config.php');

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}


// Set default timezone
date_default_timezone_set('Asia/Kolkata');

// Handle form submissions for adding a new partner
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle adding a new partner
    if (isset($_POST['add-form'])) {
        // Capture form inputs
        $status = 1; // Default to active
        $date = date("Y-m-d");

        // Handle file upload (Partner image)
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
            $targetDir = "partners/"; // Directory for partner images
            $targetFile = $targetDir . basename($_FILES["featured_image"]["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Check if image file is a valid image
            $check = getimagesize($_FILES["featured_image"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["featured_image"]["tmp_name"], $targetFile)) {
                    $featuredImage = $targetFile; // Assign the uploaded file path
                } else {
                    $_SESSION['message'] = "Sorry, there was an error uploading your file.";
                    header('Location: admin-partner.php');
                    exit();
                }
            } else {
                $_SESSION['message'] = "File is not an image.";
                header('Location: admin-partner.php');
                exit();
            }
        } else {
            $_SESSION['message'] = "No image uploaded.";
            header('Location: admin-partner.php');
            exit();
        }

        // Insert the partner data into the database
        $stmt = $db->prepare("INSERT INTO partners (image, status, created_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $featuredImage, $status, $date);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Partner added successfully!";
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
        }

        header('Location: admin-partner.php');
        exit();
    }

    // Handle editing a partner
    if (isset($_POST['edit-partner'])) {
        $idpartner = intval($_POST['idpartner']);
        $status = isset($_POST['status']) ? 1 : 0;

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image = $_FILES['image']['name'];
            $target_dir = "partners/";
            $target_file = $target_dir . basename($image);
            move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
            $image_path = $target_file;
        } else {
            $image_path = $_POST['existing_image'];
        }

        $stmt = $db->prepare("UPDATE partners SET image = ?, status = ? WHERE idpartner = ?");
        $stmt->bind_param("ssi", $image_path, $status, $idpartner);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Partner updated successfully!";
        } else {
            $_SESSION['message'] = "Error updating partner: " . $stmt->error;
        }

        $stmt->close();
        header('Location: admin-partner.php');
        exit();
    }

    // Handle deleting partners
    if (isset($_POST['delete-form'])) {
        if (!empty($_POST['ids'])) {
            $ids = $_POST['ids'];
            $idsArray = explode(',', $ids);

            // Prepare the SQL statement with placeholders
            $placeholders = implode(',', array_fill(0, count($idsArray), '?'));
            $stmt = $db->prepare("DELETE FROM partners WHERE idpartner IN ($placeholders)");

            // Bind the parameters dynamically
            $types = str_repeat('i', count($idsArray));
            $stmt->bind_param($types, ...$idsArray);

            if ($stmt->execute()) {
                $_SESSION['message'] = ($stmt->affected_rows > 0)
                    ? "Partner(s) deleted successfully!"
                    : "No partner found with those IDs.";
            } else {
                $_SESSION['message'] = "Error deleting partner(s): " . $stmt->error;
            }

            $stmt->close();
        } else {
            $_SESSION['message'] = "Invalid partner IDs.";
        }

        header('Location: admin-partner.php');
        exit();
    }

    // Redirect after processing
    header('Location: admin-partner.php');
    exit();
}

// Query to fetch partner data from the database
$partner_query = "SELECT * FROM partners ORDER BY idpartner DESC";
$resultpartner = $db->query($partner_query);

if (!$resultpartner) {
    $_SESSION['message'] = "Error fetching data: " . $db->error;
    header('Location: admin-partner.php');
    exit();
}

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="robots" content="noindex, nofollow">
    <title>Partners - Tee Mac Corporation</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">

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

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/vfs_fonts.js"></script>
    <script src="assets/js/export.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
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
                    position: 'bottom-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
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
                    position: 'bottom-end',
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
            <?php require_once('header.php'); ?>
        </div>
        <!-- /Header -->

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <?php require_once('admin-sidebar.php') ?>
        </div>
        <!-- /Sidebar -->

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content">
                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Partners</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="dashboard.php">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Partners</li>
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
                                <li><a href="javascript:void(0);" class="dropdown-item rounded-1" id="export-pdf"><i class="ti ti-file-type-pdf me-1"></i>Export as PDF</a></li>
                                <li><a href="javascript:void(0);" class="dropdown-item rounded-1" id="export-word"><i class="ti ti-file-type-doc me-1"></i>Export as Word</a></li>
                            </ul>
                        </div>
                        <div class="mb-2">
                            <a href="#" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_partner"><i class="ti ti-square-rounded-plus me-2"></i>Add Partner</a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Filter Section -->
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                        <h4 class="mb-3">Partner List</h4>
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
                        <!-- Partner List -->
                        <div class="custom-datatable-filter table-responsive">
                            <table class="table datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort">
                                            <div class="form-check form-check-md">
                                                <input class="form-check-input" type="checkbox" id="select-all">
                                            </div>
                                        </th>
                                        <th>Image</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($resultpartner->num_rows > 0) {
                                        while ($rowpartner = $resultpartner->fetch_assoc()) {
                                            $imagePath = htmlspecialchars($rowpartner['image']);
                                    ?>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input delete-checkbox" type="checkbox" value="<?php echo $rowpartner['idpartner']; ?>">
                                                    </div>
                                                </td>
                                                <td><img src="<?php echo $imagePath; ?>" style="width: 25%;" alt="Partner Image"></td>
                                                <td>
                                                    <?php if ($rowpartner['status'] == 1) { ?>
                                                        <span class="badge badge-soft-success d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Active</span>
                                                    <?php } else { ?>
                                                        <span class="badge badge-soft-danger d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Inactive</span>
                                                    <?php } ?>
                                                </td>
                                               <td><?php echo date('Y-m-d', strtotime($rowpartner['created_at'])); ?></td>

                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="#" class="btn btn-outline-light bg-white btn-icon edit-btn d-flex align-items-center justify-content-center rounded-circle p-0 me-2"
                                                            data-id="<?php echo $rowpartner['idpartner']; ?>"
                                                            data-image="<?php echo $rowpartner['image']; ?>"
                                                            data-status="<?php echo $rowpartner['status']; ?>"><i class="ti ti-edit-circle text-primary"></i></a>
                                                        <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center delete-btn rounded-circle p-0 me-3" data-id="<?php echo $rowpartner['idpartner']; ?>"><i class="ti ti-trash-x text-danger"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /Partner List -->
                    </div>
                </div>
                <!-- /Filter Section -->
            </div>
        </div>
        <!-- /Page Wrapper -->

        <!-- Add Partner Modal -->
        <div class="modal fade" id="add_partner">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">New Partner</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <form action="admin-partner.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
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
                                                    <input type="file" class="form-control image-sign" name="featured_image" required>
                                                </div>
                                            </div>
                                            <p>Upload image size 4MB, Format JPG, PNG, SVG</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" name="add-form" class="btn btn-primary">Add Partner</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Add Partner Modal -->

        <!-- Edit Partner Modal -->
        <div class="modal fade" id="edit_partner" tabindex="-1" aria-labelledby="edit_partnerLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="edit_partnerLabel">Edit Partner</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <form id="partnerForm" action="admin-partner.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <!-- Hidden input for partner ID -->
                                <input type="hidden" name="idpartner" id="edit-partner-id">
                                <!-- Image upload -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Image</label>
                                    <div class="d-flex align-items-center upload-pic flex-wrap row-gap-3">
                                        <div class="d-flex align-items-center justify-content-center avatar avatar-xxl border border-dashed me-2 flex-shrink-0 text-dark frames">
                                            <img id="edit-image-preview" src="" alt="Partner Image" class="img-fluid" style="max-width: 50%;">
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
                                <!-- Status input -->
                                <div class="col-md-6 modal-status-toggle d-flex align-items-center justify-content-between mb-4">
                                    <div class="status-title">
                                        <label for="status" class="form-label">Status *</label>
                                        <p>Change the Status by toggle</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="edit-status" name="status">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" name="edit-partner" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Edit Partner Modal -->

        <!-- Delete Partner Modal -->
        <div class="modal fade" id="delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="admin-partner.php">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="ids" id="delete-partner-ids" value="">
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
        <!-- /Delete Partner Modal -->

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

    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>

    <script>
        $(document).ready(function() {
            $(document).on('click', '.edit-btn', function(e) {
                e.preventDefault();
                var partnerId = $(this).data('id');
                var image = $(this).data('image');
                var status = $(this).data('status');

                $('#edit-partner-id').val(partnerId);
                $('#edit-image-preview').attr('src', image);
                $('#existing-image').val(image);
                $('#edit-status').prop('checked', status == 1);

                var modal = new bootstrap.Modal(document.getElementById('edit_partner'));
                modal.show();
            });

            $('#delete-selected').click(function() {
                var selectedIds = [];
                $('.delete-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length > 0) {
                    $('#delete-partner-ids').val(selectedIds.join(','));
                    var modal = new bootstrap.Modal(document.getElementById('delete-modal'));
                    modal.show();
                } else {
                    Swal.fire({ toast: true, position: 'bottom-end', icon: 'warning', title: 'Please select at least one partner', showConfirmButton: false, timer: 3000 });
                }
            });

            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var partnerId = $(this).data('id');
                $('#delete-partner-ids').val(partnerId);
                var modal = new bootstrap.Modal(document.getElementById('delete-modal'));
                modal.show();
            });
        });
    </script>
</body>
</html>