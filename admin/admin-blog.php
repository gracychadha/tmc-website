<?php
session_start();
error_reporting();
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

// Handle form submissions for adding a new blog post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form is being submitted for adding a new blog
    if (isset($_POST['add-form']) && $_SERVER['REQUEST_METHOD'] == "POST") {
        // Capture form inputs
        $blogTitle = htmlspecialchars(strip_tags(trim($_POST['title'])));
        $category = $_POST['category']; // Ensure it's an integer
        $status = isset($_POST['status']) ? 0 : 1; // Convert checkbox value to 1 or 0
        $user = htmlspecialchars(strip_tags(trim($_POST['pname'])));
        $slug = generateSlug($blogTitle); // Generate slug from title
        $description = $_POST['content']; // Getting the content

        $date = $_POST['date']; // Current date in YYYY-MM-DD format

        // Handle file upload (Featured image)
        $featuredImage = ''; // Default to empty if no image is uploaded
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
            $targetDir = "blog/"; // Define the directory for uploading the image
            $targetFile = $targetDir . basename($_FILES["featured_image"]["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Check if image file is a valid image (validate file type)
            $check = getimagesize($_FILES["featured_image"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["featured_image"]["tmp_name"], $targetFile)) {
                    $featuredImage = $targetFile; // Assign the uploaded file path to the image variable
                } else {
                    $_SESSION['message'] = "Sorry, there was an error uploading your file.";
                    header('Location: admin-blog.php');
                    exit();
                }
            } else {
                $_SESSION['message'] = "File is not an image.";
                header('Location: admin-blog.php');
                exit();
            }
        }

        // Insert the blog data into the database
        $stmt = $db->prepare("INSERT INTO blog (title, content, date, image, status, user, slug, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssisss", $blogTitle, $description, $date, $featuredImage, $status, $user, $slug, $category);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Blog post added successfully!";
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
        }

        header('Location: admin-blog.php');
        exit();
    }

    // Handle editing a blog post
    if (isset($_POST['edit-form']) && $_SERVER['REQUEST_METHOD'] == "POST") {
        $idblog = intval($_POST['idblog']);
        $title = $_POST['blog_title'];
        $slug = generateSlug($title); // Generate slug from title
        $category_id = $_POST['category_name'];
        $user = $_POST['user'];
        $status = isset($_POST['status']) ? 1 : 0;
        $content = $_POST['content'];

        // Handle image upload
        $image_path = $_POST['existing_image']; // Default to existing image
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
            $image = $_FILES['featured_image']['name'];
            $target_dir = "blog/";
            $target_file = $target_dir . basename($image);
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $target_file)) {
                $image_path = $target_file; // Update to new image path if uploaded
            } else {
                $_SESSION['message'] = "Error uploading new image.";
                header('Location: admin-blog.php');
                exit();
            }
        }

        // Update the blog post in the database
        $stmt = $db->prepare("UPDATE blog SET title = ?, slug = ?, category_id = ?, user = ?, status = ?, content = ?, image = ? WHERE idblog = ?");
        $stmt->bind_param("sssssssi", $title, $slug, $category_id, $user, $status, $content, $image_path, $idblog);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Blog post updated successfully!";
        } else {
            $_SESSION['message'] = "Error updating blog post: " . $stmt->error;
        }

        $stmt->close();
        header('Location: admin-blog.php');
        exit();
    }

    // Handle deleting blog posts
    if (isset($_POST['delete-form']) && $_SERVER['REQUEST_METHOD'] == "POST") {
        if (!empty($_POST['ids'])) { // Ensure the IDs exist
            $ids = $_POST['ids'];
            $idsArray = explode(',', $ids);

            // Prepare the SQL statement with placeholders
            $placeholders = implode(',', array_fill(0, count($idsArray), '?'));
            $stmt = $db->prepare("DELETE FROM blog WHERE idblog IN ($placeholders)");

            // Bind the parameters dynamically
            $types = str_repeat('i', count($idsArray));
            $stmt->bind_param($types, ...$idsArray);

            if ($stmt->execute()) {
                $_SESSION['message'] = ($stmt->affected_rows > 0)
                    ? "Blog post(s) deleted successfully!"
                    : "No career post found with those IDs.";
            } else {
                $_SESSION['message'] = "Error deleting career post(s): " . $stmt->error;
            }

            $stmt->close();
        } else {
            $_SESSION['message'] = "Invalid career IDs.";
        }

        header('Location: admin-blog.php');
        exit();
    }

    // Redirect after processing
    header('Location: admin-blog.php');
    exit();
}

