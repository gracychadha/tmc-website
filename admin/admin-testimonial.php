<?php
session_start();
require_once('db/config.php');

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}


// Set default timezone
date_default_timezone_set('Asia/Kolkata');

// Handle form submissions for adding, editing, and deleting testimonials
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Add a new testimonial
    if (isset($_POST['add-testinominal']) && $_SERVER['REQUEST_METHOD'] == "POST") {
        $name = htmlspecialchars(strip_tags(trim($_POST['name'])));
        $designation = htmlspecialchars(strip_tags(trim($_POST['designation'])));
        $message = htmlspecialchars(strip_tags(trim($_POST['message'])));
        $status =  '1';

        $stmt = $db->prepare("INSERT INTO testimonials (name, designation, message, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $designation, $message, $status);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Testimonial added successfully!";
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
        }

        $stmt->close();
        header('Location: admin-testimonial.php');
        exit();
    }

    // Edit an existing testimonial
    if (isset($_POST['edit-testinominal']) && $_SERVER['REQUEST_METHOD'] == "POST") {
        $id = intval($_POST['id']);
        $name = htmlspecialchars(strip_tags(trim($_POST['name'])));
        $designation = htmlspecialchars(strip_tags(trim($_POST['designation'])));
        $message = htmlspecialchars(strip_tags(trim($_POST['message'])));
        $status = isset($_POST['status']) ? 1 : 0;

        $stmt = $db->prepare("UPDATE testimonials SET name=?, designation=?, message=?, status=? WHERE id=?");
        if ($stmt === false) {
            $_SESSION['message'] = "Error preparing statement: " . $db->error;
            header('Location: admin-testimonial.php');
            exit();
        }

        $stmt->bind_param("sssii", $name, $designation, $message, $status, $id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Testimonial updated successfully!";
        } else {
            $_SESSION['message'] = "Error executing update: " . $stmt->error;
        }

        $stmt->close();
        header('Location: admin-testimonial.php');
        exit();
    }

    // Delete a testimonial
    if (isset($_POST['delete-testinominal']) && $_SERVER['REQUEST_METHOD'] == "POST") {
        $ids = $_POST['id']; // Can be a single ID or comma-separated IDs

        // Convert to array and sanitize
        $id_array = array_map('intval', explode(',', $ids));

        // Prepare the DELETE query
        $placeholders = implode(',', array_fill(0, count($id_array), '?'));
        $stmt = $db->prepare("DELETE FROM testimonials WHERE id IN ($placeholders)");

        // Bind parameters dynamically
        $stmt->bind_param(str_repeat('i', count($id_array)), ...$id_array);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Testimonial(s) deleted successfully!";
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
        }

        $stmt->close();
        header('Location: admin-testimonial.php');
        exit();
    }
}

// Query to fetch testimonials data from the database
$testimonialQuery = "SELECT * FROM testimonials ORDER BY id DESC";
$resultTestimonials = $db->query($testimonialQuery);

