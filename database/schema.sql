-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-05-2026 a las 18:29:39
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12
CREATE DATABASE IF NOT EXISTS `dbecommerce`;
USE `dbecommerce`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dbecommerce`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(200) NOT NULL,
  `province` varchar(200) NOT NULL,
  `country` varchar(200) NOT NULL,
  `postal_code` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(70) NOT NULL,
  `description` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `active`, `slug`) VALUES
(1, 'Phones', 'All types of phone', 1, 'phones'),
(2, 'Technology', 'All about technology and innovations', 1, 'technology'),
(3, 'Health', 'Health and wellness topics', 1, 'health'),
(4, 'Finance', 'Financial news and advice', 1, 'finance'),
(5, 'Education', 'Educational resources and topics', 1, 'education'),
(6, 'Travel', 'Travel guides and tips', 1, 'travel'),
(7, 'Food', 'Recipes and food culture', 1, 'food'),
(8, 'Sports', 'Sports news and updates', 1, 'sports'),
(9, 'Entertainment', 'Movies, music and entertainment', 1, 'entertainment'),
(10, 'Science', 'Scientific discoveries and research', 1, 'science'),
(11, 'Fashion', 'Fashion trends and style tips', 1, 'fashion'),
(12, 'Lifestyle', 'Daily lifestyle inspiration', 1, 'lifestyle'),
(13, 'Business', 'Business strategies and news', 1, 'business'),
(14, 'Marketing', 'Marketing trends and tips', 1, 'marketing'),
(15, 'Politics', 'Political news and discussions', 1, 'politics'),
(16, 'Environment', 'Environmental awareness and news', 1, 'environment'),
(17, 'Art', 'Art and creativity topics', 1, 'art'),
(18, 'History', 'Historical events and analysis', 1, 'history'),
(19, 'Gaming', 'Video games and gaming culture', 1, 'gaming'),
(20, 'Parenting', 'Parenting tips and advice', 1, 'parenting'),
(21, 'Real Estate', 'Property and housing market', 1, 'real-estate'),
(22, 'Automotive', 'Cars and automotive industry', 1, 'automotive'),
(23, 'DIY', 'Do it yourself projects', 1, 'diy'),
(24, 'Photography', 'Photography tips and inspiration', 1, 'photography'),
(25, 'Music', 'Music industry and trends', 1, 'music'),
(26, 'Books', 'Books and literature', 1, 'books'),
(27, 'Fitness', 'Workout and fitness tips', 1, 'fitness'),
(28, 'Mental Health', 'Mental wellness and support', 1, 'mental-health'),
(29, 'Cooking', 'Cooking techniques and recipes', 1, 'cooking'),
(30, 'Investing', 'Investment strategies and tips', 1, 'investing'),
(31, 'Startups', 'Startup ecosystem and ideas', 1, 'startups'),
(32, 'Programming', 'Coding tutorials and resources', 1, 'programming'),
(33, 'Web Development', 'Frontend and backend development', 1, 'web-development'),
(34, 'Mobile Apps', 'Mobile application development', 1, 'mobile-apps'),
(35, 'AI', 'Artificial intelligence topics', 1, 'ai'),
(36, 'Machine Learning', 'Machine learning concepts', 1, 'machine-learning'),
(37, 'Data Science', 'Data analysis and science', 1, 'data-science'),
(38, 'Cybersecurity', 'Security and data protection', 1, 'cybersecurity'),
(39, 'Cloud Computing', 'Cloud technologies and services', 1, 'cloud-computing'),
(40, 'DevOps', 'Development and operations practices', 1, 'devops'),
(41, 'Blockchain', 'Blockchain and crypto technology', 1, 'blockchain'),
(42, 'Cryptocurrency', 'Digital currencies and trends', 1, 'cryptocurrency'),
(43, 'E-commerce', 'Online business and stores', 1, 'e-commerce'),
(44, 'Customer Service', 'Support and customer relations', 1, 'customer-service'),
(45, 'HR', 'Human resources management', 1, 'hr'),
(46, 'Leadership', 'Leadership skills and strategies', 1, 'leadership'),
(47, 'Productivity', 'Time management and productivity', 1, 'productivity'),
(48, 'Remote Work', 'Working from home tips', 1, 'remote-work'),
(49, 'Freelancing', 'Freelance career advice', 1, 'freelancing'),
(50, 'Career', 'Career growth and guidance', 1, 'career');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `document_types`
--

CREATE TABLE `document_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `document_types`
--

