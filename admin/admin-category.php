<?php
session_start();
require_once('db/config.php');
if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}


// Set default timezone
date_default_timezone_set('Asia/Kolkata');

// Fetch data from the database
$stmt = $db->prepare("SELECT * FROM blog_category ORDER BY idblog_category DESC");
$stmt->execute();
$result_category = $stmt->get_result();

if (!$result_category) {
	$_SESSION['message'] = "No Data found: " . $db->error;
	header('Location: admin-category.php');
	exit;
}
// Initialize a counter variable i

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['action'])) {
		$action = $_POST['action'];

		// Add a new category
		if ($action === 'add') {
			$categoryName = $_POST['category_name'];
			$status = isset($_POST['status']) ? 1 : 0;
			$date = date("Y-m-d");

			$stmt = $db->prepare('INSERT INTO blog_category (category_name, status, date) VALUES (?, ?, ?)');
			$stmt->bind_param('sis', $categoryName, $status, $date);

			if ($stmt->execute()) {
				$_SESSION['message'] = 'Blog category added successfully!';
			} else {
				$_SESSION['message'] = 'Error: ' . $stmt->error;
			}
		}

		// Edit an existing category
		if ($action === 'edit') {
			// Get and decode the ID if it was Base64 encoded
			$id = base64_decode($_POST['id']);
			$categoryName = $_POST['category_name'];
			$status = isset($_POST['status']) ? 1 : 0;

			// Prepare SQL query to update the category
			$stmt = $db->prepare('UPDATE blog_category SET category_name = ?, status = ? WHERE idblog_category = ?');
			$stmt->bind_param('sii', $categoryName, $status, $id);

			if ($stmt->execute()) {
				$_SESSION['message'] = 'Blog category updated successfully!';
			} else {
				$_SESSION['message'] = 'Error: ' . $stmt->error;
			}
		}

		// Delete a category
		if ($action === 'delete') {
			$id = $_POST['id'];  // Get the category ID from the form submission

			// Prepare and execute SQL query to delete the category
			$stmt = $db->prepare('DELETE FROM blog_category WHERE idblog_category = ?');
			$stmt->bind_param('i', $id);

			if ($stmt->execute()) {
				$_SESSION['message'] = 'Blog category deleted successfully!';
			} else {
				$_SESSION['message'] = 'Error: ' . $stmt->error;
			}
		}

		// Redirect back to the categories page
		header('Location: admin-category.php');
		exit();
	}
}

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
	<title>Blog Category - Tee Mac Corporation </title>

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">

	<!-- Theme Script -->
	<script src="assets/js/theme-script.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">

	<!-- Feather CSS -->
	<link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">

	<!-- Tabler Icon CSS -->
	<link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">

	<!-- Daterangepicker CSS -->
	<link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

	<!-- Datetimepicker CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

	<!-- Select2 CSS -->
	<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">

	<!-- Summernote CSS -->
	<link rel="stylesheet" href="assets/plugins/summernote/summernote-lite.min.css">

	<!-- Datatable CSS -->
	<link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">

	<!-- Main CSS -->
	<link rel="stylesheet" href="assets/css/style.css">
	<!-- Toast -->
	 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

</head>

