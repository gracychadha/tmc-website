<?php
session_start();
require_once('db/config.php');
if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}

$decodedAdminId = base64_decode($_SESSION['adminId']);

// ── Handle Add Event ──
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    if ($_POST['action'] === 'add') {
        $title     = trim($_POST['title'] ?? '');
        $startDate = $_POST['start_date'] ?? '';
        $endDate   = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
        $allDay    = isset($_POST['all_day']) ? 1 : 0;
        $color     = trim($_POST['color'] ?? '#007bff');
        $type      = trim($_POST['event_type'] ?? 'event');
        $desc      = trim($_POST['description'] ?? '');

        if ($title && $startDate) {
            $stmt = $db->prepare("INSERT INTO calendar_events (title, start_date, end_date, all_day, color, event_type, description, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssissi", $title, $startDate, $endDate, $allDay, $color, $type, $desc, $decodedAdminId);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Event added successfully.";
            } else {
                $_SESSION['error'] = "Failed to add event.";
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Title and start date are required.";
        }
        header("Location: admin-calendar.php");
        exit();
    }

    if ($_POST['action'] === 'edit') {
        $id        = intval($_POST['event_id'] ?? 0);
        $title     = trim($_POST['title'] ?? '');
        $startDate = $_POST['start_date'] ?? '';
        $endDate   = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
        $allDay    = isset($_POST['all_day']) ? 1 : 0;
        $color     = trim($_POST['color'] ?? '#007bff');
        $type      = trim($_POST['event_type'] ?? 'event');
        $desc      = trim($_POST['description'] ?? '');

        if ($id && $title && $startDate) {
            $stmt = $db->prepare("UPDATE calendar_events SET title=?, start_date=?, end_date=?, all_day=?, color=?, event_type=?, description=? WHERE id=?");
            $stmt->bind_param("sssissii", $title, $startDate, $endDate, $allDay, $color, $type, $desc, $id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Event updated successfully.";
            } else {
                $_SESSION['error'] = "Failed to update event.";
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Invalid data.";
        }
        header("Location: admin-calendar.php");
        exit();
    }

    if ($_POST['action'] === 'delete' && isset($_POST['event_ids'])) {
        $ids = array_map('intval', explode(',', $_POST['event_ids']));
        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $db->prepare("DELETE FROM calendar_events WHERE id IN ($placeholders)");
            $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
            if ($stmt->execute()) {
                $_SESSION['message'] = count($ids) . " event(s) deleted successfully.";
            } else {
                $_SESSION['error'] = "Failed to delete events.";
            }
            $stmt->close();
        }
        header("Location: admin-calendar.php");
        exit();
    }
}

// Fetch all events
$allEvents = $db->query("SELECT * FROM calendar_events ORDER BY start_date DESC");

