<?php
session_start();
require_once('db/config.php');
if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}


// Fetch all 
$stmt = $db->prepare("SELECT * FROM subscribers  ORDER BY idsubscribers DESC");
$stmt->execute();
$result_subscribers = $stmt->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process Delete (Single or Multiple)
    if (isset($_POST['delete-form']) && isset($_POST['idsubscribers'])) {
        $ids = $_POST['idsubscribers'];
        // Handle both single ID and comma-separated IDs
        $id_array = array_map('intval', explode(',', $ids));
        $placeholders = implode(',', array_fill(0, count($id_array), '?'));
        $sql_delete = "DELETE FROM subscribers WHERE idsubscribers IN ($placeholders)";
        $stmt = $db->prepare($sql_delete);
        $stmt->bind_param(str_repeat('i', count($id_array)), ...$id_array);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Subscriber deleted successfully.";
        } else {
            $_SESSION['message'] = "Failed to delete Subscriber.";
        }
        $stmt->close();
        header("Location: admin-subscriber.php");
        exit();
    }
} elseif (isset($_GET['idsubscribers'])) {
    // Fetch Contact Message for Editing
    $id = intval($_GET['idsubscribers']);
    $sql_fetch = "SELECT * FROM subscribers WHERE idsubscribers = ?";
    $stmt = $db->prepare($sql_fetch);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($data = $result->fetch_assoc()) {
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Subscriber(s) not found']);
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
    <title>Subscribers - Tee Mac Corporation</title>

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
                        <h3 class="page-title mb-1">Subscriber's List</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="dashboard.php">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Subscriber's List</li>
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
                        <h4 class="mb-3">Subscriber's List</h4>
                        <div class="d-flex align-items-center flex-wrap">
                            <?php
                            $filterConfig = [
                                'page_type'    => 'subscriber',
                                'table_id'     => 'datatable',
                                'show_filters' => ['email', 'date_range'],
                                'sort_enabled' => true,
                            ];
                            include 'includes/filter-bar.php';
                            ?>
                            <div class="dropdown mb-3 me-2">
                                <a href="javascript:void(0);" class="btn btn-outline-light bg-white delete-btn" id="delete-selected"><i class="ti ti-trash me-2"></i>Delete Selected</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0 py-3">
                        <!-- Contact List -->
                        <div class="custom-datatable-filter table-responsive">
                            <table class="table datatable"
                                data-email-col="2"
                                data-date-col="3">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort" style="width:40px;">
                                            <div class="form-check form-check-md">
                                                <input class="form-check-input" type="checkbox" id="select-all">
                                            </div>
                                        </th>
                                        <th style="width:50px;">Sr. No.</th>
                                        <th>Email</th>
                                        <th>Receive Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sr_no = 1;
                                    if ($result_subscribers->num_rows > 0) {
                                        while ($rowcontact = $result_subscribers->fetch_assoc()) {
                                    ?>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input delete-checkbox" type="checkbox" value="<?php echo $rowcontact['idsubscribers']; ?>">
                                                    </div>
                                                </td>
                                                <td><?php echo $sr_no++; ?></td>
                                                <td><?php echo htmlspecialchars($rowcontact['email']); ?></td>
                                                <td><?php echo htmlspecialchars($rowcontact['created_at']); ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-3 delete-btn" data-bs-toggle="modal" data-bs-target="#delete-modal" data-id="<?php echo $rowcontact['idsubscribers']; ?>"><i class="ti ti-trash-x text-danger"></i></a>
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
                        <!-- /Contact List -->
                    </div>
                </div>
                <!-- /Filter Section -->
            </div>
        </div>
        <!-- /Page Wrapper -->

      

        <!-- Delete Contact Modal -->
        <div class="modal fade" id="delete-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="admin-subscriber.php">
                        <input type="hidden" name="delete-form" value="true">
                        <input type="hidden" name="idsubscribers" id="delete-contact-id">
                        <div class="modal-body text-center">
                            <span class="delete-icon"><i class="ti ti-trash-x"></i></span>
                            <h4>Confirm Deletion</h4>
                            <p>You want to delete the selected Subscriber(s)? This cannot be undone.</p>
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
    <script src="assets/js/filters.js"></script>
    
    
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
                    alert('Please select at least one Subscriber  to delete.');
                }
            });


        });
    </script>
</body>

</html>