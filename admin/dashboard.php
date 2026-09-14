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

// --- Count Queries (only sidebar-active items) ---
$countQueries = [
	'total_contact'       => 'contact',
	'total_subscribers'   => 'subscribers',
	'total_calendar'      => 'calendar_events',
	'total_blogs'         => 'blog',
	'total_faqs'          => 'faqs',
	'total_gallery'       => 'gallery',
	'total_partners'      => 'partners',
	'total_team'          => 'team_members',
	'total_testimonials'  => 'testimonials',
	'total_services'      => 'services',
];

$counts = [];
foreach ($countQueries as $key => $table) {
	$result = $db->query("SELECT COUNT(*) as cnt FROM `$table`");
	$row = $result->fetch_assoc();
	$counts[$key] = $row['cnt'] ?? 0;
}

// --- Services Stats ---
$servicesResult = $db->query("SELECT status, COUNT(*) as cnt FROM services GROUP BY status");
$servicesActive = 0;
$servicesInactive = 0;
if ($servicesResult) {
	while ($sRow = $servicesResult->fetch_assoc()) {
		if ($sRow['status'] == 1) $servicesActive = $sRow['cnt'];
		else $servicesInactive = $sRow['cnt'];
	}
}

// --- Recent Services ---
$recentServices = $db->query("SELECT idservices, title, status, date FROM services ORDER BY idservices DESC LIMIT 5");

// --- Blog by Category ---
$blogByCategory = $db->query("SELECT bc.category_name, COUNT(b.idblog) as cnt FROM blog b LEFT JOIN blog_category bc ON b.category_id = bc.idblog_category GROUP BY bc.category_name ORDER BY cnt DESC LIMIT 6");
$blogLabels = [];
$blogData = [];
if ($blogByCategory) {
	while ($bRow = $blogByCategory->fetch_assoc()) {
		$blogLabels[] = $bRow['category_name'] ?? 'Event';
		$blogData[] = (int)$bRow['cnt'];
	}
}

// --- Monthly Contact Trend (last 6 months) ---
$monthlyContacts = $db->query("SELECT DATE_FORMAT(created_at, '%b %Y') as month_label, DATE_FORMAT(created_at, '%Y-%m') as month_sort, COUNT(*) as cnt FROM contact WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) GROUP BY month_sort, month_label ORDER BY month_sort ASC");
$contactLabels = [];
$contactData = [];
if ($monthlyContacts) {
	while ($mRow = $monthlyContacts->fetch_assoc()) {
		$contactLabels[] = $mRow['month_label'];
		$contactData[] = (int)$mRow['cnt'];
	}
}

// --- Monthly Subscribers Trend (last 6 months) ---
$monthlySubscribers = $db->query("SELECT DATE_FORMAT(created_at, '%b %Y') as month_label, DATE_FORMAT(created_at, '%Y-%m') as month_sort, COUNT(*) as cnt FROM subscribers WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) GROUP BY month_sort, month_label ORDER BY month_sort ASC");
$subscriberLabels = [];
$subscriberData = [];
if ($monthlySubscribers) {
	while ($msRow = $monthlySubscribers->fetch_assoc()) {
		$subscriberLabels[] = $msRow['month_label'];
		$subscriberData[] = (int)$msRow['cnt'];
	}
}

// --- Monthly Blog Trend (last 6 months) ---
$blogTrendLabels = [];
$blogTrendData = [];
$blogDateCheck = $db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'blog' AND COLUMN_NAME = 'created_at'");
if ($blogDateCheck && $blogDateCheck->num_rows > 0) {
	$monthlyBlogs = $db->query("SELECT DATE_FORMAT(created_at, '%b %Y') as month_label, DATE_FORMAT(created_at, '%Y-%m') as month_sort, COUNT(*) as cnt FROM blog WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) GROUP BY month_sort, month_label ORDER BY month_sort ASC");
} else {
	$monthlyBlogs = $db->query("SELECT DATE_FORMAT(STR_TO_DATE(date, '%b %d, %Y'), '%b %Y') as month_label, DATE_FORMAT(STR_TO_DATE(date, '%b %d, %Y'), '%Y-%m') as month_sort, COUNT(*) as cnt FROM blog WHERE STR_TO_DATE(date, '%b %d, %Y') >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) GROUP BY month_sort, month_label ORDER BY month_sort ASC");
}
if ($monthlyBlogs) {
	while ($mbRow = $monthlyBlogs->fetch_assoc()) {
		if ($mbRow['month_label'] && $mbRow['month_sort']) {
			$blogTrendLabels[] = $mbRow['month_label'];
			$blogTrendData[] = (int)$mbRow['cnt'];
		}
	}
}