INSERT INTO `document_types` (`id`, `name`) VALUES
(1, 'DNI');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_change_requests`
--

CREATE TABLE `email_change_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `old_email` varchar(255) NOT NULL,
  `new_email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `used` tinyint(4) DEFAULT 0,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `email_change_requests`
--

INSERT INTO `email_change_requests` (`id`, `user_id`, `old_email`, `new_email`, `token`, `used`, `expires_at`, `created_at`) VALUES
(1, 2, 'example@hotmail.com', 'example2@hotmail.com', 'cdf83b6c43cd498fb878b316424be865960839fb0b2789a19816cc8e3454bd46', 1, '2026-05-27 19:12:12', '2026-05-26 17:12:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(200) NOT NULL,
  `stock` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `category_id`, `code`, `name`, `description`, `price`, `image`, `stock`, `active`, `created_at`, `slug`) VALUES
(1, 1, '412', 'iphone 13 pro 512GB de memoria, 8GB RAM, pantalla OLED y táctil', 'La descripción es la siguiente:\r\n— 8 GB RAM.\r\n— Pantalla Oled...\r\n— Test', 799.99, 'products/0.jpg', 0, 1, '2025-08-07 21:07:51', 'iphone-13-pro-512gb'),
(2, 1, '1779812073393', 'Smartphone X1', 'Latest generation smartphone', 699.90, 'products/1.jpg', 50, 1, '2026-04-02 15:17:41', 'smartphone-x1'),
(3, 3, '1779812017673', 'Vitamin Pack', 'Daily vitamins and supplements', 19.90, 'products/2.jpg', 120, 1, '2026-04-02 15:17:41', 'vitamin-pack'),
(4, 30, '1779812083913', 'Investment Guide Book', 'Learn how to invest wisely', 29.50, 'products/3.jpg', 40, 1, '2026-04-02 15:17:41', 'investment-guide-book'),
(5, 5, '1779812147744', 'Online Course Pro', 'Advanced online learning course', 99.90, 'products/4.jpg', 200, 1, '2026-04-02 15:17:41', 'online-course-pro'),
(6, 6, '1779812160415', 'Travel Backpack', 'Durable travel backpack', 59.90, 'products/5.jpg', 80, 1, '2026-04-02 15:17:41', 'travel-backpack'),
(7, 7, '1779812173921', 'Chef Knife Set', 'Professional kitchen knives', 89.90, 'products/6.jpg', 35, 1, '2026-04-02 15:17:41', 'chef-knife-set'),
(8, 8, '1779812183501', 'Football Ball', 'Professional football ball', 27.90, 'products/7.jpg', 150, 1, '2026-04-02 15:17:41', 'football-ball'),
(9, 9, '1779812214308', 'Bluetooth Speaker', 'Portable speaker with great sound', 45.70, 'products/8.jpg', 60, 1, '2026-04-02 15:17:41', 'bluetooth-speaker'),
(10, 10, '1779812224002', 'Microscope Kit', 'Science microscope for beginners', 120.00, 'products/9.jpg', 25, 1, '2026-04-02 15:17:41', 'microscope-kit'),
(11, 11, '1779812238150', 'Fashion Sneakers', 'Comfortable daily sneakers', 75.40, 'products/10.jpg', 90, 1, '2026-04-02 15:17:41', 'fashion-sneakers'),
(12, 12, '1779812253335', 'Lifestyle Planner', 'Daily productivity planner', 14.90, 'products/11.jpg', 100, 1, '2026-04-02 15:17:41', 'lifestyle-planner'),
(13, 13, '1779812261645', 'Business Laptop', 'High performance laptop', 1200.00, 'products/12.jpg', 20, 1, '2026-04-02 15:17:41', 'business-laptop'),
(14, 14, '1779812276762', 'Marketing Toolkit', 'Essential tools for marketers', 49.90, 'products/13.jpg', 70, 1, '2026-04-02 15:17:41', 'marketing-toolkit'),
(15, 15, '1779812283837', 'Political Book', 'Modern politics explained', 18.70, 'products/14.jpg', 60, 1, '2026-04-02 15:17:41', 'political-book'),
(16, 16, '1779812299974', 'Eco Bottle', 'Reusable eco-friendly bottle', 12.00, 'products/15.jpg', 200, 1, '2026-04-02 15:17:41', 'eco-bottle'),
(17, 17, '1779812307435', 'Art Canvas Set', 'Canvas for painting', 34.20, 'products/16.jpg', 45, 1, '2026-04-02 15:17:41', 'art-canvas-set'),
(18, 18, '1779812327685', 'History Documentary', 'Educational history film', 22.00, 'products/17.jpg', 30, 1, '2026-04-02 15:17:41', 'history-documentary'),
(19, 19, '1779812338239', 'Gaming Mouse', 'High precision gaming mouse', 39.90, 'products/18.jpg', 85, 1, '2026-04-02 15:17:41', 'gaming-mouse'),
(20, 20, '1779812356847', 'Baby Care Kit', 'Essential parenting kit', 55.00, 'products/19.jpg', 40, 1, '2026-04-02 15:17:41', 'baby-care-kit'),
(21, 21, '1779812395710', 'Property Guide', 'Real estate investing guide', 27.90, 'products/20.jpg', 75, 1, '2026-04-02 15:17:41', 'property-guide'),
(22, 22, '1779812405874', 'Car Vacuum Cleaner', 'Portable car vacuum', 49.50, 'products/21.jpg', 65, 1, '2026-04-02 15:17:41', 'car-vacuum-cleaner'),
(23, 23, '1779812416318', 'DIY Tool Set', 'Complete DIY toolkit', 79.90, 'products/22.jpg', 55, 1, '2026-04-02 15:17:41', 'diy-tool-set'),
(24, 24, '1779812424148', 'Camera Lens', 'Professional photography lens', 350.00, 'products/23.jpg', 15, 1, '2026-04-02 15:17:41', 'camera-lens'),
(25, 25, '1779812434682', 'Guitar Acoustic', 'Acoustic guitar for beginners', 150.00, 'products/24.jpg', 25, 1, '2026-04-02 15:17:41', 'guitar-acoustic'),
(26, 26, '1779812450521', 'Novel Bestseller', 'Top selling fiction book', 19.90, 'products/25.jpg', 100, 1, '2026-04-02 15:17:41', 'novel-bestseller'),
(27, 27, '1779812463091', 'Yoga Mat', 'Non-slip fitness mat', 20.00, 'products/26.jpg', 120, 1, '2026-04-02 15:17:41', 'yoga-mat'),
(28, 28, '1779812476679', 'Mindfulness Guide', 'Mental health book', 15.50, 'products/27.jpg', 60, 1, '2026-04-02 15:17:41', 'mindfulness-guide'),
(29, 29, '1779812494493', 'Cooking Pan Set', 'Non-stick pan set', 65.00, 'products/28.jpg', 50, 1, '2026-04-02 15:17:41', 'cooking-pan-set'),
(30, 30, '1779812507662', 'Stock Market Course', 'Learn trading basics', 120.00, 'products/29.jpg', 40, 1, '2026-04-02 15:17:41', 'stock-market-course'),
(31, 31, '1779812526782', 'Startup Handbook', 'Guide for entrepreneurs', 24.90, 'products/30.jpg', 80, 1, '2026-04-02 15:17:41', 'startup-handbook'),
(32, 32, '1779812536467', 'Code Editor Pro', 'Advanced coding editor software', 79.90, 'products/31.jpg', 60, 1, '2026-04-02 15:20:45', 'code-editor-pro'),
(33, 33, '1779812550840', 'Web Dev Bootcamp', 'Learn web development from scratch', 129.90, 'products/32.jpg', 40, 1, '2026-04-02 15:20:45', 'web-dev-bootcamp'),
(34, 34, '1779812570494', 'Mobile App Template', 'Starter template for apps', 49.90, 'products/33.jpg', 75, 1, '2026-04-02 15:20:45', 'mobile-app-template'),
(35, 35, '1779812585283', 'AI Assistant Tool', 'AI powered assistant software', 199.90, 'products/34.jpg', 30, 1, '2026-04-02 15:20:45', 'ai-assistant-tool'),
(36, 36, '1779812593843', 'ML Course Advanced', 'Machine learning advanced course', 149.90, 'products/35.jpg', 35, 1, '2026-04-02 15:20:45', 'ml-course-advanced'),
(37, 37, '1779812607514', 'Data Science Kit', 'Complete data science toolkit', 89.90, 'products/36.jpg', 45, 1, '2026-04-02 15:20:45', 'data-science-kit'),
(38, 38, '1779812614314', 'Cybersecurity Guide', 'Protect systems and data', 39.90, 'products/37.jpg', 80, 1, '2026-04-02 15:20:45', 'cybersecurity-guide'),
(39, 39, '1779812646735', 'Cloud Hosting Plan', 'Premium cloud hosting', 59.90, 'products/38.jpg', 100, 1, '2026-04-02 15:20:45', 'cloud-hosting-plan'),
(40, 40, '1779812662868', 'DevOps Toolkit', 'Tools for DevOps engineers', 69.90, 'products/39.jpg', 55, 1, '2026-04-02 15:20:45', 'devops-toolkit'),
(41, 41, '1779812678409', 'Blockchain Course', 'Blockchain fundamentals', 99.90, 'products/40.jpg', 50, 1, '2026-04-02 15:20:45', 'blockchain-course'),
(42, 42, '1779812687506', 'Crypto Wallet', 'Secure cryptocurrency wallet', 49.90, 'products/41.jpg', 70, 1, '2026-04-02 15:20:45', 'crypto-wallet'),
(43, 43, '1779812701711', 'Ecommerce Template', 'Online store template', 59.90, 'products/42.jpg', 65, 1, '2026-04-02 15:20:45', 'ecommerce-template'),
(44, 44, '1779812715963', 'Customer Support Tool', 'Helpdesk software solution', 89.90, 'products/43.jpg', 40, 1, '2026-04-02 15:20:45', 'customer-support-tool'),
(45, 44, 'P044', 'HR Management App', 'HR software system', 120.00, 'products/44.jpg', 30, 1, '2026-04-02 15:20:45', 'hr-management-app'),
(46, 46, '1779812735409', 'Leadership Course', 'Become a better leader', 79.90, 'products/45.jpg', 60, 1, '2026-04-02 15:20:45', 'leadership-course'),
(47, 47, '1779812747905', 'Productivity Planner Pro', 'Boost your productivity', 24.90, 'products/46.jpg', 110, 1, '2026-04-02 15:20:45', 'productivity-planner-pro'),
(48, 48, '1779812756426', 'Remote Work Kit', 'Tools for remote workers', 49.90, 'products/47.jpg', 90, 1, '2026-04-02 15:20:45', 'remote-work-kit'),
(49, 49, '1779812771747', 'Freelancer Toolkit', 'All-in-one freelancer kit', 39.90, 'products/48.jpg', 75, 1, '2026-04-02 15:20:45', 'freelancer-toolkit'),
(50, 50, '1779812777688', 'Career Coaching Session', 'Professional career advice', 99.90, 'products/49.jpg', 20, 1, '2026-04-02 15:20:45', 'career-coaching-session');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products_images`
--

