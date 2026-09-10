<?php
require_once('admin/db/config.php');
require_once('fetch-all.php');

$galleryImages = [];
$stmtFetchGallery = $db->prepare("SELECT * FROM gallery WHERE status = 1 ORDER BY idgallery ASC");
$stmtFetchGallery->execute();
$galleryImages = $stmtFetchGallery->get_result()->fetch_all(MYSQLI_ASSOC);

$fallbackGallery = [
    ['image' => 'images/gallery-1.jpg'],
    ['image' => 'images/gallery-2.jpg'],
    ['image' => 'images/gallery-3.jpg'],
    ['image' => 'images/gallery-4.jpg'],
    ['image' => 'images/gallery-5.jpg'],
    ['image' => 'images/gallery-6.jpg'],
    ['image' => 'images/gallery-7.jpg'],
    ['image' => 'images/gallery-8.jpg'],
    ['image' => 'images/gallery-9.jpg'],
];

$displayGallery = !empty($galleryImages) ? $galleryImages : $fallbackGallery;
$delays = ['0s', '0.2s', '0.4s', '0.6s', '0.8s', '1s', '1.2s', '1.4s', '1.6s'];
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
    <title>Our Gallery - Tee mac Corporation</title>
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
                        <h1 class="text-anime-style-3" data-cursor="-opaque">Our Gallery</h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Gallery</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- Page Header Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header Section End -->

    <!-- Photo Gallery Start -->
    <div class="page-gallery">
        <div class="container">
            <!-- gallery section start -->
            <div class="row gallery-items page-gallery-box">
                <?php foreach ($displayGallery as $index => $item):
                    $delay = $delays[$index % count($delays)];
                    $imagePath = !empty($item['image']) ? $item['image'] : 'images/gallery-' . (($index % 9) + 1) . '.jpg';
                    if (strpos($imagePath, 'images/') !== 0 && !file_exists($imagePath)) {
                        $imagePath = 'images/gallery-' . (($index % 9) + 1) . '.jpg';
                    } elseif (strpos($imagePath, 'gallery/') === 0 && !file_exists('admin/' . $imagePath)) {
                        $imagePath = 'images/gallery-' . (($index % 9) + 1) . '.jpg';
                    }
                ?>
                <div class="col-lg-4 col-6">
                    <!-- Image Gallery start -->
                    <div class="photo-gallery wow fadeInUp"<?php if ($delay !== '0s'): ?> data-wow-delay="<?php echo $delay; ?>"<?php endif; ?>>
                        <a href="<?php echo htmlspecialchars($imagePath); ?>" data-cursor-text="View">
                            <figure class="image-anime">
                                <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Gallery Image">
                            </figure>
                        </a>
                    </div>
                    <!-- Image Gallery end -->
                </div>
                <?php endforeach; ?>
            </div>
            <!-- gallery section end -->
        </div>
    </div>
    <!-- Photo Gallery End -->

    <?php
    require_once('includes/footer.php')
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