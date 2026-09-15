<?php

require_once('admin/db/config.php');
require_once('fetch-all.php');

$page_seo_type = 'about';

// 2. Fetch the SEO data
$seo = get_seo_data($db, $page_seo_type);

?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <title><?php echo htmlspecialchars($seo['title']); ?></title>
    <meta name="keywords" content="<?php echo htmlspecialchars($seo['keywords']); ?>">
    <meta name="description" content="<?php echo htmlspecialchars($seo['description']); ?>">
    <!-- Favicon Icon -->
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($favicon) ?>">
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
    // preloader
    require_once('includes/preloader.php');
    // header
    require_once('includes/header.php');
    ?>

    <!-- Page Header Section Start -->
    <div class="page-header parallaxie" style="background: url('<?= htmlspecialchars($bannerImage) ?>') no-repeat;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Page Header Box Start -->
                    <div class="page-header-box">
                        <h1 class="text-anime-style-3" data-cursor="-opaque">Terms & Conditions</h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Terms & Conditions</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- Page Header Box End -->
                </div>
            </div>
        </div>
    </div>
    <div class="our-speaker-metal">
        <div class="container">

            <div class="row section-row align-items-center">

                <div class="col-xl-12">

                    <!-- Section Title Start -->
                    <div class="section-title">

                        <h3 class="wow fadeInUp">
                            TERMS AND CONDITIONS
                        </h3>

                        <h2 class="text-anime-style-3"
                            data-cursor="-opaque">
                            The people behind every successful Eventful experience
                        </h2>

                    </div>
                    <!-- Section Title End -->

                </div>


                <div class="col-xl-12">

                    <!-- Section Content Button Start -->
                    <div class="section-content-btn">

                        <!-- Section Title Content Start -->
                        <div class="section-title-content wow fadeInUp"
                            data-wow-delay="0.2s">

                            <p>
                                Our experienced team brings together creativity,
                                planning and execution to make every event seamless.
                                From the first idea to the final execution, we work
                                together to create experiences that leave a lasting
                                impression.
                            </p>

                        </div>
                        <!-- Section Title Content End -->




                    </div>
                    <!-- Section Content Button End -->

                </div>

            </div>




        </div>
    </div>

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