<?php
$adminID = $_SESSION['login_user_id'];
$adminPermissionQuery = "SELECT nm.title FROM admin_permissions ap
inner join navigation_menus nm on ap.navigation_menu_id = nm.id where ap.admin_id='" . $adminID . "' ";
$adminPermissionResult = mysqli_query($db, $adminPermissionQuery);

$permissions = [];
while ($item = mysqli_fetch_row($adminPermissionResult)) {
    array_push($permissions, $item[0]);
}
?>
<nav class="pcoded-navbar menupos-fixed menu-light ">
    <div class="navbar-wrapper  ">
        <div class="navbar-content scroll-div ">
            <ul class="nav pcoded-inner-navbar ">
                <li class="nav-item pcoded-menu-caption">
                    <label>Navigation</label>
                </li>
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link " style="background:#003399; color:#fff;"><span
                            class="pcoded-micon"><i class="feather icon-home"></i></span><span
                            class="">Dashboard</span></a>
                </li>

                <?php if ((in_array('Contact form leads', $permissions)) || (in_array('Other Leads', $permissions)) || (in_array('All', $permissions))) { ?>
                <li class="nav-item pcoded-hasmenu">
                    <a href="#" class="nav-link "><span class="pcoded-micon"><i
                                class="feather icon-globe"></i></span><span class="">Leads</span></a>
                    <ul class="pcoded-submenu">
                        <?php if ((in_array('Contact form leads', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='contact-leads.php'>Contact form leads</a></li>";
                            }
                            // if ((in_array('Other Leads', $permissions)) || (in_array('All', $permissions))) {
                            //     echo "<li><a href='other-leads.php'>Other Leads</a></li>";
                            // }
                            ?>
                    </ul>
                </li>
                <?php } ?>
                <?php if ((in_array('Contact form Image', $permissions)) || (in_array('All Contact Image', $permissions)) || (in_array('All', $permissions))) { ?>
                <li class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                class="feather icon-image"></i></span><span class="pcoded-mtext">Contact Image
                        </span></a>
                    <ul class="pcoded-submenu">
                        <?php if ((in_array('contact image', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='add-contact-image.php'>Contact Image</a></li>";
                            }
                            if ((in_array('All Contact Image', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='manage-contact-image.php'>All Contact Image</a></li>";
                            }
                            ?>
                    </ul>
                </li>
                <?php } ?>

                <!-- <?php if ((in_array('Add New Category', $permissions)) || (in_array('All Categories', $permissions)) || (in_array('Add Parent Category', $permissions)) || (in_array('All Parent Category', $permissions))  || (in_array('All', $permissions))) { ?>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-edit"></i></span><span class="pcoded-mtext">Categories</span></a>
                        <ul class="pcoded-submenu">
                            <?php if ((in_array('Add New Category', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='add-category.php'>Add New Category</a></li>";
                            }
                            if ((in_array('All Categories', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='manage-category.php'>All Categories</a></li>";
                            }
                            if ((in_array('Add Parent Category', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='add-parent-category.php'>Add Parent Category</a></li>";
                            }
                            if ((in_array('All Parent Category', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='manage-parent-category.php'>All Parent Category</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ((in_array('Add New Page', $permissions)) || (in_array('All Pages', $permissions)) || (in_array('All', $permissions))) { ?>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-file-plus"></i></span><span
                                class="pcoded-mtext">Pages</span></a>
                        <ul class="pcoded-submenu">
                            <?php if ((in_array('Add New Page', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='add-page.php'>Add New Page</a></li>";
                            }
                            if ((in_array('All Pages', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='manage-page.php'>All Pages</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ((in_array('Add menu items', $permissions)) || (in_array('All menu', $permissions)) || (in_array('Add sub menu', $permissions)) || (in_array('All sub menu', $permissions)) || (in_array('All', $permissions))) { ?>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-box"></i></span><span class="pcoded-mtext">Menus</span></a>
                        <ul class="pcoded-submenu">
                            <?php if ((in_array('Add menu items', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='add-menu.php'>Add menu items</a></li>";
                            }
                            if ((in_array('All menu', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='manage-menu.php'>All menu</a></li>";
                            }
                            if ((in_array('Add sub menu', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='add-sub-menu.php'>Add sub menu</a></li>";
                            }
                            if ((in_array('All sub menu', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='manage-sub-menu.php'>All sub menu</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ((in_array('Add New Video', $permissions)) || (in_array('All Videos', $permissions)) || (in_array('All', $permissions))) { ?>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-video"></i></span><span class="pcoded-mtext">Video</span></a>
                        <ul class="pcoded-submenu">
                            <?php if ((in_array('Add New Video', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='add-video.php'>Add New Video</a></li>";
                            }
                            if ((in_array('All Videos', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='manage-videos.php'>All Videos</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ((in_array('Add Image Slider', $permissions)) || (in_array('All Slider', $permissions)) || (in_array('All', $permissions))) { ?>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-image"></i></span><span class="pcoded-mtext">Image Slider
                            </span></a>
                        <ul class="pcoded-submenu">

                            <?php if ((in_array('Add Image Slider', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='add-slider.php'>Add Image Slider</a></li>";
                            }
                            if ((in_array('All Slider', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='manage-slider.php'>All Slider</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ((in_array('Add Services', $permissions)) || (in_array('All Services', $permissions)) || (in_array('All', $permissions))) { ?>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-briefcase"></i></span><span class="pcoded-mtext">Services
                            </span></a>
                        <ul class="pcoded-submenu">

                            <?php if ((in_array('Add Services', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='add-services.php'>Add Services</a></li>";
                            }
                            if ((in_array('All Services', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='manage-services.php'>All Services</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ((in_array('Add Study Material', $permissions)) || (in_array('All Study Material', $permissions)) || (in_array('All', $permissions))) { ?>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-book"></i></span><span class="pcoded-mtext">Study Material
                            </span></a>
                        <ul class="pcoded-submenu">

                            <?php if ((in_array('Add Study Material', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='add-study-material.php'>Add Study Material</a></li>";
                            }
                            if ((in_array('All Study Material', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='manage-study-material.php'>All Study Material</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ((in_array('Add Testimonials', $permissions)) || (in_array('All Testimonials', $permissions)) || (in_array('All', $permissions))) { ?>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-users"></i></span><span class="pcoded-mtext">Testimonials
                            </span></a>
                        <ul class="pcoded-submenu">
                            <?php if ((in_array('Add Testimonials', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='add-testimonials.php'>Add Testimonials</a></li>";
                            }
                            if ((in_array('All Testimonials', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='manage-testimonials.php'>All Testimonials</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ((in_array('Add New Post', $permissions)) || (in_array('All Posts', $permissions)) || (in_array('All', $permissions))) { ?>

                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-file-text"></i></span><span class="pcoded-mtext">Blogs /Posts</span></a>
                        <ul class="pcoded-submenu">
                            <?php if ((in_array('Add New Post', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='add-post.php'>Add New Post</a></li>";
                            }
                            if ((in_array('All Posts', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='manage-post.php'>All Posts</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                <?php } ?>


                <?php if ((in_array('Add New Media', $permissions)) || (in_array('All Media', $permissions)) || (in_array('All', $permissions))) { ?>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-camera"></i></span><span class="pcoded-mtext">Media Library</span></a>
                        <ul class="pcoded-submenu">
                            <?php if ((in_array('Add New Media', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='add-media.php'>Add New Media</a></li>";
                            }
                            if ((in_array('All Media', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='manage-media.php'>All Media</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                <?php } ?>


                <?php if ((in_array('Add New Admin User', $permissions)) || (in_array('All Admin Users', $permissions)) || (in_array('All', $permissions))) { ?>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-users"></i></span><span class="pcoded-mtext">Admin Users</span></a>
                        <ul class="pcoded-submenu">
                            <?php if ((in_array('Add New Admin User', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='add-user.php'>Add New Admin User</a></li>";
                            }
                            if ((in_array('All Admin Users', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='manage-user.php'>All Admin Users</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ((in_array('Add New Client', $permissions)) || (in_array('All Clients', $permissions)) || (in_array('All', $permissions))) { ?>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-user"></i></span><span class="">Clients</span></a>
                        <ul class="pcoded-submenu">
                            <?php if ((in_array('Add New Client', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='add-client.php'>Add New Client</a></li>";
                            }
                            if ((in_array('All Clients', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='manage-client.php'>All Clients</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                <?php } ?>




                <?php if ((in_array('Add New Advt', $permissions)) || (in_array('All Vertical Advts', $permissions)) || (in_array('All Horizontal Advts', $permissions)) || (in_array('All', $permissions))) { ?>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-slack"></i></span><span class="pcoded-mtext">Advertisements</span></a>
                        <ul class="pcoded-submenu">
                            <?php if ((in_array('Add New Advt', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='add-ads.php'>Add New Advt</a></li>";
                            }
                            if ((in_array('All Vertical Advts', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='manage-vertical-advt.php'>All Vertical Advts</a></li>";
                            }
                            if ((in_array('All Horizontal Advts', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='manage-horizontal-advt.php'>All Horizontal Advts</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                <?php } ?>


                <?php if ((in_array('General Settings', $permissions)) || (in_array('Website Settings', $permissions)) || (in_array('System Settings', $permissions)) || (in_array('Financial Settings', $permissions)) || (in_array('All', $permissions))) { ?>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-settings"></i></span><span class="pcoded-mtext">Settings
                            </span></a>
                        <ul class="pcoded-submenu">

                            <?php if ((in_array('General Settings', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='profile.php'>General Settings</a></li>";
                            }
                            if ((in_array('Website Settings', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='company-settings.php'>Website Settings</a></li>";
                            }
                            if ((in_array('System Settings', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='system-settings.php'>System Settings</a></li>";
                            }
                            if ((in_array('Financial Settings', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='financial-settings.php'>Financial Settings</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ((in_array('Add Statistics', $permissions)) || (in_array('All Statistics', $permissions)) || (in_array('All', $permissions))) { ?>
                    <li class="nav-item pcoded-hasmenu">
                        <a href="#" class="nav-link "><span class="pcoded-micon"><i
                                    class="feather icon-bar-chart-2"></i></span><span class="">Statistics</span></a>
                        <ul class="pcoded-submenu">
                            <?php if ((in_array('Add Statistics', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='add-statistics.php'>Add Statistics</a></li>";
                            }
                            if ((in_array('All Statistics', $permissions)) || (in_array('All', $permissions))) {
                                echo "<li><a href='manage-statistics.php'>All Statistics</a></li>";
                            }
                            ?>
                        </ul>
                    </li>
                <?php } ?>

                <?php if ((in_array('Registered Users', $permissions)) || (in_array('All', $permissions))
                ) {
                ?>
                    <li class="nav-item">
                        <a href="registered-users.php" class="nav-link "><span class="pcoded-micon"><i class="feather icon-users"></i></span><span class="">Registered Users</span></a>
                    </li>
                <?php } ?>

                <?php if ((in_array('Logs Reports', $permissions)) || (in_array('All', $permissions))
                ) {
                ?>
                    <li class="nav-item">
                        <a href="reports.php" class="nav-link "><span class="pcoded-micon"><i class="feather icon-file-plus"></i></span><span class="">Logs Reports</span></a>
                    </li>
                <?php } ?>

                <?php if ((in_array('Backup And Recovery', $permissions)) || (in_array('All', $permissions))
                ) {
                ?>
                    <li class="nav-item">
                        <a href="backup-recovery.php" class="nav-link "><span class="pcoded-micon"><i class="feather icon-globe"></i></span><span class="">Backup And Recovery</span></a>
                    </li>
                <?php } ?>

                <?php if ((in_array('Change Password', $permissions)) || (in_array('All', $permissions))
                ) {
                ?>
                    <li class="nav-item">
                        <a href="changepass.php" class="nav-link "><span class="pcoded-micon"><i class="feather icon-lock"></i></span><span class="">Change Password</span></a>
                    </li>
                <?php } ?> -->

                <li class="nav-item">
                    <a href="logout.php" class="nav-link " style="background:#003399; color:#fff;"><span
                            class="pcoded-micon"><i class="feather icon-power"></i></span><span class="">Log
                            out</span></a>
                </li>

            </ul>

        </div>
    </div>
</nav>