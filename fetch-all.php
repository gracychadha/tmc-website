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

// Fetch active services from DB
$homeServices = [];
$stmtHomeServices = $db->prepare("SELECT * FROM services WHERE status = 1 ORDER BY idservices ASC");
$stmtHomeServices->execute();
$homeServices = $stmtHomeServices->get_result()->fetch_all(MYSQLI_ASSOC);

// Fallback services if DB returns nothing
$fallbackHomeServices = [
    [
        'idservices'  => '1',
        'title'       => 'Medical Events',
        'description' => 'From medical conferences and seminars to professional gatherings, we create well-organised experiences for the healthcare community.',
        'icon'        => 'images/icon-service-1-metal.svg'
    ],
    [
        'idservices'  => '2',
        'title'       => 'Conferences & Workshops',
        'description' => 'We manage conferences, workshops and seminars with thoughtful planning, engaging formats and seamless on-ground execution.',
        'icon'        => 'images/icon-service-2-metal.svg'
    ],
    [
        'idservices'  => '3',
        'title'       => 'Corporate Events',
        'description' => 'From corporate gatherings to brand-focused experiences, we help businesses create events that connect teams, audiences and ideas.',
        'icon'        => 'images/icon-service-3-metal.svg'
    ],
    [
        'idservices'  => '4',
        'title'       => 'Event Logistics',
        'description' => 'We coordinate venues, registrations, hospitality, transportation and on-ground requirements to keep every event running smoothly.',
        'icon'        => 'images/icon-service-4-metal.svg'
    ],
    [
        'idservices'  => '5',
        'title'       => 'Audio Visual Solutions',
        'description' => 'From sound and lighting to screens and event production, we provide the technical support needed for a powerful event experience.',
        'icon'        => 'images/icon-service-5-metal.svg'
    ],
];

// Use DB results if available, otherwise fallback
$displayHomeServices = !empty($homeServices) ? $homeServices : $fallbackHomeServices;


$homeFaqs = [];
$stmtHomeFaqs = $db->prepare("SELECT * FROM faqs WHERE status = 1 ORDER BY faqs_id ASC LIMIT 5");
$stmtHomeFaqs->execute();
$homeFaqs = $stmtHomeFaqs->get_result()->fetch_all(MYSQLI_ASSOC);

$fallbackHomeFaqs = [
    ['question' => 'No FAQ added', 'answer' => 'No FAQ added yet. Please add FAQs from the admin panel.'],
];

$displayHomeFaqs = !empty($homeFaqs) ? $homeFaqs : $fallbackHomeFaqs;


// Fetch latest 4 active events
$homeEvents = [];
$stmtHomeEvents = $db->prepare("SELECT * FROM event WHERE status = 1 ORDER BY date DESC LIMIT 4");
$stmtHomeEvents->execute();
$homeEvents = $stmtHomeEvents->get_result()->fetch_all(MYSQLI_ASSOC);

// Fallback events if DB returns nothing
$fallbackEvents = [
    [
        'idevent' => '1',
        'title' => 'Medical Conference',
        'description' => '<p>Professional conferences designed to connect healthcare professionals, experts and industry leaders.</p>',
        'image' => 'event/medical-conference.jpg',
        'date' => '2026-09-15',
        'slug' => 'medical-conference'
    ],
    [
        'idevent' => '2',
        'title' => 'Corporate Events',
        'description' => '<p>Engaging corporate experiences that bring teams, brands and audiences together.</p>',
        'image' => 'event/corporate-event.jpg',
        'date' => '2026-09-20',
        'slug' => 'corporate-events'
    ],
    [
        'idevent' => '3',
        'title' => 'Workshops & Seminars',
        'description' => '<p>Interactive learning experiences that encourage knowledge sharing, discussion and meaningful connections.</p>',
        'image' => 'event/workshop.jpg',
        'date' => '2026-09-25',
        'slug' => 'workshops-seminars'
    ]
];

// Use DB results if available, otherwise fallback
$displayEvents = !empty($homeEvents) ? $homeEvents : $fallbackEvents;

