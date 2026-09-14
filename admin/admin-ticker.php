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
$stmt = $db->prepare("SELECT * FROM ticker ORDER BY idticker DESC");
$stmt->execute();
$result_category = $stmt->get_result();

if (!$result_category) {
	$_SESSION['message'] = "No Data found: " . $db->error;
	header('Location: admin-ticker.php');
	exit;
}
// Initialize a counter variable i

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['action'])) {
		$action = $_POST['action'];

		// Add a new category
		if ($action === 'add') {
			$categoryName = $_POST['title'];
			$status = isset($_POST['status']) ? 1 : 0;
			$date = date("Y-m-d");

			$stmt = $db->prepare('INSERT INTO ticker (title, status) VALUES (?, ?)');
			$stmt->bind_param('si', $categoryName, $status);

			if ($stmt->execute()) {
				$_SESSION['message'] = 'Ticker added successfully!';
			} else {
				$_SESSION['message'] = 'Error: ' . $stmt->error;
			}
		}

		// Edit an existing ticker
		if ($action === 'edit') {
			$id = intval($_POST['id']);
			$title = $_POST['title'];
			$status = isset($_POST['status']) ? 1 : 0;

			$stmt = $db->prepare('UPDATE ticker SET title = ?, status = ? WHERE idticker = ?');
			$stmt->bind_param('sii', $title, $status, $id);

			if ($stmt->execute()) {
				$_SESSION['message'] = 'Ticker updated successfully!';
			} else {
				$_SESSION['message'] = 'Error: ' . $stmt->error;
			}
		}

		// Delete a ticker
		if ($action === 'delete') {
			$id = intval($_POST['id']);

			$stmt = $db->prepare('DELETE FROM ticker WHERE idticker = ?');
			$stmt->bind_param('i', $id);

			if ($stmt->execute()) {
				$_SESSION['message'] = 'Ticker deleted successfully!';
			} else {
				$_SESSION['message'] = 'Error: ' . $stmt->error;
			}
		}

		// Redirect back to the categories page
		header('Location: admin-ticker.php');
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
	<title>Ticker - Tee Mac Corporation </title>

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
	<link rel="stylesheet" href="assets/css/admin-custom.css">
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
						<h3 class="page-title mb-1">Ticker List</h3>
						<nav>
							<ol class="breadcrumb mb-0">
								<li class="breadcrumb-item">
									<a href="dashboard.php">Dashboard</a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Ticker List</li>
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
							<button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_category"><i class="ti ti-square-rounded-plus me-2"></i>Add
								Ticker</button>
						</div>
					</div>
				</div>
				<!-- /Page Header -->

				<!-- Filter Section -->
				<div class="card">
					<div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
						<h4 class="mb-3">Ticker List</h4>
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
								echo '<th>Ticker Name</th>';
								echo '<th>Status</th>';
								echo '<th>Action</th>';
								echo '</tr>';
								echo '</thead>';
								echo '<tbody>';
								$i = 1;

								while ($data_category = $result_category->fetch_assoc()) {
									echo '<tr>';

									echo '<td><div class="form-check form-check-md"><input class="form-check-input" type="checkbox">&nbsp;</div></td>';

									echo '<td class="text-gray-9">' . $data_category['title'] . '</td>';
									echo '<td>';
									if ($data_category['status'] == 1) {
										echo '<span class="badge badge-soft-success d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Active</span>';
									} else {
										echo '<span class="badge badge-soft-danger d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Inactive</span>';
									}
									echo '</td>';
									echo '<td>';
									echo '<div class="d-flex align-items-center">';
									echo '<button type="button" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle  p-0 me-2 tmc-ticker-edit-btn" data-bs-toggle="modal" data-bs-target="#edit_category"
									
									data-id="' . $data_category['idticker'] . '"
									data-name="' . htmlspecialchars($data_category['title']) . '"
									data-status="' . $data_category['status'] . '"><i class="ti ti-edit-circle text-primary"></i></button>';




									echo '<button type="button" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-3 tmc-ticker-delete-btn" data-bs-toggle="modal" data-bs-target="#delete-modal"  data-id="' . $data_category['idticker'] . '"
									data-name="' . htmlspecialchars($data_category['title']) . '"><i class="ti ti-trash-x text-danger"></i></button>';

									echo '</div>';


									echo '</tr>';
								}

								echo '</tbody>';
								echo '</table>';
							} else {
								echo '<p>No Ticker found.</p>';
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
						<h4 class="modal-title">Add Ticker</h4>
						<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form id="categoryForm" action="admin-ticker.php" class="m-3" method="post">
						<input type="hidden" name="action" value="add">
						<div class="mb-3">
							<label class="form-label">Ticker Name</label>
							<input type="text" class="form-control" placeholder="Enter Ticker Name" name="title" required>
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
							<button type="submit" name="add-submit" id="add-submit" class="btn btn-primary">Add </button>
						</div>
					</form>
					<div id="successMessage" style="display:none;" class="alert alert-success mt-3">Ticker added successfully!</div>

				</div>
			</div>
		</div>
		<!-- /Add Category -->


		<!-- Edit Category Modal -->
		<div class="modal fade" id="edit_category" tabindex="-1" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Edit Ticker</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form action="admin-ticker.php" method="POST">
						<input type="hidden" name="action" value="edit">
						<div class="modal-body">
							<input type="hidden" name="id" id="edit-ticker-id">
							<div class="mb-3">
								<label for="edit-ticker-name" class="form-label">Ticker Name</label>
								<input type="text" class="form-control" id="edit-ticker-name" name="title" placeholder="Enter Ticker">
							</div>

							<div class="modal-status-toggle d-flex align-items-center justify-content-between mb-3">
								<div class="status-title">
									<label for="edit-ticker-status" class="form-check-label">Status</label>
									<p>Change the Status by toggle</p>
								</div>
								<div class="form-check form-switch">
									<input class="form-check-input" type="checkbox" id="edit-ticker-status" name="status">
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
					<form action="admin-ticker.php" method="POST">
						<div class="modal-body text-center">
							<!-- Icon -->
							<span class="delete-icon">
								<i class="ti ti-trash-x"></i>
							</span>
							<!-- Title -->
							<h4>Confirm Deletion</h4>
							<!-- Description -->
							<p>You want to delete <strong id="delete-category-name"></strong>, this can't be undone once you delete.</p>

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
	<script src="assets/js/admin-custom.js" type="094c2cc781cee01c60adaad3-text/javascript"></script>

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
		document.addEventListener('DOMContentLoaded', function() {
			// Edit button click - populate edit modal
			$(document).on('click', '.tmc-ticker-edit-btn', function() {
				var tickerId = $(this).data('id');
				var tickerName = $(this).data('name');
				var tickerStatus = $(this).data('status');

				$('#edit-ticker-id').val(tickerId);
				$('#edit-ticker-name').val(tickerName);
				$('#edit-ticker-status').prop('checked', tickerStatus == '1');
			});

			// Delete button click - populate delete modal
			$(document).on('click', '.tmc-ticker-delete-btn', function() {
				var tickerId = $(this).data('id');
				var tickerName = $(this).data('name');

				$('#delete-category-id').val(tickerId);
				$('#delete-category-name').text(tickerName);
			});
		});
	</script>

</html>