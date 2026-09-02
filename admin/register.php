<?php
session_start();
error_reporting(E_ALL); // Enable error reporting for debugging
ini_set('display_errors', 1);

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}


require("db/config.php");

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
    if (isset($_POST['add-user'])) {
        // Retrieve and sanitize user inputs
        $username = mysqli_real_escape_string($db, $_POST['username']);
        $email = mysqli_real_escape_string($db, $_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $mobile = mysqli_real_escape_string($db, $_POST['mobile']);
        $status = 0;
        $activationToken = bin2hex(random_bytes(16)); // Generate activation token

        // Check if passwords match
        if ($password !== $confirm_password) {
            $errors['confirm_password'] = "Passwords do not match.";
        }

        date_default_timezone_set("Asia/Kolkata");
        $current_time = time();
        $current_datetime = date('Y-m-d H:i:s', $current_time);
        $expire_time = $current_time + 15 * 60; // Token expiration time

        // Check if the email already exists
        $stmt = $db->prepare("SELECT * FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors['email'] = "Email already exists. Please use a different email.";
        }

        // Check if the username already exists
        $stmt = $db->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors['username'] = "Username already exists. Please use a different username.";
        }

        if (empty($errors)) {
            $hash = password_hash($password, PASSWORD_BCRYPT); // Hash the password

            // Insert the data into the database
            $stmt = $db->prepare("INSERT INTO admin (username, email, phone, password, date, activation, status, expire_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('ssssssss', $username, $email, $mobile, $hash, $current_datetime, $activationToken, $status, date('Y-m-d', $expire_time));
            if ($stmt->execute()) {
                $_SESSION['msg'] = "Registration successful! You can now log in.";
                $_SESSION['msg_type'] = 'success';
                header("location:register.php");
            } else {
                $_SESSION['msg'] = "Error during registration: " . $stmt->error;
                $_SESSION['msg_type'] = 'error';
                header("location:register.php");
            }
            exit();
        }
    }

    if (isset($_POST['delete-user']) && isset($_POST['delete_id'])) {
        $id = $_POST['delete_id'];

        $sql = "DELETE FROM admin WHERE admin_id=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $_SESSION['msg'] = "User record deleted successfully!";
            $_SESSION['msg_type'] = 'success';
            header("location:register.php");
        } else {
            $_SESSION['msg'] = "Error deleting user record: " . $stmt->error;
            $_SESSION['msg_type'] = 'error';
            header("location:register.php");
        }
        exit();
    }
}

// Fetch user records
$sql_user = "SELECT * FROM admin ORDER BY admin_id DESC";
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
    <title>Add User - Admin</title>

    <!-- Favicon -->
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
    <link rel="stylesheet" href="assets/css/admin-custom.css">
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
                        <h3 class="page-title mb-1">Add User </h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="dashboard.php">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Add User </li>
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
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="ti ti-square-rounded-plus me-2"> </i>Add User</button>
                        </div>

                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                        <h4 class="mb-3">User List</h4>
                        <div class="d-flex align-items-center flex-wrap">
                            <?php
                            $filterConfig = [
                                'page_type'    => 'register',
                                'table_id'     => 'datatable',
                                'show_filters' => ['name', 'date_range'],
                                'sort_enabled' => true,
                            ];
                            include 'includes/filter-bar.php';
                            ?>
                            <div class="dropdown mb-3 me-2">
                                <a href="javascript:void(0);" class="btn btn-outline-light bg-white delete-btn" id="delete-selected" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash me-2"></i>Delete Selected</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0 py-3">
                        <div class="custom-datatable-filter table-responsive">
                            <table class="table datatable" data-name-col="1" data-date-col="3">
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
                                                    <input class="form-check-input delete-checkbox" type="checkbox" value="<?php echo $row['admin_id']; ?>">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="ms-2">
                                                        <p class="text-dark mb-0"><?php echo htmlspecialchars($row['username']); ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                            <td>
                                                <?php if ($row['status'] == 'Enable') : ?>
                                                    <span class="badge badge-soft-success d-inline-flex align-items-center">
                                                        <i class="ti ti-circle-filled fs-5 me-1"></i>Active
                                                    </span>
                                                <?php else : ?>
                                                    <span class="badge badge-soft-danger d-inline-flex align-items-center">
                                                        <i class="ti ti-circle-filled fs-5 me-1"></i>Inactive
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">

                                                    <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-3 delete-btn"
                                                        data-bs-toggle="modal" data-bs-target="#delete-modal"
                                                        data-id="<?php echo $row['admin_id']; ?>">
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





        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="" id="addUserForm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Username</label>
                                <input type="text" name="username" placeholder="Enter Username" class="form-control" required>
                                <?php if (isset($errors['username'])) : ?>
                                    <div class="text-danger"><?= $errors['username'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" placeholder="Enter Email" name="email" class="form-control" required>
                                <?php if (isset($errors['email'])) : ?>
                                    <div class="text-danger"><?= $errors['email'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label>Phone</label>
                                <input placeholder="Enter Phone" type="text" name="mobile" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Password</label>
                                <input placeholder="Enter Password" type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Confirm Password</label>
                                <input type="password" placeholder="Confirm Password" name="confirm_password" id="confirm_password" class="form-control" required>
                                <div id="password-error" class="text-danger"></div>
                            </div>


                        </div>
                        <div class="modal-footer ">
                            <button type="submit" name="add-user" class="btn btn-primary ">Add User</button>
                            <button type="button" class="btn btn-danger tmc-close-mr" data-bs-dismiss="modal">Close</button>
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
                            <p>You want to delete all the marked items, this cannot be undone once you delete.</p>
                            <div class="d-flex justify-content-center">
                                <a href="#" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                                <button type="submit" name="delete-user" class="btn btn-danger">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
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
    <script src="assets/js/admin-custom.js"></script>
    <script src="assets/js/rocket-loader.min.js" data-cf-settings="094c2cc781cee01c60adaad3-|49" defer=""></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/vfs_fonts.js"></script>
    <script src="assets/js/export.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
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
    </script>

    <script>
        $(document).ready(function() {
            $('.delete-btn').click(function() {
                var userId = $(this).data('id');
                $('#delete-id').val(userId);
            });

            // Real-time password validation
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