// Fetch favicon
$faviconPath = "logo/favicon.png";
if ($stmt = $db->prepare("SELECT favicon FROM system_setting LIMIT 1")) {
    $stmt->execute();
    $stmt->bind_result($favicon);
    if ($stmt->fetch()) { $faviconPath = "logo/" . $favicon; }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Calendar Events - Tee Mac Corporation</title>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['message'])): ?>
                Swal.fire({ toast: true, position: 'bottom-end', icon: 'success', title: "<?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>", showConfirmButton: false, timer: 4000 });
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                Swal.fire({ toast: true, position: 'bottom-end', icon: 'error', title: "<?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); ?>", showConfirmButton: false, timer: 4000 });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });
    </script>

    <div class="main-wrapper">
        <div class="header">
            <?php require_once('header.php'); ?>
        </div>
        <div class="sidebar" id="sidebar">
            <?php require_once('admin-sidebar.php'); ?>
        </div>

        <div class="page-wrapper">
            <div class="content">

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Calendar Events</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Calendar Events</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                        <div class="pe-1 mb-2">
                            <a href="admin-calendar.php" class="btn btn-outline-light bg-white btn-icon me-1" data-bs-toggle="tooltip" title="Refresh">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                        <div class="pe-1 mb-2">
                            <button type="button" class="btn btn-outline-light bg-white btn-icon me-1" data-bs-toggle="tooltip" title="Print" onclick="window.print();">
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
                    </div>
                </div>

                <!-- Table Card -->
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                        <h4 class="mb-3">All Events</h4>
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="mb-3 me-2">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                                    <i class="ti ti-plus me-1"></i>Add Event
                                </button>
                            </div>
                            <div class="mb-3 me-2">
                                <a href="javascript:void(0);" class="btn btn-outline-light bg-white delete-btn" id="delete-selected">
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
                                        <th class="no-sort" style="width:40px;">
                                            <div class="form-check form-check-md">
                                                <input class="form-check-input" type="checkbox" id="select-all">
                                            </div>
                                        </th>
                                        <th style="width:50px;">Sr. No.</th>
                                        <th>Event Title</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Type</th>
                                        <th>Color</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sr_no = 1;
                                    if ($allEvents && $allEvents->num_rows > 0) {
                                        while ($evt = $allEvents->fetch_assoc()) {
                                            $typeBadge = '';
                                            switch ($evt['event_type']) {
                                                case 'holiday':
                                                    $typeBadge = '<span class="badge bg-danger">Holiday</span>';
                                                    break;
                                                case 'reminder':
                                                    $typeBadge = '<span class="badge bg-warning text-dark">Reminder</span>';
                                                    break;
                                                default:
                                                    $typeBadge = '<span class="badge bg-primary">Event</span>';
                                            }
                                            $endDateDisplay = ($evt['end_date'] && $evt['end_date'] !== '0000-00-00') ? htmlspecialchars($evt['end_date']) : '-';
                                    ?>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input delete-checkbox" type="checkbox" value="<?php echo $evt['id']; ?>">
                                                    </div>
                                                </td>
                                                <td><?php echo $sr_no++; ?></td>
                                                <td>
                                                    <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:<?php echo htmlspecialchars($evt['color']); ?>;margin-right:8px;"></span>
                                                    <?php echo htmlspecialchars($evt['title']); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($evt['start_date']); ?></td>
                                                <td><?php echo $endDateDisplay; ?></td>
                                                <td><?php echo $typeBadge; ?></td>
                                                <td>
                                                    <span style="display:inline-block;width:20px;height:20px;border-radius:4px;background:<?php echo htmlspecialchars($evt['color']); ?>;"></span>
                                                </td>
                                                <td><?php echo htmlspecialchars($evt['description'] ?: '-'); ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-2 edit-event-btn"
                                                            data-id="<?php echo $evt['id']; ?>"
                                                            data-title="<?php echo htmlspecialchars($evt['title']); ?>"
                                                            data-start="<?php echo htmlspecialchars($evt['start_date']); ?>"
                                                            data-end="<?php echo htmlspecialchars($evt['end_date']); ?>"
                                                            data-color="<?php echo htmlspecialchars($evt['color']); ?>"
                                                            data-type="<?php echo htmlspecialchars($evt['event_type']); ?>"
                                                            data-desc="<?php echo htmlspecialchars($evt['description']); ?>"
                                                            data-allday="<?php echo $evt['all_day']; ?>"
                                                            data-bs-toggle="modal" data-bs-target="#editEventModal">
                                                            <i class="ti ti-edit-circle text-primary"></i>
                                                        </a>
                                                        <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-3 delete-event-btn"
                                                            data-id="<?php echo $evt['id']; ?>" data-title="<?php echo htmlspecialchars($evt['title']); ?>">
                                                            <i class="ti ti-trash-x text-danger"></i>
                                                        </a>
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
                    </div>
                </div>

            </div>

            <footer class="footer">
                <div class="mt-5 text-center">
                    <?php require_once('copyright.php'); ?>
                </div>
            </footer>
        </div>
    </div>

    <!-- Add Event Modal -->
    <div class="modal fade" id="addEventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="ti ti-calendar-plus me-2"></i>Add New Event</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="admin-calendar.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Event Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" required placeholder="e.g. Team Meeting">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="start_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Event Type</label>
                                <select class="form-select" name="event_type">
                                    <option value="event">Event</option>
                                    <option value="reminder">Reminder</option>
                                    <option value="holiday">Holiday</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Color</label>
                                <select class="form-select" name="color">
                                    <option value="#007bff">Blue</option>
                                    <option value="#28a745">Green</option>
                                    <option value="#dc3545">Red</option>
                                    <option value="#ffc107">Yellow</option>
                                    <option value="#17a2b8">Teal</option>
                                    <option value="#6f42c1">Purple</option>
                                    <option value="#fd7e14">Orange</option>
                                    <option value="#6c757d">Grey</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="all_day" value="1" checked>
                                <label class="form-check-label">All Day Event</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Optional description..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="ti ti-check me-1"></i>Save Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Event Modal -->
    <div class="modal fade" id="editEventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="ti ti-calendar-up me-2"></i>Edit Event</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="admin-calendar.php" method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="event_id" id="edit-event-id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Event Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" id="edit-event-title" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="start_date" id="edit-event-start" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" id="edit-event-end">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Event Type</label>
                                <select class="form-select" name="event_type" id="edit-event-type">
                                    <option value="event">Event</option>
                                    <option value="reminder">Reminder</option>
                                    <option value="holiday">Holiday</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Color</label>
                                <select class="form-select" name="color" id="edit-event-color">
                                    <option value="#007bff">Blue</option>
                                    <option value="#28a745">Green</option>
                                    <option value="#dc3545">Red</option>
                                    <option value="#ffc107">Yellow</option>
                                    <option value="#17a2b8">Teal</option>
                                    <option value="#6f42c1">Purple</option>
                                    <option value="#fd7e14">Orange</option>
                                    <option value="#6c757d">Grey</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="all_day" value="1" id="edit-event-allday">
                                <label class="form-check-label">All Day Event</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit-event-desc" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="ti ti-check me-1"></i>Update Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Single Event Modal -->
    <div class="modal fade" id="deleteEventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="admin-calendar.php">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="event_ids" id="delete-event-id">
                    <div class="modal-body text-center">
                        <span class="delete-icon"><i class="ti ti-trash-x"></i></span>
                        <h4>Confirm Deletion</h4>
                        <p>You want to delete "<strong id="delete-event-name"></strong>"? This cannot be undone.</p>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Yes, Delete</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Selected Modal -->
    <div class="modal fade" id="deleteSelectedModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="admin-calendar.php">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="event_ids" id="delete-selected-ids">
                    <div class="modal-body text-center">
                        <span class="delete-icon"><i class="ti ti-trash-x"></i></span>
                        <h4>Confirm Deletion</h4>
                        <p>You want to delete the selected event(s)? This cannot be undone.</p>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Yes, Delete</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/email-decode.min.js"></script>
    <script src="assets/js/feather.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/plugins/select2/js/select2.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="assets/js/filters.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/vfs_fonts.js"></script>
    <script src="assets/js/export.js"></script>
    <script>
        $(document).ready(function() {

            // Select All
            $('#select-all').on('click', function() {
                $('.delete-checkbox').prop('checked', this.checked);
            });

            // Edit Event - populate modal
            $('.edit-event-btn').on('click', function() {
                $('#edit-event-id').val($(this).data('id'));
                $('#edit-event-title').val($(this).data('title'));
                $('#edit-event-start').val($(this).data('start'));
                $('#edit-event-end').val($(this).data('end') && $(this).data('end') !== '0000-00-00' ? $(this).data('end') : '');
                $('#edit-event-color').val($(this).data('color'));
                $('#edit-event-type').val($(this).data('type'));
                $('#edit-event-desc').val($(this).data('desc'));
                $('#edit-event-allday').prop('checked', $(this).data('allday') == 1);
            });

            // Delete Single Event
            $('.delete-event-btn').on('click', function(e) {
                e.preventDefault();
                $('#delete-event-id').val($(this).data('id'));
                $('#delete-event-name').text($(this).data('title'));
                var modal = new bootstrap.Modal(document.getElementById('deleteEventModal'));
                modal.show();
            });

            // Delete Selected
            $('#delete-selected').on('click', function() {
                var selected = [];
                $('.delete-checkbox:checked').each(function() {
                    selected.push($(this).val());
                });
                if (selected.length > 0) {
                    $('#delete-selected-ids').val(selected.join(','));
                    var modal = new bootstrap.Modal(document.getElementById('deleteSelectedModal'));
                    modal.show();
                } else {
                    Swal.fire({ toast: true, position: 'bottom-end', icon: 'warning', title: 'Please select at least one event', showConfirmButton: false, timer: 3000 });
                }
            });

        });
    </script>
</body>

</html>
