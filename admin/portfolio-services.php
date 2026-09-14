<?php
session_start();
error_reporting(E_ALL);
require_once('db/config.php');

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}

date_default_timezone_set('Asia/Kolkata');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ADD SERVICE
    if (isset($_POST['add-form'])) {
    
        $title = htmlspecialchars(trim($_POST['title']));
        $description = $_POST['description']; 
        $link = htmlspecialchars(trim($_POST['link']));
        $status = 1; // Default to active

        $stmt = $db->prepare("INSERT INTO portfolio_services ( title, description, status) VALUES ( ?, ?, ?)");
        $stmt->bind_param("ssi", $title, $description, $status);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Service added successfully!";
        } else {
            $_SESSION['error'] = "Error: " . $stmt->error;
        }
        $stmt->close();
        header('Location: portfolio-services.php');
        exit();
    }

    // EDIT SERVICE
    if (isset($_POST['edit-form'])) {
        $id = intval($_POST['id']);
     
        $title = htmlspecialchars(trim($_POST['title']));
        $description = $_POST['description'];

        $status = isset($_POST['status']) ? 1 : 0;

        $stmt = $db->prepare("UPDATE portfolio_services SET  title = ?, description = ?,status = ? WHERE id = ?");
        $stmt->bind_param("ssii", $title, $description, $status, $id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Service updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating service: " . $stmt->error;
        }
        $stmt->close();
        header('Location: portfolio-services.php');
        exit();
    }

    // DELETE SERVICES
    if (isset($_POST['delete-form'])) {
        if (!empty($_POST['ids'])) {
            $ids = $_POST['ids'];
            $idsArray = explode(',', $ids);

            $placeholders = implode(',', array_fill(0, count($idsArray), '?'));
            $stmt = $db->prepare("DELETE FROM portfolio_services WHERE id IN ($placeholders)");

            $types = str_repeat('i', count($idsArray));
            $stmt->bind_param($types, ...$idsArray);

            if ($stmt->execute()) {
                $_SESSION['message'] = ($stmt->affected_rows > 0) ? "Service(s) deleted successfully!" : "No services found with those IDs.";
            } else {
                $_SESSION['error'] = "Error deleting service(s): " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Invalid service IDs.";
        }
        header('Location: portfolio-services.php');
        exit();
    }
}

