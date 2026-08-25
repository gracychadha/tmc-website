-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2024 at 11:24 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `business`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `actvities_id` int(11) NOT NULL,
  `Activities_Name` varchar(255) NOT NULL,
  `Type` varchar(255) NOT NULL,
  `Add_Details` longtext NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1',
  `history` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `mobile` varchar(100) NOT NULL,
  `Staff_Email` varchar(200) NOT NULL,
  `activation_token` varchar(300) NOT NULL,
  `_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`username`, `password`, `email`, `status`, `history`, `type`, `mobile`, `Staff_Email`, `activation_token`, `_id`) VALUES
('amit', '202cb962ac59075b964b07152d234b70', 'rakeshrai@gmail.com', '1', '09-16-2023 ', '', '9870443528', '', 'd1b6788d68c8bc592489c7e2f5c5784c', 1),
('vibrantick', '202cb962ac59075b964b07152d234b70', 'vibrantick@gmail.com', '1', '03-01-2024 ', '', '8547399999', '', '918dfbb1b8261f442fc9bc325dd6b497', 2),
('abhi', '202cb962ac59075b964b07152d234b70', 'abhi@gmail.com', '1', '03-05-2024 ', '', '7885994943', '', '5bc0268e03feb6b153736943c7c378df', 3);

-- --------------------------------------------------------

--
-- Table structure for table `admin_permissions`
--

CREATE TABLE `admin_permissions` (
  `_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `navigation_menu_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_permissions`
--

