<?php
session_start();
require_once('db/config.php');
if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}


// Fetch all Service request messages
$stmt = $db->prepare("SELECT * FROM sevice_apply  ORDER BY idbannerform DESC");
$stmt->execute();
$result_contact = $stmt->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process Delete (Single or Multiple)
    if (isset($_POST['delete-form']) && isset($_POST['idbannerform'])) {
        $ids = $_POST['idbannerform'];
        // Handle both single ID and comma-separated IDs
        $id_array = array_map('intval', explode(',', $ids));
        $placeholders = implode(',', array_fill(0, count($id_array), '?'));
        $sql_delete = "DELETE FROM sevice_apply WHERE idbannerform IN ($placeholders)";
        $stmt = $db->prepare($sql_delete);
        $stmt->bind_param(str_repeat('i', count($id_array)), ...$id_array);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Service request message(s) deleted successfully.";
        } else {
            $_SESSION['message'] = "Failed to delete Service request message(s).";
        }
        $stmt->close();
        header("Location: admin-serviceapply.php");
        exit();
    }
    // Process Edit
    elseif (isset($_POST['action']) && $_POST['action'] === 'edit-id' && isset($_POST['idbannerform'])) {
        $id = intval($_POST['idbannerform']);
        $name = filter_var($_POST['fname'] ?? '', FILTER_SANITIZE_STRING);

        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $message = filter_var($_POST['message'] ?? '', FILTER_SANITIZE_STRING);
        $subject = filter_var($_POST['subject'] ?? '', FILTER_SANITIZE_STRING);
        $service = filter_var($_POST['service'] ?? '', FILTER_SANITIZE_STRING);
        $address = filter_var($_POST['address'] ?? '', FILTER_SANITIZE_STRING);

        // Basic validation
        $errors = [];

        if (empty($errors)) {
            $sql_update = "UPDATE sevice_apply SET name = ?,  email = ?, message = ?,service=? WHERE idbannerform = ?";
            $stmt = $db->prepare($sql_update);
            $stmt->bind_param('ssssi', $name, $email, $message,$service, $id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Service request message updated successfully.";
            } else {
                $_SESSION['message'] = "Failed to update the Service request message.";
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = implode("<br>", $errors);
        }
        header("Location: admin-serviceapply.php");
        exit();
    }
} elseif (isset($_GET['idbannerform'])) {
    // Fetch Service request Message for Editing
    $id = intval($_GET['idbannerform']);
    $sql_fetch = "SELECT * FROM sevice_apply WHERE idbannerform = ?";
    $stmt = $db->prepare($sql_fetch);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($data = $result->fetch_assoc()) {
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Contact not found']);
    }
    $stmt->close();
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
    <title>Service  Messages - Tee Mac Corporation</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">
    <link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">
    <link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
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
                <!-- Display Messages -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert <?php echo strpos($_SESSION['message'], 'successfully') !== false ? 'alert-success' : 'alert-danger'; ?>">
                        <?php echo htmlspecialchars($_SESSION['message']); ?>
                    </div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Service List</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="dashboard.php">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Service List</li>
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
                <!-- /Page Header -->

                <!-- Filter Section -->
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                        <h4 class="mb-3">Service List</h4>
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
                                <a href="javascript:void(0);" class="btn btn-outline-light bg-white delete-btn" id="delete-selected"><i class="ti ti-trash me-2"></i>Delete Selected</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0 py-3">
                        <!-- Service List -->
                        <div class="custom-datatable-filter table-responsive">
                            <table class="table datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort">
                                            <div class="form-check form-check-md">
                                                <input class="form-check-input" type="checkbox" id="select-all">
                                            </div>
                                        </th>
                                        <th> Name</th>
                                        <th>Email</th>
                                        <th>Service</th>
                                       
                                        <th>Receive Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result_contact->num_rows > 0) {
                                        while ($rowcontact = $result_contact->fetch_assoc()) {
                                    ?>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input delete-checkbox" type="checkbox" value="<?php echo $rowcontact['idbannerform']; ?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="ms-2">
                                                            <p class="text-dark mb-0"><?php echo htmlspecialchars($rowcontact['name']); ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($rowcontact['email']); ?></td>
                                                <td><?php echo htmlspecialchars($rowcontact['service']); ?></td>
                                               
                                                <td><?php echo htmlspecialchars($rowcontact['date']); ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle edit-btn p-0 me-2" data-bs-toggle="modal"
                                                            data-id="<?php echo $rowcontact['idbannerform']; ?>"

                                                            data-name="<?php echo htmlspecialchars($rowcontact['name']); ?>"
                                                            data-email="<?php echo htmlspecialchars($rowcontact['email']); ?>"
                                                            data-service="<?php echo htmlspecialchars($rowcontact['service']); ?>"
                                                           
                                                            data-message="<?php echo htmlspecialchars($rowcontact['message']); ?>"
                                                          
                                                            data-bs-target="#edit_role"><i class="ti ti-edit-circle text-primary"></i></a>
                                                        <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-3 delete-btn" data-bs-toggle="modal" data-bs-target="#delete-modal" data-id="<?php echo $rowcontact['idbannerform']; ?>"><i class="ti ti-trash-x text-danger"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /Service List -->
                    </div>
                </div>
                <!-- /Filter Section -->
            </div>
        </div>
        <!-- /Page Wrapper -->

        <!-- Edit Contact Modal -->
        <div class="modal fade" id="edit_role" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Service request Message</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <form action="admin-serviceapply.php" method="POST">
                        <input type="hidden" name="action" value="edit-id">
                        <input type="hidden" name="idbannerform" id="edit-contact-id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit-fname" class="form-label">Name</label>
                                <input type="text" class="form-control" id="edit-fname" name="fname" pattern="[A-Za-z\s]{2,50}" title="Letters and spaces only, 2-50 characters" required>
                            </div>

                            <div class="mb-3">
                                <label for="edit-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit-email" name="email" required>
                            </div>
                          
                              <div class="mb-3">
                                <label for="edit-fname" class="form-label">Service</label>
                                <input type="text" class="form-control" id="edit-service" name="service" required>
                            </div>
                            
                           
                           
                            <div class="mb-3">
                                <label for="edit-message" class="form-label">Message</label>
                                <textarea class="form-control" id="edit-message" name="message" rows="4" pattern=".{10,500}" title="Message must be 10-500 characters" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Edit Contact Modal -->

        <!-- Delete Contact Modal -->
        <div class="modal fade" id="delete-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="admin-serviceapply.php">
                        <input type="hidden" name="delete-form" value="true">
                        <input type="hidden" name="idbannerform" id="delete-contact-id">
                        <div class="modal-body text-center">
                            <span class="delete-icon"><i class="ti ti-trash-x"></i></span>
                            <h4>Confirm Deletion</h4>
                            <p>You want to delete the selected Service request message(s)? This cannot be undone.</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Delete Contact Modal -->

        <!-- Footer -->
        <footer class="footer">
            <div class="mt-5 text-center">
                <?php require_once('copyright.php'); ?>
            </div>
        </footer>
    </div>

    <!-- jQuery and Scripts -->
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/email-decode.min.js"></script>
    <script src="assets/js/moment.js"></script>
    <script src="assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script src="assets/js/feather.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/plugins/select2/js/select2.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="assets/js/script.js"></script>
    
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/vfs_fonts.js"></script>
    <script src="assets/js/export.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            // Select All Checkbox
            $('#select-all').on('click', function() {
                $('.delete-checkbox').prop('checked', this.checked);
            });

            // Edit modal data population
            $('.edit-btn').on('click', function() {
                var contactId = $(this).data('id');
                var fname = $(this).data('name');
                var email = $(this).data('email');
                var phone = $(this).data('phone');
                var message = $(this).data('message');
                var subject = $(this).data('subject');
                var service = $(this).data('service');
                var address = $(this).data('address');

                $('#edit-contact-id').val(contactId);
                $('#edit-fname').val(fname);
                $('#edit-email').val(email);
                $('#edit-phone').val(phone);
                $('#edit-message').val(message);
                $('#edit-subject').val(subject);
                $('#edit-service').val(service);
                $('#edit-address').val(address);
            });

            // Delete single contact
            $('.delete-btn').on('click', function() {
                var contactId = $(this).data('id');
                $('#delete-contact-id').val(contactId);
                $('#delete-modal').modal('show');
            });

            // Delete selected contacts
            $('#delete-selected').on('click', function() {
                var selectedIds = [];
                $('.delete-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length > 0) {
                    $('#delete-contact-id').val(selectedIds.join(','));
                    $('#delete-modal').modal('show');
                } else {
                    alert('Please select at least one Service request message to delete.');
                }
            });


        });
    </script>
</body>

</html>