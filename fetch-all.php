<?php
require_once('admin/db/config.php');

// hero
// Fetch latest active hero from database
$heroData = null;
$stmtHero = $db->prepare("SELECT * FROM hero WHERE status = 1 ORDER BY idhero DESC LIMIT 1");
$stmtHero->execute();
$heroData = $stmtHero->get_result()->fetch_assoc();

// Fallback data if database returns nothing
$fallbackHero = [
    'sub_title' => 'EVENTS • EXPERIENCES • CONNECTIONS',
    'main_title' => 'We Create Events That People Remember.',
    'description' => 'From medical conferences and corporate events to workshops, branded experiences, and live gatherings, we bring together the right people, ideas, and experiences to make every event impactful.',
    'image' => 'hero-bg.jpg'
];

// Use DB results if available, otherwise fallback
$displayHero = !empty($heroData) ? $heroData : $fallbackHero;

// Prepare background image path
$heroBgImage = !empty($displayHero['image']) ? 'admin/' . $displayHero['image'] : 'images/hero-bg.jpg';



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

// Ticker
// Fetch active tickers from DB
$tickers = [];
$stmtTicker = $db->prepare("SELECT * FROM ticker WHERE status = 1 ORDER BY idticker ASC");
$stmtTicker->execute();
$tickers = $stmtTicker->get_result()->fetch_all(MYSQLI_ASSOC);

// Fallback data if database returns nothing
$fallbackTickers = [
    ['title' => 'Medical Events'],
    ['title' => 'Conferences'],
    ['title' => 'Corporate Events'],
    ['title' => 'Workshops'],
    ['title' => 'Branded Events'],
    ['title' => 'Event Management'],
    ['title' => 'Event Logistics'],
    ['title' => 'Venue Management'],
    ['title' => 'Audio Visual Solutions'],
    ['title' => 'Seamless Experiences']
];

// Use DB results if available, otherwise fallback
$displayTickers = !empty($tickers) ? $tickers : $fallbackTickers;

// Generate the HTML for the ticker items once
$tickerHtml = '';
foreach ($displayTickers as $item) {
    $tickerHtml .= '
    <span>
        <img src="images/icon-asterisk.svg" alt="icon">
        ' . htmlspecialchars($item['title']) . '
    </span>';
}

// home about
// Fetch about section data from database
$aboutMetal = null;
$stmtAboutMetal = $db->prepare("SELECT * FROM about_section WHERE id = 1 LIMIT 1");
$stmtAboutMetal->execute();
$aboutMetal = $stmtAboutMetal->get_result()->fetch_assoc();

// Fallback data if database returns nothing
$fallbackAboutMetal = [
    'title' => 'Creating meaningful events that bring people, ideas and experiences together',
    'sub_title' => 'About Tee Mac Corporation',
    'content' => 'Tee Mac Corporation is a professional event management company dedicated to creating impactful and memorable experiences. From medical conferences and corporate events to workshops, seminars and branded experiences, we bring together creativity, strategy and seamless execution.',
    'benefits' => 'From planning and venue management to event logistics, production, audio-visual solutions and on-ground execution, our team takes care of every detail. We work closely with our clients to understand their objectives and deliver experiences that connect audiences, strengthen brands and leave a lasting impression.',
    'image' => 'about-us-image-1-metal.jpg',
    'image2' => 'about-us-image-2-metal.jpg'
];

// Use DB results if available, otherwise fallback
$displayAboutMetal = !empty($aboutMetal) ? $aboutMetal : $fallbackAboutMetal;

// Prepare image paths
$aboutImage1 = !empty($displayAboutMetal['image']) ? 'admin/about/' . $displayAboutMetal['image'] : 'images/about-us-image-1-metal.jpg';
$aboutImage2 = !empty($displayAboutMetal['image2']) ? 'admin/about/' . $displayAboutMetal['image2'] : 'images/about-us-image-2-metal.jpg';

// why choose us
// Fetch data from why_choose_us table
$whyChooseUs = null;
$stmtWhy = $db->prepare("SELECT * FROM why_choose_us WHERE id = 1 LIMIT 1");
$stmtWhy->execute();
$whyChooseUs = $stmtWhy->get_result()->fetch_assoc();

// Fallback data if database returns nothing
$fallbackWhy = [
    'sub_title' => 'Why Choose us',
    'title' => 'Everything you need to create an event that truly makes an impact',
    'content' => '<p>We combine strategic planning, creative thinking and seamless execution to deliver events that meet your objectives and create meaningful experiences for every attendee.</p>',
    'benefits' => '<ul>
        <li>End-to-end event planning and execution</li>
        <li>Experienced team for conferences, medical and corporate events</li>
        <li>Seamless venue, logistics and on-ground coordination</li>
        <li>Professional audio-visual and production support</li>
        <li>Creative solutions tailored to your event objectives</li>
        <li>Dedicated support from planning to completion</li>
    </ul>',
    'image' => 'our-benefits-image-1.jpg',
    'image2' => 'our-benefits-image-2.jpg'
];

// Use DB results if available, otherwise fallback
$displayWhy = !empty($whyChooseUs) ? $whyChooseUs : $fallbackWhy;

// Prepare image paths (adjust 'admin/' prefix based on your upload folder)
$benefitsImage1 = !empty($displayWhy['image']) ? 'admin/why_choose/' . $displayWhy['image'] : 'images/our-benefits-image-1.jpg';
$benefitsImage2 = !empty($displayWhy['image2']) ? 'admin/why_choose/' . $displayWhy['image2'] : 'images/our-benefits-image-2.jpg';