// Helper function to format date
function formatEventDate($dateString) {
    $timestamp = strtotime($dateString);
    return date('M d, Y', $timestamp);
}




// event page

// Fetch active schedule items from DB (Limit to 6 for this grid)
$scheduleItems = [];
$stmtSchedule = $db->prepare("SELECT * FROM event WHERE status = 1 ORDER BY date DESC");
$stmtSchedule->execute();
$scheduleItems = $stmtSchedule->get_result()->fetch_all(MYSQLI_ASSOC);

// Fallback data if database returns nothing
$fallbackSchedule = [
    [
        'idevent'  => '1',
        'title'       => 'Creative Entrepreneurship Forum',
        'description' => 'Unlock your potential and elevate your career with our Professional Skills Development designed.',
        'image'       => 'images/event-schedule-image-1.jpg',
        'time'        => '8 AM - 4 PM',
        'location'    => 'Sterling Conference Hall'
    ],
    [
        'idevent'  => '2',
        'title'       => 'Global HR Excellence Workshop',
        'description' => 'Unlock your potential and elevate your career with our Professional Skills Development designed.',
        'image'       => 'images/event-schedule-image-2.jpg',
        'time'        => '10 AM - 6 PM',
        'location'    => 'Crescent Academy'
    ],
    [
        'idevent'  => '3',
        'title'       => 'Sustainable Business Leadership Meet',
        'description' => 'Unlock your potential and elevate your career with our Professional Skills Development designed.',
        'image'       => 'images/event-schedule-image-3.jpg',
        'time'        => '7:30 AM - 3 PM',
        'location'    => 'Emerald Royale Hall'
    ]
    // Add more fallback items if needed
];

// Use DB results if available, otherwise fallback
$displaySchedule = !empty($scheduleItems) ? $scheduleItems : $fallbackSchedule;


// breadcrumb
// Fetch banner image from common_banner table
$pageBanner = null;
$stmtBanner = $db->prepare("SELECT * FROM common_banner WHERE status = 1 LIMIT 1");
$stmtBanner->execute();
$pageBanner = $stmtBanner->get_result()->fetch_assoc();

// Fallback image if no banner found in database
$bannerImage = !empty($pageBanner['image']) ? 'admin/' . $pageBanner['image'] : 'images/page-header-bg.jpg';


// about us on about page
// Fetch about section data from database
$aboutSection = null;
$stmtAbout = $db->prepare("SELECT * FROM about_section WHERE id = 1 LIMIT 1");
$stmtAbout->execute();
$aboutSection = $stmtAbout->get_result()->fetch_assoc();

// Fallback data if database returns nothing
$fallbackAbout = [
    'title' => 'About Us',
    'sub_title' => 'Professional Medical Event Management Solutions',
    'content' => 'Established in Chandigarh, India, Tee Mac Corporation is a professional medical event management company dedicated to delivering creative and reliable solutions for medical events.',
    'benefits' => 'From medical seminars, conferences and workshops to other professional events, our experienced team manages every aspect of the event with attention to detail and a commitment to delivering the highest standards of service.',
    'image' => 'about-us-image.jpg',
    'image2' => 'about-achievement-image.png'
];

// Use DB results if available, otherwise fallback
$displayAbout = !empty($aboutSection) ? $aboutSection : $fallbackAbout;

// Prepare image paths
$aboutImage = !empty($displayAbout['image']) ? 'admin/about/' . $displayAbout['image'] : 'images/about-us-image.jpg';
$aboutImage2 = !empty($displayAbout['image2']) ? 'admin/about/' . $displayAbout['image2'] : 'images/about-achievement-image.png';


// approach
// Fetch approach section data from database
$approachData = null;
$stmtApproach = $db->prepare("SELECT * FROM approach WHERE id = 1 LIMIT 1");
$stmtApproach->execute();
$approachData = $stmtApproach->get_result()->fetch_assoc();

