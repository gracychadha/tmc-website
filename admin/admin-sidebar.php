<?php

// Ensure session adminId exists
if (!isset($_SESSION['adminId'])) {
	die("Access Denied. No Admin Session Found.");
}

// Decode the admin ID from session
$admin_id = base64_decode($_SESSION['adminId']);

// Fetch the admin's permissions (menu IDs stored as JSON in the admin table)
$sql = "SELECT permission FROM admin WHERE admin_id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if admin exists
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$permissions = json_decode($row['permission'], true); // Convert JSON string to array
} else {
	die("Admin not found.");
}

$stmt->close();

// Initialize menu titles array
$menu_titles = [];

if (!empty($permissions) && is_array($permissions)) {
	// Prepare SQL query with placeholders
	$placeholders = implode(',', array_fill(0, count($permissions), '?'));
	$sql = "SELECT title FROM menu WHERE id IN ($placeholders)";

	$stmt = $db->prepare($sql);

	if ($stmt) {
		// Bind parameters dynamically
		$stmt->bind_param(str_repeat('i', count($permissions)), ...$permissions);

		// Execute query and fetch results
		$stmt->execute();
		$result = $stmt->get_result();

		while ($menu = $result->fetch_assoc()) {
			$menu_titles[] = $menu['title']; // Store titles instead of IDs

		}

		$stmt->close();
	}
}

$db->close();
?>