// Fetch services
$stmtServices = $db->prepare("SELECT * FROM portfolio_services ORDER BY id DESC");
$stmtServices->execute();
$result_services = $stmtServices->get_result();

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
    <title>Manage Services - Tee Mac Corporation</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">
    <script src="assets/js/theme-script.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">
    <link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin-custom.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['message'])): ?>
                Swal.fire({
                    toast: true, position: 'bottom-end', icon: 'success',
                    title: "<?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>",
                    showConfirmButton: false, timer: 8000, timerProgressBar: true
                });
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                Swal.fire({
                    toast: true, position: 'bottom-end', icon: 'error',
                    title: "<?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); ?>",
                    showConfirmButton: false, timer: 8000, timerProgressBar: true
                });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });
    </script>

    <div class="main-wrapper">
        <div class="header"><?php require_once('header.php'); ?></div>
        <div class="sidebar" id="sidebar"><?php require_once('admin-sidebar.php') ?></div>
        
        <div class="page-wrapper">
            <div class="content">
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Portfolio Services</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">What I Do (Services)</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                        <div class="mb-2">
                            <a href="#" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_service">
                                <i class="ti ti-square-rounded-plus me-2"></i>Add Service
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                        <h4 class="mb-3">All Services List</h4>
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="dropdown mb-3 me-2">
                                <a href="javascript:void(0);" class="btn btn-outline-light bg-white delete-btn" id="delete-selected" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <i class="ti ti-trash me-2"></i>Delete Selected
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0 py-3">
                        <div class="custom-datatable-filter table-responsive">
                            <table class="table datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort"><div class="form-check form-check-md"><input class="form-check-input" type="checkbox" id="select-all"></div></th>
                                      
                                        <th>Title</th>
                                        <th>Description</th>
                                      
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result_services->num_rows > 0): ?>
                                        <?php while ($row = $result_services->fetch_assoc()): ?>
                                            <tr>
                                                <td><div class="form-check form-check-md"><input class="form-check-input delete-checkbox" type="checkbox" value="<?php echo $row['id']; ?>"></div></td>
                                                
                                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                                <td><?php echo htmlspecialchars(substr(strip_tags($row['description']), 0, 60)) . '...'; ?></td>
                                               
                                                <td>
                                                    <?php if ($row['status'] == 1): ?>
                                                        <span class="badge badge-soft-success d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-soft-danger d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle edit-btn p-0 me-2" 
                                                           data-bs-toggle="modal" data-bs-target="#edit_service"
                                                           data-id="<?php echo $row['id']; ?>"
                                                           data-icon="<?php echo htmlspecialchars($row['icon']); ?>"
                                                           data-title="<?php echo htmlspecialchars($row['title']); ?>"
                                                           data-description="<?php echo htmlspecialchars($row['description']); ?>"
                                                           
                                                           data-status="<?php echo $row['status']; ?>">
                                                           <i class="ti ti-edit-circle text-primary"></i>
                                                        </a>
                                                        <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-3 delete-btn" 
                                                           data-bs-toggle="modal" data-bs-target="#delete-modal" data-id="<?php echo $row['id']; ?>">
                                                           <i class="ti ti-trash-x text-danger"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Service Modal -->
        <div class="modal fade" id="add_service">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add New Service</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"><i class="ti ti-x"></i></button>
                    </div>
                    <form action="portfolio-services.php" method="POST">
                        <div class="modal-body">
                            <div class="row">
                                
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Service Title *</label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g. App Development" required>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description *</label>
                                    <textarea name="description" class="form-control" rows="4" placeholder="Enter service description..." required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" name="add-form" class="btn btn-primary">Add Service</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Service Modal -->
        <div class="modal fade" id="edit_service">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Service</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"><i class="ti ti-x"></i></button>
                    </div>
                    <form action="portfolio-services.php" method="POST">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="modal-body">
                            <div class="row">
                              
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Service Title *</label>
                                    <input type="text" name="title" id="edit-title" class="form-control" required>
                                </div>
                                                        
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description *</label>
                                    <textarea name="description" id="edit-description" class="form-control" rows="4" required></textarea>
                                </div>
                                <div class="col-md-12 modal-status-toggle d-flex align-items-center justify-content-between mb-4">
                                    <div class="status-title">
                                        <label class="form-label">Status *</label>
                                        <p class="mb-0 text-muted">Toggle to activate or deactivate</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="edit-status" name="status">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" name="edit-form" class="btn btn-primary">Update Service</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="portfolio-services.php">
                        <input type="hidden" name="ids" id="delete-service-id" value="">
                        <div class="modal-body text-center">
                            <span class="delete-icon"><i class="ti ti-trash-x"></i></span>
                            <h4>Confirm Deletion</h4>
                            <p>You want to delete the selected service(s), this cannot be undone.</p>
                            <div class="d-flex justify-content-center">
                                <a href="#" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                                <button type="submit" name="delete-form" class="btn btn-danger">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="mt-5 text-center"><?php require_once('copyright.php'); ?></div>
        </footer>
    </div>

    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/feather.min.js"></script>
    <script src="assets/js/jquery.dataTables.min.js"></script>
     <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="assets/js/admin-custom.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            // Populate Edit Modal
            $('.edit-btn').on('click', function() {
                $('#edit-id').val($(this).data('id'));
                $('#edit-icon').val($(this).data('icon'));
                $('#edit-title').val($(this).data('title'));
                $('#edit-description').val($(this).data('description'));
                $('#edit-link').val($(this).data('link'));
                $('#edit-status').prop('checked', $(this).data('status') == 1);
            });

            // Single Delete
            $('.delete-btn').on('click', function() {
                var id = $(this).data('id');
                if(id) {
                    $('#delete-service-id').val(id);
                }
            });

            // Bulk Delete
            $('#delete-selected').click(function() {
                var selectedIds = [];
                $('.delete-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length > 0) {
                    $('#delete-service-id').val(selectedIds.join(','));
                    $('#delete-modal').modal('show');
                } else {
                    Swal.fire({
                        toast: true, position: 'bottom-end', icon: 'warning',
                        title: "Please select at least one service to delete.",
                        showConfirmButton: false, timer: 3000
                    });
                }
            });

            // Select All Checkbox
            $('#select-all').on('click', function() {
                $('.delete-checkbox').prop('checked', this.checked);
            });
        });
    </script>
</body>
</html>