// Fallback data if database returns nothing
$fallbackApproach = [
    'title' => 'Our Approach',
    'sub_title' => 'A strategic approach to delivering seamless medical events',
    'step1' => 'Seamless Execution',
    'step2' => 'Collaborative Planning',
    'step3' => 'Continuous Improvement',
    'description1' => 'From planning to execution, we manage every detail with precision to ensure your medical event runs smoothly and successfully.',
    'description2' => 'We work closely with clients, healthcare professionals, speakers and partners to create well-structured and impactful events.',
    'description3' => 'We continuously refine our processes, learn from every event and embrace innovative ideas to deliver better experiences every time.',
    'image1' => 'our-approach-image-1.jpg',
    'image2' => 'our-approach-image-2.jpg',
    'image3' => 'our-approach-image-3.jpg'
];

// Use DB results if available, otherwise fallback
$displayApproach = !empty($approachData) ? $approachData : $fallbackApproach;

// Prepare image paths
$approachImage1 = !empty($displayApproach['image1']) ? 'admin/approach/' . $displayApproach['image1'] : 'images/our-approach-image-1.jpg';
$approachImage2 = !empty($displayApproach['image2']) ? 'admin/approach/' . $displayApproach['image2'] : 'images/our-approach-image-2.jpg';
$approachImage3 = !empty($displayApproach['image3']) ? 'admin/approach/' . $displayApproach['image3'] : 'images/our-approach-image-3.jpg';



// achievements

// Fetch achievements section data from database
$achievementsData = null;
$stmtAchievements = $db->prepare("SELECT * FROM achievements WHERE id = 1 LIMIT 1");
$stmtAchievements->execute();
$achievementsData = $stmtAchievements->get_result()->fetch_assoc();

// Fallback data if database returns nothing
$fallbackAchievements = [
    'title' => 'Our Achievements',
    'sub_title' => 'Delivering excellence through experience and expertise',
    'content' => 'Our journey is built on experience, strong partnerships, meticulous planning, and a commitment to delivering impactful medical and corporate events that create meaningful experiences.',
    'image' => 'our-achievements-image.jpg',
    'achievementscol' => json_encode([
        ['icon' => 'icon-our-achievement-1.svg', 'number' => '15', 'suffix' => '+', 'label' => 'Years of Industry Experience'],
        ['icon' => 'icon-our-achievement-2.svg', 'number' => '500', 'suffix' => '+', 'label' => 'Events Successfully Delivered'],
        ['icon' => 'icon-our-achievement-3.svg', 'number' => '50', 'suffix' => 'K+', 'label' => 'Professionals Engaged'],
        ['icon' => 'icon-our-achievement-4.svg', 'number' => '100', 'suffix' => '+', 'label' => 'Trusted Clients & Partners']
    ])
];

// Use DB results if available, otherwise fallback
$displayAchievements = !empty($achievementsData) ? $achievementsData : $fallbackAchievements;

// Prepare image path
$achievementsImage = !empty($displayAchievements['image']) ? 'admin/achievements/' . $displayAchievements['image'] : 'images/our-achievements-image.jpg';

// Decode achievements items
$achievementsItems = !empty($displayAchievements['achievementscol']) 
    ? json_decode($displayAchievements['achievementscol'], true) 
    : [];

// If achievementscol is empty or invalid, use fallback items
if (empty($achievementsItems) || !is_array($achievementsItems)) {
    $achievementsItems = [
        ['icon' => 'icon-our-achievement-1.svg', 'number' => '15', 'suffix' => '+', 'label' => 'Years of Industry Experience'],
        ['icon' => 'icon-our-achievement-2.svg', 'number' => '500', 'suffix' => '+', 'label' => 'Events Successfully Delivered'],
        ['icon' => 'icon-our-achievement-3.svg', 'number' => '50', 'suffix' => 'K+', 'label' => 'Professionals Engaged'],
        ['icon' => 'icon-our-achievement-4.svg', 'number' => '100', 'suffix' => '+', 'label' => 'Trusted Clients & Partners']
    ];
}

?>