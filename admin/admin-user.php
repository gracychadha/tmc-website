<?php
session_start();
require_once('db/config.php');

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete-form']) && isset($_POST['idcontact'])) {
        // Process Delete Contact Messages
        $id = intval($_POST['idcontact']); // Get single id from form
        $sql_delete = "DELETE FROM contact WHERE idcontact = ?";
        $stmt = $db->prepare($sql_delete);
        $stmt->bind_param('i', $id); // Bind the id as an integer

        if ($stmt->execute()) {
            $_SESSION['message'] = "Contact message deleted successfully.";
        } else {
            $_SESSION['message'] = "Failed to delete the contact message.";
        }
        $stmt->close();
        header("Location: admin-contact.php");
        exit();
    } elseif (isset($_POST['idcontact'])) {
        // Process Edit Contact Messages
        $id = intval($_POST['idcontact']);
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $message = $_POST['message'];

        $sql_update = "UPDATE contact SET name = ?, phone = ?, email = ?, message = ? WHERE idcontact = ?";
        $stmt = $db->prepare($sql_update);
        $stmt->bind_param('ssssi', $name, $phone, $email, $message, $id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Contact message updated successfully.";
        } else {
            $_SESSION['message'] = "Failed to update the contact message.";
        }
        $stmt->close();
        header("Location: admin-contact.php");
        exit();
    }
} elseif (isset($_GET['idcontact'])) {
    // Fetch Contact Message for Editing
    $id = intval($_GET['idcontact']);
    $sql_fetch = "SELECT * FROM contact WHERE idcontact = ?";
    $stmt = $db->prepare($sql_fetch);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    echo json_encode($data);
    $stmt->close();
    exit();
}

// Fetch existing data for the contact messages section
$sql_check_contact = "SELECT * FROM contact ORDER BY idcontact DESC";
$result_contact = $db->query($sql_check_contact);

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
    <title>Contact Messages - Admin</title>

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
                <div class="d-md-flex align-items-center justify-content-between mb-3">
                  
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Contact Message</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="dashboard.php">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Contact Message</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Display Messages -->
                <?php
                if (isset($_SESSION['message'])) {
                    echo '<div id="messageBox" class="alert ' . (strpos($_SESSION['message'], 'successfully') !== false ? 'alert-success' : 'alert-danger') . ' alert-dismissible fade show" role="alert">';
                    echo $_SESSION['message'];
                    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                    echo '</div>';
                    unset($_SESSION['message']);
                }
                ?>

                <!-- Contact Messages Table -->
                <div class="card">
                    <div class="card-header">
                        <h4>Contact Messages</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        // Check if there are any contacts
                        $stmt = $db->prepare("SELECT * FROM contact ORDER BY idcontact DESC");
                        $stmt->execute();
                        $result_contact = $stmt->get_result();

                        if ($result_contact->num_rows > 0): ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>SN</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Message</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    while ($data_contact = $result_contact->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo htmlspecialchars($data_contact['name']); ?></td>
                                            <td><?php echo htmlspecialchars($data_contact['phone']); ?></td>
                                            <td><?php echo htmlspecialchars($data_contact['email']); ?></td>
                                            <td><?php echo htmlspecialchars($data_contact['message']); ?></td>
                                            <td><?php echo date('Y-m-d', strtotime($data_contact['date'])); ?></td>
                                            <td>
                                               
                                                <button class="btn btn-primary btn-sm btn-edit" data-id="<?php echo $data_contact['idcontact']; ?>">Edit</button>
                                                <button class="btn btn-danger btn-sm btn-delete" data-id="<?php echo $data_contact['idcontact']; ?>">Delete</button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>No contact messages found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        <footer class="footer">
            <div class="mt-5 text-center">
                <?php
                require_once('copyright.php');
                ?>
            </div>
        </footer>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="delete-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="admin-contact.php">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="idcontact" id="delete-idcontact">
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Contact Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST" action="admin-contact.php">
                        <input type="hidden" name="idcontact" id="editIdcontact">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPhone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="editPhone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editMessage" class="form-label">Message</label>
                            <textarea class="form-control" id="editMessage" name="message" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
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
    <script src="assets/js/rocket-loader.min.js" data-cf-settings="feb024e4d970c7c806ef5348-|49" defer=""></script>
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
    document.addEventListener('DOMContentLoaded', function () {
        // Delete Button Click
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {
                const idcontact = this.getAttribute('data-id');
                document.getElementById('delete-idcontact').value = idcontact;
                const deleteModal = new bootstrap.Modal(document.getElementById('delete-modal'));
                deleteModal.show();
            });
        });

        // Edit Button Click
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function () {
                const idcontact = this.getAttribute('data-id');
                fetch(`admin-contact.php?idcontact=${idcontact}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editIdcontact').value = data.idcontact;
                    document.getElementById('editName').value = data.name;
                    document.getElementById('editPhone').value = data.phone;
                    document.getElementById('editEmail').value = data.email;
                    document.getElementById('editMessage').value = data.message;
                    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                    editModal.show();
                });
            });
        });
    });
    </script>
</body>

</html>
