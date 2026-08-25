<?php
require_once('admin/db/config.php');

$blogPosts = [];
$stmtFetchBlogs = $db->prepare("SELECT * FROM blog WHERE status = 1 ORDER BY idblog DESC");
$stmtFetchBlogs->execute();
$blogPosts = $stmtFetchBlogs->get_result()->fetch_all(MYSQLI_ASSOC);

$blogCategories = [];
$stmtFetchCats = $db->prepare("SELECT * FROM blog_category WHERE status = 1 ORDER BY idblog_category ASC");
$stmtFetchCats->execute();
$blogCategories = $stmtFetchCats->get_result()->fetch_all(MYSQLI_ASSOC);

$fallbackBlogs = [
    ['idblog' => '1', 'title' => 'How to Plan a Successful Medical Conference from Start to Finish', 'image' => 'images/post-1.jpg', 'user' => 'TMC Team', 'date' => 'Aug 15, 2025', 'category_id' => 'Medical Events', 'slug' => 'how-to-plan-a-successful-medical-conference'],
    ['idblog' => '2', 'title' => 'Top Trends in Corporate Event Management for 2025', 'image' => 'images/post-2.jpg', 'user' => 'TMC Team', 'date' => 'Jul 20, 2025', 'category_id' => 'Corporate Events', 'slug' => 'top-trends-in-corporate-event-management'],
    ['idblog' => '3', 'title' => 'Live Surgery Broadcasts: Bringing Real-Time Medical Learning to Global Audiences', 'image' => 'images/post-3.jpg', 'user' => 'TMC Team', 'date' => 'Jun 10, 2025', 'category_id' => 'Medical Events', 'slug' => 'live-surgery-broadcasts'],
    ['idblog' => '4', 'title' => 'Seamless Event Logistics: The Backbone of Every Successful Conference', 'image' => 'images/post-4.jpg', 'user' => 'TMC Team', 'date' => 'May 05, 2025', 'category_id' => 'Event Logistics', 'slug' => 'seamless-event-logistics'],
    ['idblog' => '5', 'title' => 'Audio Visual Solutions That Transform the Event Experience', 'image' => 'images/post-5.jpg', 'user' => 'TMC Team', 'date' => 'Apr 12, 2025', 'category_id' => 'AV Solutions', 'slug' => 'audio-visual-solutions'],
    ['idblog' => '6', 'title' => 'Hybrid and Virtual Events: The Future of Global Conferencing', 'image' => 'images/post-6.jpg', 'user' => 'TMC Team', 'date' => 'Mar 25, 2025', 'category_id' => 'Virtual Events', 'slug' => 'hybrid-and-virtual-events'],
];

$displayBlogs = !empty($blogPosts) ? $blogPosts : $fallbackBlogs;

$activeCategory = isset($_GET['category']) ? $_GET['category'] : '';
if ($activeCategory !== '') {
    $filteredBlogs = array_filter($displayBlogs, function ($item) use ($activeCategory) {
        return $item['category_id'] === $activeCategory;
    });
    $filteredBlogs = array_values($filteredBlogs);
} else {
    $filteredBlogs = $displayBlogs;
}

$allCategories = [];
if (!empty($blogPosts) && !empty($blogCategories)) {
    foreach ($blogCategories as $cat) {
        $allCategories[] = $cat['category_name'];
    }
} else {
    $catSet = [];
    foreach ($displayBlogs as $item) {
        if (!empty($item['category_id']) && !in_array($item['category_id'], $catSet)) {
            $catSet[] = $item['category_id'];
            $allCategories[] = $item['category_id'];
        }
    }
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
    <title>Our Blogs - Tee Mac Corporation</title>
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

    <!-- Page Blog Section Start -->
    <div class="page-blog">
        <div class="container">
            <!-- Category Filter Tabs -->
            <div class="row mb-4">
                <div class="col-lg-12 text-center wow fadeInUp">
                    <div class="blog-category-tabs d-flex flex-wrap justify-content-center gap-2 mb-4">
                        <a href="our-blogs.php" class="btn <?php echo $activeCategory === '' ? 'btn-default' : 'btn-outline-default'; ?>">All</a>
                        <?php foreach ($allCategories as $catName): ?>
                            <a href="our-blogs.php?category=<?php echo urlencode($catName); ?>"
                               class="btn <?php echo $activeCategory === $catName ? 'btn-default' : 'btn-outline-default'; ?>">
                                <?php echo htmlspecialchars($catName); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php if (!empty($filteredBlogs)): ?>
                    <?php foreach ($filteredBlogs as $index => $blog):
                        $delay = ($index % 3) * 0.2;
                        $delayStr = $delay > 0 ? number_format($delay, 1) . 's' : '0s';
                        $imagePath = !empty($blog['image']) ? $blog['image'] : 'images/post-' . (($index % 6) + 1) . '.jpg';
                        if ($imagePath !== 'images/post-' . (($index % 6) + 1) . '.jpg' && !file_exists($imagePath)) {
                            $imagePath = 'images/post-' . (($index % 6) + 1) . '.jpg';
                        }
                        $blogId = $blog['idblog'] ?? ($index + 1);
                        $blogTitle = $blog['title'] ?? 'Blog Post';
                        $blogUser = $blog['user'] ?? 'TMC Team';
                        $blogDate = $blog['date'] ?? '';
                        $blogSlug = $blog['slug'] ?? '';
                    ?>
                <div class="col-xl-4 col-md-6">
                    <!-- Post Item Start -->
                    <div class="post-item wow fadeInUp"<?php if ($delayStr !== '0s'): ?> data-wow-delay="<?php echo $delayStr; ?>"<?php endif; ?>>
                        <div class="post-featured-image">
                            <a href="blog-details.php?id=<?php echo htmlspecialchars($blogId); ?>" data-cursor-text="View">
                                <figure class="image-anime">
                                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($blogTitle); ?>">
                                </figure>
                            </a>
                        </div>
                        <div class="post-item-body">
                            <div class="post-item-body-content">
                                <div class="post-item-meta">
                                    <ul>
                                        <li><img src="images/icon-author.svg" alt=""><?php echo htmlspecialchars($blogUser); ?></li>
                                    </ul>
                                </div>
                                <div class="post-item-content">
                                    <h2><a href="blog-details.php?id=<?php echo htmlspecialchars($blogId); ?>"><?php echo htmlspecialchars($blogTitle); ?></a></h2>
                                </div>
                            </div>
                            <div class="post-item-btn">
                                <a href="blog-details.php?id=<?php echo htmlspecialchars($blogId); ?>" class="readmore-btn">read more</a>
                            </div>
                        </div>
                    </div>
                    <!-- Post Item End -->
                </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No blog posts found in this category.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Page Blog Section End -->

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
</body>

</html>