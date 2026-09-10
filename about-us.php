<?php

require_once('admin/db/config.php');
require_once('fetch-all.php');

$homeFaqs = [];
$stmtHomeFaqs = $db->prepare("SELECT * FROM faqs WHERE status = 1 ORDER BY faqs_id ASC LIMIT 5");
$stmtHomeFaqs->execute();
$homeFaqs = $stmtHomeFaqs->get_result()->fetch_all(MYSQLI_ASSOC);

$fallbackHomeFaqs = [
    ['question' => 'No FAQ added', 'answer' => 'No FAQ added yet. Please add FAQs from the admin panel.'],
];

$displayHomeFaqs = !empty($homeFaqs) ? $homeFaqs : $fallbackHomeFaqs;
?>
<?php
require_once('admin/db/config.php');
$homeFaqs = [];
$stmtHomeFaqs = $db->prepare("SELECT * FROM faqs WHERE status = 1 ORDER BY faqs_id ASC LIMIT 5");
$stmtHomeFaqs->execute();
$homeFaqs = $stmtHomeFaqs->get_result()->fetch_all(MYSQLI_ASSOC);
$displayHomeFaqs = !empty($homeFaqs) ? $homeFaqs : [
    ['question' => 'No FAQ added', 'answer' => 'No FAQ added yet. Please add FAQs from the admin panel.'],
];
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
    <title>About Us - Tee Mac Corporation</title>
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
                        <h1 class="text-anime-style-3" data-cursor="-opaque">About Us</h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">about us</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- Page Header Box End -->
                </div>
            </div>
        </div>
    </div>
      <!-- About Us Section Start -->
<div class="about-us-metal">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-xl-5">
                <!-- About Us Image Box Start -->
                <div class="about-us-image-box-metal wow fadeInUp" data-wow-delay="0.2s">

                    <!-- About Us Image Box-1 Start -->
                    <div class="about-us-image-box-1-metal">

                        <!-- About Us Image Start -->
                        <div class="about-us-image-metal">
                            <figure class="image-anime">
                                <img src="<?= htmlspecialchars($aboutImage1) ?>"
                                    alt="<?= htmlspecialchars($displayAboutMetal['sub_title']) ?>">
                            </figure>
                        </div>
                        <!-- About Us Image End -->

                        <!-- Contact Us Circle Start -->
                        <div class="contact-us-circle-metal">
                            <a href="contact-us.php">
                                <img src="images/contact-us-circle-metal.svg"
                                    alt="Contact Tee Mac">
                            </a>
                        </div>
                        <!-- Contact Us Circle End -->

                    </div>
                    <!-- About Us Image Box-1 End -->


                    <!-- About Us Image Box-2 Start -->
                    <div class="about-us-image-box-2-metal">

                        <!-- About Us Image Start -->
                        <div class="about-us-image-metal">
                            <figure class="image-anime">
                                <img src="<?= htmlspecialchars($aboutImage2) ?>"
                                    alt="Event Conference Management">
                            </figure>
                        </div>
                        <!-- About Us Image End -->

                    </div>
                    <!-- About Us Image Box-2 End -->

                </div>
                <!-- About Us Image Box End -->
            </div>


            <div class="col-xl-7">

                <!-- About us Content Start -->
                <div class="about-us-content-metal">

                    <!-- Section Title Start -->
                    <div class="section-title">

                        <h3 class="wow fadeInUp">
                            <?= htmlspecialchars($displayAboutMetal['sub_title']) ?>
                        </h3>

                        <h2 class="text-anime-style-3" data-cursor="-opaque">
                            <?= htmlspecialchars($displayAboutMetal['title']) ?>
                        </h2>

                        <p class="wow fadeInUp text-justify" data-wow-delay="0.2s">
                            <?= nl2br(htmlspecialchars(strip_tags($displayAboutMetal['content']))) ?>
                        </p>

                        <p class="wow fadeInUp text-justify" data-wow-delay="0.3s">
                            <?= nl2br(htmlspecialchars(strip_tags($displayAboutMetal['benefits']))) ?>
                        </p>

                    </div>
                    <!-- Section Title End -->


                    <!-- About Us Client Box Start -->
                    <div class="about-us-client-box-metal wow fadeInUp" data-wow-delay="0.6s">

                        <!-- About Client Content Start -->
                        <div class="about-client-content-metal">
                            <h3>
                                Delivering Experiences That Bring People Together
                            </h3>
                        </div>
                        <!-- About Client Content End -->


                        <!-- Satisfy Client Images Start -->
                        <div class="satisfy-client-images">

                            <div class="satisfy-client-image">
                                <figure class="image-anime">
                                    <img src="images/author-1.jpg" alt="Event Attendee">
                                </figure>
                            </div>

                            <div class="satisfy-client-image">
                                <figure class="image-anime">
                                    <img src="images/author-2.jpg" alt="Conference Attendee">
                                </figure>
                            </div>

                            <div class="satisfy-client-image">
                                <figure class="image-anime">
                                    <img src="images/author-3.jpg" alt="Corporate Event">
                                </figure>
                            </div>

                            <div class="satisfy-client-image">
                                <figure class="image-anime">
                                    <img src="images/author-4.jpg" alt="Event Experience">
                                </figure>
                            </div>

                            <div class="satisfy-client-image add-more">
                                <h3>
                                    <span class="counter">100</span>+
                                </h3>
                            </div>

                        </div>
                        <!-- Satisfy Client Images End -->

                    </div>
                    <!-- About Us Client Box End -->


                    <!-- About Us Footer Start -->
                    <div class="about-us-footer-metal wow fadeInUp" data-wow-delay="0.8s">

                        <!-- About Us Btn Start -->
                        <div class="about-us-btn-metal">
                            <a href="about-us.php" class="btn-default">
                                Discover Our Story
                            </a>
                        </div>
                        <!-- About Us Btn End -->


                        <!-- About Email Box Start -->
                        <div class="about-email-box-metal">

                            <div class="icon-box">
                                <img src="images/icon-email.svg" alt="Email">
                            </div>

                            <div class="about-email-box-content-metal">

                                <h3>
                                    Plan Your Next Event
                                </h3>

                                <p>
                                    <a href="mailto:info@teemaccorp.com" target="_blank">
                                        info@teemaccorp.com
                                    </a>
                                </p>

                            </div>

                        </div>
                        <!-- About Email Box End -->

                    </div>
                    <!-- About Us Footer End -->

                </div>
                <!-- About us Content End -->

            </div>

        </div>
    </div>
