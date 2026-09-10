<?php
require_once('admin/db/config.php');
require_once('fetch-all.php');

$teamMembers = [];
$stmtFetchTeam = $db->prepare("SELECT * FROM team_members WHERE status = 1 ORDER BY idteam_members ASC");
$stmtFetchTeam->execute();
$teamMembers = $stmtFetchTeam->get_result()->fetch_all(MYSQLI_ASSOC);

$fallbackTeam = [];
for ($i = 1; $i <= 8; $i++) {
    $fallbackTeam[] = [
        'member_name' => ['Ryan Cooper', 'Brooklyn Simmons', 'Marvin McKinney', 'Ronald Richards'][($i - 1) % 4],
        'role' => 'AI Researcher',
        'profile_picture' => 'images/speaker-item-image-' . (($i - 1) % 4 + 1) . '-metal.jpg',
        'linkedin' => '#', 'twitter' => '#', 'facebook' => '#',
    ];
}

$displayTeam = !empty($teamMembers) ? $teamMembers : $fallbackTeam;
$delays = ['0s', '0.2s', '0.4s', '0.6s'];
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="TMCport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Awaiken">
    <!-- Page Title -->
    <title>Our Team - Tee Mac Corporation</title>
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
                        <h1 class="text-anime-style-3" data-cursor="-opaque">Our Team</h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Team</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- Page Header Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header Section End -->

    <!-- Page Speaker Start -->
    <div class="page-speaker">
        <div class="container">
            <div class="row">
                <?php foreach ($displayTeam as $index => $member):
                    $delay = $delays[$index % count($delays)];
                    $imagePath = !empty($member['profile_picture']) ? 'admin/' . $member['profile_picture'] : 'images/speaker-item-image-' . (($index % 4) + 1) . '-metal.jpg';
                    if (!file_exists($imagePath)) {
                        $imagePath = 'images/speaker-item-image-' . (($index % 4) + 1) . '-metal.jpg';
                    }
                    $socialLinks = [
                        ['url' => $member['linkedin'] ?? '#', 'icon' => 'fa-linkedin-in', 'label' => 'LinkedIn'],
                        ['url' => $member['twitter'] ?? '#', 'icon' => 'fa-x-twitter', 'label' => 'X'],
                        ['url' => $member['facebook'] ?? '#', 'icon' => 'fa-facebook-f', 'label' => 'Facebook'],
                    ];
                ?>
                <div class="col-xl-3 col-md-6">
                    <!-- Speaker Item Start -->
                    <div class="speaker-item-metal wow fadeInUp"<?php if ($delay !== '0s'): ?> data-wow-delay="<?php echo $delay; ?>"<?php endif; ?>>
                        <!-- Speaker Item Image Start -->
                        <div class="speaker-item-image-metal">
                            <a href="#" data-cursor-text="TMC">
                                <figure class="image-anime">
                                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($member['member_name']); ?>">
                                </figure>
                            </a>
                        </div>
                        <!-- Speaker Item Image End -->

                        <!-- Speaker Item Body Start -->
                        <div class="speaker-item-body-metal">
                            <!-- Speaker Item Content Start -->
                            <div class="speaker-item-content-metal">
                                <h3><a href="#"><?php echo htmlspecialchars($member['member_name']); ?></a></h3>
                                <p><?php echo htmlspecialchars($member['role']); ?></p>
                            </div>
                            <!-- Speaker Item Content End -->

                            <!-- Speaker Social List Start -->
                            <div class="speaker-social-list-metal">
                                <ul>
                                    <?php foreach ($socialLinks as $social): ?>
                                    <li><a href="<?php echo htmlspecialchars($social['url']); ?>" aria-label="<?php echo $social['label']; ?>"><i class="fa-brands <?php echo $social['icon']; ?>"></i></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <!-- Speaker Social List End -->
                        </div>
                        <!-- Speaker Item Body End -->
                    </div>
                    <!-- Speaker Item End -->
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <!-- Page Speaker End -->

    <?php require_once('includes/footer.php'); ?>

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