// --- Services by Status ---
$servicesByStatus = $db->query("SELECT CASE WHEN status = 1 THEN 'Active' ELSE 'Inactive' END as status_label, COUNT(*) as cnt FROM services GROUP BY status");
$serviceStatusLabels = [];
$serviceStatusData = [];
if ($servicesByStatus) {
	while ($ssRow = $servicesByStatus->fetch_assoc()) {
		$serviceStatusLabels[] = $ssRow['status_label'];
		$serviceStatusData[] = $ssRow['cnt'];
	}
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
	<script src="assets/plugins/fullcalendar/calendar.js"></script>
	<script src="assets/plugins/fullcalendar/calendar-data.js"></script>
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">
	<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
	<link rel="stylesheet" href="assets/css/style.css">
	<link rel="stylesheet" href="assets/css/dashboard-custom.css">
	<link rel="stylesheet" href="assets/css/admin-custom.css">
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
										<p class="tmc-dash-welcome-greeting">Welcome to Tee Mac Corporation!!</p>
										<h1 class="tmc-dash-welcome-name">
											<span><?php echo htmlspecialchars($userName); ?></span>
										</h1>
										<p class="tmc-dash-welcome-msg">Manage your events, services, and team from here.</p>
									</div>
									<div class="tmc-dash-welcome-right">
										<span class="tmc-dash-welcome-badge">
											<i class="fa-solid fa-shield-halved"></i> TMC Admin Panel
										</span>
										<div class="tmc-dash-welcome-datetime" id="welcomeDateTime"></div>
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

					<a href="admin-subscriber.php" class="text-decoration-none">
						<div class="card tmc-dash-stat-card tmc-dash-stat-teal">
							<div class="card-body tmc-dash-stat-body">
								<div class="tmc-dash-stat-info">
									<p class="tmc-dash-stat-label">Subscribers</p>
									<h2 class="tmc-dash-stat-count"><?php echo $counts['total_subscribers']; ?></h2>
								</div>
								<div class="tmc-dash-stat-icon">
									<i class="fa-solid fa-users"></i>
								</div>
							</div>
						</div>
					</a>

					<a href="admin-calendar.php" class="text-decoration-none">
						<div class="card tmc-dash-stat-card tmc-dash-stat-blue">
							<div class="card-body tmc-dash-stat-body">
								<div class="tmc-dash-stat-info">
									<p class="tmc-dash-stat-label">Calendar Events</p>
									<h2 class="tmc-dash-stat-count"><?php echo $counts['total_calendar']; ?></h2>
								</div>
								<div class="tmc-dash-stat-icon">
									<i class="fa-solid fa-calendar-days"></i>
								</div>
							</div>
						</div>
					</a>

					<a href="admin-blog.php" class="text-decoration-none">
						<div class="card tmc-dash-stat-card tmc-dash-stat-indigo">
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

					<a href="admin-faqs.php" class="text-decoration-none">
						<div class="card tmc-dash-stat-card tmc-dash-stat-orange">
							<div class="card-body tmc-dash-stat-body">
								<div class="tmc-dash-stat-info">
									<p class="tmc-dash-stat-label">FAQs</p>
									<h2 class="tmc-dash-stat-count"><?php echo $counts['total_faqs']; ?></h2>
								</div>
								<div class="tmc-dash-stat-icon">
									<i class="fa-solid fa-circle-question"></i>
								</div>
							</div>
						</div>
					</a>

					<a href="admin-gallery.php" class="text-decoration-none">
						<div class="card tmc-dash-stat-card tmc-dash-stat-pink">
							<div class="card-body tmc-dash-stat-body">
								<div class="tmc-dash-stat-info">
									<p class="tmc-dash-stat-label">Gallery</p>
									<h2 class="tmc-dash-stat-count"><?php echo $counts['total_gallery']; ?></h2>
								</div>
								<div class="tmc-dash-stat-icon">
									<i class="fa-solid fa-images"></i>
								</div>
							</div>
						</div>
					</a>

					<a href="admin-partner.php" class="text-decoration-none">
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
					</a>

					<a href="admin-team.php" class="text-decoration-none">
						<div class="card tmc-dash-stat-card tmc-dash-stat-cyan">
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
					</a>

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

					<a href="admin-service.php" class="text-decoration-none">
						<div class="card tmc-dash-stat-card tmc-dash-stat-red">
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
					</a>

				</div>

				<!-- Charts Section -->
				<h4 class="tmc-dash-section-title">Analytics</h4>
				<div class="row mb-4">
					<div class="col-lg-8 col-md-12">
						<div class="card tmc-dash-chart-card">
							<div class="card-header d-flex align-items-center justify-content-between">
								<h5 class="mb-0"><i class="fa-solid fa-chart-line me-2"></i>Contacts & Subscribers Trend (Last 6 Months)</h5>
							</div>
							<div class="card-body">
								<canvas id="trendChart" height="100"></canvas>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-12">
						<div class="card tmc-dash-chart-card">
							<div class="card-header d-flex align-items-center justify-content-between">
								<h5 class="mb-0"><i class="fa-solid fa-chart-pie me-2"></i>Content Distribution</h5>
							</div>
							<div class="card-body d-flex align-items-center justify-content-center">
								<canvas id="distributionChart" height="200"></canvas>
							</div>
						</div>
					</div>
				</div>

				<div class="row mb-4">
					<div class="col-lg-6 col-md-12">
						<div class="card tmc-dash-chart-card">
							<div class="card-header d-flex align-items-center justify-content-between">
								<h5 class="mb-0"><i class="fa-solid fa-chart-bar me-2"></i>Blog Posts by Category</h5>
							</div>
							<div class="card-body">
								<canvas id="blogCategoryChart" height="120"></canvas>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-12">
						<div class="card tmc-dash-chart-card">
							<div class="card-header d-flex align-items-center justify-content-between">
								<h5 class="mb-0"><i class="fa-solid fa-chart-bar me-2"></i>Services Status</h5>
							</div>
							<div class="card-body d-flex align-items-center justify-content-center">
								<canvas id="servicesStatusChart" height="120"></canvas>
							</div>
						</div>
					</div>
				</div>

				<!-- Services Management Section -->
				<h4 class="tmc-dash-section-title">Services Management</h4>
				<div class="row mb-4">
					<div class="col-lg-4 col-md-6 mb-3">
						<div class="card tmc-dash-service-summary">
							<div class="card-body text-center">
								<div class="tmc-dash-service-icon tmc-dash-service-active">
									<i class="fa-solid fa-check-circle"></i>
								</div>
								<h3 class="tmc-dash-service-count"><?php echo $servicesActive; ?></h3>
								<p class="tmc-dash-service-label">Active Services</p>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-6 mb-3">
						<div class="card tmc-dash-service-summary">
							<div class="card-body text-center">
								<div class="tmc-dash-service-icon tmc-dash-service-inactive">
									<i class="fa-solid fa-pause-circle"></i>
								</div>
								<h3 class="tmc-dash-service-count"><?php echo $servicesInactive; ?></h3>
								<p class="tmc-dash-service-label">Inactive Services</p>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-6 mb-3">
						<div class="card tmc-dash-service-summary">
							<div class="card-body text-center">
								<div class="tmc-dash-service-icon tmc-dash-service-total">
									<i class="fa-solid fa-layer-group"></i>
								</div>
								<h3 class="tmc-dash-service-count"><?php echo $counts['total_services']; ?></h3>
								<p class="tmc-dash-service-label">Total Services</p>
							</div>
						</div>
					</div>
				</div>

				<!-- Recent Services Table -->
				<div class="row mb-4">
					<div class="col-lg-12">
						<div class="card tmc-dash-chart-card">
							<div class="card-header d-flex align-items-center justify-content-between">
								<h5 class="mb-0"><i class="fa-solid fa-list-check me-2"></i>Recent Services</h5>
								<a href="admin-service.php" class="btn btn-sm btn-primary">View All</a>
							</div>
							<div class="card-body">
								<?php if ($recentServices && $recentServices->num_rows > 0): ?>
									<div class="table-responsive">
										<table class="table table-hover tmc-dash-table">
											<thead>
												<tr>
													<th>#</th>
													<th>Service Title</th>
													<th>Date</th>
													<th>Status</th>
												</tr>
											</thead>
											<tbody>
												<?php $sNo = 1;
												while ($sRow = $recentServices->fetch_assoc()): ?>
													<tr>
														<td><?php echo $sNo++; ?></td>
														<td><?php echo htmlspecialchars($sRow['title'] ?? ''); ?></td>
														<td><?php echo htmlspecialchars($sRow['date'] ?? ''); ?></td>
														<td>
															<?php if (($sRow['status'] ?? 0) == 1): ?>
																<span class="badge bg-success">Active</span>
															<?php else: ?>
																<span class="badge bg-secondary">Inactive</span>
															<?php endif; ?>
														</td>
													</tr>
												<?php endwhile; ?>
											</tbody>
										</table>
									</div>
								<?php else: ?>
									<div class="text-center py-4 text-muted">
										<i class="fa-solid fa-inbox fa-2x mb-2"></i>
										<p>No services found. <a href="admin-service.php">Add your first service</a></p>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
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
	<script src="assets/js/admin-custom.js"></script>
	<script src="assets/js/rocket-loader.min.js" data-cf-settings="feb024e4d970c7c806ef5348-|49" defer=""></script>

	<!-- Live Clock -->
	<script>
		function updateWelcomeClock() {
			var now = new Date();
			var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
			var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
			var day = days[now.getDay()];
			var date = now.getDate();
			var month = months[now.getMonth()];
			var year = now.getFullYear();
			var hours = now.getHours();
			var ampm = hours >= 12 ? 'PM' : 'AM';
			hours = hours % 12;
			hours = hours ? hours : 12;
			var minutes = now.getMinutes().toString().padStart(2, '0');
			var seconds = now.getSeconds().toString().padStart(2, '0');
			var el = document.getElementById('welcomeDateTime');
			if (el) {
				el.innerHTML = '<i class="fa-regular fa-clock"></i> ' + day + ', ' + date + ' ' + month + ' ' + year + ' &mdash; ' + hours + ':' + minutes + ':' + seconds + ' ' + ampm;
			}
		}
		updateWelcomeClock();
		setInterval(updateWelcomeClock, 1000);
	</script>

	<!-- Chart.js -->
	<script src="assets/plugins/chartjs/chart.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			// --- Trend Line Chart (Contacts & Subscribers) ---
			var trendCtx = document.getElementById('trendChart');
			if (trendCtx) {
				var contactLabels = <?php echo json_encode($contactLabels); ?>;
				var subLabels = <?php echo json_encode($subscriberLabels); ?>;
				var mergedLabels = [...new Set([...contactLabels, ...subLabels])].sort();

				var contactMap = {};
				<?php foreach ($contactLabels as $i => $l): ?>
					contactMap[<?php echo json_encode($l); ?>] = <?php echo $contactData[$i] ?? 0; ?>;
				<?php endforeach; ?>

				var subMap = {};
				<?php foreach ($subscriberLabels as $i => $l): ?>
					subMap[<?php echo json_encode($l); ?>] = <?php echo $subscriberData[$i] ?? 0; ?>;
				<?php endforeach; ?>

				var contactFill = mergedLabels.map(function(l) {
					return contactMap[l] || 0;
				});
				var subFill = mergedLabels.map(function(l) {
					return subMap[l] || 0;
				});

				new Chart(trendCtx.getContext('2d'), {
					type: 'line',
					data: {
						labels: mergedLabels.length ? mergedLabels : ['No Data'],
						datasets: [{
								label: 'Contacts',
								data: contactFill.length ? contactFill : [0],
								borderColor: '#d4af37',
								backgroundColor: 'rgba(212, 175, 55, 0.1)',
								fill: true,
								tension: 0.4,
								pointRadius: 5,
								pointBackgroundColor: '#d4af37',
								borderWidth: 2
							},
							{
								label: 'Subscribers',
								data: subFill.length ? subFill : [0],
								borderColor: '#0196a3',
								backgroundColor: 'rgba(1, 150, 163, 0.1)',
								fill: true,
								tension: 0.4,
								pointRadius: 5,
								pointBackgroundColor: '#0196a3',
								borderWidth: 2
							}
						]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: {
								position: 'top',
								labels: {
									usePointStyle: true,
									padding: 20
								}
							}
						},
						scales: {
							y: {
								beginAtZero: true,
								ticks: {
									stepSize: 1
								}
							}
						}
					}
				});
			}

			// --- Doughnut Chart (Content Distribution) ---
			var distCtx = document.getElementById('distributionChart');
			if (distCtx) {
				new Chart(distCtx.getContext('2d'), {
					type: 'doughnut',
					data: {
						labels: ['Blogs', 'Testimonials', 'Gallery', 'Partners', 'Team', 'FAQs', 'Services'],
						datasets: [{
							data: [
								<?php echo (int)$counts['total_blogs']; ?>,
								<?php echo (int)$counts['total_testimonials']; ?>,
								<?php echo (int)$counts['total_gallery']; ?>,
								<?php echo (int)$counts['total_partners']; ?>,
								<?php echo (int)$counts['total_team']; ?>,
								<?php echo (int)$counts['total_faqs']; ?>,
								<?php echo (int)$counts['total_services']; ?>
							],
							backgroundColor: [
								'#2a5298', '#9d50bb', '#e91e63',
								'#43a047', '#0196a3', '#ff7300', '#d32f2f'
							],
							borderWidth: 2,
							borderColor: '#fff'
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: {
								position: 'bottom',
								labels: {
									usePointStyle: true,
									padding: 12,
									font: {
										size: 11
									}
								}
							}
						},
						cutout: '60%'
					}
				});
			}

			// --- Blog by Category Bar Chart ---
			var blogCtx = document.getElementById('blogCategoryChart');
			if (blogCtx) {
				var bLabels = <?php echo json_encode($blogLabels); ?>;
				var bData = <?php echo json_encode($blogData); ?>;
				new Chart(blogCtx.getContext('2d'), {
					type: 'bar',
					data: {
						labels: bLabels.length ? bLabels : ['No Data'],
						datasets: [{
							label: 'Blog Posts',
							data: bData.length ? bData : [0],
							backgroundColor: [
								'#2a5298', '#d4af37', '#43a047',
								'#ff7300', '#9d50bb', '#0196a3'
							],
							borderRadius: 6,
							borderSkipped: false
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: {
								display: false
							}
						},
						scales: {
							y: {
								beginAtZero: true,
								ticks: {
									stepSize: 1
								}
							}
						}
					}
				});
			}

			// --- Services Status Chart ---
			var svcCtx = document.getElementById('servicesStatusChart');
			if (svcCtx) {
				var sLabels = <?php echo json_encode($serviceStatusLabels); ?>;
				var sData = <?php echo json_encode($serviceStatusData); ?>;
				new Chart(svcCtx.getContext('2d'), {
					type: 'doughnut',
					data: {
						labels: sLabels.length ? sLabels : ['No Data'],
						datasets: [{
							data: sData.length ? sData : [1],
							backgroundColor: ['#43a047', '#9e9e9e'],
							borderWidth: 2,
							borderColor: '#fff'
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: {
								position: 'bottom',
								labels: {
									usePointStyle: true,
									padding: 12
								}
							}
						},
						cutout: '55%'
					}
				});
			}
		});
	</script>

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