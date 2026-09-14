<?php
require_once('admin/db/config.php');
require_once('fetch-all.php');
$page_seo_type = 'contact';

// Fetch the SEO data
$seo = get_seo_data($db, $page_seo_type);
?>
<!DOCTYPE html>
<html lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <title><?php echo htmlspecialchars($seo['title'] ?? 'Contact Us'); ?></title>
    <meta name="keywords" content="<?php echo htmlspecialchars($seo['keywords'] ?? ''); ?>">
    <meta name="description" content="<?php echo htmlspecialchars($seo['description'] ?? ''); ?>">
    
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($favicon ?? 'favicon.png') ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Onest:wght@100..900&display=swap" rel="stylesheet">
    
    <!-- CSS Files -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="css/slicknav.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/swiper-bundle.min.css">
    <link href="css/all.min.css" rel="stylesheet" media="screen">
    <link href="css/animate.css" rel="stylesheet">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/mousecursor.css">
    <link href="css/custom.css" rel="stylesheet" media="screen">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- intl-tel-input -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    
    <!-- Google reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>
    <?php require_once('includes/preloader.php'); ?>
    <?php require_once('includes/header.php'); ?>

    <!-- Page Header Section Start -->
    <div class="page-header parallaxie" style="background: url('<?= htmlspecialchars($bannerImage ?? 'images/default-banner.jpg') ?>') no-repeat;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header-box">
                        <h1 class="text-anime-style-3" data-cursor="-opaque">Contact Us</h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
                            </ol>
                        </nav>
                    </div>
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
                    <div class="contact-image-form-box">
                        <div class="contact-image-box">
                            <div class="contact-us-image">
                                <figure><img src="images/contact-us-img.jpg" alt="Contact Us"></figure>
                            </div>

                            <div class="contact-info-list wow fadeInUp">
                                <div class="contact-info-item">
                                    <div class="icon-box"><img src="images/icon-phone-white.svg" alt="Phone"></div>
                                    <div class="contact-info-content">
                                        <h3>Call Now!</h3>
                                        <p><a href="tel:+917380015666">+91 73800 15666</a></p>
                                    </div>
                                </div>
                                <div class="contact-info-item">
                                    <div class="icon-box"><img src="images/icon-mail-white.svg" alt="Email"></div>
                                    <div class="contact-info-content">
                                        <h3>E-mail Us!</h3>
                                        <p><a href="mailto:info@teemac.co.in">info@teemac.co.in</a></p>
                                    </div>
                                </div>
                                <div class="contact-info-item location-item">
                                    <div class="icon-box"><img src="images/icon-location-white.svg" alt="Location"></div>
                                    <div class="contact-info-content">
                                        <h3>Our Location!</h3>
                                        <p>355, 2nd Floor, SCO, Main Market, Sector 44D, Sector 44, Chandigarh, 160043</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Form Start -->
                        <div class="contact-form">
                            <div class="section-title">
                                <h3 class="wow fadeInUp">Contact Us</h3>
                                <h2 class="text-anime-style-3" data-cursor="-opaque">Get in touch with our team anytime today</h2>
                                <p class="wow fadeInUp" data-wow-delay="0.2s">Our team is always here to listen, support, and guide you.</p>
                            </div>

                            <form id="contactForm" method="POST" class="wow fadeInUp" data-wow-delay="0.4s">
                                <div class="row">
                                    <div class="form-group col-md-6 mb-4">
                                        <input type="text" name="fname" class="form-control" id="fname" placeholder="First Name" required>
                                    </div>
                                    <div class="form-group col-md-6 mb-4">
                                        <input type="text" name="lname" class="form-control" id="lname" placeholder="Last Name" required>
                                    </div>
                                    <div class="form-group col-md-6 mb-4">
                                        <input type="tel" name="phone" class="form-control" id="phone" placeholder="Mobile Number" required>
                                    </div>
                                    <div class="form-group col-md-6 mb-4">
                                        <input type="email" name="email" class="form-control" id="email" placeholder="E-mail Address" required>
                                    </div>
                                    <div class="form-group col-md-12 mb-4">
                                        <textarea name="message" class="form-control" id="message" rows="5" placeholder="Write your message here..." required></textarea>
                                    </div>
                                    
                                    <!-- Google reCAPTCHA -->
                                    <div class="form-group col-md-12 mb-4">
                                        <div class="g-recaptcha" data-sitekey="6LduZ7UtAAAAABPi4-m1AlD_Ua8LvGARfusTkN3T"></div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="contact-form-btn">
                                            <button type="submit" class="btn-default"><span>Send a Message</span></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- Contact Form End -->
                        </div>
                    </div>
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
                    <div class="google-map-iframe">
                        <iframe src="https://www.google.com/maps?q=355%2C%202nd%20Floor%2C%20SCO%2C%20Main%20Market%2C%20Sector%2044D%2C%20Sector%2044%2C%20Chandigarh%2C%20160043&output=embed" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Google Map End -->

    <?php require_once('includes/footer.php'); ?>

    <!-- JS Files -->
    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/swiper-bundle.min.js"></script>
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/SmoothScroll.js"></script>
    <script src="js/parallaxie.js"></script>
    <script src="js/gsap.min.js"></script>
    <script src="js/magiccursor.js"></script>
    <script src="js/SplitText.min.js"></script>
    <script src="js/ScrollTrigger.min.js"></script>
    <script src="js/jquery.mb.YTPlayer.min.js"></script>
    <script src="js/wow.min.js"></script>
    <script src="js/function.js"></script>
    <script src="../assets/js/theme-panel-dynamic.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- intl-tel-input -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize International Telephone Input
        var phoneInput = document.querySelector("#phone");
        var iti = window.intlTelInput(phoneInput, {
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            preferredCountries: ["in"],
            separateDialCode: true,
            initialCountry: "in"
        });

        $('#contactForm').on('submit', function(e) {
            e.preventDefault(); 
            
            // Get full international phone number (e.g., +919876543210)
            var fullPhoneNumber = iti.getNumber();
            var formData = $(this).serializeArray();
            
            // Override the phone value with the properly formatted international number
            var phoneIndex = formData.findIndex(item => item.name === 'phone');
            if (phoneIndex !== -1) {
                formData[phoneIndex].value = fullPhoneNumber;
            }

            var $btn = $(this).find('button[type="submit"]');
            var originalBtnText = $btn.html();
            
            // Frontend reCAPTCHA check
            if (typeof grecaptcha !== 'undefined' && grecaptcha.getResponse() === "") {
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Please complete the reCAPTCHA.' });
                return false;
            }

            $btn.prop('disabled', true).html('<span>Sending...</span>');

            $.ajax({
                url: 'process-contact.php', 
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Message Sent!',
                            text: response.message,
                            timer: 3000,
                            showConfirmButton: false
                        });
                        $('#contactForm')[0].reset();
                        iti.setCountry("in"); // Reset phone input
                        if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Validation Error', text: response.message });
                        if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
                    }
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Connection Error', text: 'An error occurred. Please try again later.' });
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalBtnText);
                }
            });
        });
    });
    </script>
</body>
</html>