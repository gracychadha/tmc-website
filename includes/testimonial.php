<?php
require_once('admin/db/config.php');

// fetch active testimonials
$testimonials = [];
$stmtFetchTestimonial = $db->prepare("SELECT * FROM testimonials WHERE status = 1 ORDER BY id ASC");
$stmtFetchTestimonial->execute();
$testimonials = $stmtFetchTestimonial->get_result()->fetch_all(MYSQLI_ASSOC);

$fallbackTestimonials = [
    ['name' => 'Kristin Watson', 'designation' => 'Senior Developer', 'message' => 'Attending this conference was one of the best professional decisions I\'ve made. The sessions were not just informative but incredibly inspiring. I connected with industry experts and gained insights I could apply immediately.'],
    ['name' => 'Dianne Russell', 'designation' => 'Startup Founder', 'message' => 'The conference gave me insights that I could immediately apply to my business. The speakers were truly world-class, and the discussions were rich with practical knowledge that made a real difference.'],
    ['name' => 'Jacob Jones', 'designation' => 'UX Designer at Bright Studio', 'message' => 'From the energy of the crowd to the quality of the presentations, everything about this event was exceptional. I enjoyed the hands-on workshops and loved how approachable the speakers were.'],
    ['name' => 'Darrell Steward', 'designation' => 'UX Designer at Studio', 'message' => 'From the energy of the crowd to the quality of the presentations, everything about this event was exceptional. The networking opportunities were unmatched and I left with meaningful professional connections.'],
];

$fallbackLogos = [
    'images/testimonial-logo-1-metal.svg',
    'images/testimonial-logo-2-metal.svg',
    'images/testimonial-logo-3-metal.svg',
    'images/testimonial-logo-4-metal.svg',
];

$displayTestimonials = !empty($testimonials) ? $testimonials : $fallbackTestimonials;
?>

<div class="our-testimonials-metal dark-section">
    <div class="container">
        <div class="row section-row">
            <div class="col-lg-12">
                <!-- Section Title Start -->
                <div class="section-title section-title-center">
                    <h3 class="wow fadeInUp">Our Testimonials</h3>
                    <h2 class="text-anime-style-3" data-cursor="-opaque">Honest impressions from attendees who joined us on journey</h2>
                </div>
                <!-- Section Title End -->
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <!-- Testimonial Slider Start -->
                <div class="testimonial-slider-metal wow fadeInUp" data-wow-delay="0.2s">
                    <div class="swiper">
                        <div class="swiper-wrapper" data-cursor-text="Drag">
                            <?php foreach ($displayTestimonials as $index => $item): ?>
                                <?php $logoIndex = $index % count($fallbackLogos); ?>
                                <!-- Testimonial Slide Start -->
                                <div class="swiper-slide">
                                    <!-- Testimonial Item Start -->
                                    <div class="testimonial-item-metal">
                                        <!-- Testimonial Item Header Start -->
                                        <div class="testimonial-item-header-metal">
                                            
                                            <div class="testimonial-item-quote-metal">
                                                <img src="images/testimonial-quote-metal.svg" alt="">
                                            </div>
                                        </div>
                                        <!-- Testimonial Item Header End -->

                                        <!-- Testimonial Item Body Start -->
                                        <div class="testimonial-item-body-metal">
                                            <div class="testimonial-item-content-metal">
                                                <p class="text-justify"><?php echo htmlspecialchars($item['message']); ?></p>
                                            </div>
                                            <div class="testimonials-author-content-metal">
                                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                                <p><?php echo htmlspecialchars($item['designation']); ?></p>
                                            </div>
                                        </div>
                                        <!-- Testimonial Item Body End -->
                                    </div>
                                    <!-- Testimonial Item End -->
                                </div>
                                <!-- Testimonial Slide End -->
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <!-- Testimonial Slider End -->
            </div>
        </div>
    </div>
</div>
