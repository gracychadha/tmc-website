<?php
require_once('admin/db/config.php');

$homeBlogs = [];
$stmtHomeBlogs = $db->prepare("SELECT * FROM blog WHERE status = 1 ORDER BY idblog DESC LIMIT 3");
$stmtHomeBlogs->execute();
$homeBlogs = $stmtHomeBlogs->get_result()->fetch_all(MYSQLI_ASSOC);

$fallbackHomeBlogs = [
    ['idblog' => '1', 'title' => 'Mastering Public Speaking: Expert Tips for Confident Presentations', 'image' => 'images/post-1.jpg', 'user' => 'Esther Howard', 'slug' => 'mastering-public-speaking'],
    ['idblog' => '2', 'title' => 'Simple Self-Defense Skills Everyone Should Learn for Safety', 'image' => 'images/post-2.jpg', 'user' => 'Esther Howard', 'slug' => 'simple-self-defense-skills'],
    ['idblog' => '3', 'title' => 'The Power of Networking: Building Connections That Last', 'image' => 'images/post-3.jpg', 'user' => 'Esther Howard', 'slug' => 'power-of-networking'],
];

$displayHomeBlogs = !empty($homeBlogs) ? $homeBlogs : $fallbackHomeBlogs;
$homeBlogPrimary = $displayHomeBlogs[0] ?? $fallbackHomeBlogs[0];
$homeBlogSecondary = array_slice($displayHomeBlogs, 1, 2);
if (count($homeBlogSecondary) < 2) {
    $homeBlogSecondary[] = $fallbackHomeBlogs[2] ?? $fallbackHomeBlogs[0];
}

$homeFaqs = [];
$stmtHomeFaqs = $db->prepare("SELECT * FROM faqs WHERE status = 1 ORDER BY faqs_id ASC LIMIT 5");
$stmtHomeFaqs->execute();
$homeFaqs = $stmtHomeFaqs->get_result()->fetch_all(MYSQLI_ASSOC);

$fallbackHomeFaqs = [
    ['question' => 'No FAQ added', 'answer' => 'No FAQ added yet. Please add FAQs from the admin panel.'],
];

