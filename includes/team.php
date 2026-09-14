<?php
require_once('admin/db/config.php');

$teamMembers = [];
$stmtFetchTeam = $db->prepare("SELECT * FROM team_members WHERE status = 1 ORDER BY idteam_members ASC LIMIT 4");
$stmtFetchTeam->execute();
$teamMembers = $stmtFetchTeam->get_result()->fetch_all(MYSQLI_ASSOC);

$fallbackTeam = [
    ['member_name' => 'Event Planning', 'role' => 'Strategy & Planning', 'profile_picture' => 'images/speaker-item-image-1-metal.jpg', 'linkedin' => '#', 'twitter' => '#', 'facebook' => '#'],
    ['member_name' => 'Event Management', 'role' => 'Coordination & Execution', 'profile_picture' => 'images/speaker-item-image-2-metal.jpg', 'linkedin' => '#', 'twitter' => '#', 'facebook' => '#'],
    ['member_name' => 'Event Logistics', 'role' => 'Venue & On-Ground Support', 'profile_picture' => 'images/speaker-item-image-3-metal.jpg', 'linkedin' => '#', 'twitter' => '#', 'facebook' => '#'],
    ['member_name' => 'Event Production', 'role' => 'AV & Technical Solutions', 'profile_picture' => 'images/speaker-item-image-4-metal.jpg', 'linkedin' => '#', 'twitter' => '#', 'facebook' => '#'],
];

$displayTeam = !empty($teamMembers) ? $teamMembers : $fallbackTeam;
$delays = ['0s', '0.2s', '0.4s', '0.6s'];
?>

<!-- Our Team Section Start -->
<div class="our-speaker-metal">
    <div class="container">

        <div class="row section-row align-items-center">

            <div class="col-xl-6">

                <!-- Section Title Start -->
                <div class="section-title">

                    <h3 class="wow fadeInUp">
                        Our Team
                    </h3>

                    <h2 class="text-anime-style-3"
                        data-cursor="-opaque">
                        The people behind every successful Eventful experience
                    </h2>

                </div>
                <!-- Section Title End -->

            </div>


            <div class="col-xl-6">

                <!-- Section Content Button Start -->
                <div class="section-content-btn">

                    <!-- Section Title Content Start -->
                    <div class="section-title-content wow fadeInUp"
                        data-wow-delay="0.2s">

                        <p>
                            Our experienced team brings together creativity,
                            planning and execution to make every event seamless.
                            From the first idea to the final execution, we work
                            together to create experiences that leave a lasting
                            impression.
                        </p>

                    </div>
                    <!-- Section Title Content End -->


                    <!-- Section Button Start -->
                    <div class="section-btn wow fadeInUp"
                        data-wow-delay="0.4s">

                        <a href="our-team.php" class="btn-default">
                            Meet Our Team
                        </a>

                    </div>
                    <!-- Section Button End -->

                </div>
                <!-- Section Content Button End -->

            </div>

        </div>


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

                <div class="speaker-item-metal wow fadeInUp"
                    <?php if ($delay !== '0s'): ?>data-wow-delay="<?php echo $delay; ?>"<?php endif; ?>>

                    <!-- Team Item Image Start -->
                    <div class="speaker-item-image-metal">

                        <a href="team-single.html"
                            data-cursor-text="TMC">

                            <figure class="image-anime">

                                <img src="<?php echo htmlspecialchars($imagePath); ?>"
                                    alt="<?php echo htmlspecialchars($member['member_name']); ?>">

                            </figure>

                        </a>

                    </div>
                    <!-- Team Item Image End -->


                    <!-- Team Item Body Start -->
                    <div class="speaker-item-body-metal">

                        <!-- Team Item Content Start -->
                        <div class="speaker-item-content-metal">

                            <h3>
                                <a href="team-single.html">
                                    <?php echo htmlspecialchars($member['member_name']); ?>
                                </a>
                            </h3>

                            <p>
                                <?php echo htmlspecialchars($member['role']); ?>
                            </p>

                        </div>
                        <!-- Team Item Content End -->


                        <!-- Team Social List Start -->
                        <div class="speaker-social-list-metal">

                            <ul>

                                <?php foreach ($socialLinks as $social): ?>
                                <li>
                                    <a href="<?php echo htmlspecialchars($social['url']); ?>"
                                        aria-label="<?php echo $social['label']; ?>">
                                        <i class="fa-brands <?php echo $social['icon']; ?>"></i>
                                    </a>
                                </li>
                                <?php endforeach; ?>

                            </ul>

                        </div>
                        <!-- Team Social List End -->

                    </div>
                    <!-- Team Item Body End -->

                </div>

            </div>

            <?php endforeach; ?>

        </div>

    </div>
</div>
<!-- Our Team Section End -->