CREATE TABLE `products_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `active` tinyint(1) DEFAULT 1,
  `type` enum('main','thumbnail','gallery') DEFAULT 'gallery',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `products_images`
--

INSERT INTO `products_images` (`id`, `product_id`, `image`, `active`, `type`, `created_at`, `updated_at`) VALUES
(1, 1, 'products/gallery/30c9c32d7296a988.webp', 1, 'main', '2026-05-24 17:12:28', '2026-05-24 17:12:28'),
(2, 1, 'products/gallery/04f14229ab5c6e3c.png', 1, 'gallery', '2026-05-24 17:14:32', '2026-05-24 17:14:32'),
(3, 1, 'products/gallery/85d71ccab18d6d3b.jpg', 1, 'gallery', '2026-05-24 17:14:50', '2026-05-24 17:14:50'),
(4, 1, 'products/gallery/83e65b49789b4bdc.png', 1, 'gallery', '2026-05-24 17:15:07', '2026-05-24 17:15:07'),
(5, 2, 'products/gallery/336a3bea63f36909.webp', 1, 'main', '2026-05-24 17:31:23', '2026-05-24 17:31:23'),
(6, 2, 'products/gallery/6da58d23528586ab.jpg', 1, 'gallery', '2026-05-24 17:31:46', '2026-05-24 17:32:22'),
(7, 2, 'products/gallery/2b0226685d926b71.jpg', 1, 'gallery', '2026-05-24 17:31:58', '2026-05-24 17:32:17'),
(8, 2, 'products/gallery/bc1b98dcca6a8414.jpg', 1, 'gallery', '2026-05-24 17:32:07', '2026-05-24 17:32:11'),
(9, 3, 'products/gallery/4c5e2f7b884ed8c3.webp', 1, 'main', '2026-05-24 17:36:12', '2026-05-24 17:36:12'),
(10, 3, 'products/gallery/2ff26bcebcdd488e.jpg', 1, 'gallery', '2026-05-24 17:36:26', '2026-05-24 17:36:26'),
(11, 3, 'products/gallery/8e7bdf4a79148568.webp', 1, 'gallery', '2026-05-24 17:36:33', '2026-05-24 17:36:33'),
(12, 4, 'products/gallery/67255956b3561039.jpg', 1, 'main', '2026-05-24 17:37:42', '2026-05-24 17:37:42'),
(13, 4, 'products/gallery/739bebb36d11d28c.jpg', 1, 'gallery', '2026-05-24 17:37:52', '2026-05-24 17:37:52'),
(14, 5, 'products/gallery/be5f01802ad07183.webp', 1, 'main', '2026-05-24 17:38:59', '2026-05-24 17:38:59'),
(15, 5, 'products/gallery/6fcd8953441e2115.png', 1, 'gallery', '2026-05-24 17:39:09', '2026-05-24 17:39:09'),
(16, 6, 'products/gallery/2adf657bed1a5fd9.webp', 1, 'main', '2026-05-24 17:39:41', '2026-05-24 17:39:41'),
(17, 6, 'products/gallery/9aeda24a95f086c6.jpg', 1, 'gallery', '2026-05-24 17:40:21', '2026-05-24 17:40:21'),
(18, 6, 'products/gallery/33f0f4f61748eb37.webp', 1, 'gallery', '2026-05-24 17:40:32', '2026-05-24 17:40:32'),
(19, 7, 'products/gallery/27543a1702d6e6b2.jpg', 1, 'main', '2026-05-24 17:42:05', '2026-05-24 17:42:05'),
(20, 7, 'products/gallery/3d58b247c6a07662.webp', 1, 'gallery', '2026-05-24 17:42:16', '2026-05-24 17:42:16'),
(21, 7, 'products/gallery/3e9fa0d054ca2a8a.jpg', 1, 'gallery', '2026-05-24 17:42:27', '2026-05-24 17:42:27'),
(22, 8, 'products/gallery/077f12ad1ec1322d.webp', 1, 'main', '2026-05-24 17:43:55', '2026-05-24 17:43:55'),
(23, 8, 'products/gallery/881e40fd1ae3eaf3.webp', 1, 'gallery', '2026-05-24 17:44:17', '2026-05-24 17:44:17'),
(24, 8, 'products/gallery/133767e5c92764d7.jpg', 1, 'gallery', '2026-05-24 17:44:27', '2026-05-24 17:44:27'),
(25, 9, 'products/gallery/5f92664771d8e596.webp', 1, 'main', '2026-05-24 17:45:31', '2026-05-24 17:45:31'),
(26, 9, 'products/gallery/dd779039bddc9640.jpg', 1, 'gallery', '2026-05-24 17:45:42', '2026-05-24 17:45:42'),
(27, 10, 'products/gallery/c3a63b2931c68f11.jpg', 1, 'main', '2026-05-24 17:46:31', '2026-05-24 17:46:31'),
(28, 10, 'products/gallery/b477f2a29223ce45.jpg', 1, 'gallery', '2026-05-24 17:46:57', '2026-05-24 17:46:57'),
(29, 11, 'products/gallery/57dc2cad51a4ee54.jpg', 1, 'main', '2026-05-24 17:48:08', '2026-05-24 17:48:08'),
(30, 11, 'products/gallery/65ba8bc8ea422210.webp', 1, 'gallery', '2026-05-24 17:48:39', '2026-05-24 17:48:39'),
(31, 12, 'products/gallery/4d7594199b75e0da.webp', 1, 'main', '2026-05-24 17:51:06', '2026-05-24 17:51:06'),
(32, 12, 'products/gallery/b6681b70b508ce56.webp', 1, 'gallery', '2026-05-24 17:51:29', '2026-05-24 17:51:29'),
(33, 13, 'products/gallery/977d00c62527f0bb.jpg', 1, 'main', '2026-05-24 17:52:50', '2026-05-24 17:52:50'),
(34, 13, 'products/gallery/e0608b061c47ca8b.webp', 1, 'gallery', '2026-05-24 17:53:10', '2026-05-24 17:53:10'),
(35, 13, 'products/gallery/26b1a403037e7af7.jpg', 1, 'gallery', '2026-05-24 17:53:20', '2026-05-24 17:53:28'),
(36, 14, 'products/gallery/f8790a92a0712f65.jpg', 1, 'main', '2026-05-24 17:56:15', '2026-05-24 17:56:15'),
(37, 14, 'products/gallery/6e5f2652c380f79f.webp', 1, 'gallery', '2026-05-24 17:56:29', '2026-05-24 17:56:29'),
(38, 15, 'products/gallery/aba720a0053d7ab6.jpg', 1, 'main', '2026-05-24 18:24:51', '2026-05-24 18:24:51'),
(39, 15, 'products/gallery/88d70f34057e65cf.jpg', 1, 'gallery', '2026-05-24 18:25:02', '2026-05-24 18:25:02'),
(40, 16, 'products/gallery/0e4d14eaccd11c30.webp', 1, 'main', '2026-05-24 18:26:44', '2026-05-24 18:26:44'),
(41, 16, 'products/gallery/5eae69d3972e58c7.webp', 1, 'gallery', '2026-05-24 18:26:56', '2026-05-24 18:26:56'),
(42, 16, 'products/gallery/d26230c828fb6fde.webp', 1, 'gallery', '2026-05-24 18:27:04', '2026-05-24 18:27:04'),
(43, 17, 'products/gallery/a2bd34a6abaf7d7c.jpg', 1, 'main', '2026-05-24 18:28:01', '2026-05-24 18:28:01'),
(44, 17, 'products/gallery/d9e473ceb98289f4.jpg', 1, 'gallery', '2026-05-24 18:28:10', '2026-05-24 18:28:10'),
(45, 17, 'products/gallery/ff7b2ea91aec67d6.webp', 1, 'gallery', '2026-05-24 18:28:22', '2026-05-24 18:28:22'),
(46, 18, 'products/gallery/5307e34670a1390c.jpg', 1, 'main', '2026-05-24 18:29:43', '2026-05-24 18:29:43'),
(47, 18, 'products/gallery/2317f6cdb8c80450.webp', 1, 'gallery', '2026-05-24 18:29:54', '2026-05-24 18:29:54'),
(48, 19, 'products/gallery/5274276dadc06d2d.jpg', 1, 'main', '2026-05-24 18:30:36', '2026-05-24 18:30:36'),
(49, 19, 'products/gallery/4ee4bb063dacee06.jpg', 1, 'gallery', '2026-05-24 18:30:58', '2026-05-24 18:30:58'),
(50, 20, 'products/gallery/32fc7c0e7aa77f2c.jpg', 1, 'main', '2026-05-24 18:31:58', '2026-05-24 18:31:58'),
(51, 20, 'products/gallery/216cf8726e27af6a.webp', 1, 'gallery', '2026-05-24 18:32:08', '2026-05-24 18:32:08'),
(52, 20, 'products/gallery/8fc082541f04d20c.jpg', 1, 'gallery', '2026-05-24 18:32:20', '2026-05-24 18:32:20'),
(53, 21, 'products/gallery/bcfe798684c79f98.png', 1, 'main', '2026-05-24 18:33:27', '2026-05-24 18:33:27'),
(54, 21, 'products/gallery/59343901570a7fc4.webp', 1, 'gallery', '2026-05-24 18:33:54', '2026-05-24 18:33:54'),
(55, 22, 'products/gallery/07e96d63c7cd63c3.jpg', 1, 'main', '2026-05-24 18:34:45', '2026-05-24 18:34:45'),
(56, 22, 'products/gallery/9ebc2d4d81eb2d04.webp', 1, 'gallery', '2026-05-24 18:34:55', '2026-05-24 18:34:55'),
(57, 23, 'products/gallery/233fe6981ee89c24.jpg', 1, 'main', '2026-05-24 18:35:54', '2026-05-24 18:35:54'),
(58, 23, 'products/gallery/6844a35436c73e8a.jpg', 1, 'gallery', '2026-05-24 18:36:07', '2026-05-24 18:36:07'),
(59, 24, 'products/gallery/81d7400f1f16af0c.jpg', 1, 'main', '2026-05-24 18:37:21', '2026-05-24 18:37:21'),
(60, 24, 'products/gallery/f3bd7d7f829e43f3.webp', 1, 'gallery', '2026-05-24 18:37:35', '2026-05-24 18:37:35'),
(61, 25, 'products/gallery/b3e91156de879b42.jpg', 1, 'main', '2026-05-24 18:38:49', '2026-05-24 18:38:49'),
(62, 25, 'products/gallery/5ae12234b0e2dafc.jpg', 1, 'gallery', '2026-05-24 18:39:04', '2026-05-24 18:39:04'),
(63, 25, 'products/gallery/f7af8b382e479095.webp', 1, 'gallery', '2026-05-24 18:39:14', '2026-05-24 18:39:14'),
(64, 26, 'products/gallery/ad36a8a7f7a06211.jpg', 1, 'main', '2026-05-24 18:40:20', '2026-05-24 18:40:20'),
(65, 26, 'products/gallery/a9c9e36c042f48c2.jpg', 1, 'gallery', '2026-05-24 18:40:39', '2026-05-24 18:40:39'),
(66, 27, 'products/gallery/8a13e981a667119c.jpg', 1, 'main', '2026-05-24 18:41:59', '2026-05-24 18:41:59'),
(67, 27, 'products/gallery/a5702b8f727eb509.webp', 1, 'gallery', '2026-05-24 18:42:09', '2026-05-24 18:42:09'),
(68, 28, 'products/gallery/d8776b0906b1110d.jpg', 1, 'main', '2026-05-24 18:43:40', '2026-05-24 18:43:40'),
(69, 28, 'products/gallery/c2f6823740223231.jpg', 1, 'gallery', '2026-05-24 18:43:58', '2026-05-24 18:44:05'),
(70, 29, 'products/gallery/75b8586d9fcb8905.jpg', 1, 'main', '2026-05-24 18:44:54', '2026-05-24 18:44:54'),
(71, 29, 'products/gallery/ffac0d6191378656.webp', 1, 'gallery', '2026-05-24 18:45:32', '2026-05-24 18:45:32'),
(72, 30, 'products/gallery/777da2f829435f7c.jpg', 1, 'main', '2026-05-24 18:47:45', '2026-05-24 18:47:45'),
(73, 30, 'products/gallery/c484351340353277.jpg', 1, 'gallery', '2026-05-24 18:50:21', '2026-05-24 18:50:21'),
(74, 31, 'products/gallery/1782572caf302c13.jpg', 1, 'main', '2026-05-24 18:51:14', '2026-05-24 18:51:14'),
(75, 31, 'products/gallery/34d218e62904d323.jpg', 1, 'gallery', '2026-05-24 18:51:55', '2026-05-24 18:51:55'),
(76, 32, 'products/gallery/b686ac7cdc5b17f1.png', 1, 'main', '2026-05-24 18:53:34', '2026-05-24 18:53:34'),
(77, 32, 'products/gallery/1f9137a1a575230f.webp', 1, 'gallery', '2026-05-24 18:53:43', '2026-05-24 18:53:43'),
(78, 33, 'products/gallery/ca491cecab28092b.png', 1, 'main', '2026-05-24 18:54:26', '2026-05-24 18:54:26'),
(79, 33, 'products/gallery/5023414dd1804855.jpg', 1, 'gallery', '2026-05-24 18:54:51', '2026-05-24 18:54:51'),
(80, 34, 'products/gallery/40f6e3fbaee05071.jpg', 1, 'main', '2026-05-24 18:55:40', '2026-05-24 18:55:40'),
(81, 34, 'products/gallery/e23fad4a2f279f0e.jpg', 1, 'gallery', '2026-05-24 18:55:58', '2026-05-24 18:55:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products_information`
--

