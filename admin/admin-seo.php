<?php
session_start();
require_once('db/config.php');

if (!isset($_SESSION['adminId'])) {
    header("Location: index.php");
    exit;
}

// SQL query with a prepared statement for favicon
$sqlfav = "SELECT favicon FROM system_setting LIMIT 1";
if ($stmt = $db->prepare($sqlfav)) {
    $stmt->execute();
    $stmt->bind_result($favicon);
    if ($stmt->fetch()) {
        $faviconPath = "logo/" . $favicon;
    }
    $stmt->close();
}

// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Function to fetch SEO data by type
function fetch_seo_data($type) {
    global $db;
    $sql = "SELECT * FROM seo_settings WHERE type = ?";
    if ($stmt = $db->prepare($sql)) {
        $stmt->bind_param("s", $type);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        $stmt->close();
    }
    return null;
}

// Function to insert or update SEO data
function handle_seo_data($type, $seo_title, $seo_description) {
    global $db;
    $existing_data = fetch_seo_data($type);
    if ($existing_data) {
        $sql_update = "UPDATE seo_settings SET seo_title = ?, seo_description = ? WHERE type = ?";
        $stmt_update = $db->prepare($sql_update);
        $stmt_update->bind_param("sss", $seo_title, $seo_description, $type);
        $stmt_update->execute();
        $stmt_update->close();
        $_SESSION['message'] = "SEO data for $type updated successfully.";
    } else {
        $sql_insert = "INSERT INTO seo_settings (type, seo_title, seo_description) VALUES (?, ?, ?)";
        $stmt_insert = $db->prepare($sql_insert);
        $stmt_insert->bind_param("sss", $type, $seo_title, $seo_description);
        $stmt_insert->execute();
        $stmt_insert->close();
        $_SESSION['message'] = "SEO data for $type added successfully.";
    }
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['home'])) {
        $title = $_POST['home_title'];
        $content = $_POST['content'];
        $type = 'home';
        handle_seo_data($type, $title, $content);
        header("Location: admin-seo.php");
        exit();
    } elseif (isset($_POST['about'])) {
        $title = $_POST['about_title'];
        $content = $_POST['about_content'];
        $type = 'about';
        handle_seo_data($type, $title, $content);
        header("Location: admin-seo.php");
        exit();
    } elseif (isset($_POST['contact'])) {
        $title = $_POST['contact_title'];
        $content = $_POST['contact_content'];
        $type = 'contact';
        handle_seo_data($type, $title, $content);
        header("Location: admin-seo.php");
        exit();
    } elseif (isset($_POST['dynamic'])) {
        $type = $_POST['dynamic_type'];
        $title = $_POST['dynamic_title'];
        $content = $_POST['dynamic_content'];
        handle_seo_data($type, $title, $content);
        header("Location: admin-seo.php");
        exit();
    }
}

// Fetch services, blogs, and courses
$sqlservices = "SELECT idservices, title FROM services";
$resultservices = $db->query($sqlservices);
$services = [];
while ($rowservices = $resultservices->fetch_assoc()) {
    $services[] = $rowservices;
}

$sqlblogs = "SELECT idblog, title FROM blog";
$resultblogs = $db->query($sqlblogs);
$blogs = [];
while ($rowblogs = $resultblogs->fetch_assoc()) {
    $blogs[] = $rowblogs;
}

$sqlcourse = "SELECT idcourses, title FROM courses";
$resultcourse = $db->query($sqlcourse);
$courses = [];
while ($rowcourse = $resultcourse->fetch_assoc()) {
    $courses[] = $rowcourse;
}

