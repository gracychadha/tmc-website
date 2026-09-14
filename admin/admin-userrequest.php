<?php
session_start();
error_reporting(E_ALL); // Enable error reporting for debugging
ini_set('display_errors', 1);

require("db/config.php");

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}


// Function to sanitize input data
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Function to validate email address
function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  
    if (isset($_POST['delete-user']) && isset($_POST['delete_id'])) {
        $id = $_POST['delete_id'];

        $sql = "DELETE FROM users WHERE id=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $_SESSION['msg'] = "User Request deleted successfully!";
            $_SESSION['msg_type'] = 'success';
            header("location: admin-userrequest.php");
        } else {
            $_SESSION['msg'] = "Error deleting user record: " . $stmt->error;
            $_SESSION['msg_type'] = 'error';
            header("location: admin-userrequest.php");
        }
        exit();
    }

    if (isset($_POST['edit-user']) && isset($_POST['edit_id'])) {
        $id = $_POST['edit_id'];
        $name = mysqli_real_escape_string($db, $_POST['name']);
        $email = mysqli_real_escape_string($db, $_POST['email']);
        $mobile = mysqli_real_escape_string($db, $_POST['mobile']);
        $status = mysqli_real_escape_string($db, $_POST['status']);

        $sql = "UPDATE users SET name = ?, email = ?, phone = ?, status = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ssssi', $name, $email, $mobile, $status,  $id);
        if ($stmt->execute()) {
            $_SESSION['msg'] = "User Request updated successfully!";
            $_SESSION['msg_type'] = 'success';
            header("location: admin-userrequest.php");
        } else {
            $_SESSION['msg'] = "Error updating user record: " . $stmt->error;
            $_SESSION['msg_type'] = 'error';
            header("location: admin-userrequest.php");
        }
        exit();
    }
}

