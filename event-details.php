<?php
require_once("admin/db/config.php");
require_once("fetch-all.php");


// 1. Get slug or id from the URL
$eventSlug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$eventDetails = null;

// 2. Fetch by Slug (Primary)
if (!empty($eventSlug)) {
    $stmt = $db->prepare("SELECT * FROM event WHERE slug = ? AND status = 1 LIMIT 1");
    $stmt->bind_param("s", $eventSlug);
    $stmt->execute();
    $eventDetails = $stmt->get_result()->fetch_assoc();
} 
// 3. Fallback: Fetch by ID
elseif ($eventId > 0) {
    $stmt = $db->prepare("SELECT * FROM event WHERE idevent = ? AND status = 1 LIMIT 1");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $eventDetails = $stmt->get_result()->fetch_assoc();
}

// 4. Redirect to events page if event is not found or inactive
if (!$eventDetails) {
    header("Location: our-events.php");
    exit;
}

// 5. Prepare image path
$detailImagePath = !empty($eventDetails['image']) ? 'admin/' . $eventDetails['image'] : 'images/event-default.jpg';
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
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png">
    <!-- Google Fonts Css-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="../css2?family=Onest:wght@100..900&display=swap" rel="stylesheet">

    <!-- Bootstrap Css -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <!-- SlickNav Css -->
    <link href="css/slicknav.min.css" rel="stylesheet">
    <!-- Swiper Css -->
    <link rel="stylesheet" href="css/swiper-bundle.min.css">
    <!-- Font Awesome Icon Css-->
    <link href="css/all.min.css" rel="stylesheet" media="screen">
    <!-- Animated Css -->
    <link href="css/animate.css" rel="stylesheet">
    <!-- Magnific Popup Core Css File -->
    <link rel="stylesheet" href="css/magnific-popup.css">
    <!-- Mouse Cursor Css File -->
    <link rel="stylesheet" href="css/mousecursor.css">
    <!-- Main Custom Css -->
    <link href="css/custom.css" rel="stylesheet" media="screen">
</head>

<body>
    <?php

    require_once('includes/preloader.php');

    require_once('includes/header.php');
    ?>


   <!-- Page Header Section Start -->
<div class="page-header parallaxie" style="background: url('<?= htmlspecialchars($bannerImage) ?>') no-repeat;">
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
                                <span><img src="images/icon-category-list-1.svg" alt="Date"></span> 
                                <?= !empty($eventDetails['date']) ? date('d M Y', strtotime($eventDetails['date'])) : 'TBA' ?>
                            </li>
                            <li>
                                <span><img src="images/icon-category-list-2.svg" alt="Time"></span>
                                <?= htmlspecialchars($eventDetails['time'] ?? 'TBA') ?>
                            </li>
                            <li>
                                <span><img src="images/icon-category-list-3.svg" alt="Category"></span>
                                <?= htmlspecialchars($eventDetails['category'] ?? 'General') ?>
                            </li>
                            <li>
                                <span><img src="images/icon-category-list-4.svg" alt="Organizer"></span>
                                <?= htmlspecialchars($eventDetails['organizer'] ?? 'N/A') ?>
                            </li>
                            <li>
                                <span><img src="images/icon-category-list-5.svg" alt="Location"></span>
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
                            <img src="<?= htmlspecialchars($detailImagePath) ?>" alt="<?= htmlspecialchars($eventDetails['title']) ?>">
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
    <script src="js/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap js file -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Validator js file -->
    <script src="js/validator.min.js"></script>
    <!-- SlickNav js file -->
    <script src="js/jquery.slicknav.js"></script>
    <!-- Swiper js file -->
    <script src="js/swiper-bundle.min.js"></script>
    <!-- Counter js file -->
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <!-- Magnific js file -->
    <script src="js/jquery.magnific-popup.min.js"></script>
    <!-- SmoothScroll -->
    <script src="js/SmoothScroll.js"></script>
    <!-- Parallax js -->
    <script src="js/parallaxie.js"></script>
    <!-- MagicCursor js file -->
    <script src="js/gsap.min.js"></script>
    <script src="js/magiccursor.js"></script>
    <!-- Text Effect js file -->
    <script src="js/SplitText.min.js"></script>
    <script src="js/ScrollTrigger.min.js"></script>
    <!-- YTPlayer js File -->
    <script src="js/jquery.mb.YTPlayer.min.js"></script>
    <!-- Wow js file -->
    <script src="js/wow.min.js"></script>
    <!-- Main Custom js file -->
    <script src="js/function.js"></script>
    <script src="../assets/js/theme-panel-dynamic.js"></script>
</body>

</html>