// Fetch blog posts
$stmtblog = $db->prepare("SELECT * FROM blog ORDER BY idblog DESC");
$stmtblog->execute();
$result_blog = $stmtblog->get_result();

// Fetch categories where status is active (1)
$stmtcategory = $db->prepare("SELECT idblog_category, category_name FROM blog_category WHERE status = 1");
$stmtcategory->execute();
$result_category = $stmtcategory->get_result();


$stmtcategoryss = $db->prepare("SELECT idblog_category, category_name FROM blog_category WHERE status = 1");
$stmtcategoryss->execute();
$result_categoryss = $stmtcategoryss->get_result();

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
    <title>All Blogs - Tee Mac Corporation</title>
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
                        <h3 class="page-title mb-1">All Blogs</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">All Blogs</li>
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
                            <a href="#" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_blog"><i class="ti ti-square-rounded-plus me-2"></i>Add Blogs</a>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                        <h4 class="mb-3">All Blogs List</h4>
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
                                        <th>Publish By</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result_blog->num_rows > 0) {
                                        while ($rowblog = $result_blog->fetch_assoc()) {
                                            $imagePath = htmlspecialchars($rowblog['image']);
                                    ?>
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-md"><input class="form-check-input delete-checkbox" type="checkbox" value="<?php echo $rowblog['idblog']; ?>"></div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="ms-2">
                                                            <p class="text-dark mb-0"><?php echo $rowblog['title']; ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><img src='<?php echo $imagePath; ?>' alt='Blog Image' style='max-width: 100px; max-height: 100px;'></td>
                                                <td><?php echo $rowblog['slug']; ?></td>
                                                <td><?php echo $rowblog['user']; ?></td>
                                                <td>
                                                    <?php if ($rowblog['status'] == 1) { ?>
                                                        <span class="badge badge-soft-success d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Active</span>
                                                    <?php } else { ?>
                                                        <span class="badge badge-soft-danger d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Inactive</span>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $rowblog['date']; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle edit-btn p-0 me-2" data-bs-toggle="modal"
                                                            data-id="<?php echo $rowblog['idblog']; ?>"
                                                            data-title="<?php echo $rowblog['title']; ?>"
                                                            data-content="<?php echo htmlspecialchars($rowblog['content']); ?>"
                                                            data-date="<?php echo $rowblog['date']; ?>"
                                                            data-image="<?php echo $rowblog['image']; ?>"
                                                            data-status="<?php echo $rowblog['status']; ?>"
                                                            data-user="<?php echo $rowblog['user']; ?>"
                                                            data-slug="<?php echo $rowblog['slug']; ?>"
                                                            data-category="<?php echo $rowblog['category_id']; ?>"
                                                            data-bs-target="#edit_role"><i class="ti ti-edit-circle text-primary"></i></a>
                                                        <a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-3 delete-btn" data-bs-toggle="modal" data-bs-target="#delete-modal" data-id="<?php echo $rowblog['idblog']; ?>"><i class="ti ti-trash-x text-danger"></i></a>
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

        <!-- Add All Blogs -->
        <div class="modal fade" id="add_blog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add New Blog</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <form action="admin-blog.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <!-- Title input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" id="add-title" placeholder="Enter Blog Title" class="form-control" required>
                                </div>
                                <!-- Slug input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Url (Auto-generated)</label>
                                    <input type="text" name="slug" id="add-slug" placeholder="Auto-generated from title" class="form-control" readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Blog Category</label>
                                    <select name="category" id="blog_category" class="form-control" required>
                                        <option value="">--Select Blog Category--</option>
                                        <?php while ($row = $result_category->fetch_assoc()): ?>
                                            <option value="<?php echo $row['category_name']; ?>">
                                                <?php echo htmlspecialchars($row['category_name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <!-- Publish By input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Publish By</label>
                                    <input type="text" name="pname" placeholder="Enter Publisher Name" class="form-control" required>
                                </div>

                                 <div class="col-md-6 mb-3">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="date"  class="form-control" >
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
                                <!-- Description input -->
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <label for="title" class="form-label">Content *</label>
                                    <div id="summernote"></div>
                                    <input type="hidden" name="content" id="blog-content">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" name="add-form" id="add-form" class="btn btn-primary">Add Blog</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Add All Blogs -->

        <!-- Edit All Blogs Modal -->
        <div class="modal fade" id="edit_role" tabindex="-1" aria-labelledby="edit_serviceLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="edit_serviceLabel">Edit Blog</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                    <form id="blogForm" action="admin-blog.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <!-- Hidden input for blog ID -->
                                <input type="hidden" name="idblog" id="edit-blog-id">
                                <!-- Hidden input for existing image -->
                                <input type="hidden" name="existing_image" id="existing-image">
                                <!-- Title input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="blog_title" id="edit-title" placeholder="Enter Blog Title" class="form-control" required>
                                </div>
                                <!-- Slug input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Url (Auto-generated)</label>
                                    <input type="text" name="slug" id="edit-slug" placeholder="Auto-generated from title" class="form-control" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Blog Category</label>
                                    <select name="category_name" id="edit-category" class="form-control" required>
                                        <option value="">--Select Blog Category--</option>
                                        <?php while ($row = $result_categoryss->fetch_assoc()): ?>
                                            <option value="<?php echo $row['category_name']; ?>">
                                                <?php echo htmlspecialchars($row['category_name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <!-- Publish By input -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Publish By</label>
                                    <input type="text" name="user" id="edit-user" placeholder="Enter Publisher Name" class="form-control" required>
                                </div>
                                <!-- Image upload -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Image (Optional)</label>
                                    <div class="d-flex align-items-center upload-pic flex-wrap row-gap-3">
                                        <div class="d-flex align-items-center justify-content-center avatar avatar-xxl border border-dashed me-2 flex-shrink-0 text-dark frames">
                                            <img id="edit-image-preview" src="" alt="Blog Image" class="img-fluid" style="max-width: 100%; max-height: 100%;">
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
                                    <label for="title" class="form-label">Content *</label>
                                    <div id="summernote1"></div>
                                    <input type="hidden" name="content" id="edit-content">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" name="edit-form" class="btn btn-primary">Update Blog</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Edit All Blogs Modal -->

        <!-- Delete Modal -->
        <div class="modal fade" id="delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="admin-blog.php">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="ids" id="delete-blog-id" value="">
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
                $('#blog-content').val(content);
            });

            // Edit modal data population
            // Edit modal data population
            $('.edit-btn').on('click', function() {
                var blogId = $(this).data('id');
                var title = $(this).data('title');
                var content = $(this).data('content');
                var date = $(this).data('date');
                var image = $(this).data('image');
                var status = $(this).data('status');
                var user = $(this).data('user');
                var slug = $(this).data('slug');
                var categoryId = $(this).data('category'); // New line

                console.log(categoryId);

                $('#edit-blog-id').val(blogId);
                $('#edit-title').val(title);
                $('#edit-slug').val(slug);
                $('#edit-user').val(user);
                $('#edit-status').prop('checked', status == 1);
                $('#edit-content').val(content);
                $('#edit-image-preview').attr('src', image);
                $('#existing-image').val(image);
                $('#summernote1').summernote('code', content);

                $('#edit-category').val(categoryId); // Set selected category
            });


            // Set content for Edit modal before submission
            $('#blogForm').on('submit', function() {
                var content = $('#summernote1').summernote('code');
                $('#edit-content').val(content);
            });

            // Delete functionality
            $('.delete-btn').on('click', function() {
                var blogId = $(this).data('id');
                $('#delete-blog-id').val(blogId);
                $('#delete-modal').modal('show');
            });

            $('#delete-selected').click(function() {
                var selectedIds = [];
                $('.delete-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length > 0) {
                    $('#delete-blog-id').val(selectedIds.join(','));
                    $('#delete-modal').modal('show');
                } else {
                    alert('Please select at least one blog post to delete.');
                }
            });
        });
    </script>
</body>

</html>