</div>
<!-- About Us Section End -->

    <!-- Our Approach Section Start -->
    <div class="our-approach dark-section">
        <div class="container">
            <div class="row section-row">
                <div class="col-lg-12">
                    <!-- Section Title Start -->
                    <div class="section-title section-title-center">
                        <h3 class="wow fadeInUp"><?= htmlspecialchars($displayApproach['title']) ?></h3>
                        <h2 class="text-anime-style-3" data-cursor="-opaque">
                            <?= htmlspecialchars($displayApproach['sub_title']) ?>
                        </h2>
                    </div>
                    <!-- Section Title End -->
                </div>
            </div>

            <div class="row">

                <!-- Approach Item 1 Start -->
                <div class="col-xl-4 col-md-6">
                    <div class="approach-item wow fadeInUp">

                        <div class="approach-item-image">
                            <figure>
                                <img src="<?= htmlspecialchars($approachImage1) ?>"
                                    alt="<?= htmlspecialchars($displayApproach['step1']) ?>">
                            </figure>
                        </div>

                        <div class="approach-item-body">
                            <div class="icon-box">
                                <img src="images/icon-approach-1.svg"
                                    alt="<?= htmlspecialchars($displayApproach['step1']) ?>">
                            </div>

                            <div class="approach-item-content">
                                <h3><?= htmlspecialchars($displayApproach['step1']) ?></h3>
                                <p>
                                    <?= nl2br(htmlspecialchars(strip_tags($displayApproach['description1']))) ?>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Approach Item 1 End -->


                <!-- Approach Item 2 Start -->
                <div class="col-xl-4 col-md-6">
                    <div class="approach-item wow fadeInUp" data-wow-delay="0.2s">

                        <div class="approach-item-image">
                            <figure>
                                <img src="<?= htmlspecialchars($approachImage2) ?>"
                                    alt="<?= htmlspecialchars($displayApproach['step2']) ?>">
                            </figure>
                        </div>

                        <div class="approach-item-body">
                            <div class="icon-box">
                                <img src="images/icon-approach-2.svg"
                                    alt="<?= htmlspecialchars($displayApproach['step2']) ?>">
                            </div>

                            <div class="approach-item-content">
                                <h3><?= htmlspecialchars($displayApproach['step2']) ?></h3>
                                <p>
                                    <?= nl2br(htmlspecialchars(strip_tags($displayApproach['description2']))) ?>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Approach Item 2 End -->


                <!-- Approach Item 3 Start -->
                <div class="col-xl-4 col-md-6">
                    <div class="approach-item wow fadeInUp" data-wow-delay="0.4s">

                        <div class="approach-item-image">
                            <figure>
                                <img src="<?= htmlspecialchars($approachImage3) ?>"
                                    alt="<?= htmlspecialchars($displayApproach['step3']) ?>">
                            </figure>
                        </div>

                        <div class="approach-item-body">
                            <div class="icon-box">
                                <img src="images/icon-approach-3.svg"
                                    alt="<?= htmlspecialchars($displayApproach['step3']) ?>">
                            </div>

                            <div class="approach-item-content">
                                <h3><?= htmlspecialchars($displayApproach['step3']) ?></h3>
                                <p>
                                    <?= nl2br(htmlspecialchars(strip_tags($displayApproach['description3']))) ?>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Approach Item 3 End -->


                <div class="col-lg-12">
                    <!-- Company Support Slider Box Start -->
                    <div class="company-supports-slider-box wow fadeInUp"
                        data-wow-delay="0.6s">

                        <!-- Company Support Content Start -->
                        <div class="company-supports-content">
                            <hr>
                            <h3>Trusted by Partners Who Drive Excellence</h3>
                            <hr>
                        </div>
                        <!-- Company Support Content End -->


                        <!-- Company Support Slider Start -->
                        <?php
                        require_once('includes/partner.php');
                        ?>
                        <!-- Company Support Slider End -->

                    </div>
                    <!-- Company Support Slider Box End -->
                </div>

            </div>
        </div>
    </div>
    <!-- Our Approach Section End -->
    <!-- Our Speaker Section Start -->
    <?php
    require_once('includes/team.php');
    ?>
    <!-- Our Speaker Section End -->

    <!-- Our Achievements Section Start -->
    <div class="our-achievements dark-section">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-xl-6">
                    <!-- Achievements Image Start -->
                    <div class="achievements-image wow fadeInUp">
                        <figure>
                            <img src="<?= htmlspecialchars($achievementsImage) ?>"
                                alt="<?= htmlspecialchars($displayAchievements['title']) ?>">
                        </figure>
                    </div>
                    <!-- Achievements Image End -->
                </div>

                <div class="col-xl-6">
                    <div class="achievements-content">

                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp"><?= htmlspecialchars($displayAchievements['title']) ?></h3>

                            <h2 class="text-anime-style-3" data-cursor="-opaque">
                                <?= htmlspecialchars($displayAchievements['sub_title']) ?>
                            </h2>

                            <p class="wow fadeInUp" data-wow-delay="0.2s">
                                <?= nl2br(htmlspecialchars(strip_tags($displayAchievements['content']))) ?>
                            </p>
                        </div>
                        <!-- Section Title End -->


                        <!-- Achievements List Start -->
                        <div class="achievement-items-list wow fadeInUp"
                            data-wow-delay="0.4s">

                            <?php foreach ($achievementsItems as $index => $item): ?>
                                <?php
                                $icon = !empty($item['icon']) ? $item['icon'] : 'icon-our-achievement-' . ($index + 1) . '.svg';
                                $number = $item['number'] ?? '0';
                                $suffix = $item['suffix'] ?? '+';
                                $label = $item['label'] ?? 'Achievement';
                                ?>

                                <!-- Achievement Item Start -->
                                <div class="achievement-item">
                                    <div class="icon-box">
                                        <img src="images/<?= htmlspecialchars($icon) ?>"
                                            alt="<?= htmlspecialchars($label) ?>">
                                    </div>

                                    <div class="achievement-item-content">
                                        <h3>
                                            <span class="counter"><?= htmlspecialchars($number) ?></span><?= htmlspecialchars($suffix) ?>
                                        </h3>
                                        <p><?= htmlspecialchars($label) ?></p>
                                    </div>
                                </div>
                                <!-- Achievement Item End -->

                            <?php endforeach; ?>

                        </div>
                        <!-- Achievements List End -->


                        <!-- Achievements Content Button Start -->
                        <div class="achievements-content-btn wow fadeInUp"
                            data-wow-delay="0.6s">

                            <a href="contact-us.php"
                                class="btn-default btn-highlighted">
                                Plan Your Event
                            </a>

                        </div>
                        <!-- Achievements Content Button End -->

                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Our Achievements Section End -->



    <!-- Our Faqs Section Start -->
    <div class="our-faqs">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-xl-5">

                    <!-- Faqs Image Box Start -->
                    <div class="faqs-image-box wow fadeInUp" data-wow-delay="0.2s">

                        <!-- Faqs Image Start -->
                        <div class="faqs-image">
                            <figure class="image-anime">
                                <img src="images/faqs-image.jpg"
                                    alt="Eventful Event Management">
                            </figure>
                        </div>
                        <!-- Faqs Image End -->


                        <!-- Faqs CTA Image Box Start -->
                        <div class="faqs-cta-image-box">

                            <!-- Faqs CTA Box Start -->
                            <div class="faqs-cta-box">

                                <!-- Faqs CTA Box Title Start -->
                                <div class="faqs-cta-box-title">
                                    <h3>
                                        Have questions about your event?
                                    </h3>
                                </div>
                                <!-- Faqs CTA Box Title End -->


                                <!-- Faqs CTA Box Item Start -->
                                <div class="faqs-cta-box-item">

                                    <div class="icon-box">
                                        <img src="images/icon-phone-accent.svg"
                                            alt="Call Eventful">
                                    </div>

                                    <p>
                                        <a href="tel:+919999999999">
                                            +91 99999 99999
                                        </a>
                                    </p>

                                </div>
                                <!-- Faqs CTA Box Item End -->

                            </div>
                            <!-- Faqs CTA Box End -->

                        </div>
                        <!-- Faqs CTA Image Box End -->

                    </div>
                    <!-- Faqs Image Box End -->

                </div>


                <div class="col-xl-7">

                    <!-- Faqs Content Start -->
                    <div class="faqs-content">

                        <!-- Section Title Start -->
                        <div class="section-title">

                            <h3 class="wow fadeInUp">
                                FAQs
                            </h3>

                            <h2 class="text-anime-style-3"
                                data-cursor="-opaque">
                                Everything you need to know about working with Eventful
                            </h2>

                        </div>
                        <!-- Section Title End -->


                        <!-- FAQ Accordion Start -->
                        <div class="faq-accordion" id="accordion">

                            <?php foreach ($displayHomeFaqs as $faqIndex => $faqItem):
                                $faqNum = $faqIndex + 1;
                                $faqDelay = $faqIndex * 0.2;
                                $faqCollapseClass = $faqIndex === 0 ? 'show' : '';
                                $faqBtnClass = $faqIndex === 0 ? 'accordion-button' : 'accordion-button collapsed';
                                $faqExpanded = $faqIndex === 0 ? 'true' : 'false';
                            ?>
                                <div class="accordion-item wow fadeInUp" <?php if ($faqDelay > 0): ?> data-wow-delay="<?php echo number_format($faqDelay, 1); ?>s" <?php endif; ?>>
                                    <h2 class="accordion-header" id="heading<?php echo $faqNum; ?>">
                                        <button class="<?php echo $faqBtnClass; ?>"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse<?php echo $faqNum; ?>"
                                            aria-expanded="<?php echo $faqExpanded; ?>"
                                            aria-controls="collapse<?php echo $faqNum; ?>">
                                            <?php echo $faqNum . '. ' . htmlspecialchars($faqItem['question']); ?>
                                        </button>
                                    </h2>
                                    <div id="collapse<?php echo $faqNum; ?>"
                                        class="accordion-collapse collapse <?php echo $faqCollapseClass; ?>"
                                        role="region"
                                        aria-labelledby="heading<?php echo $faqNum; ?>"
                                        data-bs-parent="#accordion">
                                        <div class="accordion-body">
                                            <p><?php echo htmlspecialchars($faqItem['answer']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        </div>
                        <!-- FAQ Accordion End -->

                    </div>
                    <!-- Faqs Content End -->

                </div>

            </div>
        </div>
    </div>
    <!-- Our Faqs Section End -->



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