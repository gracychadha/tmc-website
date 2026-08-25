<?php
require_once('admin/db/config.php');

// Form submission handler
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["name"])) {
    // Sanitize inputs
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $service = trim(filter_input(INPUT_POST, 'service', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_NUMBER_INT));
    $message = trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    $errors = [];

    // Validation
    if (empty($name) || !preg_match("/^[a-zA-Z\s]{2,50}$/", $name)) {
        $errors[] = "Please enter a valid name (2–50 letters and spaces only).";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (empty($service) || !in_array($service, array_column($services, 'title'))) {
        $errors[] = "Please select a valid service.";
    }

    if (empty($phone) || !preg_match('/^[\+]?[0-9]{10,15}$/', $phone)) {
        $errors[] = "Please enter a valid phone number (10–15 digits).";
    }

    if (empty($message) || strlen($message) < 10) {
        $errors[] = "Message must be at least 10 characters long.";
    }

    if (empty($recaptcha_response)) {
        $errors[] = "Please complete the reCAPTCHA to prove you're not a robot.";
    }

    // Return validation errors
    if (!empty($errors)) {
        echo json_encode(["status" => "error", "message" => implode(" ", $errors)]);
        exit();
    }

    // Verify reCAPTCHA with Google
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_data = [
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($recaptcha_data)
        ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($recaptcha_url, false, $context);

    if (!$result) {
        echo json_encode(["status" => "error", "message" => "reCAPTCHA verification failed. Network error."]);
        exit();
    }

    $captcha_result = json_decode($result);

    if (!$captcha_result->success) {
        echo json_encode(["status" => "error", "message" => "reCAPTCHA verification failed. Please try again."]);
        exit();
    }

    // Insert into database (prepared statement)
    $stmt = $db->prepare("INSERT INTO contact (name, email, phone, service, message) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $db->error]);
        exit();
    }

    $stmt->bind_param("sssss", $name, $email, $phone, $service, $message);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Thank you, $name! We've received your message and will get back to you soon."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to submit. Please try again later."
        ]);
    }

    $stmt->close();
    exit();
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
    <title>Contact Us - Tee Mac Corporation</title>
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
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- intl-tel-input -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

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
                        <h1 class="text-anime-style-3" data-cursor="-opaque">Contact us</h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- Page Header Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header Section End -->

    <!-- Page Contact Us Start -->
    <div class="page-contact-us">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <!-- Contact Image Form Box Start -->
                    <div class="contact-image-form-box">
                        <div class="contact-image-box">
                            <!-- Contact Us Image Start -->
                            <div class="contact-us-image">
                                <figure>
                                    <img src="images/contact-us-img.jpg" alt="">
                                </figure>
                            </div>
                            <!-- Contact Us Image End -->

                            <!-- Contact Info List Start -->
                            <div class="contact-info-list wow fadeInUp">
                                <!-- Conatct Info Item Start -->
                                <div class="contact-info-item">
                                    <div class="icon-box">
                                        <img src="images/icon-phone-white.svg" alt="">
                                    </div>
                                    <div class="contact-info-content">
                                        <h3>Call Now!</h3>
                                        <p><a href="tel:+917380015666">+91 73800 15666</a></p>
                                    </div>
                                </div>
                                <!-- Conatct Info Item End -->

                                <!-- Conatct Info Item Start -->
                                <div class="contact-info-item">
                                    <div class="icon-box">
                                        <img src="images/icon-mail-white.svg" alt="">
                                    </div>
                                    <div class="contact-info-content">
                                        <h3>E-mail Us!</h3>
                                        <p><a href="mailto:info@teemac.co.in" target="_blank">info@teemac.co.in</a></p>
                                    </div>
                                </div>
                                <!-- Conatct Info Item End -->

                                <!-- Conatct Info Item Start -->
                                <div class="contact-info-item location-item">
                                    <div class="icon-box">
                                        <img src="images/icon-location-white.svg" alt="">
                                    </div>
                                    <div class="contact-info-content">
                                        <h3>Our Location!</h3>
                                        <p>355, 2nd Floor, SCO, Main Market, Sector 44D, Sector 44, Chandigarh, 160043</p>
                                    </div>
                                </div>
                                <!-- Conatct Info Item End -->
                            </div>
                            <!-- Contact Info List End -->
                        </div>

                        <!-- Contact Form Start -->
                        <div class="contact-form">
                            <!-- Section Title Start -->
                            <div class="section-title">
                                <h3 class="wow fadeInUp">Contact Us</h3>
                                <h2 class="text-anime-style-3" data-cursor="-opaque">Get in touch with our team anytime today</h2>
                                <p class="wow fadeInUp" data-wow-delay="0.2s">Our team is always here to listen, support, and guide you. Whether you have questions, need assistance, or want to discuss your next project or event.</p>
                            </div>
                            <!-- Section Title End -->

                            <!-- Contact Form Start -->
                            <form id="contactForm" action="#" method="POST" data-toggle="validator" class="wow fadeInUp" data-wow-delay="0.4s">
                                <div class="row">
                                    <div class="form-group col-md-6 mb-4">
                                        <input type="text" name="fname" class="form-control" id="fname" placeholder="First Name" required="">
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <input type="text" name="lname" class="form-control" id="lname" placeholder="Last Name" required="">
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <input type="number" name="phone" class="form-control" id="phone" placeholder="Mobile Number" required="">
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <input type="email" name="email" class="form-control" id="email" placeholder="E-mail Address" required="">
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-12 mb-5">
                                        <textarea name="message" class="form-control" id="message" rows="5" placeholder="Write your message here..."></textarea>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="contact-form-btn">
                                            <button type="submit" class="btn-default"><span>Send a Message</span></button>
                                            <div id="msgSubmit" class="h3 hidden"></div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- Contact Form End -->
                        </div>
                        <!-- Contact Form End -->
                    </div>
                    <!-- Contact Image Form Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Contact Us End -->

    <!-- Google Map Start -->
    <div class="google-map">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Google Map IFrame Start -->
                    <div class="google-map-iframe">
                        <iframe
                            src="https://www.google.com/maps?q=355%2C%202nd%20Floor%2C%20SCO%2C%20Main%20Market%2C%20Sector%2044D%2C%20Sector%2044%2C%20Chandigarh%2C%20160043&output=embed"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>

                    </div>
                    <!-- Google Map IFrame End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Google Map End -->

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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <!-- intl-tel-input -->
    <script>
        fetch("https://ipapi.co/json/")
            .then(response => response.json())
            .then(data => {
                const userCountry = data.country_code.toLowerCase() || "us";
                const phoneInput = document.querySelector("#mobile");
                window.intlTelInput(phoneInput, {
                    initialCountry: userCountry,
                    strictMode: true,
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
                });
            })
            .catch(() => {
                // Fallback if API fails
                const phoneInput = document.querySelector("#mobile");
                window.intlTelInput(phoneInput, {
                    initialCountry: "us",
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
                });
            });
    </script>

    <!-- AJAX Form Handler -->
    <script>
        $(document).ready(function() {
            $('#contactForm').on('submit', function(e) {
                e.preventDefault();

                const recaptchaResponse = grecaptcha.getResponse();
                if (!recaptchaResponse) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'reCAPTCHA Required',
                        text: 'Please complete the reCAPTCHA to proceed.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Sending...',
                    text: 'Please wait while we process your request.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: 'contact-us.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: res.message,
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                $('#contactForm')[0].reset();
                                grecaptcha.reset();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: res.message,
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Network Error',
                            text: 'Unable to connect. Please check your internet connection.',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>