<?php
session_start();
require_once('db/config.php');

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
	<title>Testinominal - Continuity Care</title>

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

</head>

<body>

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
						<h3 class="page-title mb-1">Students List</h3>
						<nav>
							<ol class="breadcrumb mb-0">
								<li class="breadcrumb-item">
									<a href="index.html">Dashboard</a>
								</li>
								<li class="breadcrumb-item">
									Students
								</li>
								<li class="breadcrumb-item active" aria-current="page">All Students</li>
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
									<a href="javascript:void(0);" class="dropdown-item rounded-1"><i class="ti ti-file-type-pdf me-2"></i>Export as PDF</a>
								</li>
								<li>
									<a href="javascript:void(0);" class="dropdown-item rounded-1"><i class="ti ti-file-type-xls me-2"></i>Export as Excel </a>
								</li>
							</ul>
						</div>
						<div class="mb-2">
							<a href="add-student.html" class="btn btn-primary d-flex align-items-center"><i class="ti ti-square-rounded-plus me-2"></i>Add Student</a>
						</div>
					</div>
				</div>
				<!-- /Page Header -->

				<!-- Students List -->
				<div class="card">
					<div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
						<h4 class="mb-3">Students List</h4>
						<div class="d-flex align-items-center flex-wrap">

							<div class="dropdown mb-3 me-2">
								<a href="javascript:void(0);" class="btn btn-outline-light bg-white dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="ti ti-filter me-2"></i>Filter</a>
								<div class="dropdown-menu drop-width">
									<form action="students.html">
										<div class="d-flex align-items-center border-bottom p-3">
											<h4>Filter</h4>
										</div>
										<div class="p-3 pb-0 border-bottom">
											<div class="row">
												<div class="col-md-6">
													<div class="mb-3">
														<label class="form-label">Class</label>
														<select class="select">
															<option>Select</option>
															<option>I</option>
															<option>II</option>
															<option>III</option>
														</select>
													</div>
												</div>
												<div class="col-md-6">
													<div class="mb-3">
														<label class="form-label">Section</label>
														<select class="select">
															<option>Select</option>
															<option>A</option>
															<option>B</option>
															<option>C</option>
														</select>
													</div>
												</div>
												<div class="col-md-12">
													<div class="mb-3">
														<label class="form-label">Name</label>
														<select class="select">
															<option>Select</option>
															<option>Janet</option>
															<option>Joann</option>
															<option>Kathleen</option>
														</select>
													</div>
												</div>
												<div class="col-md-6">
													<div class="mb-3">
														<label class="form-label">Gender</label>
														<select class="select">
															<option>Select</option>
															<option>Male</option>
															<option>Female</option>
														</select>
													</div>
												</div>
												<div class="col-md-6">
													<div class="mb-3">
														<label class="form-label">Status</label>
														<select class="select">
															<option>Select</option>
															<option>Active</option>
															<option>Inactive</option>
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="p-3 d-flex align-items-center justify-content-end">
											<a href="#" class="btn btn-light me-3">Reset</a>
											<button type="submit" class="btn btn-primary">Apply</button>
										</div>
									</form>
								</div>
							</div>


						</div>
					</div>
					<div class="card-body p-0 py-3">

						<!-- Student List -->
						<div class="custom-datatable-filter table-responsive">
							<table class="table datatable">
								<thead class="thead-light">
									<tr>
										<th class="no-sort">
											<div class="form-check form-check-md">
												<input class="form-check-input" type="checkbox" id="select-all">
											</div>
										</th>
										<th>Admission No</th>
										<th>Roll No</th>
										<th>Name</th>
										<th>Class </th>
										<th>Section</th>
										<th>Gender</th>
										<th>Status</th>
										<th>Start Date </th>
										<th>End Date </th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<div class="form-check form-check-md">
												<input class="form-check-input" type="checkbox">
											</div>
										</td>
										<td><a href="student-details.html" class="link-primary">AD9892424</a></td>
										<td>35003</td>
										<td>
											<div class="d-flex align-items-center">
												<a href="student-details.html" class="avatar avatar-md"><img src="assets/img/students/student-11.jpg" class="img-fluid rounded-circle" alt="img"></a>
												<div class="ms-2">
													<p class="text-dark mb-0"><a href="student-details.html">Veronica</a></p>
												</div>
											</div>
										</td>
										<td>IX</td>
										<td>A</td>
										<td>Female</td>
										<td>
											<span class="badge badge-soft-success d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Active</span>
										</td>
										<td>15 Dec 2024</td>
										<td>27 Dec 2009</td>
										<td>
											<div class="d-flex align-items-center">
												<a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle  p-0 me-2"><i class="ti ti-brand-hipchat"></i></a>
												<a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle  p-0 me-2"><i class="ti ti-phone"></i></a>
												<a href="#" class="btn btn-outline-light bg-white btn-icon d-flex align-items-center justify-content-center rounded-circle p-0 me-3"><i class="ti ti-mail"></i></a>
												<a href="#" data-bs-toggle="modal" data-bs-target="#add_fees_collect" class="btn btn-light fs-12 fw-semibold me-3">Collect Fees</a>
												<div class="dropdown">
													<a href="#" class="btn btn-white btn-icon btn-sm d-flex align-items-center justify-content-center rounded-circle p-0" data-bs-toggle="dropdown" aria-expanded="false">
														<i class="ti ti-dots-vertical fs-14"></i>
													</a>
													<ul class="dropdown-menu dropdown-menu-right p-3">
														<li>
															<a class="dropdown-item rounded-1" href="student-details.html"><i class="ti ti-menu me-2"></i>View Student</a>
														</li>
														<li>
															<a class="dropdown-item rounded-1" href="edit-student.html"><i class="ti ti-edit-circle me-2"></i>Edit</a>
														</li>
														<li>
															<a class="dropdown-item rounded-1" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#login_detail"><i class="ti ti-lock me-2"></i>Login Details</a>
														</li>
														<li>
															<a class="dropdown-item rounded-1" href="javascript:void(0);"><i class="ti ti-toggle-right me-2"></i>Disable</a>
														</li>
														<li>
															<a class="dropdown-item rounded-1" href="student-promotion.html"><i class="ti ti-arrow-ramp-right-2 me-2"></i>Promote
																Student</a>
														</li>
														<li>
															<a class="dropdown-item rounded-1" href="#" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash-x me-2"></i>Delete</a>
														</li>
													</ul>
												</div>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<!-- /Student List -->
					</div>
				</div>
				<!-- /Students List -->

			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- Login Details -->
		<div class="modal fade" id="login_detail">
			<div class="modal-dialog modal-dialog-centered  modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Login Details</h4>
						<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<div class="modal-body">
						<div class="student-detail-info">
							<span class="student-img"><img src="assets/img/students/student-01.jpg" alt="Img"></span>
							<div class="name-info">
								<h6>Janet <span>III, A</span></h6>
							</div>
						</div>
						<div class="table-responsive custom-table no-datatable_length">
							<table class="table datanew">
								<thead class="thead-light">
									<tr>
										<th>User Type</th>
										<th>User Name</th>
										<th>Password </th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Parent</td>
										<td>parent53</td>
										<td>parent@53</td>
									</tr>
									<tr>
										<td>Student</td>
										<td>student20</td>
										<td>stdt@53</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer">
						<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
					</div>
				</div>
			</div>
		</div>
		<!-- /Login Details -->

		<!-- Delete Modal -->
		<div class="modal fade" id="delete-modal">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<form action="students.html">
						<div class="modal-body text-center">
							<span class="delete-icon">
								<i class="ti ti-trash-x"></i>
							</span>
							<h4>Confirm Deletion</h4>
							<p>You want to delete all the marked items, this cant be undone once you delete.</p>
							<div class="d-flex justify-content-center">
								<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-danger">Yes, Delete</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /Delete Modal -->

		<!-- Add Fees Collect -->
		<div class="modal fade" id="add_fees_collect">
			<div class="modal-dialog modal-dialog-centered  modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<div class="d-flex align-items-center">
							<h4 class="modal-title">Collect Fees</h4>
							<span class="badge badge-sm bg-primary ms-2">AD124556</span>
						</div>
						<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
					<form action="students.html">
						<div class="modal-body">
							<div class="bg-light-300 p-3 pb-0 rounded mb-4">
								<div class="row align-items-center">
									<div class="col-lg-3 col-md-6">
										<div class="d-flex align-items-center mb-3">
											<a href="student-details.html" class="avatar avatar-md me-2">
												<img src="assets/img/students/student-01.jpg" alt="img">
											</a>
											<a href="student-details.html" class="d-flex flex-column"><span class="text-dark">Janet</span>III, A</a>
										</div>
									</div>
									<div class="col-lg-3 col-md-6">
										<div class="mb-3">
											<span class="fs-12 mb-1">Total Outstanding</span>
											<p class="text-dark">2000</p>
										</div>
									</div>
									<div class="col-lg-3 col-md-6">
										<div class="mb-3">
											<span class="fs-12 mb-1">Last Date</span>
											<p class="text-dark">25 May 2024</p>
										</div>
									</div>
									<div class="col-lg-3 col-md-6">
										<div class="mb-3">
											<span class="badge badge-soft-danger"><i class="ti ti-circle-filled me-2"></i>Unpaid</span>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-6">
									<div class="mb-3">
										<label class="form-label">Fees Group</label>
										<select class="select">
											<option>Select</option>
											<option>Class 1 General</option>
											<option>Monthly Fees</option>
											<option>Admission-Fees</option>
											<option>Class 1- I Installment</option>
										</select>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="mb-3">
										<label class="form-label">Fees Type</label>
										<select class="select">
											<option>Select</option>
											<option>Tuition Fees</option>
											<option>Monthly Fees</option>
											<option>Admission Fees</option>
											<option>Bus Fees</option>
										</select>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="mb-3">
										<label class="form-label">Amount</label>
										<input type="text" class="form-control" placeholder="Enter Amout">
									</div>
								</div>
								<div class="col-lg-6">
									<div class="mb-3">
										<label class="form-label">Collection Date</label>
										<div class="date-pic">
											<input type="text" class="form-control datetimepicker" placeholder="Select">
											<span class="cal-icon"><i class="ti ti-calendar"></i></span>
										</div>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="mb-3">
										<label class="form-label">Payment Type</label>
										<select class="select">
											<option>Select</option>
											<option>Paytm</option>
											<option>Cash On Delivery</option>
										</select>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="mb-3">
										<label class="form-label">Payment Reference No</label>
										<input type="text" class="form-control" placeholder="Enter Payment Reference No">
									</div>
								</div>
								<div class="col-lg-12">
									<div class="modal-satus-toggle d-flex align-items-center justify-content-between mb-3">
										<div class="status-title">
											<h5>Status</h5>
											<p>Change the Status by toggle </p>
										</div>
										<div class="status-toggle modal-status">
											<input type="checkbox" id="user1" class="check">
											<label for="user1" class="checktoggle"> </label>
										</div>
									</div>
								</div>
								<div class="col-lg-12">
									<div class="mb-0">
										<label class="form-label">Notes</label>
										<textarea rows="4" class="form-control" placeholder="Add Notes"></textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
							<button type="submit" class="btn btn-primary">Pay Fees</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- Add Fees Collect -->

	</div>
	<!-- /Main Wrapper -->

	<!-- jQuery -->
	<script src="assets/js/jquery-3.7.1.min.js" type="7dc4ae2289fc87b2b252493e-text/javascript"></script>

	<!-- Bootstrap Core JS -->
	<script src="assets/js/bootstrap.bundle.min.js" type="7dc4ae2289fc87b2b252493e-text/javascript"></script>

	<!-- Daterangepikcer JS -->
	<script src="assets/js/moment.js" type="7dc4ae2289fc87b2b252493e-text/javascript"></script>
	<script src="assets/plugins/daterangepicker/daterangepicker.js" type="7dc4ae2289fc87b2b252493e-text/javascript"></script>

	<!-- Feather Icon JS -->
	<script src="assets/js/feather.min.js" type="7dc4ae2289fc87b2b252493e-text/javascript"></script>

	<!-- Slimscroll JS -->
	<script src="assets/js/jquery.slimscroll.min.js" type="7dc4ae2289fc87b2b252493e-text/javascript"></script>

	<!-- Datatable JS -->
	<script src="assets/js/jquery.dataTables.min.js" type="7dc4ae2289fc87b2b252493e-text/javascript"></script>
	<script src="assets/js/dataTables.bootstrap5.min.js" type="7dc4ae2289fc87b2b252493e-text/javascript"></script>

	<!-- Select2 JS -->
	<script src="assets/plugins/select2/js/select2.min.js" type="7dc4ae2289fc87b2b252493e-text/javascript"></script>

	<!-- Datetimepicker JS -->
	<script src="assets/js/bootstrap-datetimepicker.min.js" type="7dc4ae2289fc87b2b252493e-text/javascript"></script>

	<!-- Custom JS -->
	<script src="assets/js/script.js" type="7dc4ae2289fc87b2b252493e-text/javascript"></script>

	<script src="../../cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="7dc4ae2289fc87b2b252493e-|49" defer=""></script>
</body>

</html>