<body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
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
			<div class="content">

				<!-- Page Header -->
				<div class="d-md-flex d-block align-items-center justify-content-between mb-3">
					<div class="my-auto mb-2">
						<h3 class="page-title mb-1">Blog Categories</h3>
						<nav>
							<ol class="breadcrumb mb-0">
								<li class="breadcrumb-item">
									<a href="dashboard.php">Dashboard</a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Blog Categories</li>
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
							<ul class="dropdown-menu  dropdown-menu-end p-3">
								<li>
									<a href="javascript:void(0);" class="dropdown-item rounded-1" id="export-pdf"><i class="ti ti-file-type-pdf me-1"></i>Export as PDF</a>
								</li>
								<li>
									<a href="javascript:void(0);" class="dropdown-item rounded-1" id="export-word"><i class="ti ti-file-type-doc me-1"></i>Export as Word</a>
								</li>
							</ul>
						</div>
						<div class="mb-2">
							<a href="#" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_category"><i class="ti ti-square-rounded-plus me-2"></i>Add
								Category</a>
						</div>
					</div>
				</div>
				<!-- /Page Header -->

				<!-- Filter Section -->
				<div class="card">
					<div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
						<h4 class="mb-3">Categories List</h4>
						<div class="d-flex align-items-center flex-wrap">
							<div class="dropdown mb-3 me-2">
								<a href="javascript:void(0);" class="btn btn-outline-light bg-white dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-filter me-2"></i>Filter</a>
							
							</div>
							<div class="dropdown mb-3">
								<a href="javascript:void(0);" class="btn btn-outline-light bg-white dropdown-toggle" data-bs-toggle="dropdown"><i class="ti ti-sort-ascending-2 me-2"></i>Sort by A-Z
								</a>
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
					</div>
					<div class="card-body p-0 py-3">
						<!-- Categories List -->
						<div class="custom-datatable-filter
 						table-responsive">
							<?php


							// Display data in the HTML table
							if ($result_category->num_rows > 0) {
								echo '<table class="table datatable">';
								echo '<thead class="thead-light">';
								echo '<tr>';
								echo '<th class="no-sort"><div class="form-check form-check-md"><input class="form-check-input" type="checkbox" id="select-all"></div> SN</th>';
								echo '<th>Blog Category Name</th>';
								echo '<th>Added on</th>';
								echo '<th>Status</th>';
								echo '<th>Action</th>';
								echo '</tr>';
								echo '</thead>';
								echo '<tbody>';
								$i = 1;

								while ($data_category = $result_category->fetch_assoc()) {
									echo '<tr>';

									echo '<td><div class="form-check form-check-md"><input class="form-check-input" type="checkbox">&nbsp;</div></td>';

									echo '<td class="text-gray-9">' . $data_category['category_name'] . '</td>';
									echo '<td class="text-gray-9">' . $data_category['date'] . '</td>';
									echo '<td>';
									if ($data_category['status'] == 1) {
										echo '<span class="badge badge-soft-success d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Active</span>';
									} else {
										echo '<span class="badge badge-soft-danger d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Inactive</span>';
									}
									echo '</td>';
									echo '<td>';
									echo '<div class="d-flex align-items-center">';
									echo '<a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle  p-0 me-2" data-bs-toggle="modal" data-bs-target="#edit_category"
									
									data-id="' . $data_category['idblog_category'] . '"
									data-name="' . htmlspecialchars($data_category['category_name']) . '"
									data-status="' . $data_category['status'] . '"
									onclick="setEditCategoryValues(this)"><i class="ti ti-edit-circle text-primary"></i></a>';




									echo '<a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-3" data-bs-toggle="modal" data-bs-target="#delete-modal"  data-id="' . $data_category['idblog_category'] . '"
									data-name="' . htmlspecialchars($data_category['category_name']) . '"
									onclick="setDeleteCategoryValues(this)"><i class="ti ti-trash-x text-danger"></i></a>';

									echo '</div>';


									echo '</tr>';
								}

								echo '</tbody>';
								echo '</table>';
							} else {
								echo '<p>No categories found.</p>';
							}


							?>

						</div>
						<!-- /Categories List -->
					</div>
				</div>
				<!-- /Filter Section -->
			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- Add Category -->
		<div class="modal fade" id="add_category">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Add Category</h4>
						<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form id="categoryForm" action="admin-category.php" class="m-3" method="post">
						<input type="hidden" name="action" value="add">
						<div class="mb-3">
							<label class="form-label">Blog Category Name</label>
							<input type="text" class="form-control" placeholder="Enter Blog Category" name="category_name" required>
						</div>
						<div class="modal-status-toggle d-flex align-items-center justify-content-between mb-3">
							<div class="status-title">
								<h5>Status</h5>
								<p>Change the Status by toggle</p>
							</div>
							<div class="form-check form-switch">
								<input class="form-check-input" type="checkbox" role="switch" id="switch-sm" name="status">
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
							<button type="submit" name="add-submit" id="add-submit" class="btn btn-primary">Add Category</button>
						</div>
					</form>
					<div id="successMessage" style="display:none;" class="alert alert-success mt-3">Category added successfully!</div>

				</div>
			</div>
		</div>
		<!-- /Add Category -->


		<!-- Edit Category Modal -->
		<div class="modal fade" id="edit_category" tabindex="-1" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Edit Category</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form action="admin-category.php" method="POST">
						<input type="hidden" name="action" value="edit">
						<div class="modal-body">
							<input type="hidden" name="id" id="edit-category-id">
							<div class="mb-3">
								<label for="edit-category-name" class="form-label">Category Name</label>
								<input type="text" class="form-control" id="edit-category-name" name="category_name" placeholder="Enter Category">
							</div>

							<div class="modal-status-toggle d-flex align-items-center justify-content-between mb-3">
								<div class="status-title">
									<label for="edit-category-status" class="form-check-label">Status</label>
									<p>Change the Status by toggle</p>
								</div>
								<div class="form-check form-switch">
									<input class="form-check-input" type="checkbox" id="edit-category-status" name="status">
								</div>
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
		<!-- /Edit Category Modal -->



		<!-- Delete Category Modal -->
		<div class="modal fade" id="delete-modal" tabindex="-1" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<form action="admin-category.php" method="POST">
						<div class="modal-body text-center">
							<!-- Icon -->
							<span class="delete-icon">
								<i class="ti ti-trash-x"></i>
							</span>
							<!-- Title -->
							<h4>Confirm Deletion</h4>
							<!-- Description -->
							<p>You want to delete this category, this can't be undone once you delete.</p>

							<!-- Hidden input to pass the category ID -->
							<input type="hidden" name="action" value="delete">
							<input type="hidden" name="id" id="delete-category-id"> <!-- Hidden input for category ID -->

							<!-- Action Buttons -->
							<div class="d-flex justify-content-center">
								<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-danger">Yes, Delete</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /Delete Category Modal -->



		<footer class="footer">
			<div class="mt-5 text-center">
				<?php
				require_once('copyright.php');
				?>
			</div>
		</footer>

	</div>
	<!-- /Main Wrapper -->

	<!-- jQuery -->
	<script src="assets/js/jquery-3.7.1.min.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

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
	<script src="assets/js/script.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

	<script src="assets/js/rocket-loader.min.js" data-cf-settings="094c2cc781cee01c60adaad3-|49" defer=""></script>

	
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/vfs_fonts.js"></script>
    <script src="assets/js/export.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

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
		// Function to set values in the modal inputs
		function setEditCategoryValues(element) {
			// Get data attributes from the clicked link
			var categoryId = $(element).data('id');
			var categoryName = $(element).data('name');
			var categoryStatus = $(element).data('status'); // This should be either '1' (active) or '0' (inactive)

			// Optionally encode the ID if necessary (Base64 encoding in this case)
			var encodedCategoryId = btoa(categoryId); // Example: Base64 encoding the ID

			// Set values to modal's input fields using jQuery
			$('#edit-category-id').val(encodedCategoryId); // Set encoded ID
			$('#edit-category-name').val(categoryName);

			// Check or uncheck the status checkbox based on the category's status
			$('#edit-category-status').prop('checked', categoryStatus == '1'); // If categoryStatus is '1', the checkbox is checked
		}

		// Attach the click event handler to the "Edit" link
		$('a[data-bs-toggle="modal"]').on('click', function() {
			setEditCategoryValues(this);
		});



		// Function to set values for the delete modal
		function setDeleteCategoryValues(element) {
			var categoryId = $(element).data('id');
			var categoryName = $(element).data('name');

			// Set the category ID to the hidden input field in the form
			$('#delete-category-id').val(categoryId);

			// Set the category name to the confirmation message
			$('#delete-category-name').text(categoryName);
		}

		// Attach the click event handler to the "Delete" link
		$('a[data-bs-toggle="modal"]').on('click', function() {
			setDeleteCategoryValues(this);
		});
	</script>

</html>