INSERT INTO `admin_permissions` (`_id`, `admin_id`, `navigation_menu_id`) VALUES
(7, 1, 1),
(8, 2, 2),
(9, 2, 3),
(10, 2, 4),
(11, 2, 5),
(12, 2, 1),
(29, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `b_id` int(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `hotel` varchar(100) NOT NULL,
  `date` text NOT NULL,
  `duration` varchar(100) NOT NULL,
  `subject` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`b_id`, `username`, `phone`, `email`, `country`, `hotel`, `date`, `duration`, `subject`) VALUES
(15, 'simranjit', '8360236135', 'sk9150049@gmail.com', 'India', '5 Estrellas', '11-17-2023 ', '1', ' yes'),
(28, 'Test', '1234567890', '1@gmail.com', 'India', 'Los Hoteles de Lujos', '01-17-2024 ', '10', ' ');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `parent_category` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`, `parent_category`, `status`) VALUES
(45, 'Frontend Development', 'Web development', '1'),
(46, 'Backend Development', 'Web development', '1'),
(47, 'Machine learning', 'Artificial Intelligence', '1'),
(48, 'Deep Learning', 'Artificial Intelligence', '1'),
(49, 'Ethical Hacking', 'Cyber Security', '1');

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE `city` (
  `city_id` int(11) NOT NULL,
  `city_name` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `Client_id` int(11) NOT NULL,
  `Client_name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1',
  `created_date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`Client_id`, `Client_name`, `image`, `status`, `created_date`) VALUES
(6, 'design', '65fd6fd5deb48_1.png', '1', '2024-03-22 17:17:33 PM'),
(7, 'Wenelux', '65fd6ffc9baa2_11.png', '1', '2024-03-22 17:18:12 PM'),
(8, 'Amotrio', '65fd701aa0a4e_2.png', '1', '2024-03-22 17:18:42 PM'),
(9, 'Happenz', '65fd702e7e2f5_3.png', '1', '2024-03-22 17:19:02 PM'),
(10, 'Mark', '65fd703bd29ef_4.png', '1', '2024-03-22 17:19:15 PM'),
(11, 'Overlap', '65fd704be2709_5.png', '1', '2024-03-22 17:19:31 PM'),
(12, 'Casual', '65fd705ae1c86_6.png', '1', '2024-03-22 17:19:46 PM'),
(13, 'Seavanes', '65fd706e866e5_7.png', '1', '2024-03-22 17:20:06 PM'),
(16, 'Happenz', '6603bfff62bba_2.jpg', '1', '2024-03-22 17:20:58 PM');

-- --------------------------------------------------------

--
-- Table structure for table `company_address`
--

CREATE TABLE `company_address` (
  `id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `postal_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_address`
--

INSERT INTO `company_address` (`id`, `address`, `city`, `state`, `country`, `postal_code`) VALUES
(1, 'Business Centre,Sharjah, UAE', 'Sharjah Publishing City Free Zone', 'Sharjah', 'United Arab Emirates', '123222');

-- --------------------------------------------------------

--
-- Table structure for table `company_info`
--

CREATE TABLE `company_info` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `fax` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `about_us` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_info`
--

INSERT INTO `company_info` (`id`, `company_name`, `email`, `mobile`, `fax`, `website`, `about_us`) VALUES
(1, 'Electro World', 'info@electroworldfze.com', '+147785455669', '343235', 'https://electroworldfze.com/', 'Electro World FZE is a leading company in the electrical industry with a glorious track record of over 2 decades.  we believe that our competitive edge lies in product innovation as well as superior quality and ready availability.');

-- --------------------------------------------------------

--
-- Table structure for table `google_captcha`
--

CREATE TABLE `google_captcha` (
  `captcha_id` int(11) NOT NULL,
  `site_key` varchar(255) NOT NULL,
  `secret_key` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `google_captcha`
--

INSERT INTO `google_captcha` (`captcha_id`, `site_key`, `secret_key`) VALUES
(1, '6LcIG2IoAAAAABxtlR3P5EsEUoQcQZZABFFuIVlj', '6LcIG2IoAAAAAKlroahk-ydH_xFhfpZ4f9rNEQoL');

-- --------------------------------------------------------

--
-- Table structure for table `horizontal_ad`
--

CREATE TABLE `horizontal_ad` (
  `ad_id` int(11) NOT NULL,
  `ad_title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1',
  `ad_date` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `horizontal_ad`
--

INSERT INTO `horizontal_ad` (`ad_id`, `ad_title`, `image`, `status`, `ad_date`) VALUES
(8, 'testing', '6603c510cb36c_3.jpg', '1', '2024-03-27 12:15:07 PM');

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `lead_id` int(11) NOT NULL,
  `lead_name` varchar(255) NOT NULL,
  `lead_company` varchar(255) NOT NULL,
  `lead_email` varchar(255) NOT NULL,
  `lead_source` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `localization`
--

CREATE TABLE `localization` (
  `id` int(11) NOT NULL,
  `website_language` varchar(100) NOT NULL,
  `website_timezone` varchar(100) NOT NULL,
  `website_date_format` varchar(100) NOT NULL,
  `website_time_format` varchar(100) NOT NULL,
  `website_starting_month` varchar(100) NOT NULL,
  `website_financial_year` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `localization`
--

INSERT INTO `localization` (`id`, `website_language`, `website_timezone`, `website_date_format`, `website_time_format`, `website_starting_month`, `website_financial_year`, `status`) VALUES
(1, 'English', 'UTC+05:30', 'DD-MM-YYYY', '12 Hours', 'April', '2024', '1');

-- --------------------------------------------------------

--
-- Table structure for table `login_settings`
--

CREATE TABLE `login_settings` (
  `id` int(11) NOT NULL,
  `backend_panel_logo` varchar(255) NOT NULL,
  `favicon` varchar(255) NOT NULL,
  `landing_page_logo` varchar(255) NOT NULL,
  `helpdesk_no` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_settings`
--

INSERT INTO `login_settings` (`id`, `backend_panel_logo`, `favicon`, `landing_page_logo`, `helpdesk_no`) VALUES
(1, 'logo/backend_panel_logo/logo200x47.svg', 'logo/favicon/fav.png', 'logo/landing_page_logo/logo.svg', '+91-9870443528');

-- --------------------------------------------------------

--
-- Table structure for table `map`
--

CREATE TABLE `map` (
  `map_id` int(11) NOT NULL,
  `map_api_key` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `map`
--

INSERT INTO `map` (`map_id`, `map_api_key`) VALUES
(1, 'https://maps.google.com/maps?q=rstheme&amp;t=&amp;z=13&amp;ie=UTF8&amp;iwloc=&amp;output=embed');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `media_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media_dest`
--

CREATE TABLE `media_dest` (
  `media_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `dest_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `media_dest`
--

INSERT INTO `media_dest` (`media_id`, `image`, `status`, `dest_id`) VALUES
(14, '6536517e74af7_wp6612954.jpg', 1, 25);

-- --------------------------------------------------------

--
-- Table structure for table `media_images`
--

CREATE TABLE `media_images` (
  `id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `image_filename` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media_tours`
--

CREATE TABLE `media_tours` (
  `media_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT '1',
  `tour_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `member_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `firm_name` varchar(100) NOT NULL,
  `mobile` varchar(100) NOT NULL,
  `email_id` varchar(100) NOT NULL,
  `city_state` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `created_date` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1',
  `activation_token` varchar(100) NOT NULL,
  `expiry_time` varchar(100) NOT NULL,
  `max_request` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL,
  `menu_name` varchar(255) NOT NULL,
  `navigation_link` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1',
  `created_date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`menu_id`, `menu_name`, `navigation_link`, `description`, `status`, `created_date`) VALUES
(12, 'Reports ', 'reports.php', 'Leverage agile frameworks to provide a robust synopsis for high level overviews', '1', '2024-03-19 07:37:49 AM'),
(26, 'About Us', 'about_us.php', 'This menu leds you to about us page', '1', '2024-03-27 15:48:51 PM');

-- --------------------------------------------------------

--
-- Table structure for table `navigation_menus`
--

CREATE TABLE `navigation_menus` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `navigation_menus`
--

INSERT INTO `navigation_menus` (`id`, `title`) VALUES
(1, 'All'),
(2, 'Add New Category'),
(3, 'All Categories'),
(4, 'Add Parent Category'),
(5, 'All Parent Category'),
(6, 'Add New Page'),
(7, 'All Pages'),
(8, 'Add menu items'),
(9, 'All menu'),
(10, 'Add sub menu'),
(11, 'All sub menu'),
(12, 'Add Image Slider'),
(13, 'All Slider'),
(14, 'Add Testimonials'),
(15, 'All Testimonials'),
(16, 'Add New Post'),
(17, 'All Posts'),
(18, 'Add New Media'),
(19, 'All Media'),
(20, 'Add New Admin User'),
(21, 'All Admin Users'),
(22, 'Add New Client'),
(23, 'All Clients'),
(24, 'Contact form leads'),
(25, 'Other Leads'),
(26, 'General Settings'),
(27, 'Website Settings'),
(28, 'System Settings'),
(29, 'Logs Reports'),
(30, 'Backup And Recovery'),
(31, 'Change Password'),
(32, 'Add New Video'),
(33, 'All Videos'),
(34, 'Registered Users'),
(35, 'Add New Advt'),
(36, 'All Vertical Advts'),
(37, 'All Horizontal Advts'),
(38, 'Add Statistics'),
(39, 'All Statistics'),
(40, 'Add Study Material'),
(41, 'All Study Material'),
(42, 'Financial Settings'),
(43, 'Add Services'),
(44, 'All Services');

-- --------------------------------------------------------

--
-- Table structure for table `page`
--

CREATE TABLE `page` (
  `page_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug_url` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1',
  `content` longtext NOT NULL,
  `created_date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `page`
--

INSERT INTO `page` (`page_id`, `title`, `slug_url`, `status`, `content`, `created_date`) VALUES
(1, 'Business Model', 'business-model', '1', 'Established businesses should regularly update their business model or they&#39;ll fail to anticipate trends and challenges ahead. Business models also help investors evaluate companies that interest them and employees understand the future of a company they may aspire to join.', '2024-03-23 11:30:46 AM'),
(5, 'Developer', 'developer', '1', '<p>Become a developer in the 6 months and enhance your coding skills</p>', '2024-03-27 15:47:47 PM');

-- --------------------------------------------------------

--
-- Table structure for table `parent_category`
--

CREATE TABLE `parent_category` (
  `parent_category_id` int(11) NOT NULL,
  `parent_category_name` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parent_category`
--

INSERT INTO `parent_category` (`parent_category_id`, `parent_category_name`, `status`) VALUES
(28, 'Web development', '1'),
(29, 'Artificial Intelligence', '1'),
(30, 'Cyber Security', '1');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `method` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `method`, `status`) VALUES
(1, 'Stripe', '1');

-- --------------------------------------------------------

--
-- Table structure for table `payment_method_details`
--

CREATE TABLE `payment_method_details` (
  `id` int(11) NOT NULL,
  `method_name` varchar(255) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `secret_key` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_method_details`
--

INSERT INTO `payment_method_details` (`id`, `method_name`, `email_address`, `api_key`, `secret_key`, `status`) VALUES
(1, 'Stripe', 'stripe@gmail.com', 'stripe123456', '12345stripe', '1');

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `post_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1',
  `content` longtext NOT NULL,
  `image` varchar(255) NOT NULL,
  `publish` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_categories`
--

CREATE TABLE `post_categories` (
  `id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `parent_category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registered_users`
--

CREATE TABLE `registered_users` (
  `student_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `date_of_birth` varchar(50) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `registration_date` varchar(100) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `course_name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `mentor` varchar(255) DEFAULT NULL,
  `enrollment_id` int(11) DEFAULT NULL,
  `enrollment_date` varchar(100) DEFAULT NULL,
  `start_date` varchar(100) DEFAULT NULL,
  `end_date` varchar(100) DEFAULT NULL,
  `fees_status` varchar(100) DEFAULT NULL,
  `fees_amount` varchar(100) DEFAULT NULL,
  `emergency_contact_number` varchar(15) DEFAULT NULL,
  `guardian_name` varchar(100) DEFAULT NULL,
  `guardian_contact_number` varchar(15) DEFAULT NULL,
  `guardian_email` varchar(100) DEFAULT NULL,
  `employment_status` varchar(100) DEFAULT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `job_title` varchar(50) DEFAULT NULL,
  `status` varchar(100) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `title`, `description`, `image`, `status`) VALUES
(7, 'testing', '<p>test</p>', '6603b4d6389b6_1.jpg', '1');

-- --------------------------------------------------------

--
-- Table structure for table `slider`
--

CREATE TABLE `slider` (
  `s_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slider`
--

INSERT INTO `slider` (`s_id`, `title`, `description`, `image`, `status`) VALUES
(35, 'COMPANY OPERATIONS', '<p>Leverage agile frameworks to provide a robust synopsis for high level overviews. Iterative approaches to corporate strategy data foster the collaborative thinking to empowerment.</p>', '6603b31b9a6a6_1.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `smtp_email`
--

CREATE TABLE `smtp_email` (
  `smtp_id` int(11) NOT NULL,
  `from_email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `host` varchar(100) NOT NULL,
  `port` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `smtp_email`
--

INSERT INTO `smtp_email` (`smtp_id`, `from_email`, `password`, `host`, `port`) VALUES
(1, 'info@anixskillup.in', '@@Zxcv@123', 'mail.hostinger.com', '465');

-- --------------------------------------------------------

--
-- Table structure for table `social_links`
--

CREATE TABLE `social_links` (
  `id` int(11) NOT NULL,
  `instagram` varchar(255) NOT NULL,
  `facebook` varchar(255) NOT NULL,
  `twitter` varchar(255) NOT NULL,
  `whatsapp` varchar(255) NOT NULL,
  `youtube` varchar(255) NOT NULL,
  `linkedin` varchar(255) NOT NULL,
  `pinterest` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `social_links`
--

INSERT INTO `social_links` (`id`, `instagram`, `facebook`, `twitter`, `whatsapp`, `youtube`, `linkedin`, `pinterest`) VALUES
(1, 'https://www.instagram.com/vibrantickinfotech/', 'https://www.facebook.com/vibranticksolutions/', 'https://twitter.com/vibrantick', 'https://whatsapp.com/vibrantick', 'https://youtube.com/vibrantick', 'https://www.linkedin.com/company/vibrantick-infotech-solutions/mycompany/verification/?viewAsMember=true', 'https://in.pinterest.com/vibrantick/');

-- --------------------------------------------------------

--
-- Table structure for table `statistics`
--

CREATE TABLE `statistics` (
  `stat_id` int(11) NOT NULL,
  `our_achievements` varchar(100) NOT NULL,
  `performance_rating` varchar(100) NOT NULL,
  `our_clients` varchar(100) NOT NULL,
  `our_projects` varchar(100) NOT NULL,
  `our_experience` varchar(100) NOT NULL,
  `our_overseas_engagements` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `statistics`
--

INSERT INTO `statistics` (`stat_id`, `our_achievements`, `performance_rating`, `our_clients`, `our_projects`, `our_experience`, `our_overseas_engagements`, `status`) VALUES
(1, '72%', '34%', '552', '652', '22', '63', '1');

-- --------------------------------------------------------

--
-- Table structure for table `study_material`
--

CREATE TABLE `study_material` (
  `material_id` int(11) NOT NULL,
  `study_material_name` varchar(255) NOT NULL,
  `study_material` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1',
  `created_date` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `study_material_category`
--

CREATE TABLE `study_material_category` (
  `id` int(11) NOT NULL,
  `study_material_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub_menu`
--

CREATE TABLE `sub_menu` (
  `sub_menu_id` int(11) NOT NULL,
  `sub_menu_name` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1',
  `parent_menu` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `navigation_link` varchar(255) NOT NULL,
  `created_date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_menu`
--

INSERT INTO `sub_menu` (`sub_menu_id`, `sub_menu_name`, `status`, `parent_menu`, `description`, `navigation_link`, `created_date`) VALUES
(8, 'testing', '1', 'Reports ', 'testing', 'testing.php', '2024-03-24 08:57:06 AM'),
(12, 'Who we are', '1', 'About Us', 'We are the leading company int the field of IT services', 'about.php', '2024-03-27 15:51:10 PM');

-- --------------------------------------------------------

--
-- Table structure for table `testimonial`
--

CREATE TABLE `testimonial` (
  `test_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonial`
--

INSERT INTO `testimonial` (`test_id`, `name`, `designation`, `message`, `image`, `status`) VALUES
(11, 'Pablo Benjamin', 'Developer', 'Capitalize on low hanging fruit to identify a ballpark value added activity to beta test. Override the digital divide with additional clickthroughs from DevOps. Nanotechnology immersion along the information highway.', '6603b618435f5_1.jpg', 1),
(15, 'Alex', 'Tourist ', 'Capitalize on low hanging fruit to identify a ballpark value added activity to beta test. Override the digital divide with additional clickthroughs from DevOps. Nanotechnology immersion along the information highway.', '654cbaebadc00_IMG-20230909-WA0015.jpg', 1),
(20, 'Merry', 'Web Developer', 'Capitalize on low hanging fruit to identify a ballpark value added activity to beta test. Override the digital divide with additional clickthroughs from DevOps. Nanotechnology immersion along the information highway.', '65f4241dd9878_Profile1.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `tour_id` int(11) NOT NULL,
  `tour_name` varchar(255) NOT NULL,
  `tour_menu` varchar(255) NOT NULL,
  `weather` varchar(255) NOT NULL,
  `tour_details` longtext NOT NULL,
  `map` varchar(255) NOT NULL,
  `tour_image` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `dest_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_logs`
--

CREATE TABLE `user_logs` (
  `id` int(100) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `user_ip` varchar(100) NOT NULL,
  `login_time` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_logs`
--

INSERT INTO `user_logs` (`id`, `user_id`, `username`, `user_ip`, `login_time`) VALUES
(1, 'nv8v2bg21416cmd29qkk8hipu9', 'amit', '::1', '2024-03-07 11:58:05 AM'),
(2, 'l4n4030pqksrqcpp6hiom3af86', 'abhi', '::1', '2024-03-07 12:53:13 PM'),
(3, '1u3sodpi4kumddfg4r4pebs2m3', 'vibrantick', '::1', '2024-03-07 12:53:33 PM'),
(4, 'sihqr7imgvkldk73c4ilpac5oa', 'vibrantick', '::1', '2024-03-07 13:49:37 PM'),
(5, 'lahhgb1i7tndonhsplnh5ue0s5', 'amit', '::1', '2024-03-07 16:37:30 PM'),
(6, '68mbtilpg7nnvr9pkj3tr3uurb', 'amit', '::1', '2024-03-07 16:38:33 PM'),
(7, 'ennec5lbptc4n796n3v72ushs3', 'amit', '::1', '2024-03-07 17:26:37 PM'),
(8, 'm024mcfcoan5pn5g88u001qkv8', 'amit', '::1', '2024-03-07 18:04:44 PM'),
(9, '82sgovss3gj9bm3i8pnb1gk9ra', 'amit', '::1', '2024-03-07 18:32:24 PM'),
(10, 'd4i17ausnjo4416qnq6lfc9eo4', 'amit', '::1', '2024-03-08 09:34:01 AM'),
(11, 'g2ci38p63cgsirj9hggagrl49f', 'amit', '::1', '2024-03-09 16:29:18 PM'),
(12, '154ge0lpes150sim2leetm73r4', 'amit', '::1', '2024-03-09 21:39:56 PM'),
(13, '99dsg0vki6j1s51p4oest37bpe', 'amit', '::1', '2024-03-09 22:19:08 PM'),
(14, '10cr1cv0sdukmbnulfthbgdlod', 'amit', '::1', '2024-03-10 08:41:59 AM'),
(15, '33rvfqdnt07vuic9i086q16mc7', 'abhi', '::1', '2024-03-10 09:38:09 AM'),
(16, 'truijl0n0uavutl4vsc1i5k8er', 'amit', '::1', '2024-03-11 16:03:36 PM'),
(17, '7nuggbo7u90i2mjc5lidu56gkm', 'amit', '::1', '2024-03-11 17:13:47 PM'),
(18, 'no1a098bht7il7ra2gc4nbmprk', 'amit', '::1', '2024-03-13 09:31:49 AM'),
(19, '4f5nornqi6iu50uas2k5nq3v68', 'abhi', '::1', '2024-03-13 11:14:20 AM'),
(20, '731ijpguumdfj4v3sdbo9q9p5v', 'amit', '::1', '2024-03-13 11:14:34 AM'),
(21, 'v1js9f56p3ikrmk497u8ujnfpe', 'amit', '::1', '2024-03-13 11:15:33 AM'),
(22, 'nvnko74iduuurcn6h21h6n9d72', 'amit', '::1', '2024-03-13 11:17:01 AM'),
(23, '6dd5t9hb93vrfn0rcdl3h3aaj8', 'amit', '::1', '2024-03-13 21:30:50 PM'),
(24, 'ee0lgeet4jnmqg4pcbs5l0pfor', 'amit', '::1', '2024-03-14 00:04:25 AM'),
(25, 'rk6ehp0lanfpsdtn2kkg78hvet', 'amit', '::1', '2024-03-14 10:41:12 AM'),
(26, 'omebq3r56dk0e8ckeprpr0q337', 'amit', '::1', '2024-03-14 13:38:07 PM'),
(27, 'qslufgdv4p0h94kc38k8pjhvj6', 'amit', '::1', '2024-03-14 13:49:20 PM'),
(28, '4svscga885omvtjv913tmnmloo', 'amit', '::1', '2024-03-14 17:13:38 PM'),
(29, '3u6poqrhisqpsedrjtlefe2hs2', 'amit', '::1', '2024-03-15 08:52:30 AM'),
(30, 'rh7om84sg88o4johhkiu1o74cl', 'amit', '::1', '2024-03-15 10:12:24 AM'),
(31, 'l7ne62cvu30csa3bgcb4b9b7jq', 'amit', '::1', '2024-03-15 16:02:42 PM'),
(32, '9je3pp3adfh041e7gef95mve9h', 'amit', '::1', '2024-03-15 17:30:00 PM'),
(33, '2mksnrfsi715abp207idbko0qk', 'amit', '::1', '2024-03-15 17:32:05 PM'),
(34, '52drj09um0aqus1dscj4j38hv1', 'amit', '::1', '2024-03-15 17:35:47 PM'),
(35, 'ij8q50dguvnii1e6vuj3ufgk96', 'amit', '::1', '2024-03-15 17:36:25 PM'),
(36, 'ep4da5r7p83rvc8eegg1frmsf4', 'amit', '::1', '2024-03-15 18:08:17 PM'),
(37, 'vsd5igspveja2u2vhjgh9rdao1', 'amit', '::1', '2024-03-15 18:19:23 PM'),
(38, 'plea8s8ngp6n6c81rpjb3uslqb', 'amit', '::1', '2024-03-15 18:55:38 PM'),
(39, '3vvtfs5v2qc8otsb9a7a2tdg2g', 'amit', '::1', '2024-03-15 20:10:55 PM'),
(40, 'rqs8ubnjbnj90mf7c13geclpua', 'amit', '::1', '2024-03-15 21:06:56 PM'),
(41, 'ej3s2n404g2kma2d99gffh8rtg', 'amit', '::1', '2024-03-15 21:08:16 PM'),
(42, '4qpkqcec4vgu9esegjqvlm98ck', 'amit', '::1', '2024-03-15 21:11:20 PM'),
(43, 'jl9685md4e2se8a1frf84o40hf', 'amit', '::1', '2024-03-15 21:20:23 PM'),
(44, 'dg4jh96kpb1hst66ghmhh0qjh1', 'amit', '::1', '2024-03-15 21:21:19 PM'),
(45, 'nn64bck4p1t7arr85u2bq3kdp3', 'amit', '::1', '2024-03-18 09:04:05 AM'),
(46, '5nsoueqlcjvd84j6feoar0uj4t', 'amit', '::1', '2024-03-18 10:35:49 AM'),
(47, '3se79rujpabkgig94411fgh6u6', 'amit', '::1', '2024-03-18 11:20:06 AM'),
(48, 'thpedfi82iijkvoit69bdh7v5p', 'amit', '::1', '2024-03-18 15:43:35 PM'),
(49, '6d86926mbt4nhu248qeonhbfpj', 'amit', '::1', '2024-03-18 17:17:21 PM'),
(50, 'edinncme8rc7smullur7ci4loh', 'amit', '::1', '2024-03-18 21:29:59 PM'),
(51, 'bma88cp6afcq5fquq1a2ob228q', 'amit', '::1', '2024-03-19 07:16:32 AM'),
(52, 'h48c7tcjfd9d9sa7lfbuf8upos', 'amit', '::1', '2024-03-19 15:07:01 PM'),
(53, 'snomocqphs7ahb23m14big5c3v', 'amit', '::1', '2024-03-19 19:26:31 PM'),
(54, '9ht2kblr4abkeo4v1272j9mrss', 'amit', '::1', '2024-03-21 07:03:08 AM'),
(55, '8k4vjrji03rgusjkbnse00m0e1', 'amit', '::1', '2024-03-21 17:33:38 PM'),
(56, 'fp83vilogftrnv30scos21kmbk', 'amit', '::1', '2024-03-21 17:58:59 PM'),
(57, 'fvgjfpqkfc14er3ik136pl9a6v', 'amit', '::1', '2024-03-22 13:54:19 PM'),
(58, 'ca01vq9ahb52e8m6phjerkkpnh', 'amit', '::1', '2024-03-22 13:57:40 PM'),
(59, 'mbkvqmvqglrlhcsnqi6uancgf8', 'amit', '::1', '2024-03-23 08:40:16 AM'),
(60, 'dcbal53v228t3cq1biuf5d8on4', 'amit', '::1', '2024-03-23 08:49:17 AM'),
(61, 'jdug64nlmotu2m4bbg0k3rmlpp', 'amit', '::1', '2024-03-23 08:50:24 AM'),
(62, 'ov4fm3bfcveapmgbgkefne3ied', 'amit', '::1', '2024-03-23 08:50:43 AM'),
(63, 'a94bcnamvrajn64f8ijpbtq893', 'amit', '::1', '2024-03-23 08:54:03 AM'),
(64, 'k8i2hcu3nan9ijil4jp6f2ng2d', 'amit', '::1', '2024-03-23 08:59:24 AM'),
(65, 'df5p4dpgqeangqashg422e0n41', 'amit', '::1', '2024-03-23 08:59:37 AM'),
(66, '7jjm2ar923l4e4r1o23gi19rf7', 'amit', '::1', '2024-03-23 17:28:36 PM'),
(67, 'op70gv1ipnblrt4htkpf6ga0o9', 'amit', '::1', '2024-03-24 08:18:18 AM'),
(68, 'm1potu74sqbaqpiqvk987slb90', 'amit', '::1', '2024-03-24 08:21:37 AM'),
(69, '6t4bvo4unob62vst85hkhleb79', 'amit', '::1', '2024-03-24 10:01:54 AM'),
(70, '9ugajema9gcg9pkmsd5iomod5b', 'amit', '::1', '2024-03-24 22:16:26 PM'),
(71, 'c8focl956e249tlf7srnlabo3v', 'amit', '::1', '2024-03-24 22:51:29 PM'),
(72, '42sfvncm02e7721t9jki6vtkf3', 'amit', '::1', '2024-03-25 08:33:11 AM'),
(73, '2cmrck15brs4hg2399mhgdt2nh', 'amit', '::1', '2024-03-25 08:40:55 AM'),
(74, 'e2nvielvkqvijjfnfkqk11473f', 'amit', '::1', '2024-03-25 17:15:29 PM'),
(75, 'iintbsr79dbbnt0ij2m2litkip', 'amit', '::1', '2024-03-26 07:52:53 AM'),
(76, 'ec1tgedg0088fpgdbn2qabvsif', 'amit', '::1', '2024-03-26 15:16:36 PM'),
(77, '06fd06mt05vflcna73o8p67b02', 'amit', '::1', '2024-03-27 08:02:10 AM'),
(78, 'hpjolmnh6a5tda1ja1uf8mqk14', 'amit', '::1', '2024-03-27 09:51:29 AM'),
(79, 'rfmirt2sh5iqgd06tpavji9655', 'amit', '::1', '2024-03-27 13:04:20 PM'),
(80, 'n3tdfkiic3ulle74mte4evq6ec', 'amit', '::1', '2024-03-27 14:02:13 PM'),
(81, 'podtcfepj9ii5s4hv11p7ocu0t', 'amit', '::1', '2024-03-27 14:05:19 PM'),
(82, 'huq8l9ngth3j7lsuddjiijiiov', 'amit', '::1', '2024-03-27 14:07:56 PM'),
(83, 'aski1lovnmvsiv35dk5dqqdo80', 'amit', '::1', '2024-03-27 14:15:53 PM'),
(84, '42d9uve8el6a54gaftf28aqio6', 'amit', '::1', '2024-03-27 14:19:33 PM'),
(85, '11tfhttef4l1qgnmd1vpouus2q', 'amit', '::1', '2024-03-27 14:23:19 PM'),
(86, 'aj7vi87oevdkc3b0at90dgt361', 'amit', '::1', '2024-03-27 14:26:35 PM'),
(87, '04nujoi44rh86q5a41acc6vs8p', 'amit', '::1', '2024-03-27 14:29:21 PM'),
(88, 'lk8q7nd08lek0oa4p0o9u7lkp9', 'amit', '::1', '2024-03-27 14:32:14 PM'),
(89, 'chl35dk7ehj4biqtf4bltes6h3', 'amit', '::1', '2024-03-27 14:35:03 PM'),
(90, 'hilm58ndted5fpn3dj6fvadi9r', 'amit', '::1', '2024-03-27 14:37:20 PM');

-- --------------------------------------------------------

--
-- Table structure for table `vertical_ad`
--

CREATE TABLE `vertical_ad` (
  `ad_id` int(11) NOT NULL,
  `ad_title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1',
  `ad_date` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vertical_ad`
--

INSERT INTO `vertical_ad` (`ad_id`, `ad_title`, `image`, `status`, `ad_date`) VALUES
(7, 'testing', '6603c42258f95_4.jpg', '1', '2024-03-27 12:14:55');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `video_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `category_name` varchar(255) DEFAULT NULL,
  `video_title` varchar(255) DEFAULT NULL,
  `video_description` varchar(255) DEFAULT NULL,
  `video_filename` varchar(255) DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `thumbnail_url` varchar(255) DEFAULT NULL,
  `status` varchar(100) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `web_content`
--

CREATE TABLE `web_content` (
  `cont_id` int(11) NOT NULL,
  `mobile_no` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `about_us` text NOT NULL,
  `footer` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `web_content`
--

INSERT INTO `web_content` (`cont_id`, `mobile_no`, `email`, `about_us`, `footer`, `title`, `address`) VALUES
(5, '8085101343', 'info@viajesdivinaindia.com', 'werwrwrewrwwe', 'viajesdivinaindia', 'x', '1627, Housing Board Colony, sector 10 A\r\nGurgaon, Haryana-\r\n122001');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`actvities_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`_id`);

--
-- Indexes for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  ADD PRIMARY KEY (`_id`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`b_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`city_id`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`Client_id`);

--
-- Indexes for table `company_address`
--
ALTER TABLE `company_address`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_info`
--
ALTER TABLE `company_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `google_captcha`
--
ALTER TABLE `google_captcha`
  ADD PRIMARY KEY (`captcha_id`);

--
-- Indexes for table `horizontal_ad`
--
ALTER TABLE `horizontal_ad`
  ADD PRIMARY KEY (`ad_id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`lead_id`);

--
-- Indexes for table `localization`
--
ALTER TABLE `localization`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_settings`
--
ALTER TABLE `login_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `map`
--
ALTER TABLE `map`
  ADD PRIMARY KEY (`map_id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`media_id`);

--
-- Indexes for table `media_dest`
--
ALTER TABLE `media_dest`
  ADD PRIMARY KEY (`media_id`);

--
-- Indexes for table `media_images`
--
ALTER TABLE `media_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_id` (`media_id`);

--
-- Indexes for table `media_tours`
--
ALTER TABLE `media_tours`
  ADD PRIMARY KEY (`media_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `navigation_menus`
--
ALTER TABLE `navigation_menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page`
--
ALTER TABLE `page`
  ADD PRIMARY KEY (`page_id`);

--
-- Indexes for table `parent_category`
--
ALTER TABLE `parent_category`
  ADD PRIMARY KEY (`parent_category_id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_method_details`
--
ALTER TABLE `payment_method_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`post_id`);

--
-- Indexes for table `post_categories`
--
ALTER TABLE `post_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `parent_category_id` (`parent_category_id`);

--
-- Indexes for table `registered_users`
--
ALTER TABLE `registered_users`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `slider`
--
ALTER TABLE `slider`
  ADD PRIMARY KEY (`s_id`);

--
-- Indexes for table `smtp_email`
--
ALTER TABLE `smtp_email`
  ADD PRIMARY KEY (`smtp_id`);

--
-- Indexes for table `social_links`
--
ALTER TABLE `social_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `statistics`
--
ALTER TABLE `statistics`
  ADD PRIMARY KEY (`stat_id`);

--
-- Indexes for table `study_material`
--
ALTER TABLE `study_material`
  ADD PRIMARY KEY (`material_id`);

--
-- Indexes for table `study_material_category`
--
ALTER TABLE `study_material_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `study_material_category_ibfk_2` (`category_id`),
  ADD KEY `study_material_category_ibfk_1` (`study_material_id`);

--
-- Indexes for table `sub_menu`
--
ALTER TABLE `sub_menu`
  ADD PRIMARY KEY (`sub_menu_id`);

--
-- Indexes for table `testimonial`
--
ALTER TABLE `testimonial`
  ADD PRIMARY KEY (`test_id`);

--
-- Indexes for table `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`tour_id`),
  ADD KEY `FOREIGN KEY` (`dest_id`);

--
-- Indexes for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vertical_ad`
--
ALTER TABLE `vertical_ad`
  ADD PRIMARY KEY (`ad_id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`video_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `web_content`
--
ALTER TABLE `web_content`
  ADD PRIMARY KEY (`cont_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `actvities_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `b_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `city`
--
ALTER TABLE `city`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `Client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `company_address`
--
ALTER TABLE `company_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `company_info`
--
ALTER TABLE `company_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `google_captcha`
--
ALTER TABLE `google_captcha`
  MODIFY `captcha_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `horizontal_ad`
--
ALTER TABLE `horizontal_ad`
  MODIFY `ad_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `lead_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `localization`
--
ALTER TABLE `localization`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `login_settings`
--
ALTER TABLE `login_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `map`
--
ALTER TABLE `map`
  MODIFY `map_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `media_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `media_dest`
--
ALTER TABLE `media_dest`
  MODIFY `media_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `media_images`
--
ALTER TABLE `media_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `media_tours`
--
ALTER TABLE `media_tours`
  MODIFY `media_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `navigation_menus`
--
ALTER TABLE `navigation_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `page`
--
ALTER TABLE `page`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `parent_category`
--
ALTER TABLE `parent_category`
  MODIFY `parent_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_method_details`
--
ALTER TABLE `payment_method_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `post_categories`
--
ALTER TABLE `post_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `registered_users`
--
ALTER TABLE `registered_users`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `slider`
--
ALTER TABLE `slider`
  MODIFY `s_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `smtp_email`
--
ALTER TABLE `smtp_email`
  MODIFY `smtp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `social_links`
--
ALTER TABLE `social_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `statistics`
--
ALTER TABLE `statistics`
  MODIFY `stat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `study_material`
--
ALTER TABLE `study_material`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `study_material_category`
--
ALTER TABLE `study_material_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `sub_menu`
--
ALTER TABLE `sub_menu`
  MODIFY `sub_menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `testimonial`
--
ALTER TABLE `testimonial`
  MODIFY `test_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `tour_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `vertical_ad`
--
ALTER TABLE `vertical_ad`
  MODIFY `ad_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `video_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `web_content`
--
ALTER TABLE `web_content`
  MODIFY `cont_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `media_images`
--
ALTER TABLE `media_images`
  ADD CONSTRAINT `media_images_ibfk_1` FOREIGN KEY (`media_id`) REFERENCES `media` (`media_id`) ON DELETE CASCADE;

--
-- Constraints for table `post_categories`
--
ALTER TABLE `post_categories`
  ADD CONSTRAINT `post_categories_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_categories_ibfk_2` FOREIGN KEY (`parent_category_id`) REFERENCES `parent_category` (`parent_category_id`) ON DELETE CASCADE;

--
-- Constraints for table `study_material_category`
--
ALTER TABLE `study_material_category`
  ADD CONSTRAINT `study_material_category_ibfk_1` FOREIGN KEY (`study_material_id`) REFERENCES `study_material` (`material_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `study_material_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `tours`
--
ALTER TABLE `tours`
  ADD CONSTRAINT `FOREIGN KEY` FOREIGN KEY (`dest_id`) REFERENCES `page` (`page_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