CREATE TABLE `products_information` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `manufacturer` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `weight` varchar(50) DEFAULT NULL,
  `material` varchar(100) DEFAULT NULL,
  `warranty` varchar(50) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `package_contents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`package_contents`)),
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `rating_average` decimal(3,2) DEFAULT 0.00,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `discount` decimal(10,2) DEFAULT 0.00,
  `color_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`color_options`)),
  `size_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`size_options`)),
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `technical_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`technical_details`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `products_information`
--

INSERT INTO `products_information` (`id`, `product_id`, `brand`, `manufacturer`, `model`, `dimensions`, `color`, `weight`, `material`, `warranty`, `release_date`, `expiration_date`, `package_contents`, `features`, `rating_average`, `tags`, `discount`, `color_options`, `size_options`, `is_featured`, `created_at`, `updated_at`, `technical_details`) VALUES
(1, 2, 'TechNova', 'TechNova Ltd.', 'TN-X1', '15 x 7 x 0.8 cm', 'Black', '180g', 'Aluminum / Glass', '24 months', '2024-01-10', NULL, '{\"box_contents\":{\"device\":true,\"charger\":true,\"manual\":true}}', '{\"display\":\"6.5 inch\",\"battery\":\"4000mAh\",\"camera\":\"48MP\"}', 4.50, '{\"category\":\"electronics\",\"type\":\"smartphone\"}', 5.00, '{\"available_colors\":[\"Black\",\"Blue\"]}', '{\"available_sizes\":[\"128GB\",\"256GB\"]}', 1, '2026-04-02 13:36:31', '2026-04-14 12:55:57', '{\"processor\":\"Octa-core\",\"ram\":\"8GB\",\"os\":\"Android\"}'),
(2, 3, 'HealthPlus', 'Health Labs', 'HP-VIT', '10 x 5 x 5 cm', 'White', '120g', 'Plastic', '24 months', '2024-02-01', '2026-02-01', '{\"box_contents\":{\"bottle\":true,\"tablets\":60}}', '{\"benefit\":\"immune support\",\"dosage\":\"1 daily\"}', 4.60, '[]', 10.00, '{\"flavor\":[\"Orange\"],\"available_colors\":[]}', '{\"available_sizes\":[]}', 0, '2026-04-02 13:36:31', '2026-04-29 16:41:34', '{\"storage\":\"dry place\"}'),
(3, 4, 'FinBooks', 'Finance Publishing', 'FB-INV', '20 x 13 x 2 cm', 'Blue', '300g', 'Paper', '12 months', '2023-05-10', NULL, '{\"box_contents\":{\"book\":1}}', '{\"pages\":250,\"level\":\"beginner\"}', 4.20, '{\"category\":\"books\"}', 0.00, NULL, NULL, 0, '2026-04-02 13:36:31', '2026-04-02 13:36:31', '{\"isbn\":\"123456789\"}'),
(4, 5, 'EduPro', 'EduPro Academy', 'EP-COURSE', 'N/A', 'N/A', '0g', 'Digital', '12 months', '2025-01-01', '0000-00-00', '{\"box_contents\":{\"access\":\"online\"}}', '{\"duration\":\"40h\",\"level\":\"advanced\"}', 4.70, '[]', 15.00, '{\"available_colors\":[]}', '{\"available_sizes\":[]}', 1, '2026-04-02 13:36:31', '2026-04-29 16:56:34', '{\"platform\":\"web\"}'),
(5, 6, 'TravelPro', 'Travel Gear Inc.', 'TP-BAG', '50 x 30 x 20 cm', 'Gray', '1.2kg', 'Polyester', '18 months', '2024-03-01', NULL, '{\"box_contents\":{\"backpack\":true}}', '{\"capacity\":\"40L\",\"waterproof\":true}', 4.40, '{\"category\":\"travel\"}', 0.00, '{\"available_colors\":[\"Gray\",\"Black\"]}', NULL, 0, '2026-04-02 13:36:31', '2026-04-02 13:36:31', '{\"zippers\":\"reinforced\"}'),
(6, 7, 'KitchenMaster', 'KitchenMaster Ltd.', 'KM-KNIFE', '35 x 10 x 5 cm', 'Silver', '800g', 'Steel', '24 months', '2023-08-10', NULL, '{\"box_contents\":{\"knives\":5}}', '{\"blade\":\"stainless steel\"}', 4.55, '{\"category\":\"kitchen\"}', 5.00, NULL, NULL, 0, '2026-04-02 13:36:31', '2026-04-02 13:36:31', '{\"dishwasher_safe\":false}'),
(7, 8, 'Sportix', 'Sportix Corp.', 'SP-BALL', '22 cm diameter', 'White', '450g', 'Synthetic leather', '12 months', '2023-06-01', NULL, '{\"box_contents\":{\"ball\":1}}', '{\"usage\":\"professional\"}', 4.30, '{\"category\":\"sports\"}', 0.00, NULL, NULL, 0, '2026-04-02 13:36:31', '2026-04-02 13:36:31', '{\"pressure\":\"standard\"}'),
(8, 9, 'SoundMax', 'SoundMax Audio', 'SM-SPK', '20 x 10 x 8 cm', 'Black', '600g', 'Plastic', '12 months', '2024-01-15', NULL, '{\"box_contents\":{\"speaker\":true,\"cable\":true}}', '{\"bluetooth\":\"5.0\",\"battery\":\"10h\"}', 4.45, '{\"category\":\"audio\"}', 10.00, '{\"available_colors\":[\"Black\",\"Red\"]}', NULL, 1, '2026-04-02 13:36:31', '2026-04-02 13:36:31', '{\"power\":\"20W\"}'),
(9, 10, 'SciLab', 'Science Labs', 'SL-MICRO', '25 x 15 x 10 cm', 'White', '1kg', 'Metal', '24 months', '2023-04-20', NULL, '{\"box_contents\":{\"microscope\":true}}', '{\"zoom\":\"1000x\"}', 4.60, '{\"category\":\"science\"}', 0.00, NULL, NULL, 0, '2026-04-02 13:36:31', '2026-04-02 13:36:31', '{\"lens\":\"optical\"}'),
(10, 11, 'UrbanWear', 'UrbanWear', 'UW-SNK', '30 x 20 x 10 cm', 'White', '700g', 'Fabric', '6 months', '2024-02-10', '0000-00-00', '{\"box_contents\":{\"pair\":1}}', '{\"comfort\":\"high\"}', 4.25, '[]', 15.00, '{\"available_colors\":[\"White\",\"Black\"]}', '{\"sizes\":[\"40\",\"41\",\"42\",\"43\"],\"available_sizes\":[\"40\",\"41\",\"42\",\"43\"]}', 0, '2026-04-02 13:36:31', '2026-04-29 16:53:34', '{\"sole\":\"rubber\"}'),
(11, 12, 'LifeStyleCo', 'LifeStyle Co.', 'LS-PLAN', '21 x 15 x 2 cm', 'Beige', '400g', 'Paper', '6 months', '2024-01-05', NULL, '{\"box_contents\":{\"planner\":1}}', '{\"pages\":180,\"layout\":\"daily\"}', 4.30, '{\"category\":\"lifestyle\"}', 0.00, NULL, NULL, 0, '2026-04-02 13:39:20', '2026-04-02 13:39:20', '{\"binding\":\"softcover\"}'),
(12, 13, 'CompTech', 'CompTech Ltd.', 'CT-LAP', '35 x 24 x 2 cm', 'Gray', '1.8kg', 'Aluminum', '24 months', '2024-02-20', NULL, '{\"box_contents\":{\"laptop\":true,\"charger\":true}}', '{\"screen\":\"15 inch\",\"ram\":\"16GB\"}', 4.70, '{\"category\":\"technology\"}', 5.00, NULL, NULL, 1, '2026-04-02 13:39:20', '2026-04-02 13:39:20', '{\"cpu\":\"i7\",\"storage\":\"512GB SSD\"}'),
(13, 14, 'MarketPro', 'MarketPro Agency', 'MP-TOOL', 'N/A', 'N/A', '0g', 'Digital', '12 months', '2025-01-01', '0000-00-00', '{\"box_contents\":[]}', '{\"tools\":\"SEO, Ads\"}', 4.40, '[]', 10.00, '{\"available_colors\":[]}', '{\"available_sizes\":[]}', 0, '2026-04-02 13:39:20', '2026-04-29 16:49:49', '{\"platform\":\"web\"}'),
(14, 15, 'PoliBooks', 'Politics Press', 'PB-2024', '22 x 14 x 3 cm', 'Red', '500g', 'Paper', '12 months', '2023-09-10', NULL, '{\"box_contents\":{\"book\":1}}', '{\"topic\":\"modern politics\"}', 4.10, '{\"category\":\"politics\"}', 0.00, NULL, NULL, 0, '2026-04-02 13:39:20', '2026-04-02 13:39:20', '{\"isbn\":\"99887766\"}'),
(15, 16, 'EcoLife', 'EcoLife Ltd.', 'EL-BOTTLE', '25 x 7 x 7 cm', 'Green', '300g', 'Steel', '12 months', '2024-03-01', NULL, '{\"box_contents\":{\"bottle\":1}}', '{\"capacity\":\"750ml\"}', 4.50, '{\"category\":\"environment\"}', 5.00, '{\"available_colors\":[\"Green\",\"Blue\"]}', NULL, 0, '2026-04-02 13:39:20', '2026-04-02 13:39:20', '{\"bpa_free\":true}'),
(16, 17, 'Artify', 'Artify Studio', 'AR-CANVAS', '40 x 30 x 2 cm', 'White', '600g', 'Cotton', '12 months', '2023-07-15', NULL, '{\"box_contents\":{\"canvas\":3}}', '{\"texture\":\"fine\"}', 4.35, '{\"category\":\"art\"}', 0.00, NULL, NULL, 0, '2026-04-02 13:39:20', '2026-04-02 13:39:20', '{\"frame\":\"wood\"}'),
(17, 18, 'HistoryMedia', 'History Media Inc.', 'HM-DOC', 'N/A', 'N/A', '0g', 'Digital', '12 months', '2024-01-01', '0000-00-00', '{\"box_contents\":[]}', '{\"duration\":\"90min\"}', 4.25, '[]', 0.00, '{\"available_colors\":[]}', '{\"available_sizes\":[]}', 0, '2026-04-02 13:39:20', '2026-04-29 16:47:50', '{\"resolution\":\"HD\"}'),
(18, 19, 'GamePro', 'GamePro Gear', 'GP-MOUSE', '12 x 6 x 4 cm', 'Black', '120g', 'Plastic', '12 months', '2024-02-01', NULL, '{\"box_contents\":{\"mouse\":true}}', '{\"dpi\":\"16000\"}', 4.60, '{\"category\":\"gaming\"}', 10.00, '{\"available_colors\":[\"Black\",\"RGB\"]}', NULL, 1, '2026-04-02 13:39:20', '2026-04-02 13:39:20', '{\"connection\":\"USB\"}'),
(19, 20, 'BabyCare', 'BabyCare Ltd.', 'BC-KIT', '30 x 20 x 10 cm', 'White', '900g', 'Mixed', '12 months', '2024-03-10', NULL, '{\"box_contents\":{\"items\":5}}', '{\"safe\":\"yes\"}', 4.45, '{\"category\":\"parenting\"}', 5.00, NULL, NULL, 0, '2026-04-02 13:39:20', '2026-04-02 13:39:20', '{\"age\":\"0-2\"}'),
(20, 21, 'EstatePro', 'EstatePro Inc.', 'EP-GUIDE', '21 x 14 x 2 cm', 'Blue', '350g', 'Paper', '12 months', '2023-11-01', NULL, '{\"box_contents\":{\"book\":1}}', '{\"topic\":\"real estate\"}', 4.20, '{\"category\":\"real-estate\"}', 0.00, NULL, NULL, 0, '2026-04-02 13:39:20', '2026-04-02 13:39:20', '{\"isbn\":\"55443322\"}'),
(21, 22, 'AutoClean', 'AutoClean Ltd.', 'AC-VAC', '30 x 10 x 10 cm', 'Black', '1kg', 'Plastic', '12 months', '2024-01-15', NULL, '{\"box_contents\":{\"vacuum\":true}}', '{\"power\":\"120W\"}', 4.40, '{\"category\":\"automotive\"}', 5.00, NULL, NULL, 0, '2026-04-02 13:39:20', '2026-04-02 13:39:20', '{\"voltage\":\"12V\"}'),
(22, 23, 'ToolMaster', 'ToolMaster Inc.', 'TM-SET', '40 x 25 x 10 cm', 'Red', '2kg', 'Steel', '24 months', '2023-10-10', NULL, '{\"box_contents\":{\"tools\":20}}', '{\"durability\":\"high\"}', 4.55, '{\"category\":\"diy\"}', 10.00, NULL, NULL, 0, '2026-04-02 13:39:20', '2026-04-02 13:39:20', '{\"case\":\"included\"}'),
(23, 24, 'PhotoPro', 'PhotoPro Ltd.', 'PP-LENS', '15 x 8 x 8 cm', 'Black', '500g', 'Glass', '24 months', '2024-02-01', NULL, '{\"box_contents\":{\"lens\":true}}', '{\"zoom\":\"4x\"}', 4.65, '{\"category\":\"photography\"}', 5.00, NULL, NULL, 1, '2026-04-02 13:39:20', '2026-04-02 13:39:20', '{\"mount\":\"universal\"}'),
(24, 25, 'MusicGear', 'MusicGear Co.', 'MG-GTR', '100 x 35 x 10 cm', 'Brown', '2.5kg', 'Wood', '24 months', '2023-06-20', NULL, '{\"box_contents\":{\"guitar\":true}}', '{\"type\":\"acoustic\"}', 4.50, '{\"category\":\"music\"}', 0.00, NULL, NULL, 0, '2026-04-02 13:39:20', '2026-04-02 13:39:20', '{\"strings\":\"steel\"}'),
(25, 26, 'BookWorld', 'BookWorld Ltd.', 'BW-NOVEL', '21 x 14 x 3 cm', 'Black', '400g', 'Paper', '12 months', '2024-01-01', NULL, '{\"box_contents\":{\"book\":1}}', '{\"genre\":\"fiction\"}', 4.35, '{\"category\":\"books\"}', 5.00, NULL, NULL, 0, '2026-04-02 13:39:20', '2026-04-02 13:39:20', '{\"pages\":320}'),
(26, 27, 'FitLife', 'FitLife Corp.', 'FL-MAT', '180 x 60 x 1 cm', 'Purple', '900g', 'Foam', '12 months', '2024-02-15', NULL, '{\"box_contents\":{\"mat\":1}}', '{\"non_slip\":true}', 4.60, '{\"category\":\"fitness\"}', 10.00, '{\"available_colors\":[\"Purple\",\"Blue\"]}', NULL, 0, '2026-04-02 13:39:20', '2026-04-02 13:39:20', '{\"thickness\":\"1cm\"}'),
(27, 28, 'MindCare', 'MindCare Ltd.', 'MC-GUIDE', '20 x 13 x 2 cm', 'White', '300g', 'Paper', '12 months', '2023-12-01', NULL, '{\"box_contents\":{\"book\":1}}', '{\"topic\":\"mental health\"}', 4.50, '{\"category\":\"mental-health\"}', 0.00, NULL, NULL, 0, '2026-04-02 13:39:20', '2026-04-02 13:39:20', '{\"pages\":200}'),
(28, 29, 'CookPro', 'CookPro Inc.', 'CP-PAN', '30 x 30 x 10 cm', 'Black', '1.5kg', 'Aluminum', '24 months', '2024-01-20', NULL, '{\"box_contents\":{\"pans\":3}}', '{\"non_stick\":true}', 4.55, '{\"category\":\"cooking\"}', 5.00, NULL, NULL, 0, '2026-04-02 13:39:20', '2026-04-02 13:39:20', '{\"heat\":\"induction\"}'),
(29, 30, 'InvestSmart', 'InvestSmart Ltd.', 'IS-COURSE', 'N/A', 'N/A', '0g', 'Digital', '12 months', '2025-01-01', '0000-00-00', '{\"box_contents\":[]}', '{\"level\":\"beginner\"}', 4.70, '[]', 15.00, '{\"available_colors\":[]}', '{\"available_sizes\":[]}', 1, '2026-04-02 13:39:20', '2026-04-29 16:12:36', '{\"duration\":\"20h\"}'),
(30, 31, 'StartupLab', 'StartupLab Inc.', 'SL-BOOK', '21 x 14 x 2 cm', 'Yellow', '350g', 'Paper', '12 months', '2024-02-01', NULL, '{\"box_contents\":{\"book\":1}}', '{\"topic\":\"startups\"}', 4.40, '{\"category\":\"startups\"}', 0.00, NULL, NULL, 0, '2026-04-02 13:39:20', '2026-04-02 13:39:20', '{\"pages\":250}'),
(31, 32, 'DevTools', 'DevTools Ltd.', 'DT-EDITOR', 'N/A', 'Dark', '0g', 'Digital', '12 months', '2025-01-01', '0000-00-00', '{\"box_contents\":[]}', '{\"features\":\"syntax highlight\"}', 4.65, '[]', 10.00, '{\"available_colors\":[]}', '{\"available_sizes\":[]}', 1, '2026-04-02 13:39:20', '2026-04-29 16:11:11', '{\"platform\":\"cross-platform\"}'),
(32, 33, 'test', 'test', 'test', 'test', 'test', 'test', 'test', 'test', '2026-04-14', '0000-00-00', '{\"box_contents\":{\"charger\":\"true\",\"manual\":\"true\"}}', '{\"display\":\"6.5 inch\",\"battery\":\"4000mAh\"}', 5.00, '[\"phone\",\"iphone\"]', 10.00, '{\"available_colors\":[\"red\",\"green\",\"blue\"]}', '{\"available_sizes\":[\"128GB\",\"256GB\"]}', 0, '2026-04-14 12:52:52', '2026-04-14 12:52:52', '{\"processor\":\"Octa-core\",\"RAM\":\"16GB\"}');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `active` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `active` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `active`) VALUES
(1, 'admin', 'Administrator role with full system access. Can manage users, roles, products, settings, reports, and has complete control over the platform.', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `image` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `document` varchar(255) NOT NULL,
  `document_type_id` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `verification_token` varchar(64) DEFAULT NULL,
  `token_expires_at` datetime DEFAULT NULL,
  `registration_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `username`, `phone`, `image`, `address`, `document`, `document_type_id`, `active`, `verification_token`, `token_expires_at`, `registration_date`) VALUES
(1, 'demo', 'temp-email@example.com', '$argon2id$v=19$m=65536,t=4,p=1$L1drZXFYZTAxZWpOcHI3VQ$ATmLIjfHc4MqZP31qVlyTk9b6hdbWjerHqSD1OT2JkI', 'demoadmin', '', '', '', '', 1, 1, '', '2026-05-27 17:33:45', '2026-05-26 17:33:45'),
(2, 'demo', 'temp-email2@example.com', '$argon2id$v=19$m=65536,t=4,p=1$VDJFdG1sOGswZ2Vrc3cuNQ$r7zQydFg+nMu5MbBBp9dePP7iuZBgiJbTbTE5kRBOOY', 'demo', '', '', '', '', 1, 1, NULL, '2026-05-27 17:47:06', '2026-05-26 17:47:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_roles`
--

CREATE TABLE `users_roles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users_roles`
--

INSERT INTO `users_roles` (`id`, `user_id`, `role_id`) VALUES
(2, 1, 1);

--
-- Estructura de tabla para la tabla `shipments`
--

CREATE TABLE `shipments` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `shippo_shipment_id` varchar(100) NOT NULL,
  `shippo_transaction_id` varchar(100) NOT NULL,
  `stripe_session_id` varchar(200) NOT NULL,
  `tracking_number` varchar(100) NOT NULL,
  `carrier` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `label_url` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indices de la tabla `shipments`
--
ALTER TABLE `shipments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_shipment_sale_id` (`sale_id`);


--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `shipments`
--
ALTER TABLE `shipments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `shipments`
--
ALTER TABLE `shipments`
  ADD CONSTRAINT `fk_shipment_sale_id` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`),
  ADD KEY `fk_product_id` (`product_id`);

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `document_types`
--
ALTER TABLE `document_types`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `email_change_requests`
--
ALTER TABLE `email_change_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `fk_products_categories` (`category_id`);

--
-- Indices de la tabla `products_images`
--
ALTER TABLE `products_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indices de la tabla `products_information`
--
ALTER TABLE `products_information`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`);

--
-- Indices de la tabla `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reviews_products` (`product_id`),
  ADD KEY `fk_reviews_users` (`user_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sales_users` (`user_id`);

--
-- Indices de la tabla `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sale_items_products` (`product_id`),
  ADD KEY `fk_sale_items_sales` (`sale_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_document_types` (`document_type_id`);

--
-- Indices de la tabla `users_roles`
--
ALTER TABLE `users_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_roles_roles` (`role_id`),
  ADD KEY `fk_users_roles_users` (`user_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `document_types`
--
ALTER TABLE `document_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `email_change_requests`
--
ALTER TABLE `email_change_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `products_images`
--
ALTER TABLE `products_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de la tabla `products_information`
--
ALTER TABLE `products_information`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `users_roles`
--
ALTER TABLE `users_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_categories` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Filtros para la tabla `products_images`
--
ALTER TABLE `products_images`
  ADD CONSTRAINT `products_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `products_information`
--
ALTER TABLE `products_information`
  ADD CONSTRAINT `products_information_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_reviews_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `fk_sales_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `fk_sale_items_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_sale_items_sales` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`);

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_document_types` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `users_roles`
--
ALTER TABLE `users_roles`
  ADD CONSTRAINT `fk_users_roles_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `fk_users_roles_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