// counter
// Fetch counters data from database
$counterData = null;
$stmtCounters = $db->prepare("SELECT * FROM counters WHERE idcounters = 1 LIMIT 1");
$stmtCounters->execute();
$counterData = $stmtCounters->get_result()->fetch_assoc();

// Fallback data if database returns nothing
$fallbackCounters = [
    'title1' => 'Years of Experience', 'counter1' => '4',
    'title2' => 'National Conferences', 'counter2' => '50',
    'title3' => 'Online Meetups', 'counter3' => '100',
    'title4' => 'Live Surgeries', 'counter4' => '20'
];

// Use DB results if available, otherwise fallback
$displayCounters = !empty($counterData) ? $counterData : $fallbackCounters;

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



// footer details
// Fetch company info from database
$companyInfo = null;
$stmtCompany = $db->prepare("SELECT * FROM company_info WHERE id = 1 LIMIT 1");
$stmtCompany->execute();
$companyInfo = $stmtCompany->get_result()->fetch_assoc();

// Fallback data if database returns nothing
$fallbackCompany = [
    'name' => 'Tee Mac Corporation',
    'about_company' => 'Experience professionally curated medical events designed to inspire innovation, empower healthcare professionals, and connect leaders from across the medical community.',
    'address' => '355, 2nd Floor, SCO, Main Market, Sector 44D',
    'city' => 'Chandigarh',
    'state' => 'Chandigarh',
    'country' => 'India',
    'phone1' => '+91 73800 15666',
    'phone2' => '',
    'email' => 'info@teemac.co.in',
    'fax_number' => ''
];

// Use DB results if available, otherwise fallback
$displayCompany = !empty($companyInfo) ? $companyInfo : $fallbackCompany;

// Build full address
$fullAddress = array_filter([
    $displayCompany['address'],
    $displayCompany['city'],
    $displayCompany['state'],
    $displayCompany['country']
]);
$formattedAddress = implode(', ', $fullAddress);


// social media
// Fetch the single row of social links
$stmtSocial = $db->prepare("SELECT * FROM social_link LIMIT 1");
$stmtSocial->execute();
$socialRow = $stmtSocial->get_result()->fetch_assoc();

$displaySocialLinks = [];

if ($socialRow) {
    // 1. Facebook
    if (!empty($socialRow['facebook'])) {
        $displaySocialLinks[] = ['platform' => 'facebook', 'url' => $socialRow['facebook'], 'icon' => 'fab fa-facebook-f'];
    }
    
    // 2. Instagram
    if (!empty($socialRow['instagram'])) {
        $displaySocialLinks[] = ['platform' => 'instagram', 'url' => $socialRow['instagram'], 'icon' => 'fa-brands fa-instagram'];
    }
    
    // 3. Twitter / X 
  $twitterUrl = $socialRow['twiter'] ?? $socialRow['twitter'] ?? $socialRow['twitter_url'] ?? '';
if (!empty($twitterUrl)) {
    $displaySocialLinks[] = [
        'platform' => 'twitter', 
        'url' => $twitterUrl, 
        'icon' => 'fa-brands fa-x-twitter'
    ];
}
    
    // 4. LinkedIn
    if (!empty($socialRow['linkedin'])) {
        $displaySocialLinks[] = ['platform' => 'linkedin', 'url' => $socialRow['linkedin'], 'icon' => 'fa-brands fa-linkedin'];
    }
}

// Fallback 
if (empty($displaySocialLinks)) {
    $displaySocialLinks = [
        ['platform' => 'facebook', 'url' => 'https://facebook.com', 'icon' => 'fab fa-facebook-f'],
        ['platform' => 'instagram', 'url' => 'https://instagram.com', 'icon' => 'fa-brands fa-instagram'],
        ['platform' => 'twitter', 'url' => 'https://twitter.com', 'icon' => 'fa-brands fa-x-twitter'],
        ['platform' => 'linkedin', 'url' => 'https://linkedin.com', 'icon' => 'fa-brands fa-linkedin']
    ];
}

// logo
// Fetch system settings from database
$systemSettings = null;
$stmtSettings = $db->prepare("SELECT * FROM system_setting WHERE id = 1 LIMIT 1");
$stmtSettings->execute();
$systemSettings = $stmtSettings->get_result()->fetch_assoc();

// Fallback data if database returns nothing
$fallbackSettings = [
    'backpanel_logo' => 'backpanel.jpg',
    'favicon' => 'tmc.png',
    'black_image' => 'tmc.png',      // For light backgrounds
    'white_image' => 'tmc.png',      // For dark backgrounds (footer)
    'helpdesk' => '+91 98885 36653'
];

// Use DB results if available, otherwise fallback
$displaySettings = !empty($systemSettings) ? $systemSettings : $fallbackSettings;

// Determine which logo to use for footer (white_image for dark footer)
$footerLogo = !empty($displaySettings['white_image']) ? 'admin/logo/' . $displaySettings['white_image'] : 'images/logo.svg';
$siteLogo = !empty($displaySettings['black_image']) ? 'admin/logo/' . $displaySettings['black_image'] : 'images/logo.svg';
$favicon = !empty($displaySettings['favicon']) ? 'admin/logo/' . $displaySettings['favicon'] : 'images/favicon.ico';




?>

