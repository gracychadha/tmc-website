<?php
session_start();
include('db/config.php'); // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add-study'])) {
        $content = $_POST['content'];
        $status = $_POST['status'];
        $country = $_POST['country'];
        $slug = $_POST['slug'];

        $sql = "INSERT INTO study (content, status, country,slug) VALUES (?, ?, ?,?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssss", $content, $status, $country, $slug);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = "Study record added successfully!";
        header('Location: admin-study.php');
        exit();
    }

    if (isset($_POST['edit-study'])) {
        $id = $_POST['idstudy'];
        $content = $_POST['content'];
        $status = $_POST['status'];
        $country = $_POST['country'];
        $slug = $_POST['slug'];

        $sql = "UPDATE study SET content=?, status=?, country=?,slug=? WHERE idstudy=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssssi", $content, $status, $country, $slug, $id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = "Study record updated successfully!";
        header('Location: admin-study.php');
        exit();
    }

    if (isset($_POST['delete-study']) && isset($_POST['delete_id'])) {
        $id = $_POST['delete_id'];

        $sql = "DELETE FROM study WHERE idstudy=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = "Study record deleted successfully!";
        header('Location: admin-study.php');
        exit();
    }
}

// Fetch study records
$sql_study = "SELECT * FROM study ORDER BY idstudy DESC";
$result_study = $db->query($sql_study);



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
    <title>Study Material - Tee Mac Corporation</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">

    <!-- Theme Script -->
    <script src="assets/js/theme-script.js" type="feb024e4d970c7c806ef5348-text/javascript"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- Feather CSS -->
    <link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">

    <!-- Daterangepikcer CSS -->
    <link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">a
    <!-- Add CSS for status select dropdown -->
    <style>
        .status-select {
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }
    </style>
</head>

