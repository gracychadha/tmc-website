<?php
require_once("admin/db/config.php");
require_once("fetch-all.php");

// Get slug from PATH_INFO or normal GET parameter
$eventSlug = '';

if (!empty($_GET['slug'])) {
    $eventSlug = trim($_GET['slug']);
} elseif (!empty($_SERVER['PATH_INFO'])) {
    $eventSlug = trim($_SERVER['PATH_INFO'], '/');
}

$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$eventDetails = null;

// Fetch by slug
if (!empty($eventSlug)) {

    $stmt = $db->prepare("
        SELECT *
        FROM event
        WHERE slug = ?
        AND status = 1
        LIMIT 1
    ");

    $stmt->bind_param("s", $eventSlug);
    $stmt->execute();

    $eventDetails = $stmt->get_result()->fetch_assoc();
}

// Fallback: Fetch by ID
elseif ($eventId > 0) {

    $stmt = $db->prepare("
        SELECT *
        FROM event
        WHERE idevent = ?
        AND status = 1
        LIMIT 1
    ");

    $stmt->bind_param("i", $eventId);
    $stmt->execute();

    $eventDetails = $stmt->get_result()->fetch_assoc();
}

// Redirect if event not found
if (!$eventDetails) {
    header("Location: /tmc-website/our-events.php");
    exit;
}

// Image path
$detailImagePath = !empty($eventDetails['image'])
    ? 'admin/' . $eventDetails['image']
    : 'http://localhost/tmc-website/images/event-default.jpg';
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Awaiken">
    <!-- Page Title -->
    <title>Welcome to Tee Mac Corporation</title>
    <!-- Favicon Icon -->
    <link rel="shortcut icon" type="image/x-icon" href="http://localhost/tmc-website/images/favicon.png">
    <!-- Google Fonts Css-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="../css2?family=Onest:wght@100..900&display=swap" rel="stylesheet">

    <!-- Bootstrap Css -->
    <link href="http://localhost/tmc-website/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <!-- SlickNav Css -->
    <link href="http://localhost/tmc-website/css/slicknav.min.css" rel="stylesheet">
    <!-- Swiper Css -->
    <link rel="stylesheet" href="http://localhost/tmc-website/css/swiper-bundle.min.css">
    <!-- Font Awesome Icon Css-->
    <link href="http://localhost/tmc-website/css/all.min.css" rel="stylesheet" media="screen">
    <!-- Animated Css -->
    <link href="http://localhost/tmc-website/css/animate.css" rel="stylesheet">
    <!-- Magnific Popup Core Css File -->
    <link rel="stylesheet" href="http://localhost/tmc-website/css/magnific-popup.css">
    <!-- Mouse Cursor Css File -->
    <link rel="stylesheet" href="http://localhost/tmc-website/css/mousecursor.css">
    <!-- Main Custom Css -->
    <link href="http://localhost/tmc-website/css/custom.css" rel="stylesheet" media="screen">
    <style>
        .about-us-footer-metal {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 20px 30px;
            margin-top: 40px;
            justify-content: center;
        }
    </style>
</head>

<body>
    <?php

    require_once('includes/preloader.php');

    require_once('includes/header.php');
    ?>


    <!-- Page Header Section Start -->
    <div class="page-header parallaxie" style="background: url('http://localhost/tmc-website/<?= htmlspecialchars($bannerImage) ?>') no-repeat;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Page Header Box Start -->
                    <div class="page-header-box">
                        <h1 class="text-anime-style-3" data-cursor="-opaque"><?= htmlspecialchars($eventDetails['title']) ?></h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">home</a></li>
                                <li class="breadcrumb-item"><a href="our-events.php">Our Events</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($eventDetails['title']) ?></li>
                            </ol>
                        </nav>
                    </div>
                    <!-- Page Header Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header Section End -->

    <!-- Page Schedule Single Start -->
    <div class="page-schedule-single">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <!-- Page Single Sidebar Start -->
                    <div class="page-single-sidebar">
                        <!-- Page Category List Start -->
                        <div class="page-category-list wow fadeInUp">
                            <h3>Event Information</h3>
                            <ul>
                                <li>
                                    <span><img src="http://localhost/tmc-website/images/icon-category-list-1.svg" alt="Date"> Start Date: </span>
                                    <?= !empty($eventDetails['date']) ? date('d M Y', strtotime($eventDetails['date'])) : 'TBA' ?>
                                </li>
                                <li>
                                    <span><img src="http://localhost/tmc-website/images/icon-category-list-1.svg" alt="Date"> End Date: </span>
                                    <?= !empty($eventDetails['end_date']) ? date('d M Y', strtotime($eventDetails['end_date'])) : 'TBA' ?>
                                </li>


                                <li>
                                    <span><img src="http://localhost/tmc-website/images/icon-category-list-5.svg" alt="Location"> Location: </span>
                                    <?= htmlspecialchars($eventDetails['location'] ?? 'TBA') ?>
                                </li>


                            </ul>
                        </div>
                        <!-- Page Category List End -->
                    </div>
                    <!-- Page Single Sidebar End -->
                </div>

                <div class="col-lg-8">
                    <!-- Schedule Single Content Start -->
                    <div class="schedule-single-content">
                        <!-- Page Single image Start -->
                        <div class="page-single-image">
                            <figure class="image-anime reveal">
                                <img src="http://localhost/tmc-website/<?= htmlspecialchars($detailImagePath) ?>" alt="<?= htmlspecialchars($eventDetails['title']) ?>">
                            </figure>
                        </div>
                        <!-- Page Single image End -->

                        <!-- Schedule Entry Start -->
                        <div class="schedule-entry">
                            <div class="wow fadeInUp">
                                <!-- Output description. We allow HTML here for formatting on the details page -->
                                <?= $eventDetails['description'] ?>
                            </div>
                        </div>
                        <!-- Schedule Entry End -->
                        <!-- About Us Footer Start -->
                        <div class="about-us-footer-metal wow fadeInUp" data-wow-delay="0.8s">

                            <!-- About Us Btn Start -->
                            <div class="about-us-btn-metal">
                                <?php if (!empty($eventDetails['image_link'])): ?>
                                    <a href="<?= htmlspecialchars($eventDetails['image_link']) ?>" class="btn-default" target="_blank">
                                        Scientific Program
                                    </a>
                                <?php endif; ?>
                            </div>
                            <!-- About Us Btn End -->


                            <!-- About Email Box Start -->
                            <div class="about-email-box-metal">

                                <?php if (!empty($eventDetails['program_link'])): ?>
                                    <a href="<?= htmlspecialchars($eventDetails['program_link']) ?>" class="btn-default" target="_blank">
                                        Conference Memories
                                    </a>
                                <?php endif; ?>

                            </div>
                            <!-- About Email Box End -->

                        </div>
                        <!-- About Us Footer End -->
                    </div>
                    <!-- Schedule Single Content End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Schedule Single End -->

    <?php
    require_once('includes/footer.php');
    ?>
    <!-- Jquery Library File -->
    <script src="http://localhost/tmc-website/js/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap js file -->
    <script src="http://localhost/tmc-website/js/bootstrap.min.js"></script>
    <!-- Validator js file -->
    <script src="http://localhost/tmc-website/js/validator.min.js"></script>
    <!-- SlickNav js file -->
    <script src="http://localhost/tmc-website/js/jquery.slicknav.js"></script>
    <!-- Swiper js file -->
    <script src="http://localhost/tmc-website/js/swiper-bundle.min.js"></script>
    <!-- Counter js file -->
    <script src="http://localhost/tmc-website/js/jquery.waypoints.min.js"></script>
    <script src="http://localhost/tmc-website/js/jquery.counterup.min.js"></script>
    <!-- Magnific js file -->
    <script src="http://localhost/tmc-website/js/jquery.magnific-popup.min.js"></script>
    <!-- SmoothScroll -->
    <script src="http://localhost/tmc-website/js/SmoothScroll.js"></script>
    <!-- Parallax js -->
    <script src="http://localhost/tmc-website/js/parallaxie.js"></script>
    <!-- MagicCursor js file -->
    <script src="http://localhost/tmc-website/js/gsap.min.js"></script>
    <script src="http://localhost/tmc-website/js/magiccursor.js"></script>
    <!-- Text Effect js file -->
    <script src="http://localhost/tmc-website/js/SplitText.min.js"></script>
    <script src="http://localhost/tmc-website/js/ScrollTrigger.min.js"></script>
    <!-- YTPlayer js File -->
    <script src="http://localhost/tmc-website/js/jquery.mb.YTPlayer.min.js"></script>
    <!-- Wow js file -->
    <script src="http://localhost/tmc-website/js/wow.min.js"></script>
    <!-- Main Custom js file -->
    <script src="http://localhost/tmc-website/js/function.js"></script>
    <script src="../assets/http://localhost/tmc-website/js/theme-panel-dynamic.js"></script>
</body>

</html>