// Fetch existing data for static pages
$home_data = fetch_seo_data('home');
$about_data = fetch_seo_data('about');
$contact_data = fetch_seo_data('contact');
$visitor_data = fetch_seo_data('visitor');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="robots" content="noindex, nofollow">
    <title>SEO Setup - Tee Mac Corporation</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($faviconPath); ?>">
    <script src="assets/js/theme-script.js"></script>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">
    <link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.css">
    <link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .main-wrapper {
            display: flex;
            width: 100%;
            height: 100%;
        }

        .main-sidebar {
            width: 250px;
            background-color: #343a40;
            color: #ffffff;
            border-right: 1px solid #4b545c;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .main-sidebar a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #ffffff;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 10px;
            transition: background-color 0.3s;
        }

        .main-sidebar a:hover,
        .main-sidebar a.active {
            background-color: #4b545c;
            color: #ffffff;
        }

        .main-sidebar a i {
            margin-right: 10px;
        }

        .content-wrapper {
            flex-grow: 1;
            display: flex;
            height: 100vh;
        }

        .secondary-sidebar {
            width: 250px;
            background-color: #ffffff;
            color: #343a40;
            border-right: 1px solid #dee2e6;
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .page-wrapper {
            width: -webkit-fill-available;
        }

        .secondary-sidebar a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #343a40;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 10px;
            transition: background-color 0.3s;
        }

        .secondary-sidebar a:hover,
        .secondary-sidebar a.active {
            background-color: #dee2e6;
            color: #343a40;
        }

        .secondary-sidebar a i {
            margin-right: 10px;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
            background-color: #f8f9fa;
            overflow-y: auto;
        }

        .section {
            display: none;
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .section.active {
            display: block;
        }

        .crancy-ptabs__separate {
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .crancy__item-label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        .crancy__item-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .crancy__item-textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            margin-bottom: 15px;
            height: 150px;
        }

        .crancy-btn {
            background-color: #125581;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .crancy-btn:hover {
            background-color: #243444;
        }

        .list-group-item {
            gap: 20px;
            padding: 20px;
            display: flex;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <div class="header">
            <?php require_once('header.php'); ?>
        </div>
        <div class="sidebar" id="sidebar">
            <?php require_once('admin-sidebar.php'); ?>
        </div>
        <div class="page-wrapper">
            <div class="content content-two">
                <div class="my-auto mb-2">
                    <h3 class="page-title mb-1">SEO Setup</h3>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">SEO Setup</li>
                        </ol>
                    </nav>
                </div>
                <section class="crancy-adashboard crancy-show">
                    <div class="container container__bscreen">
                        <div class="row__bscreen">
                            <div class="col-12">
                                <div class="crancy-body">
                                    <div class="crancy-dsinner">
                                        <div class="crancy-personals mg-top-30">
                                            <div class="row">
                                                <div class="col-lg-3 col-md-2 col-12 crancy-personals__list">
                                                    <div class="crancy-psidebar">
                                                        <div class="list-group crancy-psidebar__list" id="list-tab" role="tablist">
                                                            <a class="list-group-item d-flex gap-10 active" data-bs-toggle="list" href="#id11" role="tab" aria-selected="true">
                                                                <span class="crancy-psidebar__icon"><i class="fas fa-list"></i></span>
                                                                <h4 class="crancy-psidebar__title">Home</h4>
                                                            </a>
                                                            <a class="list-group-item" data-bs-toggle="list" href="#id13" role="tab" aria-selected="false">
                                                                <span class="crancy-psidebar__icon"><i class="fas fa-list"></i></span>
                                                                <h4 class="crancy-psidebar__title">About Us</h4>
                                                            </a>
                                                            <a class="list-group-item" data-bs-toggle="collapse" href="#servicesMenu" role="button" aria-expanded="false" aria-controls="servicesMenu">
                                                                <span class="crancy-psidebar__icon"><i class="fas fa-list"></i></span>
                                                                <h4 class="crancy-psidebar__title">Visa Services</h4>
                                                            </a>
                                                            <ul class="collapse list-group" id="servicesMenu">
                                                                <?php foreach ($services as $service) { ?>
                                                                    <li class="list-group-item">
                                                                        <a href="#id_dynamic" class="dynamic-link" 
                                                                           data-type="<?php echo htmlspecialchars($service['title']); ?>" 
                                                                           data-title="<?php echo htmlspecialchars($service['title']); ?>">
                                                                            <?php echo htmlspecialchars($service['title']); ?>
                                                                        </a>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                            
                                                            <a class="list-group-item" data-bs-toggle="collapse" href="#blogsMenu" role="button" aria-expanded="false" aria-controls="blogsMenu">
                                                                <span class="crancy-psidebar__icon"><i class="fas fa-blog"></i></span>
                                                                <h4 class="crancy-psidebar__title">Blogs</h4>
                                                            </a>
                                                            <ul class="collapse list-group" id="blogsMenu">
                                                                <?php foreach ($blogs as $blog) { ?>
                                                                    <li class="list-group-item">
                                                                        <a href="#id_dynamic" class="dynamic-link" 
                                                                           data-type="<?php echo htmlspecialchars($blog['title']); ?>" 
                                                                           data-title="<?php echo htmlspecialchars($blog['title']); ?>">
                                                                            <?php echo htmlspecialchars($blog['title']); ?>
                                                                        </a>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                            <!-- <a class="list-group-item" data-bs-toggle="collapse" href="#courseMenu" role="button" aria-expanded="false" aria-controls="courseMenu">
                                                                <span class="crancy-psidebar__icon"><i class="fas fa-book"></i></span>
                                                                <h4 class="crancy-psidebar__title">Courses</h4>
                                                            </a>
                                                            <ul class="collapse list-group" id="courseMenu">
                                                                <?php foreach ($courses as $course) { ?>
                                                                    <li class="list-group-item">
                                                                        <a href="#id_dynamic" class="dynamic-link" 
                                                                           data-type="<?php echo htmlspecialchars($course['title']); ?>" 
                                                                           data-title="<?php echo htmlspecialchars($course['title']); ?>">
                                                                            <?php echo htmlspecialchars($course['title']); ?>
                                                                        </a>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul> -->
                                                            <a class="list-group-item" data-bs-toggle="list" href="#id14" role="tab" aria-selected="false">
                                                                <span class="crancy-psidebar__icon"><i class="fas fa-list"></i></span>
                                                                <h4 class="crancy-psidebar__title">Contact Us</h4>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9 col-md-10 col-12 crancy-personals__content">
                                                    <div class="crancy-ptabs">
                                                        <?php if (isset($_SESSION['message'])): ?>
                                                            <div class="alert alert-success" id="messageBox">
                                                                <?php
                                                                echo $_SESSION['message'];
                                                                unset($_SESSION['message']);
                                                                ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="crancy-ptabs__inner">
                                                            <div class="tab-content" id="nav-tabContent">
                                                                <!-- Home Tab -->
                                                                <div class="tab-pane fade active show" id="id11" role="tabpanel">
                                                                    <form action="" method="POST" enctype="multipart/form-data">
                                                                        <input type="hidden" name="_token" value="VkNJzh35qAY2eYHKAENBvr3jnIZo97YAofNCNMPy" autocomplete="off">
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <div class="crancy-ptabs__separate">
                                                                                    <div class="crancy-ptabs__form-main">
                                                                                        <div class="crancy__item-group">
                                                                                            <h3 class="crancy__item-group__title mb-4">Home Page</h3>
                                                                                            <div class="crancy__item-form--group">
                                                                                                <div class="row">
                                                                                                    <div class="col-12">
                                                                                                        <div class="crancy__item-form--group mg-top-form-20">
                                                                                                            <label class="crancy__item-label">SEO Title</label>
                                                                                                            <input class="crancy__item-input" placeholder="Enter Seo Title" type="text" value="<?php echo isset($home_data['seo_title']) ? htmlspecialchars($home_data['seo_title']) : ''; ?>" name="home_title">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-12">
                                                                                                        <div class="crancy__item-form--group mg-top-form-20">
                                                                                                            <label class="crancy__item-label">SEO Description</label>
                                                                                                            <textarea class="crancy__item-input crancy__item-textarea" placeholder="Enter a short and relevant description for search engines." name="content"><?php echo isset($home_data['seo_description']) ? htmlspecialchars($home_data['seo_description']) : ''; ?></textarea>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="mg-top-40 mt-4">
                                                                                                <button class="crancy-btn" name="home" type="submit">Update</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                <!-- About Us Tab -->
                                                                <div class="tab-pane fade" id="id13" role="tabpanel">
                                                                    <form action="" method="POST" enctype="multipart/form-data">
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <div class="crancy-ptabs__separate">
                                                                                    <div class="crancy-ptabs__form-main">
                                                                                        <div class="crancy__item-group">
                                                                                            <h3 class="crancy__item-group__title mb-4">About Us Page</h3>
                                                                                            <div class="crancy__item-form--group">
                                                                                                <div class="row">
                                                                                                    <div class="col-12">
                                                                                                        <div class="crancy__item-form--group mg-top-form-20">
                                                                                                            <label class="crancy__item-label">SEO Title</label>
                                                                                                            <input class="crancy__item-input" placeholder="Enter Seo Title" type="text" value="<?php echo isset($about_data['seo_title']) ? htmlspecialchars($about_data['seo_title']) : ''; ?>" name="about_title">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-12">
                                                                                                        <div class="crancy__item-form--group mg-top-form-20">
                                                                                                            <label class="crancy__item-label">SEO Description</label>
                                                                                                            <textarea class="crancy__item-input crancy__item-textarea" placeholder="Enter a short and relevant description for search engines."            name="about_content"><?php echo isset($about_data['seo_description']) ? htmlspecialchars($about_data['seo_description']) : ''; ?></textarea>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="mg-top-40 mt-4">
                                                                                                <button class="crancy-btn" name="about" type="submit">Update</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                <!-- Visitor Tab -->
                                                                <div class="tab-pane fade" id="id_visitor" role="tabpanel">
                                                                    <form action="" method="POST" enctype="multipart/form-data">
                                                                        <input type="hidden" name="dynamic_type" value="visitor">
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <div class="crancy-ptabs__separate">
                                                                                    <div class="crancy-ptabs__form-main">
                                                                                        <div class="crancy__item-group">
                                                                                            <h3 class="crancy__item-group__title mb-4">OSHC/Visitor Health Cover</h3>
                                                                                            <div class="crancy__item-form--group">
                                                                                                <div class="row">
                                                                                                    <div class="col-12">
                                                                                                        <div class="crancy__item-form--group mg-top-form-20">
                                                                                                            <label class="crancy__item-label">SEO Title</label>
                                                                                                            <input class="crancy__item-input" placeholder="Enter Seo Title" type="text" value="<?php echo isset($visitor_data['seo_title']) ? htmlspecialchars($visitor_data['seo_title']) : ''; ?>" name="dynamic_title">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-12">
                                                                                                        <div class="crancy__item-form--group mg-top-form-20">
                                                                                                            <label class="crancy__item-label">SEO Description</label>
                                                                                                            <textarea class="crancy__item-input crancy__item-textarea" placeholder="Enter a short and relevant description for search engines." name="dynamic_content"><?php echo isset($visitor_data['seo_description']) ? htmlspecialchars($visitor_data['seo_description']) : ''; ?></textarea>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="mg-top-40 mt-4">
                                                                                                <button class="crancy-btn" name="dynamic" type="submit">Update</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                <!-- Contact Us Tab -->
                                                                <div class="tab-pane fade" id="id14" role="tabpanel">
                                                                    <form action="" method="POST" enctype="multipart/form-data">
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <div class="crancy-ptabs__separate">
                                                                                    <div class="crancy-ptabs__form-main">
                                                                                        <div class="crancy__item-group">
                                                                                            <h3 class="crancy__item-group__title mb-4">Contact Us Page</h3>
                                                                                            <div class="crancy__item-form--group">
                                                                                                <div class="row">
                                                                                                    <div class="col-12">
                                                                                                        <div class="crancy__item-form--group mg-top-form-20">
                                                                                                            <label class="crancy__item-label">SEO Title</label>
                                                                                                            <input class="crancy__item-input" placeholder="Enter Seo Title" type="text" value="<?php echo isset($contact_data['seo_title']) ? htmlspecialchars($contact_data['seo_title']) : ''; ?>" name="contact_title">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-12">
                                                                                                        <div class="crancy__item-form--group mg-top-form-20">
                                                                                                            <label class="crancy__item-label">SEO Description</label>
                                                                                                            <textarea class="crancy__item-input crancy__item-textarea" placeholder="Enter a short and relevant description for search engines." name="contact_content"><?php echo isset($contact_data['seo_description']) ? htmlspecialchars($contact_data['seo_description']) : ''; ?></textarea>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="mg-top-40 mt-3">
                                                                                                <button class="crancy-btn" name="contact" type="submit">Update</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                <!-- Dynamic Tab for Services, Blogs, and Courses -->
                                                                <div class="tab-pane fade" id="id_dynamic" role="tabpanel">
                                                                    <form action="" method="POST" enctype="multipart/form-data">
                                                                        <input type="hidden" name="_token" value="VkNJzh35qAY2eYHKAENBvr3jnIZo97YAofNCNMPy" autocomplete="off">
                                                                        <input type="hidden" name="dynamic_type" id="dynamic_type">
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <div class="crancy-ptabs__separate">
                                                                                    <div class="crancy-ptabs__form-main">
                                                                                        <div class="crancy__item-group">
                                                                                            <h3 class="crancy__item-group__title mb-4" id="dynamic_title">Dynamic Page</h3>
                                                                                            <div class="crancy__item-form--group">
                                                                                                <div class="row">
                                                                                                    <div class="col-12">
                                                                                                        <div class="crancy__item-form--group mg-top-form-20">
                                                                                                            <label class="crancy__item-label">SEO Title</label>
                                                                                                            <input class="crancy__item-input" placeholder="Enter Seo Title" type="text" name="dynamic_title" id="dynamic_seo_title">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-12">
                                                                                                        <div class="crancy__item-form--group mg-top-form-20">
                                                                                                            <label class="crancy__item-label">SEO Description</label>
                                                                                                            <textarea class="crancy__item-input crancy__item-textarea" placeholder="Enter a short and relevant description for search engines." name="dynamic_content" id="dynamic_content"></textarea>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="mg-top-40 mt-4">
                                                                                                <button class="crancy-btn" name="dynamic" type="submit">Update</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/moment.js"></script>
    <script src="assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="assets/js/feather.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/plugins/select2/js/select2.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        $(document).ready(function() {
            // Handle sidebar link clicks
            document.querySelectorAll('.crancy-psidebar__list a[data-bs-toggle="list"]').forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const target = this.getAttribute('href');
                    document.querySelectorAll('.tab-pane').forEach(section => {
                        section.classList.remove('active', 'show');
                    });
                    document.querySelector(target).classList.add('active', 'show');
                    document.querySelectorAll('.crancy-psidebar__list a').forEach(a => {
                        a.classList.remove('active');
                    });
                    this.classList.add('active');
                });
            });

            // Handle dynamic link clicks (services, blogs, courses)
            document.querySelectorAll('.dynamic-link').forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const type = this.getAttribute('data-type');
                    const title = this.getAttribute('data-title');

                    // Update dynamic tab
                    document.getElementById('dynamic_type').value = type;
                    document.getElementById('dynamic_title').textContent = title + ' Page';
                    document.getElementById('dynamic_seo_title').value = '';
                    document.getElementById('dynamic_content').value = '';

                    // Fetch SEO data via AJAX
                    $.ajax({
                        url: 'fetch_seo_data.php',
                        method: 'POST',
                        data: { type: type },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                document.getElementById('dynamic_seo_title').value = response.data.seo_title || '';
                                document.getElementById('dynamic_content').value = response.data.seo_description || '';
                            }
                        },
                        error: function() {
                            console.error('Error fetching SEO data.');
                        }
                    });

                    // Show dynamic tab
                    document.querySelectorAll('.tab-pane').forEach(section => {
                        section.classList.remove('active', 'show');
                    });
                    document.querySelector('#id_dynamic').classList.add('active', 'show');
                    document.querySelectorAll('.crancy-psidebar__list a').forEach(a => {
                        a.classList.remove('active');
                    });
                    this.classList.add('active');
                });
            });

            // Hide success message after 4 seconds
            const messageBox = document.getElementById('messageBox');
            if (messageBox) {
                setTimeout(() => {
                    messageBox.style.display = 'none';
                }, 6000);
            }
        });
    </script>
</body>
</html>