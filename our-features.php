<?php
require_once('admin/db/config.php');
require_once('fetch-all.php');


// Fetch active services from DB
$homeServices = [];
$stmtHomeServices = $db->prepare("SELECT * FROM services WHERE status = 1 ORDER BY idservices ASC");
$stmtHomeServices->execute();
$homeServices = $stmtHomeServices->get_result()->fetch_all(MYSQLI_ASSOC);

// Fallback services if DB returns nothing
$fallbackHomeServices = [
    [
        'idservices'  => '1',
        'title'       => 'Medical Events',
        'description' => 'From medical conferences and seminars to professional gatherings, we create well-organised experiences for the healthcare community.',
        'icon'        => 'images/icon-service-1-metal.svg'
    ],
    [
        'idservices'  => '2',
        'title'       => 'Conferences & Workshops',
        'description' => 'We manage conferences, workshops and seminars with thoughtful planning, engaging formats and seamless on-ground execution.',
        'icon'        => 'images/icon-service-2-metal.svg'
    ],
    [
        'idservices'  => '3',
        'title'       => 'Corporate Events',
        'description' => 'From corporate gatherings to brand-focused experiences, we help businesses create events that connect teams, audiences and ideas.',
        'icon'        => 'images/icon-service-3-metal.svg'
    ],
    [
        'idservices'  => '4',
        'title'       => 'Event Logistics',
        'description' => 'We coordinate venues, registrations, hospitality, transportation and on-ground requirements to keep every event running smoothly.',
        'icon'        => 'images/icon-service-4-metal.svg'
    ],
    [
        'idservices'  => '5',
        'title'       => 'Audio Visual Solutions',
        'description' => 'From sound and lighting to screens and event production, we provide the technical support needed for a powerful event experience.',
        'icon'        => 'images/icon-service-5-metal.svg'
    ],
];

// Use DB results if available, otherwise fallback
$displayHomeServices = !empty($homeServices) ? $homeServices : $fallbackHomeServices;



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
    <title>Our Features - Tee Mac Corporation</title>
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
                        <h1 class="text-anime-style-3" data-cursor="-opaque">Our Features</h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Features</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- Page Header Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header Section End -->

  <!-- Page Features Start -->
<div class="page-features">
    <div class="container">
        <div class="row feature-item-list">

            <?php foreach ($displayHomeServices as $index => $service): ?>
                <?php
                // 1. Calculate animation delay dynamically (0s, 0.2s, 0.4s, 0.6s, etc.)
                $delay = ($index * 0.2) . 's';

                // 2. Resolve icon: use DB icon if exists, otherwise fallback to feature-item pattern
                $iconPath = !empty($service['icon']) 
                    ? $service['icon'] 
                    : 'images/icon-feature-item-' . ($index + 1) . '.svg';

                // 3. Resolve link: use slug if available, otherwise fallback to ID param
                $serviceLink = !empty($service['slug']) 
                    ? 'service/' . $service['slug'] 
                    : 'our-features.php?id=' . $service['idservices'];

                // 4. Clean description: strips HTML tags and converts newlines to <br>
                $cleanDesc = nl2br(strip_tags($service['description']));
                
                // 5. Optional: Replicate the 'active' class on the 2nd item (index 1) from your original design
                $activeClass = ($index === 1) ? 'active ' : '';
                ?>

                <!-- Feature Item <?= $index + 1 ?> -->
                <div class="col-xl-3 col-md-6">
                    <div class="feature-item <?= $activeClass ?>wow fadeInUp" data-wow-delay="<?= $delay ?>">
                        
                        <div class="icon-box">
                            <img src="<?= htmlspecialchars($iconPath) ?>" 
                                 alt="<?= htmlspecialchars($service['title']) ?>">
                        </div>

                        <div class="feature-item-body">
                            <div class="feature-item-content">
                                <h3><?= htmlspecialchars($service['title']) ?></h3>
                                <p><?= $cleanDesc ?></p>
                            </div>

                            <div class="feature-item-btn">
                                <a href="<?= htmlspecialchars($serviceLink) ?>" class="readmore-btn">
                                    Contact Now
                                </a>
                            </div>
                        </div>
                        
                    </div>
                </div>

            <?php endforeach; ?>

        </div>
    </div>
</div>
<!-- Page Features End -->




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