<?php
session_start();
require_once('db/config.php');
if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}

// Set default timezone
date_default_timezone_set('Asia/Kolkata');

// Function to generate a slug from the title
function generateSlug($title)
{
    $slug = strtolower($title); // Convert to lowercase
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug); // Remove special characters
    $slug = trim(preg_replace('/\s+/', '-', $slug), '-'); // Replace spaces with hyphens and trim
    return $slug;
}

// Handle form submissions for adding a new event
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //    echo '<pre>';
    //    print_r($_POST);
    //    exit;
    // 1. ADD NEW EVENT
    if (isset($_POST['add-form'])) {
        $eventTitle = htmlspecialchars(strip_tags(trim($_POST['title'])));
        $status = isset($_POST['status']) ? 1 : 0;
        $slug = generateSlug($eventTitle);
        $description = $_POST['content'];

        // ✅ FIXED: Added sanitization for location
        $location = isset($_POST['location']) ? htmlspecialchars(strip_tags(trim($_POST['location']))) : '';

        $imageLink = isset($_POST['image_link']) ? htmlspecialchars(strip_tags(trim($_POST['image_link']))) : '';
        $programLink = isset($_POST['program_link']) ? htmlspecialchars(strip_tags(trim($_POST['program_link']))) : '';
        $date = (isset($_POST['date']) && !empty($_POST['date'])) ? htmlspecialchars(trim($_POST['date'])) : date("Y-m-d");
        $enddate = (isset($_POST['end_date']) && !empty($_POST['end_date'])) ? htmlspecialchars(trim($_POST['end_date'])) : date("Y-m-d");

        $featuredImage = '';
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
            $targetDir = "event/";
            $targetFile = $targetDir . basename($_FILES["featured_image"]["name"]);

            $check = getimagesize($_FILES["featured_image"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["featured_image"]["tmp_name"], $targetFile)) {
                    $featuredImage = $targetFile;
                } else {
                    $_SESSION['message'] = "Sorry, there was an error uploading your file.";
                    header('Location: admin-event.php');
                    exit();
                }
            } else {
                $_SESSION['message'] = "File is not an image.";
                header('Location: admin-event.php');
                exit();
            }
        }

        // ✅ FIXED: 9 's' for 9 variables
        $stmt = $db->prepare("INSERT INTO event (title, description, image, date, image_link, program_link, status, slug, location, end_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $eventTitle, $description, $featuredImage, $date, $imageLink, $programLink, $status, $slug, $location, $enddate);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Event added successfully!";
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
        }
        header('Location: admin-event.php');
        exit();
    }

    // 2. EDIT EVENT
    if (isset($_POST['edit-form'])) {
        $idevent = intval($_POST['idevent']);
        $title = htmlspecialchars(strip_tags(trim($_POST['event_title'])));
        $slug = generateSlug($title);

        // Status is integer (1 or 0)
        $status = isset($_POST['status']) ? 1 : 0;

        $content = $_POST['content'];

        // Location safely
        $location = isset($_POST['location']) ? htmlspecialchars(strip_tags(trim($_POST['location']))) : '';

        $imageLink = isset($_POST['image_link']) ? htmlspecialchars(strip_tags(trim($_POST['image_link']))) : '';
        $programLink = isset($_POST['program_link']) ? htmlspecialchars(strip_tags(trim($_POST['program_link']))) : '';

        // ✅ FIXED: Use separate variables for Start Date and End Date
        $startDate = (isset($_POST['date']) && !empty($_POST['date'])) ? htmlspecialchars(trim($_POST['date'])) : date("Y-m-d");
        $endDate   = (isset($_POST['end_date']) && !empty($_POST['end_date'])) ? htmlspecialchars(trim($_POST['end_date'])) : date("Y-m-d");

        $image_path = $_POST['existing_image'];
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
            $image = $_FILES['featured_image']['name'];
            $target_dir = "event/";
            $target_file = $target_dir . basename($image);
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $target_file)) {
                $image_path = $target_file;
            }
        }

        // ✅ FIXED SQL QUERY & BIND PARAM
        // Order: title, slug, status, description, image, date, image_link, program_link, end_date, location, idevent
        $stmt = $db->prepare("UPDATE event SET 
        title = ?, 
        slug = ?, 
        status = ?, 
        description = ?, 
        image = ?, 
        date = ?, 
        image_link = ?, 
        program_link = ?, 
        end_date = ?, 
        location = ? 
        WHERE idevent = ?");

        // ✅ FIXED TYPE STRING: "ssisssssssi" (11 characters matching 11 variables)
        // s = string, i = integer
        $stmt->bind_param(
            "ssisssssssi",
            $title,
            $slug,
            $status,
            $content,
            $image_path,
            $startDate,    // Maps to date = ?
            $imageLink,
            $programLink,
            $endDate,      // Maps to end_date = ?
            $location,     // Maps to location = ?
            $idevent
        );

        if ($stmt->execute()) {
            $_SESSION['message'] = "Event updated successfully!";
        } else {
            $_SESSION['message'] = "Error updating event: " . $stmt->error;
        }

        $stmt->close();
        header('Location: admin-event.php');
        exit();
    }

    // 3. DELETE EVENT (Your existing code is fine, keeping it as is)
    if (isset($_POST['delete-form'])) {
        if (!empty($_POST['ids'])) {
            $ids = $_POST['ids'];
            $idsArray = explode(',', $ids);
            $placeholders = implode(',', array_fill(0, count($idsArray), '?'));
            $stmt = $db->prepare("DELETE FROM event WHERE idevent IN ($placeholders)");
            $types = str_repeat('i', count($idsArray));
            $stmt->bind_param($types, ...$idsArray);

            if ($stmt->execute()) {
                $_SESSION['message'] = ($stmt->affected_rows > 0) ? "Event(s) deleted successfully!" : "No event found with those IDs.";
            } else {
                $_SESSION['message'] = "Error deleting event(s): " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = "Invalid event IDs.";
        }
        header('Location: admin-event.php');
        exit();
    }
}
// Fetch events
$stmtevents = $db->prepare("SELECT * FROM event ORDER BY idevent DESC");
$stmtevents->execute();
$result_events = $stmtevents->get_result();



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
    <meta name="robots" content="noindex, nofollow">
    <title>Event - Tee Mac Corporation</title>
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
        <div class="header">
            <?php require_once('header.php'); ?>
        </div>
        <div class="sidebar" id="sidebar">
            <?php require_once('admin-sidebar.php') ?>
        </div>
        <div class="page-wrapper">
            <div class="content">
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">All Events</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">All Events</li>
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
                            <a href="#" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_event"><i class="ti ti-square-rounded-plus me-2"></i>Add Event</a>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                        <h4 class="mb-3">All Event List</h4>
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
                        <div class="custom-datatable-filter table-responsive">
                            <table class="table datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="no-sort">
                                            <div class="form-check form-check-md"><input class="form-check-input" type="checkbox" id="select-all"></div>
                                        </th>
                                        <th>Title</th>
                                        <th>Image</th>
                                        <th>Url</th>
                                        <th>Event Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result_events->num_rows > 0) {
                                        while ($rowevents = $result_events->fetch_assoc()) {
                                            $imagePath = htmlspecialchars($rowevents['image']);
                                    ?>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md"><input class="form-check-input delete-checkbox" type="checkbox" value="<?php echo $rowevents['idevent']; ?>"></div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="ms-2">
                                                            <p class="text-dark mb-0"><?php echo $rowevents['title']; ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if (!empty($imagePath)) { ?>
                                                        <img src='<?php echo $imagePath; ?>' alt='Event Image' style='max-width: 100px; max-height: 100px;'>
                                                    <?php } else { ?>
                                                        <span class="text-muted">No image</span>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $rowevents['slug']; ?></td>
                                                <td><?php echo $rowevents['date']; ?></td>
                                                <td>
                                                    <?php if ($rowevents['status'] == 1) { ?>
                                                        <span class="badge badge-soft-success d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Active</span>
                                                    <?php } else { ?>
                                                        <span class="badge badge-soft-danger d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Inactive</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle edit-btn p-0 me-2" data-bs-toggle="modal"
                                                            data-id="<?php echo $rowevents['idevent']; ?>"
                                                            data-title="<?php echo $rowevents['title']; ?>"
                                                            data-content="<?php echo htmlspecialchars($rowevents['description']); ?>"
                                                            data-date="<?php echo $rowevents['date']; ?>"
                                                            data-end-date="<?php echo $rowevents['end_date']; ?>"
                                                            data-image="<?php echo $rowevents['image']; ?>"
                                                            data-location="<?php echo $rowevents['location']; ?>"
                                                            data-image-link="<?php echo $rowevents['image_link']; ?>"
                                                            data-program-link="<?php echo $rowevents['program_link']; ?>"
                                                            data-slug="<?php echo $rowevents['slug']; ?>"
                                                            data-status="<?php echo $rowevents['status']; ?>"
                                                            data-bs-target="#edit_event"><i class="ti ti-edit-circle text-primary"></i></a>
                                                        <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-3 delete-btn" data-bs-toggle="modal" data-bs-target="#delete-modal" data-id="<?php echo $rowevents['idevent']; ?>"><i class="ti ti-trash-x text-danger"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add All Events -->
        <div class="modal fade" id="add_event">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add New Event</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <form action="admin-event.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <!-- Title input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" id="add-title" placeholder="Enter Event Title" class="form-control" required>
                                </div>
                                <!-- Slug input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Url (Auto-generated)</label>
                                    <input type="text" name="slug" id="add-slug" placeholder="Auto-generated from title" class="form-control" readonly>
                                </div>
                                <!-- Date input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Event Start Date</label>
                                    <input type="date" name="date" id="add-date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Event End Date</label>
                                    <input type="date" name="end_date" id="add-end-date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <!-- Image upload -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Image (Optional)</label>
                                    <div class="d-flex align-items-center upload-pic flex-wrap row-gap-3">
                                        <div class="d-flex align-items-center justify-content-center avatar avatar-xxl border border-dashed me-2 flex-shrink-0 text-dark frames">
                                            <i class="ti ti-photo-plus fs-16"></i>
                                        </div>
                                        <div class="profile-upload">
                                            <div class="profile-uploader d-flex align-items-center">
                                                <div class="drag-upload-btn mb-3">
                                                    Upload
                                                    <input type="file" class="form-control image-sign" name="featured_image">
                                                </div>
                                            </div>
                                            <p>Upload image size 4MB, Format JPG, PNG, SVG</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Location-->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Location</label>
                                    <input type="text" name="location" id="location" placeholder="Add Location" class="form-control">
                                </div>
                                <!-- Image Link -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Scientific Program (Link)</label>
                                    <input type="url" name="image_link" id="add-image-link" placeholder="https://example.com/image.jpg" class="form-control">
                                </div>
                                <!-- Program Link -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Conference Memories (Link)</label>
                                    <input type="url" name="program_link" id="add-program-link" placeholder="https://example.com/program" class="form-control">
                                </div>
                                <!-- Description input -->
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <label for="title" class="form-label">Description *</label>
                                    <div id="summernote"></div>
                                    <input type="hidden" name="content" id="event-content">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" name="add-form" id="add-form" class="btn btn-primary">Add Event</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Add All Events -->

        <!-- Edit All Events Modal -->
        <div class="modal fade" id="edit_event" tabindex="-1" aria-labelledby="edit_eventLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="edit_eventLabel">Edit Event</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <form id="eventForm" action="admin-event.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <!-- Hidden input for event ID -->
                                <input type="hidden" name="idevent" id="edit-event-id">
                                <!-- Hidden input for existing image -->
                                <input type="hidden" name="existing_image" id="existing-image">
                                <!-- Title input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="event_title" id="edit-title" placeholder="Enter Event Title" class="form-control" required>
                                </div>
                                <!-- Slug input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Url (Auto-generated)</label>
                                    <input type="text" name="slug" id="edit-slug" placeholder="Auto-generated from title" class="form-control" readonly>
                                </div>
                                <!-- Date input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Event Start Date</label>
                                    <input type="date" name="date" id="edit-date" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Event End Date</label>
                                    <input type="date" name="end_date" id="edit-end-date" class="form-control">
                                </div>
                                <!-- Image upload -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Image (Optional)</label>
                                    <div class="d-flex align-items-center upload-pic flex-wrap row-gap-3">
                                        <div class="d-flex align-items-center justify-content-center avatar avatar-xxl border border-dashed me-2 flex-shrink-0 text-dark frames">
                                            <img id="edit-image-preview" src="" alt="Event Image" class="img-fluid" style="max-width: 100%; max-height: 100%;">
                                        </div>
                                        <div class="profile-upload">
                                            <div class="profile-uploader d-flex align-items-center">
                                                <div class="drag-upload-btn mb-3">
                                                    Upload
                                                    <input type="file" class="form-control image-sign" name="featured_image">
                                                </div>
                                            </div>
                                            <p>Upload image size 4MB, Format JPG, PNG, SVG</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Image Link -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Location</label>
                                    <input type="text" name="location" id="edit-location" placeholder="Add Locaiton" class="form-control">
                                </div>
                                <!-- Image Link -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Image Link (Optional)</label>
                                    <input type="url" name="image_link" id="edit-image-link" placeholder="https://example.com/image.jpg" class="form-control">
                                </div>
                                <!-- Program Link -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Program Link (Optional)</label>
                                    <input type="url" name="program_link" id="edit-program-link" placeholder="https://example.com/program" class="form-control">
                                </div>
                                <!-- Status input -->
                                <div class="col-md-6 modal-status-toggle d-flex align-items-center justify-content-between mb-4">
                                    <div class="status-title">
                                        <label for="title" class="form-label">Status *</label>
                                        <p>Change the Status by toggle</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="edit-status" name="status">
                                    </div>
                                </div>
                                <!-- Description input -->
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <label for="title" class="form-label">Description *</label>
                                    <div id="summernote1"></div>
                                    <input type="hidden" name="content" id="edit-content">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" name="edit-form" class="btn btn-primary">Update Event</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Edit All Events Modal -->

        <!-- Delete Modal -->
        <div class="modal fade" id="delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="admin-event.php">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="ids" id="delete-event-id" value="">
                        <div class="modal-body text-center">
                            <span class="delete-icon"><i class="ti ti-trash-x"></i></span>
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
        <!-- /Delete Modal -->

        <footer class="footer">
            <div class="mt-5 text-center">
                <?php require_once('copyright.php'); ?>
            </div>
        </footer>
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
        // Function to generate slug from title
        function generateSlug(title) {
            return title
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
                .trim()
                .replace(/\s+/g, '-') // Replace spaces with hyphens
                .replace(/-+/g, '-'); // Replace multiple hyphens with a single hyphen
        }

        $(document).ready(function() {
            // Auto-generate slug for Add modal
            $('#add-title').on('input', function() {
                var title = $(this).val();
                var slug = generateSlug(title);
                $('#add-slug').val(slug);
            });

            // Auto-generate slug for Edit modal
            $('#edit-title').on('input', function() {
                var title = $(this).val();
                var slug = generateSlug(title);
                $('#edit-slug').val(slug);
            });

            // Handle form submission for Add modal
            $('form').on('submit', function(e) {
                let content = $('#summernote').summernote('code');
                $('#event-content').val(content);
            });

            // Edit modal data population
            $('.edit-btn').on('click', function() {
                var eventId = $(this).data('id');
                var title = $(this).data('title');
                var content = $(this).data('content');
                var date = $(this).data('date');
                var enddate = $(this).data('end-date');
                var image = $(this).data('image');
                var status = $(this).data('status');
                var slug = $(this).data('slug');
                var location = $(this).data('location');
                var imageLink = $(this).data('image-link');
                var programLink = $(this).data('program-link');

                $('#edit-event-id').val(eventId);
                $('#edit-title').val(title);
                $('#edit-slug').val(slug);
                $('#edit-location').val(location);
                $('#edit-date').val(date);
                $('#edit-end-date').val(enddate);
                $('#edit-status').prop('checked', status == 1);
                $('#edit-content').val(content);
                $('#edit-image-link').val(imageLink);
                $('#edit-program-link').val(programLink);
                $('#edit-image-preview').attr('src', image);
                $('#existing-image').val(image);
                $('#summernote1').summernote('code', content);
            });

            // Set content for Edit modal before submission
            $('#eventForm').on('submit', function() {
                var content = $('#summernote1').summernote('code');
                $('#edit-content').val(content);
            });

            // Delete functionality
            $('.delete-btn').on('click', function() {
                var eventId = $(this).data('id');
                $('#delete-event-id').val(eventId);
                $('#delete-modal').modal('show');
            });

            $('#delete-selected').click(function() {
                var selectedIds = [];
                $('.delete-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length > 0) {
                    $('#delete-event-id').val(selectedIds.join(','));
                    $('#delete-modal').modal('show');
                } else {
                    alert('Please select at least one event to delete.');
                }
            });
        });
    </script>
</body>

</html>