if (!$resultTestimonials) {
    $_SESSION['message'] = "Error fetching data: " . $db->error;
    header('Location: admin-testimonial.php');
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
    <title>Testimonial - Tee Mac Corporation</title>

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
                        <h3 class="page-title mb-1">Testimonial</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="dashboard.php">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Testimonial</li>
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
                            <a href="#" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_faqs"><i class="ti ti-square-rounded-plus me-2"></i>Add
                                Testimonial</a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Filter Section -->
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                        <h4 class="mb-3">Testimonial List</h4>
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="dropdown mb-3 me-2">
                                <a href="javascript:void(0);" class="btn btn-outline-light bg-white dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-filter me-2"></i>Filter</a>
                                <div class="dropdown-menu drop-width">
                                </div>
                            </div>
                            <div class="dropdown mb-3">
                                <a href="javascript:void(0);" class="btn btn-outline-light bg-white dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-sort-ascending-2 me-2"></i>Sort by A-Z
                                </a>
                                <ul class="dropdown-menu p-3">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1 active">
                                            Ascending
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">
                                            Descending
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">
                                            Recently Viewed
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item rounded-1">
                                            Recently Added
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="dropdown mb-3 me-2">
                                <a href="javascript:void(0);" class="btn btn-outline-light bg-white delete-btn" id="delete-selected" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-trash me-2"></i>Delete Selected</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0 py-3">
                        <!-- Categories List -->
                        <div class="custom-datatable-filter table-responsive">
                            <table class="table datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort">
                                            <div class="form-check form-check-md">
                                                <input class="form-check-input" type="checkbox" id="select-all">
                                            </div>
                                        </th>
                                        <th>Name</th>
                                        <th>Designation</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($resultTestimonials->num_rows > 0) {
                                        while ($row = $resultTestimonials->fetch_assoc()) {
                                    ?>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input" type="checkbox" value="<?php echo $row['id']; ?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="ms-2">
                                                            <p class="text-dark mb-0"><?php echo $row['name']; ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo $row['designation']; ?></td>
                                                <td>
                                                    <?php if ($row['status'] == 1) { ?>
                                                        <span class="badge badge-soft-success d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Active</span>
                                                    <?php } else { ?>
                                                        <span class="badge badge-soft-danger d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Inactive</span>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $row['created_at']; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="#" class="btn edit-btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-2" data-bs-toggle="modal" data-bs-target="#edit_role" data-id="<?php echo $row['id']; ?>" data-name="<?php echo $row['name']; ?>" data-designation="<?php echo $row['designation']; ?>" data-message="<?php echo $row['message']; ?>" data-status="<?php echo $row['status']; ?>">
                                                            <i class="ti ti-edit-circle text-primary"></i>
                                                        </a>
                                                        <a href="#" class="btn delete-btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-3" data-bs-toggle="modal" data-bs-target="#delete-modal" data-id="<?php echo $row['id']; ?>" data-name="<?php echo $row['name']; ?>">
                                                            <i class="ti ti-trash-x text-danger"></i>
                                                        </a>
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

        <!-- Add Testinominal -->
        <div class="modal fade" id="add_faqs">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Testimonial</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <form id="faqsForm" action="admin-testimonial.php" class="m-3" method="post">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" placeholder="Enter Name" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Designation</label>
                            <input type="text" placeholder="Enter Designation" class="form-control" name="designation" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message/Quotes</label>
                            <textarea class="form-control" placeholder="Enter Message" name="message" rows="4" required></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add-testinominal" id="add-submit" class="btn btn-primary">Add Testimonial</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Add Testinominal -->


        <!-- Edit Testinominal Modal -->
        <!-- Edit Testinominal Modal -->
        <div class="modal fade" id="edit_role" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Testimonial</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <form action="admin-testimonial.php" method="POST">
                        <input type="hidden" name="action" value="edit">
                        <div class="modal-body">
                            <input type="hidden" name="id" id="edit-faqs-id">
                            <div class="mb-3">
                                <label for="edit-faqs-question" class="form-label">Name</label>
                                <input type="text" class="form-control" placeholder="Enter Name" id="edit-name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit-designation" class="form-label">Designation</label>
                                <input type="text" placeholder="Enter Designation" class="form-control" id="edit-designation" name="designation" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit-message" class="form-label">Message/Quotes</label>
                                <textarea class="form-control" placeholder="Enter Message" id="edit-message" name="message" rows="4" required></textarea>
                            </div>
                            <div class="col-md-12 modal-status-toggle d-flex align-items-center justify-content-between mb-4">
                                <div class="status-title">
                                    <label for="title" class="form-label">Status *</label>
                                    <p>Change the Status by toggle</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="edit-status" name="status">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="edit-testinominal" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Edit Testinominal Modal -->

        <!-- /Edit Testinominal Modal -->



      

          <!-- Delete Modal -->
        <div class="modal fade" id="delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="admin-testimonial.php">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="delete-testinominal-id" value="">
                        <div class="modal-body text-center">
                            <span class="delete-icon"><i class="ti ti-trash-x"></i></span>
                            <h4>Confirm Deletion</h4>
                            <p>You want to delete all the marked items, this cannot be undone once you delete.</p>
                            <div class="d-flex justify-content-center">
                                <a href="#" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                                <button type="submit" name="delete-testinominal" class="btn btn-danger">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Delete Modal -->

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

    <!-- Select2 JS -->
    <script src="assets/plugins/select2/js/select2.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

    <!-- Summernote JS -->
    <script src="assets/plugins/summernote/summernote-lite.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

    <!-- Datatable JS -->
    <script src="assets/js/jquery.dataTables.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <script src="assets/js/dataTables.bootstrap5.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

    <!-- Custom JS -->
    <script src="assets/js/script.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

    <script src="assets/js/rocket-loader.min.js" data-cf-settings="094c2cc781cee01c60adaad3-|49" defer=""></script>
    <!-- Include jQuery from CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


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
            $('.edit-btn').on('click', function() {
                var testinominalId = $(this).data('id');
                var name = $(this).data('name');
                var designation = $(this).data('designation');
                var message = $(this).data('message');
                var status = $(this).data('status');

                // Set the values in the edit form
                $('#edit-faqs-id').val(testinominalId);
                $('#edit-name').val(name);
                $('#edit-designation').val(designation);
                $('#edit-message').val(message);
                $('#edit-status').prop('checked', status == 1);

                // Debugging
                console.log("Edit Modal Data:", {
                    id: testinominalId,
                    name: name,
                    designation: designation,
                    message: message,
                    status: status
                });
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            // Single delete
            $('.delete-btn').on('click', function() {
                var testinominalId = $(this).data('id');
                console.log("Clicked Delete for ID:", testinominalId);

                $('#delete-testinominal-id').val(testinominalId);
                $('#delete-modal').modal('show');
            });

            // Multiple delete
            $('#delete-selected').click(function() {
                var selectedIds = [];
                $('.form-check-input:checked').each(function() {
                    if ($(this).val() && $(this).val() !== 'on') { // Exclude the "select all" checkbox
                        selectedIds.push($(this).val());
                    }
                });

                if (selectedIds.length > 0) {
                    $('#delete-testinominal-id').val(selectedIds.join(','));
                    $('#delete-modal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Selection',
                        text: 'Please select at least one testimonial to delete.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
        });
    </script>




</html>