<?php
require_once('fetch-all.php');
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
                        <h1 class="text-anime-style-3" data-cursor="-opaque">Our Events</h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Our Events</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- Page Header Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header Section End -->

    <!-- Page Schedule Section Start -->
    <div class="page-Our Events">
        <div class="container">
            <div class="row">
                <!-- Page Schedule Box Start -->
                <div class="page-schedule-box tab-content wow fadeInUp" data-wow-delay="0.2s">
                   

        
                      <div class="row mt-5">
    <?php foreach ($displaySchedule as $index => $item): ?>
        <?php
        // Clean description to prevent HTML tags from showing
        $cleanDesc = nl2br(strip_tags($item['description']));
        
        // Resolve image path (fallback to default if empty)
        $imagePath = !empty($item['image']) ? 'admin/' . $item['image'] : 'images/event-default.jpg';

        
       // Resolve link (use slug if available, otherwise fallback to ID)
$itemLink = !empty($item['slug']) ? 'event-details.php?slug=' . urlencode($item['slug']) : 'event-details.php?id=' . $item['idevent'];
        ?>

        <div class="col-xl-4 col-md-6">
            <!-- Schedule Item Start -->
            <div class="schedule-item wow fadeInUp" data-wow-delay="<?= ($index * 0.1) ?>s">
                
                <!-- Schedule Item Image Start -->
                <div class="schedule-item-image">
                    <a href="<?= htmlspecialchars($itemLink) ?>" data-cursor-text="View">
                        <figure>
                            <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                        </figure>
                    </a>
                </div>
                <!-- Schedule Item Image End -->

                <!-- Schedule Item Body Start -->
                <div class="schedule-item-body">
                    <div class="schedule-item-meta">
                        <ul>
                            <?php if (!empty($item['time'])): ?>
                                <li>
                                    <img src="images/icon-clock.svg" alt="Time">
                                    <?= htmlspecialchars($item['time']) ?>
                                </li>
                            <?php endif; ?>
                            
                            <?php if (!empty($item['location'])): ?>
                                <li>
                                    <img src="images/icon-location-accent.svg" alt="Location">
                                    <?= htmlspecialchars($item['location']) ?>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <div class="schedule-item-body-content">
                        <h3>
                            <a href="<?= htmlspecialchars($itemLink) ?>">
                                <?= htmlspecialchars($item['title']) ?>
                            </a>
                        </h3>
                        <p><?= $cleanDesc ?></p>
                    </div>
                    
                    <div class="schedule-item-btn">
                        <a href="<?= htmlspecialchars($itemLink) ?>" class="btn-default">View Details</a>
                    </div>
                </div>
                <!-- Schedule Item Body End -->                            
            </div>
            <!-- Schedule Item End -->
        </div>

    <?php endforeach; ?>
</div>                     
                   

                  
                </div>
            </div>
        </div>
    </div>
    <!-- Page Schedule Section End -->

    

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