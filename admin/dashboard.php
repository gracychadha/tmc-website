<?php
session_start();
include("db/config.php");

// Check if adminId exists in the session
if (!isset($_SESSION['adminId'])) {
	header("location: index.php");
	exit();
}

$decodedAdminId = base64_decode($_SESSION['adminId']);
$userName = $_SESSION['userName'];

// --- Count Queries ---
$countQueries = [
	'total_contact'    => 'contact',
	'total_services'   => 'services',
	'total_blogs'      => 'blog',
	'total_team'       => 'team_members',
	'total_testimonials' => 'testimonials',
	'total_partners'   => 'partners',
];

$counts = [];
foreach ($countQueries as $key => $table) {
	$result = $db->query("SELECT COUNT(*) as cnt FROM `$table`");
	$row = $result->fetch_assoc();
	$counts[$key] = $row['cnt'] ?? 0;
}
if (isset($_POST['action']) && $_POST['action'] === 'add_event') {

	$title = trim($_POST['title'] ?? '');
	$startDate = trim($_POST['start_date'] ?? '');
	$endDate = trim($_POST['end_date'] ?? '');
	$eventType = $_POST['event_type'] ?? 'event';
	$color = $_POST['color'] ?? '#007bff';
	$allDay = isset($_POST['all_day']) ? (int) $_POST['all_day'] : 1;
	$description = trim($_POST['description'] ?? '');

	// Validation
	if ($title === '' || $startDate === '') {
		echo json_encode([
			'status' => false,
			'message' => 'Event title and start date are required.'
		]);
		exit;
	}

	// Validate event type
	$allowedTypes = ['event', 'reminder', 'holiday'];

	if (!in_array($eventType, $allowedTypes, true)) {
		echo json_encode([
			'status' => false,
			'message' => 'Invalid event type.'
		]);
		exit;
	}

	// End date
	$endDateValue = $endDate !== '' ? $endDate : null;

	$sql = "INSERT INTO events 
            (
                title,
                start_datetime,
                end_datetime,
                event_type,
                color,
                all_day,
                description
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)";

	$stmt = $db->prepare($sql);

	if (!$stmt) {
		echo json_encode([
			'status' => false,
			'message' => 'Database error: ' . $db->error
		]);
		exit;
	}

	$stmt->bind_param(
		"sssssis",
		$title,
		$startDate,
		$endDateValue,
		$eventType,
		$color,
		$allDay,
		$description
	);

	if ($stmt->execute()) {

		echo json_encode([
			'status' => true,
			'message' => 'Event added successfully.',
			'id' => $stmt->insert_id
		]);
	} else {

		echo json_encode([
			'status' => false,
			'message' => 'Failed to add event.'
		]);
	}

	$stmt->close();
	exit;
}
// Favicon
$faviconPath = '';
$stmt = $db->prepare("SELECT favicon FROM system_setting LIMIT 1");
$stmt->execute();
$stmt->bind_result($favicon);
if ($stmt->fetch()) {
	$faviconPath = "logo/" . $favicon;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<meta name="robots" content="noindex, nofollow">
	<title>Dashboard - Tee Mac Corporation</title>
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">

	<script src="assets/js/theme-script.js" type="feb024e4d970c7c806ef5348-text/javascript"></script>
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">
	<link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">
	<link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">
	<link rel="stylesheet" href="assets/plugins/fullcalendar/calendar.js">
	<link rel="stylesheet" href="assets/plugins/fullcalendar/calendar-data.js">
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">
	<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
	<link rel="stylesheet" href="assets/css/style.css">
	<link rel="stylesheet" href="assets/css/dashboard-custom.css">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

				<!-- Page Header -->
				<div class="d-md-flex d-block align-items-center justify-content-between mb-3">
					<div class="my-auto mb-2">
						<h3 class="page-title mb-1">Admin Dashboard</h3>
						<nav>
							<ol class="breadcrumb mb-0">
								<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
								<li class="breadcrumb-item active" aria-current="page">Admin Dashboard</li>
							</ol>
						</nav>
					</div>
				</div>

				<!-- Welcome Banner -->
				<div class="row mb-4">
					<div class="col-md-12">
						<div class="card tmc-dash-welcome">
							<div class="card-body">
								<div class="d-flex align-items-xl-center justify-content-xl-between flex-xl-row flex-column">
									<div class="tmc-dash-welcome-content">
										<p class="tmc-dash-welcome-greeting">Welcome back,</p>
										<h1 class="tmc-dash-welcome-name">
											<span><?php echo htmlspecialchars($userName); ?></span>
										</h1>
										<p class="tmc-dash-welcome-msg">Manage your events, services, and team from here.</p>
									</div>
									<div>
										<span class="tmc-dash-welcome-badge">
											<i class="fa-solid fa-star"></i> TMC Admin Panel
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Section Title -->
				<h4 class="tmc-dash-section-title">Overview</h4>

				<!-- Stats Cards -->
				<div class="tmc-dash-stats-grid">

					<a href="admin-contact.php" class="text-decoration-none">
						<div class="card tmc-dash-stat-card tmc-dash-stat-gold">
							<div class="card-body tmc-dash-stat-body">
								<div class="tmc-dash-stat-info">
									<p class="tmc-dash-stat-label">Contact Inquiries</p>
									<h2 class="tmc-dash-stat-count"><?php echo $counts['total_contact']; ?></h2>
								</div>
								<div class="tmc-dash-stat-icon">
									<i class="fa-solid fa-envelope-open-text"></i>
								</div>
							</div>
						</div>
					</a>

					<!-- <a href="admin-service.php" class="text-decoration-none">
						<div class="card tmc-dash-stat-card tmc-dash-stat-teal">
							<div class="card-body tmc-dash-stat-body">
								<div class="tmc-dash-stat-info">
									<p class="tmc-dash-stat-label">Services</p>
									<h2 class="tmc-dash-stat-count"><?php echo $counts['total_services']; ?></h2>
								</div>
								<div class="tmc-dash-stat-icon">
									<i class="fa-solid fa-handshake"></i>
								</div>
							</div>
						</div>
					</a> -->

					<a href="admin-blog.php" class="text-decoration-none">
						<div class="card tmc-dash-stat-card tmc-dash-stat-blue">
							<div class="card-body tmc-dash-stat-body">
								<div class="tmc-dash-stat-info">
									<p class="tmc-dash-stat-label">Blog Posts</p>
									<h2 class="tmc-dash-stat-count"><?php echo $counts['total_blogs']; ?></h2>
								</div>
								<div class="tmc-dash-stat-icon">
									<i class="fa-solid fa-pen-nib"></i>
								</div>
							</div>
						</div>
					</a>

					<!-- <a href="admin-team.php" class="text-decoration-none">
						<div class="card tmc-dash-stat-card tmc-dash-stat-orange">
							<div class="card-body tmc-dash-stat-body">
								<div class="tmc-dash-stat-info">
									<p class="tmc-dash-stat-label">Team Members</p>
									<h2 class="tmc-dash-stat-count"><?php echo $counts['total_team']; ?></h2>
								</div>
								<div class="tmc-dash-stat-icon">
									<i class="fa-solid fa-people-group"></i>
								</div>
							</div>
						</div>
					</a> -->

					<a href="admin-testimonial.php" class="text-decoration-none">
						<div class="card tmc-dash-stat-card tmc-dash-stat-purple">
							<div class="card-body tmc-dash-stat-body">
								<div class="tmc-dash-stat-info">
									<p class="tmc-dash-stat-label">Testimonials</p>
									<h2 class="tmc-dash-stat-count"><?php echo $counts['total_testimonials']; ?></h2>
								</div>
								<div class="tmc-dash-stat-icon">
									<i class="fa-solid fa-quote-left"></i>
								</div>
							</div>
						</div>
					</a>

					<!-- <a href="admin-partner.php" class="text-decoration-none">
						<div class="card tmc-dash-stat-card tmc-dash-stat-green">
							<div class="card-body tmc-dash-stat-body">
								<div class="tmc-dash-stat-info">
									<p class="tmc-dash-stat-label">Partners</p>
									<h2 class="tmc-dash-stat-count"><?php echo $counts['total_partners']; ?></h2>
								</div>
								<div class="tmc-dash-stat-icon">
									<i class="fa-solid fa-building"></i>
								</div>
							</div>
						</div>
					</a> -->

				</div>

				<!-- Calendar Section -->
				<div class="row">
					<div class="col-lg-12 col-md-12">
						<div class="card tmc-dash-calendar-card">
							<div class="card-header d-flex align-items-center justify-content-between">
								<h5 class="mb-0"><i class="fa-regular fa-calendar me-2"></i>Calendar</h5>
								<button type="button" class="btn btn-sm btn-primary" id="addEventBtn">
									<i class="fa-solid fa-plus me-1"></i>Add Event
								</button>
							</div>
							<div class="card-body">
								<div id="calendar"></div>
							</div>
						</div>
					</div>
				</div>

			</div>

			<!-- Footer -->
			<footer class="footer">
				<div class="tmc-dash-footer">
					<?php require_once('copyright.php'); ?>
				</div>
			</footer>

		</div>
	</div>

	<script data-cfasync="false" src="assets/js/email-decode.min.js"></script>
	<script src="assets/js/jquery-3.7.1.min.js" type="feb024e4d970c7c806ef5348-text/javascript"></script>
	<script src="assets/js/bootstrap.bundle.min.js" type="feb024e4d970c7c806ef5348-text/javascript"></script>
	<script src="assets/js/moment.js" type="feb024e4d970c7c806ef5348-text/javascript"></script>
	<script src="assets/plugins/daterangepicker/daterangepicker.js" type="feb024e4d970c7c806ef5348-text/javascript"></script>
	<script src="assets/js/feather.min.js" type="feb024e4d970c7c806ef5348-text/javascript"></script>
	<script src="assets/js/jquery.slimscroll.min.js" type="feb024e4d970c7c806ef5348-text/javascript"></script>
	<script src="assets/js/jquery.dataTables.min.js" type="feb024e4d970c7c806ef5348-text/javascript"></script>
	<script src="assets/js/dataTables.bootstrap5.min.js" type="feb024e4d970c7c806ef5348-text/javascript"></script>
	<script src="assets/plugins/select2/js/select2.min.js" type="feb024e4d970c7c806ef5348-text/javascript"></script>
	<script src="assets/plugins/fullcalendar/calendar.js" type="feb024e4d970c7c806ef5348-text/javascript"></script>
	<script src="assets/plugins/fullcalendar/calendar-data.js" type="feb024e4d970c7c806ef5348-text/javascript"></script>
	<script src="assets/js/bootstrap-datetimepicker.min.js" type="feb024e4d970c7c806ef5348-text/javascript"></script>
	<script src="assets/js/script.js" type="feb024e4d970c7c806ef5348-text/javascript"></script>
	<script src="assets/js/rocket-loader.min.js" data-cf-settings="feb024e4d970c7c806ef5348-|49" defer=""></script>

	<!-- Add Event Modal -->
	<div class="modal fade" id="addEventModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><i class="fa-solid fa-calendar-plus me-2"></i>Add New Event</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<div class="mb-3">
						<label class="form-label">Event Title <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="eventTitle" placeholder="e.g. Team Meeting" required>
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label">Start Date <span class="text-danger">*</span></label>
							<input type="date" class="form-control" id="eventStartDate" required>
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">End Date</label>
							<input type="date" class="form-control" id="eventEndDate">
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label">Event Type</label>
							<select class="form-select" id="eventType">
								<option value="event">Event</option>
								<option value="reminder">Reminder</option>
								<option value="holiday">Holiday</option>
							</select>
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">Color</label>
							<select class="form-select" id="eventColor">
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
							<input class="form-check-input" type="checkbox" id="eventAllDay" checked>
							<label class="form-check-label" for="eventAllDay">All Day Event</label>
						</div>
					</div>
					<div class="mb-3">
						<label class="form-label">Description</label>
						<textarea class="form-control" id="eventDescription" rows="3" placeholder="Optional description..."></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-primary" id="saveEventBtn">
						<i class="fa-solid fa-check me-1"></i>Save Event
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- View Event Modal -->
	<div class="modal fade" id="viewEventModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="viewEventTitle"></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<p><strong>Date:</strong> <span id="viewEventDate"></span></p>
					<p><strong>Type:</strong> <span id="viewEventType"></span></p>
					<p><strong>Description:</strong> <span id="viewEventDesc"></span></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" id="deleteEventBtn">
						<i class="fa-solid fa-trash me-1"></i>Delete
					</button>
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

</body>

</html>