$displayHomeFaqs = !empty($homeFaqs) ? $homeFaqs : $fallbackHomeFaqs;
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


    <!-- Hero Section Start -->
    <div class="hero dark-section parallaxie">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Hero Box Start -->
                    <div class="hero-box">
                        <!-- Hero Content Start -->
                        <div class="hero-content">
                            <!-- Hero Sub Heading Start -->
                            <div class="hero-sub-heading wow fadeInUp">


                                <!-- Satisfy Client Content Start -->
                                <div class="satisfy-client-content">
                                    <p>EVENTS • EXPERIENCES • CONNECTIONS</p>
                                </div>
                                <!-- Satisfy Client Content End -->
                            </div>
                            <!-- Hero Sub Heading Start -->

                            <!-- Section Title Start -->
                            <div class="section-title">
                                <h1 class="text-anime-style-3" data-cursor="-opaque">We Create Events That People Remember.</h1>
                                <p class="wow fadeInUp" data-wow-delay="0.2s">From medical conferences and corporate events to workshops, branded experiences, and live gatherings, we bring together the right people, ideas, and experiences to make every event impactful.</p>
                            </div>
                            <!-- Section Title End -->

                            <!-- Hero Content Body Start -->
                            <div class="hero-content-body wow fadeInUp" data-wow-delay="0.4s">
                                <!-- Hero Button Start -->
                                <div class="hero-btn">
                                    <a href="about-us.php" class="btn-default btn-highlighted">Explore More</a>
                                </div>
                                <!-- Hero Button End -->

                                <!-- Video Play Button Start -->
                                <!-- <div class="video-play-button">
                                    <a href="https://www.youtube.com/watch?v=Y-x0efG1seA" class="popup-video" data-cursor-text="Play">
                                        <i class="fa-solid fa-play"></i>
                                    </a>
                                    <h3>Watch Video</h3>
                                </div> -->
                                <!-- Video Play Button End -->
                            </div>
                            <!-- Hero Content Body End -->
                        </div>
                        <!-- Hero Content End -->


                    </div>
                    <!-- Hero Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Hero Section End -->

    <!-- Scrolling Ticker Section Start -->
    <div class="our-scrolling-ticker">
        <!-- Scrolling Ticker Start -->
        <div class="scrolling-ticker-box">

            <div class="scrolling-content">

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Medical Events
                </span>

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Conferences
                </span>

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Corporate Events
                </span>

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Workshops
                </span>

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Branded Events
                </span>

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Event Management
                </span>

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Event Logistics
                </span>

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Venue Management
                </span>

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Audio Visual Solutions
                </span>

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Seamless Experiences
                </span>

            </div>


            <!-- Duplicate for seamless scrolling -->

            <div class="scrolling-content">

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Medical Events
                </span>

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Conferences
                </span>

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Corporate Events
                </span>

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Workshops
                </span>

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Branded Events
                </span>

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Event Management
                </span>

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Event Logistics
                </span>

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Venue Management
                </span>

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Audio Visual Solutions
                </span>

                <span>
                    <img src="images/icon-asterisk.svg" alt="">
                    Seamless Experiences
                </span>

            </div>

        </div>
        <!-- Scrolling Ticker End -->
    </div>
    <!-- Scrolling Ticker Section End -->

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
                                    <img src="images/about-us-image-1-metal.jpg"
                                        alt="Eventful Event Management">
                                </figure>
                            </div>
                            <!-- About Us Image End -->

                            <!-- Contact Us Circle Start -->
                            <div class="contact-us-circle-metal">
                                <a href="contact-us.php">
                                    <img src="images/contact-us-circle-metal.svg"
                                        alt="Contact Eventful">
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
                                    <img src="images/about-us-image-2-metal.jpg"
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
                                About Tee Mac Corporation
                            </h3>

                            <h2 class="text-anime-style-3"
                                data-cursor="-opaque">
                                Creating meaningful events that bring people, ideas and experiences together
                            </h2>

                            <p class="wow fadeInUp text-justify" data-wow-delay="0.2s">
                                Tee Mac Corporation is a professional event management company
                                dedicated to creating impactful and memorable experiences.
                                From medical conferences and corporate events to workshops,
                                seminars and branded experiences, we bring together
                                creativity, strategy and seamless execution.
                            </p>

                            <p class="wow fadeInUp text-justify" data-wow-delay="0.2s">
                                From planning and venue management to event logistics,
                                production, audio-visual solutions and on-ground execution,
                                our team takes care of every detail. We work closely with
                                our clients to understand their objectives and deliver
                                experiences that connect audiences, strengthen brands and
                                leave a lasting impression.
                            </p>

                        </div>
                        <!-- Section Title End -->


                        <!-- About Us Client Box Start -->
                        <div class="about-us-client-box-metal wow fadeInUp"
                            data-wow-delay="0.6s">

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
                                        <img src="images/author-1.jpg"
                                            alt="Event Attendee">
                                    </figure>
                                </div>

                                <div class="satisfy-client-image">
                                    <figure class="image-anime">
                                        <img src="images/author-2.jpg"
                                            alt="Conference Attendee">
                                    </figure>
                                </div>

                                <div class="satisfy-client-image">
                                    <figure class="image-anime">
                                        <img src="images/author-3.jpg"
                                            alt="Corporate Event">
                                    </figure>
                                </div>

                                <div class="satisfy-client-image">
                                    <figure class="image-anime">
                                        <img src="images/author-4.jpg"
                                            alt="Event Experience">
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
                        <div class="about-us-footer-metal wow fadeInUp"
                            data-wow-delay="0.8s">

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
                                    <img src="images/icon-email.svg"
                                        alt="Email">
                                </div>

                                <div class="about-email-box-content-metal">

                                    <h3>
                                        Plan Your Next Event
                                    </h3>

                                    <p>
                                        <a href="mailto:info@eventful.co.in"
                                            target="_blank">
                                            info@eventful.co.in
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
    <!-- About Us Section Start -->

    <!-- Our Feature Section Start -->
    <div class="our-feature-metal dark-section">
        <div class="container">

            <div class="row section-row">
                <div class="col-lg-12">

                    <!-- Section Title Start -->
                    <div class="section-title section-title-center">

                        <h3 class="wow fadeInUp">
                            What We Do
                        </h3>

                        <h2 class="text-anime-style-3" data-cursor="-opaque">
                            End-to-end event solutions designed to make every experience seamless
                        </h2>

                    </div>
                    <!-- Section Title End -->

                </div>
            </div>


            <div class="row">
                <div class="col-lg-12">

                    <!-- Services Slider Start -->
                    <div class="feature-slider-metal wow fadeInUp"
                        data-wow-delay="0.2s">

                        <div class="swiper">

                            <div class="swiper-wrapper" data-cursor-text="Drag">


                                <!-- Medical Events Start -->
                                <div class="swiper-slide">

                                    <div class="feature-item-metal box-bg-shape">

                                        <div class="feature-item-content-metal">

                                            <div class="feature-item-header-metal">

                                                <div class="feature-item-title-metal">
                                                    <h3>Medical Events</h3>
                                                </div>

                                                <div class="feature-item-no-metal">
                                                    <h4>01.</h4>
                                                </div>

                                            </div>

                                            <div class="feature-item-body-metal">
                                                <p>
                                                    From medical conferences and seminars
                                                    to professional gatherings, we create
                                                    well-organised experiences for the
                                                    healthcare community.
                                                </p>
                                            </div>

                                        </div>

                                        <div class="feature-item-btn-metal">
                                            <a href="our-features.php" class="readmore-btn">
                                                Explore Service
                                            </a>
                                        </div>

                                        <div class="feature-item-icon-metal">
                                            <img src="images/icon-service-1-metal.svg"
                                                alt="Medical Events">
                                        </div>

                                    </div>

                                </div>
                                <!-- Medical Events End -->


                                <!-- Conferences & Workshops Start -->
                                <div class="swiper-slide">

                                    <div class="feature-item-metal box-bg-shape">

                                        <div class="feature-item-content-metal">

                                            <div class="feature-item-header-metal">

                                                <div class="feature-item-title-metal">
                                                    <h3>Conferences & Workshops</h3>
                                                </div>

                                                <div class="feature-item-no-metal">
                                                    <h4>02.</h4>
                                                </div>

                                            </div>

                                            <div class="feature-item-body-metal">
                                                <p>
                                                    We manage conferences, workshops and
                                                    seminars with thoughtful planning,
                                                    engaging formats and seamless
                                                    on-ground execution.
                                                </p>
                                            </div>

                                        </div>

                                        <div class="feature-item-btn-metal">
                                            <a href="our-features.php" class="readmore-btn">
                                                Explore Service
                                            </a>
                                        </div>

                                        <div class="feature-item-icon-metal">
                                            <img src="images/icon-service-2-metal.svg"
                                                alt="Conferences and Workshops">
                                        </div>

                                    </div>

                                </div>
                                <!-- Conferences & Workshops End -->


                                <!-- Corporate Events Start -->
                                <div class="swiper-slide">

                                    <div class="feature-item-metal box-bg-shape">

                                        <div class="feature-item-content-metal">

                                            <div class="feature-item-header-metal">

                                                <div class="feature-item-title-metal">
                                                    <h3>Corporate Events</h3>
                                                </div>

                                                <div class="feature-item-no-metal">
                                                    <h4>03.</h4>
                                                </div>

                                            </div>

                                            <div class="feature-item-body-metal">
                                                <p>
                                                    From corporate gatherings to
                                                    brand-focused experiences, we help
                                                    businesses create events that connect
                                                    teams, audiences and ideas.
                                                </p>
                                            </div>

                                        </div>

                                        <div class="feature-item-btn-metal">
                                            <a href="our-features.php" class="readmore-btn">
                                                Explore Service
                                            </a>
                                        </div>

                                        <div class="feature-item-icon-metal">
                                            <img src="images/icon-service-3-metal.svg"
                                                alt="Corporate Events">
                                        </div>

                                    </div>

                                </div>
                                <!-- Corporate Events End -->


                                <!-- Event Logistics Start -->
                                <div class="swiper-slide">

                                    <div class="feature-item-metal box-bg-shape">

                                        <div class="feature-item-content-metal">

                                            <div class="feature-item-header-metal">

                                                <div class="feature-item-title-metal">
                                                    <h3>Event Logistics</h3>
                                                </div>

                                                <div class="feature-item-no-metal">
                                                    <h4>04.</h4>
                                                </div>

                                            </div>

                                            <div class="feature-item-body-metal">
                                                <p>
                                                    We coordinate venues, registrations,
                                                    hospitality, transportation and
                                                    on-ground requirements to keep every
                                                    event running smoothly.
                                                </p>
                                            </div>

                                        </div>

                                        <div class="feature-item-btn-metal">
                                            <a href="our-features.php" class="readmore-btn">
                                                Explore Service
                                            </a>
                                        </div>

                                        <div class="feature-item-icon-metal">
                                            <img src="images/icon-service-4-metal.svg"
                                                alt="Event Logistics">
                                        </div>

                                    </div>

                                </div>
                                <!-- Event Logistics End -->


                                <!-- Audio Visual Solutions Start -->
                                <div class="swiper-slide">

                                    <div class="feature-item-metal box-bg-shape">

                                        <div class="feature-item-content-metal">

                                            <div class="feature-item-header-metal">

                                                <div class="feature-item-title-metal">
                                                    <h3>Audio Visual Solutions</h3>
                                                </div>

                                                <div class="feature-item-no-metal">
                                                    <h4>05.</h4>
                                                </div>

                                            </div>

                                            <div class="feature-item-body-metal">
                                                <p>
                                                    From sound and lighting to screens
                                                    and event production, we provide the
                                                    technical support needed for a
                                                    powerful event experience.
                                                </p>
                                            </div>

                                        </div>

                                        <div class="feature-item-btn-metal">
                                            <a href="our-features.php" class="readmore-btn">
                                                Explore Service
                                            </a>
                                        </div>

                                        <div class="feature-item-icon-metal">
                                            <img src="images/icon-service-3-metal.svg"
                                                alt="Audio Visual Solutions">
                                        </div>

                                    </div>

                                </div>
                                <!-- Audio Visual Solutions End -->


                            </div>

                            <div class="feature-pagination-metal"></div>

                        </div>

                    </div>
                    <!-- Services Slider End -->

                </div>
            </div>

        </div>
    </div>
    <!-- Our Feature Section End -->


    <!-- Our Benefits Section Start -->
    <div class="our-benefits">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-xl-6">

                    <!-- Key Benefits Content Start -->
                    <div class="key-benefits-content-gold">

                        <!-- Section Title Start -->
                        <div class="section-title">

                            <h3 class="wow fadeInUp">
                                Why Choose Eventful
                            </h3>

                            <h2 class="text-anime-style-3"
                                data-cursor="-opaque">
                                Everything you need to create an event that truly makes an impact
                            </h2>

                            <p class="wow fadeInUp" data-wow-delay="0.2s">
                                We combine strategic planning, creative thinking and
                                seamless execution to deliver events that meet your
                                objectives and create meaningful experiences for
                                every attendee.
                            </p>

                        </div>
                        <!-- Section Title End -->


                        <!-- Key Benefits List Start -->
                        <div class="key-benefits-list-gold wow fadeInUp"
                            data-wow-delay="0.4s">

                            <ul>

                                <li>
                                    End-to-end event planning and execution
                                </li>

                                <li>
                                    Experienced team for conferences, medical and corporate events
                                </li>

                                <li>
                                    Seamless venue, logistics and on-ground coordination
                                </li>

                                <li>
                                    Professional audio-visual and production support
                                </li>

                                <li>
                                    Creative solutions tailored to your event objectives
                                </li>

                                <li>
                                    Dedicated support from planning to completion
                                </li>

                            </ul>

                        </div>
                        <!-- Key Benefits List End -->

                    </div>
                    <!-- Key Benefits Content End -->

                </div>


                <div class="col-xl-6">

                    <!-- Our Benefits Images Start -->
                    <div class="our-benefits-images">

                        <!-- Our Benefits Image Start -->
                        <div class="our-benefits-img image-1">

                            <figure class="image-anime reveal">
                                <img src="images/our-benefits-image-1.jpg"
                                    alt="Eventful Event Experience">
                            </figure>

                        </div>
                        <!-- Our Benefits Image End -->


                        <!-- Our Benefits Image Start -->
                        <div class="our-benefits-img image-2">

                            <figure class="image-anime reveal">
                                <img src="images/our-benefits-image-2.jpg"
                                    alt="Event Management and Conference">
                            </figure>

                        </div>
                        <!-- Our Benefits Image End -->

                    </div>
                    <!-- Why Choose Images End -->

                </div>

            </div>
        </div>
    </div>
    <!-- Our Benefits Section End -->


    <!-- Our Event Section Start -->
    <div class="our-event-gold dark-section">
        <div class="container">
            <div class="row">

                <div class="col-xl-5">

                    <!-- Event Content Start -->
                    <div class="our-event-content-gold">

                        <!-- Section Title Start -->
                        <div class="section-title">

                            <h3 class="wow fadeInUp">
                                Upcoming Events
                            </h3>

                            <h2 class="text-anime-style-3"
                                data-cursor="-opaque">
                                Discover the events and experiences we're bringing to life
                            </h2>

                            <p class="wow fadeInUp" data-wow-delay="0.2s">
                                Explore our upcoming conferences, workshops, corporate
                                gatherings and professional events. Stay connected with
                                the latest experiences and find the events that matter
                                to you.
                            </p>

                        </div>
                        <!-- Section Title End -->


                        <!-- Our Event Button Start -->
                        <div class="our-event-btn-gold wow fadeInUp"
                            data-wow-delay="0.4s">

                            <a href="#"
                                class="btn-default btn-highlighted">
                                Explore All Events
                            </a>

                        </div>
                        <!-- Our Event Button End -->

                    </div>
                    <!-- Event Content End -->

                </div>


                <div class="col-xl-7">

                    <!-- Event Items List Start -->
                    <div class="event-items-list-gold">


                        <!-- Event Item Start -->
                        <div class="event-item-gold wow fadeInUp">

                            <!-- Event Image Start -->
                            <div class="event-item-image-gold">

                                <a href="#"
                                    data-cursor-text="View">

                                    <figure>
                                        <img src="images/event-image-1-gold.jpg"
                                            alt="Medical Conference">
                                    </figure>

                                </a>

                            </div>
                            <!-- Event Image End -->


                            <!-- Event Item Body Start -->
                            <div class="event-item-body-gold">

                                <div class="event-schedule-content-gold">

                                    <h2>01</h2>

                                    <p>Upcoming</p>

                                    <p>Event Date</p>

                                </div>


                                <div class="event-item-info-gold">

                                    <div class="event-item-body-content-gold">

                                        <h3>
                                            <a href="#">
                                                Medical Conferences
                                            </a>
                                        </h3>

                                        <p>
                                            Professional conferences designed to
                                            connect healthcare professionals,
                                            experts and industry leaders.
                                        </p>

                                    </div>


                                    <div class="event-item-btn-gold">

                                        <a href="#"
                                            class="readmore-btn">
                                            View Event Details
                                        </a>

                                    </div>

                                </div>

                            </div>
                            <!-- Event Item Body End -->

                        </div>
                        <!-- Event Item End -->


                        <!-- Event Item Start -->
                        <div class="event-item-gold wow fadeInUp"
                            data-wow-delay="0.2s">

                            <!-- Event Image Start -->
                            <div class="event-item-image-gold">

                                <a href="#"
                                    data-cursor-text="View">

                                    <figure>
                                        <img src="images/event-image-2-gold.jpg"
                                            alt="Corporate Event">
                                    </figure>

                                </a>

                            </div>
                            <!-- Event Image End -->


                            <!-- Event Item Body Start -->
                            <div class="event-item-body-gold">

                                <div class="event-schedule-content-gold">

                                    <h2>02</h2>

                                    <p>Upcoming</p>

                                    <p>Event Date</p>

                                </div>


                                <div class="event-item-info-gold">

                                    <div class="event-item-body-content-gold">

                                        <h3>
                                            <a href="#">
                                                Corporate Events
                                            </a>
                                        </h3>

                                        <p>
                                            Engaging corporate experiences that
                                            bring teams, brands and audiences
                                            together.
                                        </p>

                                    </div>


                                    <div class="event-item-btn-gold">

                                        <a href="#"
                                            class="readmore-btn">
                                            View Event Details
                                        </a>

                                    </div>

                                </div>

                            </div>
                            <!-- Event Item Body End -->

                        </div>
                        <!-- Event Item End -->


                        <!-- Event Item Start -->
                        <div class="event-item-gold wow fadeInUp"
                            data-wow-delay="0.4s">

                            <!-- Event Image Start -->
                            <div class="event-item-image-gold">

                                <a href="#"
                                    data-cursor-text="View">

                                    <figure>
                                        <img src="images/event-image-3-gold.jpg"
                                            alt="Workshop">
                                    </figure>

                                </a>

                            </div>
                            <!-- Event Image End -->


                            <!-- Event Item Body Start -->
                            <div class="event-item-body-gold">

                                <div class="event-schedule-content-gold">

                                    <h2>03</h2>

                                    <p>Upcoming</p>

                                    <p>Event Date</p>

                                </div>


                                <div class="event-item-info-gold">

                                    <div class="event-item-body-content-gold">

                                        <h3>
                                            <a href="#">
                                                Workshops & Seminars
                                            </a>
                                        </h3>

                                        <p>
                                            Interactive learning experiences that
                                            encourage knowledge sharing, discussion
                                            and meaningful connections.
                                        </p>

                                    </div>


                                    <div class="event-item-btn-gold">

                                        <a href="#"
                                            class="readmore-btn">
                                            View Event Details
                                        </a>

                                    </div>

                                </div>

                            </div>
                            <!-- Event Item Body End -->

                        </div>
                        <!-- Event Item End -->


                        <!-- Event Item Start -->
                        <div class="event-item-gold wow fadeInUp"
                            data-wow-delay="0.6s">

                            <!-- Event Image Start -->
                            <div class="event-item-image-gold">

                                <a href="#"
                                    data-cursor-text="View">

                                    <figure>
                                        <img src="images/event-image-4-gold.jpg"
                                            alt="Branded Event">
                                    </figure>

                                </a>

                            </div>
                            <!-- Event Image End -->


                            <!-- Event Item Body Start -->
                            <div class="event-item-body-gold">

                                <div class="event-schedule-content-gold">

                                    <h2>04</h2>

                                    <p>Upcoming</p>

                                    <p>Event Date</p>

                                </div>


                                <div class="event-item-info-gold">

                                    <div class="event-item-body-content-gold">

                                        <h3>
                                            <a href="#">
                                                Branded Experiences
                                            </a>
                                        </h3>

                                        <p>
                                            Creative brand experiences designed
                                            to engage audiences and create lasting
                                            impressions.
                                        </p>

                                    </div>


                                    <div class="event-item-btn-gold">

                                        <a href="#"
                                            class="readmore-btn">
                                            View Event Details
                                        </a>

                                    </div>

                                </div>

                            </div>
                            <!-- Event Item Body End -->

                        </div>
                        <!-- Event Item End -->


                    </div>
                    <!-- Event Items List End -->

                </div>

            </div>
        </div>
    </div>
    <!-- Our Event Section End -->

    <!-- Our Speaker Section Start -->
    <?php
    require_once('includes/team.php');
    ?>
    <!-- Our Speaker Section End -->





    <!-- Intro Video Section Start -->
    <div class="intro-video-metal dark-section parallaxie">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">

                    <!-- Intro Video Content Start -->
                    <div class="intro-video-content-metal">

                        <!-- Section Title Start -->
                        <div class="section-title">

                            <h3 class="wow fadeInUp">
                                Our Experience
                            </h3>

                            <h2 class="text-anime-style-3"
                                data-cursor="-opaque">
                                Four years of creating impactful events and meaningful experiences
                            </h2>

                        </div>
                        <!-- Section Title End -->


                        <!-- Intro Video Circle Start -->
                        <div class="intro-video-circle-metal">

                            <a href="#"
                                class="popup-video"
                                data-cursor-text="Play">

                                <img src="images/intro-video-circle-metal.svg"
                                    alt="Eventful Experience">

                            </a>

                        </div>
                        <!-- Intro Video Circle End -->

                    </div>
                    <!-- Intro Video Content End -->


                    <!-- Intro Video Counter List Start -->
                    <div class="intro-video-counter-list-metal wow fadeInUp"
                        data-wow-delay="0.2s">

                        <!-- Counter Item Start -->
                        <div class="intro-video-item-metal">

                            <h2>
                                <span class="counter">4</span>+
                            </h2>

                            <p>
                                Years of Experience
                            </p>

                        </div>
                        <!-- Counter Item End -->


                        <!-- Counter Item Start -->
                        <div class="intro-video-item-metal">

                            <h2>
                                <span class="counter">50</span>+
                            </h2>

                            <p>
                                National Conferences
                            </p>

                        </div>
                        <!-- Counter Item End -->


                        <!-- Counter Item Start -->
                        <div class="intro-video-item-metal">

                            <h2>
                                <span class="counter">100</span>+
                            </h2>

                            <p>
                                Online Meetups
                            </p>

                        </div>
                        <!-- Counter Item End -->


                        <!-- Counter Item Start -->
                        <div class="intro-video-item-metal">

                            <h2>
                                <span class="counter">20</span>+
                            </h2>

                            <p>
                                Live Surgeries
                            </p>

                        </div>
                        <!-- Counter Item End -->

                    </div>
                    <!-- Intro Video Counter List End -->

                </div>
            </div>
        </div>
    </div>
    <!-- Intro Video Section End -->


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

    <!-- Our Testimonials Section Start -->
    <?php
    require_once('includes/testimonial.php');
    ?>
    <!-- Our Testimonials Section Emd -->

    

    <!-- Our Blog Section Start -->
    <div class="our-blog">
        <div class="container">
            <div class="row section-row">
                <div class="col-lg-12">
                    <!-- Section Title Start -->
                    <div class="section-title section-title-center">
                        <h3 class="wow fadeInUp">Latest Blog</h3>
                        <h2 class="text-anime-style-3" data-cursor="-opaque">Explore our latest insights stories and updates</h2>
                    </div>
                    <!-- Section Title End -->
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6">
                    <?php
                    $pImage = !empty($homeBlogPrimary['image']) ? $homeBlogPrimary['image'] : 'images/post-1.jpg';
                    if ($pImage !== 'images/post-1.jpg' && !file_exists($pImage)) { $pImage = 'images/post-1.jpg'; }
                    $pId = $homeBlogPrimary['idblog'] ?? 1;
                    $pTitle = $homeBlogPrimary['title'] ?? 'Blog Post';
                    $pUser = $homeBlogPrimary['user'] ?? 'TMC Team';
                    ?>
                    <!-- Post Item Start -->
                    <div class="post-item wow fadeInUp">
                        <div class="post-featured-image">
                            <a href="blog-details.php?id=<?php echo htmlspecialchars($pId); ?>" data-cursor-text="View">
                                <figure class="image-anime">
                                    <img src="<?php echo htmlspecialchars($pImage); ?>" alt="<?php echo htmlspecialchars($pTitle); ?>">
                                </figure>
                            </a>
                        </div>
                        <div class="post-item-body">
                            <div class="post-item-body-content">
                                <div class="post-item-meta">
                                    <ul>
                                        <li><img src="images/icon-author.svg" alt=""><?php echo htmlspecialchars($pUser); ?></li>
                                    </ul>
                                </div>
                                <div class="post-item-content">
                                    <h2><a href="blog-details.php?id=<?php echo htmlspecialchars($pId); ?>"><?php echo htmlspecialchars($pTitle); ?></a></h2>
                                </div>
                            </div>
                            <div class="post-item-btn">
                                <a href="blog-details.php?id=<?php echo htmlspecialchars($pId); ?>" class="readmore-btn">read more</a>
                            </div>
                        </div>
                    </div>
                    <!-- Post Item End -->
                </div>

                <div class="col-xl-6">
                    <div class="post-item-list">
                        <?php foreach ($homeBlogSecondary as $sIndex => $sBlog):
                            $sImage = !empty($sBlog['image']) ? $sBlog['image'] : 'images/post-' . ($sIndex + 2) . '.jpg';
                            if ($sImage !== 'images/post-' . ($sIndex + 2) . '.jpg' && !file_exists($sImage)) { $sImage = 'images/post-' . ($sIndex + 2) . '.jpg'; }
                            $sId = $sBlog['idblog'] ?? ($sIndex + 2);
                            $sTitle = $sBlog['title'] ?? 'Blog Post';
                            $sUser = $sBlog['user'] ?? 'TMC Team';
                            $sDelay = ($sIndex + 1) * 0.2;
                        ?>
                        <!-- Post Item Start -->
                        <div class="post-item wow fadeInUp" data-wow-delay="<?php echo number_format($sDelay, 1); ?>s">
                            <div class="post-featured-image">
                                <a href="blog-details.php?id=<?php echo htmlspecialchars($sId); ?>" data-cursor-text="View">
                                    <figure class="image-anime">
                                        <img src="<?php echo htmlspecialchars($sImage); ?>" alt="<?php echo htmlspecialchars($sTitle); ?>">
                                    </figure>
                                </a>
                            </div>
                            <div class="post-item-body">
                                <div class="post-item-body-content">
                                    <div class="post-item-meta">
                                        <ul>
                                            <li><img src="images/icon-author.svg" alt=""><?php echo htmlspecialchars($sUser); ?></li>
                                        </ul>
                                    </div>
                                    <div class="post-item-content">
                                        <h2><a href="blog-details.php?id=<?php echo htmlspecialchars($sId); ?>"><?php echo htmlspecialchars($sTitle); ?></a></h2>
                                    </div>
                                </div>
                                <div class="post-item-btn">
                                    <a href="blog-details.php?id=<?php echo htmlspecialchars($sId); ?>" class="readmore-btn">read more</a>
                                </div>
                            </div>
                        </div>
                        <!-- Post Item End -->
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Our Blog Section End -->




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
    <script>
        var url = 'https://wati-integration-service.clare.ai/ShopifyWidget/shopifyWidget.js?86687';
        var s = document.createElement('script');
        s.type = 'text/javascript';
        s.async = true;
        s.src = url;
        var options = {
            "enabled": true,
            "chatButtonSetting": {
                "backgroundColor": "#2ACA45;",
                "ctaText": "",
                "borderRadius": "25",
                "marginLeft": "20",
                "marginBottom": "30",
                "marginRight": "50",
                "position": "left"
            },
            "brandSetting": {
                "brandName": "Tee Mac Corporation",
                "brandSubTitle": "Typically replies within a day",
                "brandImg": "/images/favicon.png",
                "welcomeText": "Hi there!\nHow can I help you?",
                "messageText": "Hello, I have a question about ",
                "backgroundColor": "#2ACA45;",
                "ctaText": "Start Chat",
                "borderRadius": "25",
                "autoShow": false,
                "phoneNumber": "+919888536653"
            }
        };
        s.onload = function() {
            CreateWhatsappChatWidget(options);
        };
        var x = document.getElementsByTagName('script')[0];
        x.parentNode.insertBefore(s, x);
    </script>
</body>

</html>