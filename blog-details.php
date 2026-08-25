<?php
require_once('admin/db/config.php');

$blogId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$blog = null;

if ($blogId > 0) {
    $stmtBlog = $db->prepare("SELECT * FROM blog WHERE idblog = ? AND status = 1");
    $stmtBlog->bind_param("i", $blogId);
    $stmtBlog->execute();
    $resultBlog = $stmtBlog->get_result();
    $blog = $resultBlog->fetch_assoc();
}

$fallbackBlog = [
    'title' => 'How to Plan a Successful Medical Conference from Start to Finish',
    'content' => '<h3>Overview</h3><p>Medical conferences demand precision, expertise, and flawless execution. At Tee Mac Corporation, we have spent over four years perfecting the art of organizing medical events that bring together healthcare professionals, researchers, and industry leaders from across the globe. A well-planned medical conference does more than gather attendees — it creates a platform for knowledge exchange, professional networking, and advancing the future of medicine.</p><p>From venue selection and speaker coordination to live surgery broadcasts and post-event follow-ups, every element must work in harmony. This guide walks you through the essential steps to plan a medical conference that leaves a lasting impact on every participant.</p><h3>Benefits</h3><ul><li>Provides a structured roadmap to plan and execute medical conferences without missing critical details.</li><li>Helps organizers choose the right venue, technology partners, and logistics providers for seamless execution.</li><li>Ensures effective speaker management and session scheduling for maximum attendee engagement.</li><li>Integrates live surgery broadcasts and AV solutions to elevate the learning experience for remote and in-person attendees.</li><li>Maximizes sponsorship ROI through strategic branding opportunities and sponsor visibility throughout the event.</li><li>Creates lasting professional connections and knowledge-sharing opportunities that extend beyond the event day.</li></ul>',
    'date' => 'Aug 15, 2025',
    'user' => 'TMC Team',
    'category_id' => 'Medical Events',
    'image' => 'images/post-1.jpg',
];

$displayBlog = $blog ?? $fallbackBlog;
$blogTitle = $displayBlog['title'] ?? $fallbackBlog['title'];
$blogContent = $displayBlog['content'] ?? $fallbackBlog['content'];
$blogDate = $displayBlog['date'] ?? $fallbackBlog['date'];
$blogUser = $displayBlog['user'] ?? $fallbackBlog['user'];
$blogCategory = $displayBlog['category_id'] ?? $fallbackBlog['category_id'];
$blogImage = $displayBlog['image'] ?? $fallbackBlog['image'];

if ($blogImage && $blogImage !== 'images/post-1.jpg' && !file_exists($blogImage)) {
    $blogImage = 'images/post-1.jpg';
}
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
    <title>Blog Details | Tee Mac Corporation</title>
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
    <div class="page-header parallaxie">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Page Header Box Start -->
                    <div class="page-header-box">
                        <h1 class="text-anime-style-3" data-cursor="-opaque">Our Blogs</h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Blog</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- Page Header Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header Section End -->
    <!-- Blog Details Start -->
    <section class="page-single-post" style="padding-top: 60px; padding-bottom: 60px;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">

                    <?php if ($blogImage && $blogImage !== 'images/post-1.jpg'): ?>
                    <div class="blog-detail-image wow fadeInUp" style="margin-bottom: 30px; border-radius: 12px; overflow: hidden;">
                        <img src="<?php echo htmlspecialchars($blogImage); ?>" alt="<?php echo htmlspecialchars($blogTitle); ?>" style="width: 100%; height: auto;">
                    </div>
                    <?php endif; ?>

                    <!-- Blog Title -->
                    <div class="blog-detail-title wow fadeInUp" style="margin-bottom: 10px;">
                        <h1 style="font-size: 2.2rem; font-weight: 700; color: var(--accent-color);"><?php echo htmlspecialchars($blogTitle); ?></h1>
                    </div>

                    <!-- Blog Meta (Date, Published By, Category) -->
                    <div class="blog-detail-meta wow fadeInUp" style="display: flex; gap: 25px; flex-wrap: wrap; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e5e5e5;">
                        <span style="display: inline-flex; align-items: center; gap: 8px; color: #666; font-size: 0.95rem;">
                            <i class="fa-regular fa-calendar" style="color: var(--accent-color);"></i> <?php echo htmlspecialchars($blogDate); ?>
                        </span>
                        <span style="display: inline-flex; align-items: center; gap: 8px; color: #666; font-size: 0.95rem;">
                            <i class="fa-regular fa-user" style="color: var(--accent-color);"></i> <?php echo htmlspecialchars($blogUser); ?>
                        </span>
                        <span style="display: inline-flex; align-items: center; gap: 8px; color: #666; font-size: 0.95rem;">
                            <i class="fa-solid fa-folder-open" style="color: var(--accent-color);"></i> <?php echo htmlspecialchars($blogCategory); ?>
                        </span>
                    </div>

                    <!-- Blog Content -->
                    <div class="blog-detail-section wow fadeInUp" style="margin-bottom: 40px; font-size: 1.05rem; line-height: 1.8; color: #444;">
                        <?php echo $blogContent; ?>
                    </div>

                    <!-- Back to Blogs -->
                    <div class="blog-detail-back wow fadeInUp" data-wow-delay="0.4s" style="text-align: center; margin-top: 40px; padding-top: 30px; border-top: 1px solid #e5e5e5;">
                        <a href="our-blogs.php" class="btn-default" style="display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-arrow-left"></i> Back to Blogs
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- Blog Details End -->

    <!-- Footer Section Start -->
    <?php
    require_once('includes/footer.php');
    ?>
    <!-- Footer Section End -->

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
</body>

</html>