<!-- Sidebar Navigation -->
<?php if (!empty($menu_titles)) { ?>
	<div class="sidebar-inner slimscroll">
		<div id="sidebar-menu" class="sidebar-menu">

			<ul>
				<?php if (in_array('All', $menu_titles) || in_array('Contact List', $menu_titles) || in_array('Apply List', $menu_titles) || in_array('Service Requests', $menu_titles)) { ?>
					<li>
						<h6 class="submenu-hdr"><span>Main Menu</span></h6>
						<ul>
							<?php

							if (in_array('All', $menu_titles) || in_array('Dashboard', $menu_titles)) {
								echo '<li>
							<a href="dashboard.php"><i class="ti ti-layout-dashboard"></i><span>Dashboard</span></a>
						</li>';
							}
							if (in_array('All', $menu_titles) || in_array('Contact Us', $menu_titles)) {
								echo '<li>
							<a href="admin-contact.php"><i class="ti ti-license"></i><span>Contact Requests</span></a>
						</li>';
							}
							if (in_array('All', $menu_titles) || in_array('Contact Us', $menu_titles)) {
								echo '<li>
							<a href="admin-subscriber.php"><i class="ti ti-license"></i><span>Subscribers</span></a>
						</li>';
							}
							if (in_array('All', $menu_titles) || in_array('Dashboard', $menu_titles)) {
								echo '<li>
							<a href="admin-calendar.php"><i class="ti ti-calendar-event"></i><span>Calendar Events</span></a>
						</li>';
							}
							// 	if (in_array('All', $menu_titles) || in_array('Service Requests', $menu_titles)) {
							// 		echo '<li>
							// 	<a href="admin-serviceapply.php"><i class="ti ti-notebook"></i><span>Service Requests</span></a>
							// </li>';
							// 	}
							// 	if (in_array('All', $menu_titles) || in_array('Apply List', $menu_titles)) {
							// 		echo '<li>
							// 	<a href="admin-apply.php"><i class="ti ti-file"></i><span>Apply Requests</span></a>
							// </li>';
							// }
							?>
						</ul>
					</li>
				<?php } ?>

				<?php if (in_array('All', $menu_titles) || in_array('Courses', $menu_titles) || in_array('Blog List', $menu_titles) || in_array('Directory Category', $menu_titles) || in_array('Directory List', $menu_titles) || in_array('Pricing', $menu_titles) || in_array('Pricing List', $menu_titles) || in_array('Resources', $menu_titles) || in_array('Pricing', $menu_titles) || in_array('Testinominal', $menu_titles) || in_array('Hero Slider', $menu_titles)) { ?>
					<li>
						<h6 class="submenu-hdr"><span>CMS & Blogs</span></h6>
						<ul>
							<!-- <li class="">
								<?php if (in_array('All', $menu_titles) || in_array('Courses', $menu_titles)) { ?>
									<a href="admin-4courses.php"><i class="ti ti-layout-list"></i><span>Manage Courses</span></a>



								<?php } ?>

							</li> -->
						
							<!-- Event -->
							<li class="">
								<?php if (in_array('All', $menu_titles) || in_array('Event', $menu_titles)) { ?>
									<a href="admin-event.php"><i class="fa-solid fa-calendar-days"></i><span>Manage Event(s)</span></a>
								<?php } ?>
							</li>
							<!-- Faq -->
							<li class="">
								<?php if (in_array('All', $menu_titles) || in_array('Faq', $menu_titles)) { ?>
									<a href="admin-faqs.php"><i class="fa-solid fa-circle-question"></i><span>Manage Faq(s)</span></a>
								<?php } ?>
							</li>
							<!-- gallery -->
							<li class="">
								<?php if (in_array('All', $menu_titles) || in_array('Gallery', $menu_titles)) { ?>
									<a href="admin-gallery.php"><i class="fa-solid fa-images"></i><span>Manage Gallery</span></a>
								<?php } ?>
							</li>
							<!-- Partners -->
							<li class="">
								<?php if (in_array('All', $menu_titles) || in_array('Partners', $menu_titles)) { ?>
									<a href="admin-partner.php"><i class="fa-solid fa-building"></i><span>Manage Partners</span></a>
								<?php } ?>
							</li>
							<!-- team -->
							<li class="">
								<?php if (in_array('All', $menu_titles) || in_array('Team', $menu_titles)) { ?>
									<a href="admin-team.php"><i class="ti ti-users"></i><span>Manage Team</span></a>
								<?php } ?>
							</li>
							<!-- testimonial -->
							<li class="">
								<?php if (in_array('All', $menu_titles) || in_array('Testimonial', $menu_titles)) { ?>
									<a href="admin-testimonial.php"><i class="fa-solid fa-quote-left"></i><span>Manage Testimonial</span></a>
								<?php } ?>
							</li>



							<li class="submenu">
								<?php if (in_array('All', $menu_titles) || in_array('Blog Category', $menu_titles) || in_array('All Blog', $menu_titles)) { ?>
									<a href="javascript:void(0);"><i class="ti ti-brand-blogger"></i><span>Manage Blog</span><span
											class="menu-arrow"></span></a>
									<ul>
										<?php

										if (in_array('All', $menu_titles) || in_array('Blog Category', $menu_titles)) {

											echo '<li><a href="admin-category.php">Blog Category</a></li>';
										}
										if (in_array('All', $menu_titles) || in_array('All Blog', $menu_titles)) {
											echo '<li><a href="admin-blog.php">All Blog</a></li>';
										}



										?>
									</ul>
								<?php } ?>
							</li>

							<li class="submenu">
								<?php if (in_array('All', $menu_titles) || in_array('Gallery', $menu_titles) || in_array('Testinominal', $menu_titles) || in_array('Why Choose Us', $menu_titles) || in_array('Work Process', $menu_titles) || in_array('Hero Slider', $menu_titles) || in_array('banner', $menu_titles)) { ?>
									<a href="javascript:void(0);"><i class="ti ti-building-fortress"></i><span> Home Page</span><span class="menu-arrow"></span></a>
									<ul>
										<?php
										if (in_array('All', $menu_titles) || in_array('Home Banner', $menu_titles)) {
											echo '<li><a href="admin-hero.php">Banner / Flyer</a></li>';
										}
										if (in_array('All', $menu_titles) || in_array('Ticker', $menu_titles)) {
											echo '<li><a href="admin-ticker.php">Event Ticker</a></li>';
										}
										if (in_array('All', $menu_titles) || in_array('About Us', $menu_titles)) {
											echo '<li><a href="admin-about-us.php">About us </a></li>';
										}
										




										// if (in_array('All', $menu_titles) || in_array('Image Slider', $menu_titles)) {
										// 	echo '<li><a href="admin-sliders.php">Image Slider</a></li>';
										// }

										// if (in_array('All', $menu_titles) || in_array('Counter', $menu_titles)) {
										// 	echo '<li><a href="admin-counter.php">Counter</a></li>';
										// }

										// if (in_array('All', $menu_titles) || in_array('Common Services', $menu_titles)) {
										// 	echo '<li><a href="admin-common-services.php">Common Services</a></li>';
										// }

										?>
									</ul>
								<?php } ?>
							</li>
							<li class="submenu">
								<?php if (in_array('All', $menu_titles) || in_array('Gallery', $menu_titles) || in_array('Testinominal', $menu_titles) || in_array('Why Choose Us', $menu_titles) || in_array('Work Process', $menu_titles) || in_array('Hero Slider', $menu_titles) || in_array('banner', $menu_titles)) { ?>
									<a href="javascript:void(0);"><i class="ti ti-info-circle"></i><span> About Page</span><span class="menu-arrow"></span></a>
									<ul>
										<?php
										if (in_array('All', $menu_titles) || in_array('Common Banner', $menu_titles)) {
											echo '<li><a href="admin-common-banner.php">Banner / Flyer</a></li>';
										}
										if (in_array('All', $menu_titles) || in_array('About Us', $menu_titles)) {
											echo '<li><a href="admin-about-us.php">About us </a></li>';
										}
										if (in_array('All', $menu_titles) || in_array('Approach', $menu_titles)) {
											echo '<li><a href="admin-approach.php">Approach Section </a></li>';
										}
										if (in_array('All', $menu_titles) || in_array('Achievements', $menu_titles)) {
											echo '<li><a href="admin-acheivements.php">Achievements  </a></li>';
										}
										




										

										// if (in_array('All', $menu_titles) || in_array('Counter', $menu_titles)) {
										// 	echo '<li><a href="admin-counter.php">Counter</a></li>';
										// }

									

										?>
									</ul>
								<?php } ?>
							</li>
						</ul>
					</li>
				<?php } ?>

				<!-- <?php if (in_array('All', $menu_titles) || in_array('About Us', $menu_titles) || in_array('Careers', $menu_titles) || in_array('About The Company', $menu_titles) || in_array('Counter', $menu_titles) || in_array('Profile', $menu_titles) || in_array('Services', $menu_titles) || in_array('Study Abroad', $menu_titles) || in_array('Team Member', $menu_titles)) { ?>
					<li>
						<h6 class="submenu-hdr"><span>Pages</span></h6>
						<ul>

							<?php
							if (in_array('All', $menu_titles) || in_array('About Us', $menu_titles)) {
								echo '<li><a href="admin-about-us.php"><i class="ti ti-layout-distribute-horizontal"></i><span>About Founder</span></a></li>';
							}

							if (in_array('All', $menu_titles) || in_array('About The Company', $menu_titles)) {
								echo '<li><a href="admin-thecompany.php"><i class="ti ti-building"></i><span>About The Company</span></a></li>';
							}

							if (in_array('All', $menu_titles) || in_array('Counter', $menu_titles)) {
								echo '<li><a href="admin-counter.php"><i class="ti ti-layout-grid"></i><span>Counter</span></a></li>';
							}


							if (in_array('All', $menu_titles) || in_array('Profile', $menu_titles)) {

								echo '<li><a href="profile.php"><i class="ti ti-user"></i><span>Profile</span></a></li>';
							}
							if (in_array('All', $menu_titles) || in_array('Services', $menu_titles)) {

								echo '<li><a href="admin-service.php"><i class="ti ti-layout-distribute-vertical"></i><span>Event Services</span></a></li>';
							}

							if (in_array('All', $menu_titles) || in_array('Team Member', $menu_titles)) {

								echo '<li><a href="admin-team.php"><i class="ti ti-users"></i><span>Team Member</span></a></li>';
							}
							if (in_array('All', $menu_titles) || in_array('Privacy Policy', $menu_titles)) {
								echo '<li><a href="admin-privacy-policy.php"><i class="ti ti-lock"></i><span>Privacy Policy</span></a></li>';
							}
							if (in_array('All', $menu_titles) || in_array('Terms & Conditions', $menu_titles)) {
								echo '<li><a href="admin-terms-&-conditions.php"><i class="ti ti-lock"></i><span>Terms & Conditions</span></a></li>';
							}

							?>
						</ul>
					</li>
				<?php } ?> -->

				<?php if (in_array('All', $menu_titles) || in_array('Team Profile', $menu_titles)) { ?>
					<li>
						<h6 class="submenu-hdr"><span>My Account</span></h6>
						<ul>
							<li>
								<a href="team-profile.php"><i class="ti ti-user-heart"></i><span>My Team Profile</span></a>
							</li>
							<li>
								<a href="profile.php"><i class="ti ti-user"></i><span>Profile</span></a>
							</li>
						</ul>
					</li>
				<?php } ?>

				<?php if (in_array('All', $menu_titles) || in_array('General Setting', $menu_titles) || in_array('System Setting', $menu_titles) || in_array('Website Setting', $menu_titles) || in_array('SEO Setup', $menu_titles)) { ?>
					<li>
						<h6 class="submenu-hdr"><span>Settings</span></h6>
						<ul>

							<?php
							if (in_array('All', $menu_titles) || in_array('General Setting', $menu_titles)) {
								echo '<li>
								<a href="admin-general-setting.php"><i class="ti ti-shield-cog"></i><span>General Setting</span></a>
							</li>';
							}
							if (in_array('All', $menu_titles) || in_array('System Setting', $menu_titles)) {

								echo '<li>
								<a href="admin-system-setting.php"><i class="ti ti-file-symlink"></i><span>System Setting</span></a>
							</li>';
							}
							if (in_array('All', $menu_titles) || in_array('Website Setting', $menu_titles)) {
								echo '<li>
								<a href="admin-website-setting.php"><i class="ti ti-device-laptop"></i><span>Website Setting</span></a>
							</li>';
							}
							// if (in_array('All', $menu_titles) || in_array('SEO Setting', $menu_titles)) {
							// 	echo '<li>
							// 	<a href="admin-seo.php"><i class="ti ti-device-laptop"></i><span>SEO Setting</span></a>
							// </li>';
							// }
							?>
						</ul>
					</li>
				<?php } ?>
				<?php if (in_array('All', $menu_titles) || in_array('Add User', $menu_titles) || in_array('User Permission', $menu_titles)) { ?>
					<li>
						<h6 class="submenu-hdr"><span>User Management</span></h6>
						<ul>
							<?php
							if (in_array('All', $menu_titles) || in_array('Add User', $menu_titles)) {
								echo '<li><a href="register.php"><i class="ti ti-users-minus"></i><span> Add User</span></a></li>';
							}
							if (in_array('All', $menu_titles) || in_array('User Permission', $menu_titles)) {

								echo '<li><a href="user-permission.php"><i class="ti ti-shield-plus"></i><span>User
									Permissions</span></a></li>';
							}


							?>
							<li></li>

						</ul>
					</li>
				<?php } ?>



			</ul>
		</div>
	</div>
<?php } ?>