<body>

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
            <div class="content content-two">
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Study Material</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="index.php">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Study Material</li>
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
                            <a href="#" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_study"><i class="ti ti-square-rounded-plus me-2"></i>Add Study Material</a>
                        </div>
                    </div>
                </div>

                <!-- Table Filter -->
                <div class="d-flex justify-content-between flex-wrap">
                    <div class="dropdown mb-3 me-2">
                        <a href="javascript:void(0);" class="btn btn-outline-light bg-white dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-filter me-2"></i>Filter</a>
                        <div class="dropdown-menu drop-width">
                            <form action="#">
                                <div class="d-flex align-items-center border-bottom p-3">
                                    <h4>Filter</h4>
                                </div>
                                <div class="p-3 border-bottom">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Category</label>
                                                <select class="select">
                                                    <option>Select</option>
                                                    <option>Service 1</option>
                                                    <option>Service 2</option>
                                                    <option>Service 3</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select class="select">
                                                    <option>Select</option>
                                                    <option>Active</option>
                                                    <option>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-0">
                                                <label class="form-label">Added Date</label>
                                                <input type="date" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 d-flex align-items-center justify-content-end">
                                    <a href="#" class="btn btn-light me-3">Reset</a>
                                    <button type="submit" class="btn btn-primary">Apply</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="dropdown mb-3">
                        <a href="javascript:void(0);" class="btn btn-outline-light bg-white dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-sort-ascending-2 me-2"></i>Sort by A-Z </a>
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
                </div>
                <!-- /Table Filter -->

                <!-- Study Records Table -->
                <div class="card mb-3">
                    <?php
                    if (isset($_SESSION['message'])) {
                        echo '<div id="messageBox" class="alert ' . (strpos($_SESSION['message'], 'successfully') !== false ? 'alert-success' : 'alert-danger') . ' alert-dismissible fade show" role="alert">';
                        echo $_SESSION['message'];
                        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                        echo '</div>';
                        unset($_SESSION['message']);
                    }
                    ?>
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                        <h4 class="mb-3">Study Records List</h4>
                    </div>
                    <div class="pb-0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Country</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result_study->num_rows > 0) {
                                    $i = 1;
                                    while ($data_study = $result_study->fetch_assoc()) {
                                        echo '<tr>';
                                        echo '<td>' . $i++ . '</td>';
                                        echo '<td>' . htmlspecialchars($data_study['country']) . '</td>';
                                        echo '<td>';
                                        if ($data_study['status'] == 'Active') {
                                            echo '<span class="badge badge-soft-success d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Active</span>';
                                        } else {
                                            echo '<span class="badge badge-soft-danger d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Inactive</span>';
                                        }
                                        echo '</td>';
                                        echo '<td>';
                                        echo '<div class="dropdown">';
                                        echo '<a href="#" class="btn btn-white btn-icon btn-sm d-flex align-items-center justify-content-center rounded-circle p-0" data-bs-toggle="dropdown" aria-expanded="false">';
                                        echo '<i class="ti ti-dots-vertical fs-14"></i>';
                                        echo '</a>';
                                        echo '<ul class="dropdown-menu dropdown-menu-right p-3">';
                                        echo '<a class="dropdown-item rounded-1 edit-btn" href="#" data-bs-toggle="modal" data-bs-target="#edit_study"
                                            data-id="' . $data_study['idstudy'] . '"
                                            data-content="' . htmlspecialchars($data_study['content'], ENT_QUOTES) . '"
                                            data-status="' . htmlspecialchars($data_study['status'], ENT_QUOTES) . '"
                                            data-country="' . htmlspecialchars($data_study['country'], ENT_QUOTES) . '"
                                            data-slug="' . htmlspecialchars($data_study['slug'], ENT_QUOTES) . '">
                                            <i class="ti ti-edit-circle me-2"></i>Edit
                                        </a>';
                                        echo '<li><a class="dropdown-item rounded-1 delete-btn" href="#" data-bs-toggle="modal" data-bs-target="#delete-modal" data-id="' . $data_study['idstudy'] . '">
                                            <i class="ti ti-trash-x me-2"></i>Delete
                                        </a></li>';
                                        echo '</ul>';
                                        echo '</div>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="text-center">No study records found.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
        <!-- /Page Wrapper -->

        <!-- Add Study Material -->
        <div class="modal fade" id="add_study">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">New Study Material</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <form id="studyForm" action="admin-study.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <!-- Country input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" name="country" placeholder="Enter Country Name" class="form-control" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">URL</label>
                                    <input type="text" name="slug" placeholder="Enter Page Url Name" class="form-control" required>
                                </div>

                                <!-- Status input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status" required>
                                        <option value="">Select Status...</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>

                                <!-- Description input -->
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <label for="title" class="form-label">Content *</label>
                                    <div id="summernote"></div>
                                    <input type="hidden" name="content" id="content">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" name="add-study" class="btn btn-primary">Add Study Material</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Add Study Material -->

        <!-- Edit Study Material Modal -->
        <div class="modal fade" id="edit_study" tabindex="-1" aria-labelledby="edit_studyLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="edit_studyLabel">Edit Study Material</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <form id="editStudyForm" action="admin-study.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Country Field -->
                                    <div class="mb-3">
                                        <label class="form-label">Country</label>
                                        <input type="text" name="country" id="edit_country" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">URL</label>
                                    <input type="text" name="slug" id="edit_slug" placeholder="Enter Page Url Name" class="form-control" required>
                                </div>

                                <div class="col-lg-6 col-md-6">
                                    <!-- Status Field -->
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" id="edit_status" class="form-select status-select">
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Description input -->
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <label for="title" class="form-label">Content *</label>
                                    <div id="summernote1"></div>
                                    <input type="hidden" name="content" id="edit_content">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" name="edit-study" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Edit Study Material Modal -->

        <!-- Delete Modal -->
        <div class="modal fade" id="delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="admin-study.php" method="POST">
                        <div class="modal-body text-center">
                            <span class="delete-icon">
                                <i class="ti ti-trash-x"></i>
                            </span>
                            <h4>Confirm Deletion</h4>
                            <p>You want to delete this study material, this can't be undone once you delete.</p>
                            <input type="hidden" name="delete_id" id="delete-study-id"> <!-- Hidden input for study ID -->
                            <div class="d-flex justify-content-center">
                                <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                                <button type="submit" name="delete-study" class="btn btn-danger">Yes, Delete</button>
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



    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Include Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">

    <!-- Include Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>

    <!-- jQuery -->
    <script src="assets/js/jquery-3.7.1.min.js"></script>

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
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/vfs_fonts.js"></script>
    <script src="assets/js/export.js"></script>
    <script src="assets/js/script.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

    <script src="assets/js/rocket-loader.min.js" data-cf-settings="094c2cc781cee01c60adaad3-|49" defer=""></script>
    <script>
        $(document).ready(function() {
            // Initialize Summernote for Description
            $('#summernote').summernote({
                height: 200, // Set the height of the editor
                placeholder: 'Enter Description here...',
                tabsize: 2
            });

            // Handle form submission
            $('form').on('submit', function(e) {
                // Get content from each Summernote editor and store it in the hidden input fields
                $('#content').val($('#summernote').summernote('code'));
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            console.log("hello");
            // Trigger modal opening and populate values
            $('.edit-btn').on('click', function(event) {
                // var button = $(event.relatedTarget); // The button that triggered the modal
                // var id = button.data('idstudy');
                // var content = button.data('content');
                // var status = button.data('status');
                // var country = button.data('country');
                let id = $(this).data('idstudy');
                let country = $(this).data('country');
                let slug = $(this).data('slug');
                let status = $(this).data('status');
                let content = $(this).data('content');





                // Set the values in the modal
                // $('#edit_id').val(id);
                $('#edit_country').val(country);
                $('#edit_status').val(status);
                $('#edit_slug').val(slug);
                $('#edit_content').val(content);

                // Set the description in Summernote
                $('#summernote1').summernote('code', content); // Populate editor with description
            });
        });


        // Update hidden input before form submission
        $('#editStudyForm').on('submit', function() {
            var content = $('#summernote1').summernote('code'); // Get the content from Summernote
            $('#edit_content').val(content); // Set the content in the hidden input field
        });

        // When the delete button is clicked
        $('#delete-modal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // The button that triggered the modal
            var studyId = button.data('id'); // Get the study ID from the data-id attribute

            // Set the study ID to the hidden input in the modal form
            $('#delete-study-id').val(studyId);
        });
    </script>

</body>

</html>