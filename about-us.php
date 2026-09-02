
<?php
require_once('admin/db/config.php');


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
    // preloader
    require_once('includes/preloader.php');
    // header
    require_once('includes/header.php');
    ?>

    <!-- Page Header Section Start -->
    <div class="page-header parallaxie">
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
    <!-- Page Header Section End -->

    <div class="about-us">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-xl-6">
                    <!-- About Us Image Box Start -->
                    <div class="about-us-image-box wow fadeInUp">

                        <!-- About Image Circle Box Start -->
                        <div class="about-image-circle-box">

                            <!-- About Us Image Start -->
                            <div class="about-us-image">
                                <figure class="image-anime">
                                    <img src="images/about-us-image.jpg" alt="Tee Mac Corporation Medical Events">
                                </figure>
                            </div>
                            <!-- About Us Image End -->

                            <!-- Years Experience Circle Start -->
                            <div class="years-experience-circle">
                                <figure>
                                    <img src="images/years-experience-circle.svg" alt="Years of Experience">
                                </figure>

                                <!-- Years Experience Counter Box Start -->
                                <div class="years-experience-counter-box">
                                    <h2><span class="counter">25</span>+</h2>
                                </div>
                                <!-- Years Experience Counter Box End -->
                            </div>
                            <!-- Years Experience Circle End -->

                        </div>
                        <!-- About Image Circle Box End -->

                        <!-- About Achievement Box Start -->
                        <div class="about-achievement-box">

                            <div class="about-achievement-box-header">

                                <div class="about-achievement-image">
                                    <figure>
                                        <img src="images/about-achievement-image.png" alt="Tee Mac Corporation">
                                    </figure>
                                </div>

                                <div class="about-achievement-content">
                                    <h3>Professional Medical Event Management</h3>
                                    <p>Chandigarh, India</p>
                                </div>

                            </div>

                            <div class="about-achievement-box-body">
                                <h3>Creating Memorable & Successful Events</h3>
                            </div>

                        </div>
                        <!-- About Achievement Box End -->

                    </div>
                    <!-- About Us Image Box End -->
                </div>


                <div class="col-xl-6">
                    <!-- About Us Content Start -->
                    <div class="about-us-content">

                        <!-- Section Title Start -->
                        <div class="section-title">

                            <h3 class="wow fadeInUp">About Us</h3>

                            <h2 class="text-anime-style-3" data-cursor="-opaque">
                                Professional Medical Event Management Solutions
                            </h2>

                            <p class="wow fadeInUp text-justify" data-wow-delay="0.2s">
                                Established in Chandigarh, India, Tee Mac Corporation is a
                                professional medical event management company dedicated to
                                delivering creative and reliable solutions for medical events.
                            </p>

                            <p class="wow fadeInUp text-justify" data-wow-delay="0.3s">
                                From medical seminars, conferences and workshops to other
                                professional events, our experienced team manages every
                                aspect of the event with attention to detail and a commitment
                                to delivering the highest standards of service.
                            </p>

                            <p class="wow fadeInUp text-justify" data-wow-delay="0.4s">
                                We take care of planning, organisation, logistics and execution
                                so that our clients can focus on creating meaningful experiences
                                for their attendees.
                            </p>

                        </div>
                        <!-- Section Title End -->


                        <!-- About Us Footer Start -->
                        <div class="about-us-footer wow fadeInUp" data-wow-delay="0.8s">

                            <!-- About Us Button Start -->
                            <div class="about-us-btn">
                                <a href="contact-us.php" class="btn-default">
                                    Contact Now
                                </a>
                            </div>
                            <!-- About Us Button End -->


                            <!-- About Contact Box Start -->
                            <div class="about-contact-box">

                                <div class="icon-box">
                                    <img src="images/icon-phone-white.svg" alt="Call Tee Mac">
                                </div>

                                <div class="about-contact-box-content">
                                    <h3>Call Now!</h3>
                                    <p>
                                        <a href="tel:+917380015666">
                                            +91 73800 15666
                                        </a>
                                    </p>
                                </div>

                            </div>
                            <!-- About Contact Box End -->

                        </div>
                        <!-- About Us Footer End -->

                    </div>
                    <!-- About Us Content End -->
                </div>

            </div>
        </div>
    </div>

    <!-- Our Approach Section Start -->
    <div class="our-approach dark-section">
        <div class="container">
            <div class="row section-row">
                <div class="col-lg-12">
                    <!-- Section Title Start -->
                    <div class="section-title section-title-center">
                        <h3 class="wow fadeInUp">Our Approach</h3>
                        <h2 class="text-anime-style-3" data-cursor="-opaque">
                            A strategic approach to delivering seamless medical events
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
                                <img src="images/our-approach-image-1.jpg"
                                    alt="Seamless Medical Event Execution">
                            </figure>
                        </div>

                        <div class="approach-item-body">
                            <div class="icon-box">
                                <img src="images/icon-approach-1.svg"
                                    alt="Seamless Execution">
                            </div>

                            <div class="approach-item-content">
                                <h3>Seamless Execution</h3>
                                <p>
                                    From planning to execution, we manage every detail
                                    with precision to ensure your medical event runs
                                    smoothly and successfully.
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
                                <img src="images/our-approach-image-2.jpg"
                                    alt="Collaborative Medical Event Planning">
                            </figure>
                        </div>

                        <div class="approach-item-body">
                            <div class="icon-box">
                                <img src="images/icon-approach-1.svg"
                                    alt="Collaborative Planning">
                            </div>

                            <div class="approach-item-content">
                                <h3>Collaborative Planning</h3>
                                <p>
                                    We work closely with clients, healthcare
                                    professionals, speakers and partners to create
                                    well-structured and impactful events.
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
                                <img src="images/our-approach-image-3.jpg"
                                    alt="Continuous Improvement in Event Management">
                            </figure>
                        </div>

                        <div class="approach-item-body">
                            <div class="icon-box">
                                <img src="images/icon-approach-1.svg"
                                    alt="Continuous Improvement">
                            </div>

                            <div class="approach-item-content">
                                <h3>Continuous Improvement</h3>
                                <p>
                                    We continuously refine our processes, learn from
                                    every event and embrace innovative ideas to deliver
                                    better experiences every time.
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
                            <img src="images/our-achievements-image.jpg"
                                alt="Tee Mac Corporation Event Management">
                        </figure>
                    </div>
                    <!-- Achievements Image End -->
                </div>

                <div class="col-xl-6">
                    <div class="achievements-content">

                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">Our Achievements</h3>

                            <h2 class="text-anime-style-3" data-cursor="-opaque">
                                Delivering excellence through experience and expertise
                            </h2>

                            <p class="wow fadeInUp" data-wow-delay="0.2s">
                                Our journey is built on experience, strong partnerships,
                                meticulous planning, and a commitment to delivering
                                impactful medical and corporate events that create
                                meaningful experiences.
                            </p>
                        </div>
                        <!-- Section Title End -->


                        <!-- Achievements List Start -->
                        <div class="achievement-items-list wow fadeInUp"
                            data-wow-delay="0.4s">

                            <!-- Achievement Item Start -->
                            <div class="achievement-item">
                                <div class="icon-box">
                                    <img src="images/icon-our-achievement-1.svg"
                                        alt="Years of Experience">
                                </div>

                                <div class="achievement-item-content">
                                    <h3>
                                        <span class="counter">15</span>+
                                    </h3>
                                    <p>Years of Industry Experience</p>
                                </div>
                            </div>
                            <!-- Achievement Item End -->


                            <!-- Achievement Item Start -->
                            <div class="achievement-item">
                                <div class="icon-box">
                                    <img src="images/icon-our-achievement-2.svg"
                                        alt="Events Delivered">
                                </div>

                                <div class="achievement-item-content">
                                    <h3>
                                        <span class="counter">500</span>+
                                    </h3>
                                    <p>Events Successfully Delivered</p>
                                </div>
                            </div>
                            <!-- Achievement Item End -->


                            <!-- Achievement Item Start -->
                            <div class="achievement-item">
                                <div class="icon-box">
                                    <img src="images/icon-our-achievement-3.svg"
                                        alt="Healthcare Professionals">
                                </div>

                                <div class="achievement-item-content">
                                    <h3>
                                        <span class="counter">50</span>K+
                                    </h3>
                                    <p>Professionals Engaged</p>
                                </div>
                            </div>
                            <!-- Achievement Item End -->


                            <!-- Achievement Item Start -->
                            <div class="achievement-item">
                                <div class="icon-box">
                                    <img src="images/icon-our-achievement-4.svg"
                                        alt="Trusted Partners">
                                </div>

                                <div class="achievement-item-content">
                                    <h3>
                                        <span class="counter">100</span>+
                                    </h3>
                                    <p>Trusted Clients & Partners</p>
                                </div>
                            </div>
                            <!-- Achievement Item End -->

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
                            <div class="accordion-item wow fadeInUp"<?php if ($faqDelay > 0): ?> data-wow-delay="<?php echo number_format($faqDelay, 1); ?>s"<?php endif; ?>>
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