// Fetch user records
$sql_user = "SELECT * FROM users ORDER BY id DESC";
$result_user = $db->query($sql_user);

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
    <title>User Request - Tee Mac Corporation</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">
    <script src="assets/js/theme-script.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">
    <link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">
    <link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="assets/plugins/summernote/summernote-lite.min.css">
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body>

    <div class="main-wrapper">
        <!-- Header -->
        <div class="header">
            <?php require_once('header.php'); ?>
        </div>
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <?php require_once('admin-sidebar.php'); ?>
        </div>


        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content">
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">User Request</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="dashboard.php">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">User Request</li>
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

                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                        <h4 class="mb-3">User Request List</h4>
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="dropdown mb-3 me-2">
                                <a href="javascript:void(0);" class="btn btn-outline-light bg-white dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-filter me-2"></i>Filter</a>
                                <div class="dropdown-menu drop-width">
                                    <!-- Filter form can be added here -->
                                </div>
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
                                <a href="javascript:void(0);" class="btn btn-outline-light bg-white delete-btn" id="delete-selected" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash me-2"></i>Delete Selected</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0 py-3">
                        <div class="custom-datatable-filter table-responsive">
                            <table class="table datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort">
                                            <div class="form-check form-check-md">
                                                <input class="form-check-input" type="checkbox" id="select-all">
                                            </div>
                                        </th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Terms Accepted</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result_user->fetch_assoc()) : ?>
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-md">
                                                    <input class="form-check-input delete-checkbox" type="checkbox" value="<?php echo $row['id']; ?>">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="ms-2">
                                                        <p class="text-dark mb-0"><?php echo htmlspecialchars($row['name']); ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                             <td>
                                                <?php if ($row['terms_accepted'] == '1') : ?>
                                                    <span class="badge badge-soft-success d-inline-flex align-items-center">
                                                        <i class="ti ti-circle-filled fs-5 me-1"></i>Accept
                                                    </span>
                                                <?php else : ?>
                                                    <span class="badge badge-soft-danger d-inline-flex align-items-center">
                                                        <i class="ti ti-circle-filled fs-5 me-1"></i>Decline
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($row['status'] == 'approved') : ?>
                                                    <span class="badge badge-soft-success d-inline-flex align-items-center">
                                                        <i class="ti ti-circle-filled fs-5 me-1"></i>Approved
                                                    </span>
                                                <?php else : ?>
                                                    <span class="badge badge-soft-danger d-inline-flex align-items-center">
                                                        <i class="ti ti-circle-filled fs-5 me-1"></i>Pending
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle edit-btn p-0 me-2"
                                                        data-bs-toggle="modal" data-bs-target="#editUserModal"
                                                        data-id="<?php echo $row['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                                        data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                                        data-phone="<?php echo htmlspecialchars($row['phone']); ?>"
                                                        data-status="<?php echo $row['status']; ?>"
                                                        data-permission='<?php echo htmlspecialchars($row['permission']); ?>'>
                                                        <i class="ti ti-edit-circle text-primary"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-3 delete-btn"
                                                        data-bs-toggle="modal" data-bs-target="#delete-modal"
                                                        data-id="<?php echo $row['id']; ?>">
                                                        <i class="ti ti-trash-x text-danger"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="" id="editUserForm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="edit_id" id="edit-id">
                            <div class="mb-3">
                                <label>Username</label>
                                <input type="text" name="name" id="edit-name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" id="edit-email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Phone</label>
                                <input type="text" name="mobile" id="edit-mobile" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Status</label>
                                <select name="status" id="edit-status" class="form-control">
                                    <option value="" selected>-- Select Status--</option>
                                    <option value="approved">Approved</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                          

                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="edit-user" class="btn btn-primary">Save Changes</button>
                            <button type="button" class="btn btn-danger" style="margin-left: 10px;" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="">
                        <input type="hidden" name="delete_id" id="delete-id">
                        <div class="modal-body text-center">
                            <span class="delete-icon">
                                <i class="ti ti-trash-x"></i>
                            </span>
                            <h4>Confirm Deletion</h4>
                            <p>You want to delete the selected items, this cannot be undone once deleted.</p>
                            <div class="d-flex justify-content-center">
                                <a href="#" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                                <button type="submit" name="delete-user" class="btn btn-danger">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <script src="assets/js/jquery-3.7.1.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
        <script src="assets/js/bootstrap.bundle.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
        <script src="assets/js/moment.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
        <script src="assets/plugins/daterangepicker/daterangepicker.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
        <script src="assets/js/bootstrap-datetimepicker.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
        <script src="assets/js/feather.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
        <script src="assets/js/jquery.slimscroll.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
        <script src="assets/js/jquery.dataTables.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
        <script src="assets/js/dataTables.bootstrap5.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
        <script src="assets/plugins/summernote/summernote-lite.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
        <script src="assets/js/script.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
        <script src="assets/js/rocket-loader.min.js" data-cf-settings="094c2cc781cee01c60adaad3-|49" defer=""></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/vfs_fonts.js"></script>
    <script src="assets/js/export.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

        <script>
            $(document).ready(function() {


                // Select all checkboxes
                $('#select-all').on('click', function() {
                    $('.delete-checkbox').prop('checked', this.checked);
                });

                // Update select-all checkbox based on individual checkbox states
                $('.delete-checkbox').on('click', function() {
                    if ($('.delete-checkbox:checked').length === $('.delete-checkbox').length) {
                        $('#select-all').prop('checked', true);
                    } else {
                        $('#select-all').prop('checked', false);
                    }
                });

                document.addEventListener('DOMContentLoaded', function() {

                    <?php if (isset($_SESSION['msg']) && $_SESSION['msg_type'] == 'success'): ?>
                        Swal.fire({
                            toast: true,
                            position: 'bottom-end',
                            icon: 'success',
                            title: "<?php echo htmlspecialchars($_SESSION['msg'], ENT_QUOTES, 'UTF-8'); ?>",
                            showConfirmButton: false,
                            timer: 8000,
                            timerProgressBar: true
                        });
                        <?php unset($_SESSION['msg']);
                        unset($_SESSION['msg_type']); ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['msg']) && $_SESSION['msg_type'] == 'error'): ?>
                        Swal.fire({
                            toast: true,
                            position: 'bottom-end',
                            icon: 'error',
                            title: "<?php echo htmlspecialchars($_SESSION['msg'], ENT_QUOTES, 'UTF-8'); ?>",
                            showConfirmButton: false,
                            timer: 8000,
                            timerProgressBar: true
                        });
                        <?php unset($_SESSION['msg']);
                        unset($_SESSION['msg_type']); ?>
                    <?php endif; ?>
                });
                // Edit button click event
                $('.edit-btn').click(function() {
                    var userId = $(this).data('id');
                    var name = $(this).data('name');
                    var email = $(this).data('email');
                    var phone = $(this).data('phone');
                    var status = $(this).data('status');
                    var permission = $(this).data('permission');

                    $('#edit-id').val(userId);
                    $('#edit-name').val(name);
                    $('#edit-email').val(email);
                    $('#edit-mobile').val(phone);
                    $('#edit-status').val(status);
                    $('#edit-permission').val(permission ? JSON.parse(permission) : []).trigger('change');
                });

                // Delete button click event
                $('.delete-btn').click(function() {
                    var userId = $(this).data('id');
                    $('#delete-id').val(userId);
                });

                // Initialize Select2 for multiple select
                $('#edit-permission').select2();

                // Real-time password validation (if you add password fields later)
                $('#confirm_password').on('blur', function() {
                    var password = $('#password').val();
                    var confirmPassword = $(this).val();
                    if (password !== confirmPassword) {
                        $('#password-error').text('Passwords do not match.');
                    } else {
                        $('#password-error').text('');
                    }
                });
            });
        </script>


</body>

</html>