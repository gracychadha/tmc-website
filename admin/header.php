<?php


// Check if adminId exists in the session
if (!isset($_SESSION['adminId'])) {
    header("Location: login.php"); // Redirect to login page if adminId is not set
    exit();
}

// Decode the base64-encoded adminId
$decodedAdminId = base64_decode($_SESSION['adminId']);

// Fetch admin details from the database
$stmt = $db->prepare("SELECT username, image FROM admin WHERE admin_id = ?");
$stmt->bind_param("i", $decodedAdminId); // Assuming admin_id is an integer
$stmt->execute();
$result = $stmt->get_result();
$adminData = $result->fetch_assoc();

// Set variables
$userName = $adminData['username'] ?? "Admin"; // Default to "Admin" if name is missing
$adminPhoto = !empty($adminData['image']) ? "profile/" . htmlspecialchars($adminData['image']) : "assets/img/profiles/avatar-01.jpg";

// SQL query with a prepared statement
$sqlfav = "SELECT favicon, backpanel_logo, black_image, helpdesk FROM system_setting LIMIT 1";

if ($stmt = $db->prepare($sqlfav)) {
    // Execute the statement
    $stmt->execute();

    // Bind the result to variables
    $stmt->bind_result($favicon, $backpanel_logo, $black_image, $helpdesk);

    // Fetch the result
    if ($stmt->fetch()) {
        // Build the full paths
        $faviconPath = "logo/" . $favicon; 
        $backpanelLogoPath = "logo/" . $backpanel_logo; 
        $blackImagePath = "logo/" . $black_image; 

        // Optionally use $helpdesk here if needed
    }

    // Close the statement
    $stmt->close();
} else {
    // Handle errors, if any
    echo "Failed to prepare the statement.";
}

?>


<!-- Logo -->
<div class="header-left active">
	<a href="dashboard.php" class="logo logo-normal">
	<img src="<?php echo htmlspecialchars($blackImagePath); ?>"  alt="Backpanel Logo" >
	</a>
	<!-- <a href="dashboard.php" class="logo-small">
		<img src="assets/img/logo-small.svg" alt="Logo">
	</a>
	<a href="dashboard.php" class="dark-logo">
		<img src="assets/img/logo-dark.svg" alt="Logo">
	</a> -->
	<a id="toggle_btn" href="javascript:void(0);">
		<i class="ti ti-menu-deep"></i>
	</a>
</div>
<!-- /Logo -->

<a id="mobile_btn" class="mobile_btn" href="#sidebar">
	<span class="bar-icon">
		<span></span>
		<span></span>
		<span></span>
	</span>
</a>

<div class="header-user">
	<div class="nav user-menu">

		<!-- Search -->
		<div class="nav-item me-auto">
			
		</div>
		<!-- /Search -->

		<div class="d-flex align-items-center">

			
			<div class="pe-1">
				<div class="dropdown">
					<a href="#" class="btn btn-outline-light bg-white btn-icon me-1" data-bs-toggle="dropdown" aria-expanded="false">
						<i class="ti ti-square-rounded-plus"></i>
					</a>
					<div class="dropdown-menu dropdown-menu-right border shadow-sm dropdown-md">
						<div class="p-3 border-bottom">
							<h5>Add New</h5>
						</div>
						<div class="p-3 pb-0">
							<div class="row gx-2">
								<div class="col-6">
									<a href="admin-blog.php" class="d-block bg-primary-transparent ronded p-2 text-center mb-3 class-hover">
										<div class="avatar avatar-lg mb-2">
											<span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-primary rounded-circle"><i class="ti ti-brand-blogger "></i></span>
										</div>
										<p class="text-dark">Blogs</p>
									</a>
								</div>
								<div class="col-6">
									<a href="admin-service.php" class="d-block bg-success-transparent ronded p-2 text-center mb-3 class-hover">
										<div class="avatar avatar-lg mb-2">
											<span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-success rounded-circle"><i class="ti ti-layout-distribute-vertical"></i></span>
										</div>
										<p class="text-dark">Event Services</p>
									</a>
								</div>
								<div class="col-6">
									<a href="admin-user.php" class="d-block bg-warning-transparent ronded p-2 text-center mb-3 class-hover">
										<div class="avatar avatar-lg rounded-circle mb-2">
											<span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-warning rounded-circle"><i class="ti ti-users-group"></i></span>
										</div>
										<p class="text-dark">User</p>
									</a>
								</div>
								<div class="col-6">
									<a href="admin-courses.php" class="d-block bg-info-transparent ronded p-2 text-center mb-3 class-hover">
										<div class="avatar avatar-lg mb-2">
											<span class="d-inline-flex align-items-center justify-content-center w-100 h-100 bg-info rounded-circle"><i class="fa fa-book"></i></span>
										</div>
										<p class="text-dark">Events</p>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="pe-1">
				<a href="#" id="dark-mode-toggle" class="dark-mode-toggle activate btn btn-outline-light bg-white btn-icon me-1">
					<i class="ti ti-moon"></i>
				</a>
				<a href="#" id="light-mode-toggle" class="dark-mode-toggle btn btn-outline-light bg-white btn-icon me-1">
					<i class="ti ti-brightness-up"></i>
				</a>
			</div>

			<div class="pe-1">
				<a href="#" class="btn btn-outline-light bg-white btn-icon me-1" id="btnFullscreen">
					<i class="ti ti-maximize"></i>
				</a>
			</div>
			<div class="dropdown ms-1">
				<a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
					<span class="avatar avatar-md rounded">
					<img src="<?php echo $adminPhoto; ?>" alt="Admin Avatar" class="img-fluid">
					</span>
				</a>
				<div class="dropdown-menu">
					<div class="d-block">
						<div class="d-flex align-items-center p-2">
							<span class="avatar avatar-md me-2 online avatar-rounded">
							<img src="<?php echo $adminPhoto; ?>" alt="Admin Avatar" class="img-fluid">
							</span>
							<div>
								<h6 class=""><?php echo htmlspecialchars($userName); ?></h6>
							</div>
						</div>
						<hr class="m-0">
						<a class="dropdown-item d-inline-flex align-items-center p-2" href="profile.php">
							<i class="ti ti-user-circle me-2"></i>My Profile
						</a>
						<a class="dropdown-item d-inline-flex align-items-center p-2" href="admin-general-setting.php">
							<i class="ti ti-settings me-2"></i>Settings
						</a>
						<hr class="m-0">
						<a class="dropdown-item d-inline-flex align-items-center p-2" href="logout.php">
							<i class="ti ti-login me-2"></i>Logout
						</a>
					</div>
				</div>
			</div>


		</div>

	</div>
</div>

<!-- Mobile Menu -->
<div class="dropdown mobile-user-menu">
	<a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
	<div class="dropdown-menu dropdown-menu-end">
		<a class="dropdown-item" href="profile.php">My Profile</a>
		<a class="dropdown-item" href="admin-general-setting.php">Settings</a>
		<a class="dropdown-item" href="admin-logout.php">Logout</a>
	</div>
</div>
<!-- /Mobile Menu -->