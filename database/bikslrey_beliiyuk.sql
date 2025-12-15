-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 15, 2025 at 02:56 AM
-- Server version: 11.4.9-MariaDB-cll-lve-log
-- PHP Version: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bikslrey_beliiyuk`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`bikslrey`@`localhost` PROCEDURE `get_product_by_id` (IN `p_id` INT)   BEGIN
    SELECT * FROM products WHERE id = p_id;
END$$

CREATE DEFINER=`bikslrey`@`localhost` PROCEDURE `search_products` (IN `p_keyword` VARCHAR(255), IN `p_category` VARCHAR(100), IN `p_min_price` DECIMAL(12,2), IN `p_max_price` DECIMAL(12,2), IN `p_status` VARCHAR(10), IN `p_limit` INT, IN `p_offset` INT)   BEGIN
    SET @sql = 'SELECT * FROM products WHERE 1=1';
    
    IF p_keyword IS NOT NULL AND p_keyword != '' THEN
        SET @sql = CONCAT(@sql, ' AND MATCH(title, description, meta_title, meta_description) AGAINST(? IN BOOLEAN MODE)');
    END IF;
    
    IF p_category IS NOT NULL AND p_category != '' THEN
        SET @sql = CONCAT(@sql, ' AND category = ?');
    END IF;
    
    IF p_min_price IS NOT NULL THEN
        SET @sql = CONCAT(@sql, ' AND price >= ?');
    END IF;
    
    IF p_max_price IS NOT NULL THEN
        SET @sql = CONCAT(@sql, ' AND price <= ?');
    END IF;
    
    IF p_status IS NOT NULL AND p_status != '' THEN
        SET @sql = CONCAT(@sql, ' AND status = ?');
    END IF;
    
    SET @sql = CONCAT(@sql, ' ORDER BY created_at DESC');
    
    IF p_limit IS NOT NULL THEN
        SET @sql = CONCAT(@sql, ' LIMIT ?');
    END IF;
    
    IF p_offset IS NOT NULL THEN
        SET @sql = CONCAT(@sql, ' OFFSET ?');
    END IF;
    
    PREPARE stmt FROM @sql;
    
    -- Bind parameters dynamically
    SET @param_count = 0;
    
    IF p_keyword IS NOT NULL AND p_keyword != '' THEN
        SET @param_count = @param_count + 1;
        SET @keyword_param = p_keyword;
    END IF;
    
    IF p_category IS NOT NULL AND p_category != '' THEN
        SET @param_count = @param_count + 1;
        SET @category_param = p_category;
    END IF;
    
    IF p_min_price IS NOT NULL THEN
        SET @param_count = @param_count + 1;
        SET @min_price_param = p_min_price;
    END IF;
    
    IF p_max_price IS NOT NULL THEN
        SET @param_count = @param_count + 1;
        SET @max_price_param = p_max_price;
    END IF;
    
    IF p_status IS NOT NULL AND p_status != '' THEN
        SET @param_count = @param_count + 1;
        SET @status_param = p_status;
    END IF;
    
    IF p_limit IS NOT NULL THEN
        SET @param_count = @param_count + 1;
        SET @limit_param = p_limit;
    END IF;
    
    IF p_offset IS NOT NULL THEN
        SET @param_count = @param_count + 1;
        SET @offset_param = p_offset;
    END IF;
    
    -- Execute with appropriate parameters
    CASE @param_count
        WHEN 0 THEN EXECUTE stmt;
        WHEN 1 THEN EXECUTE stmt USING @keyword_param;
        WHEN 2 THEN EXECUTE stmt USING @keyword_param, @category_param;
        WHEN 3 THEN EXECUTE stmt USING @keyword_param, @category_param, @min_price_param;
        WHEN 4 THEN EXECUTE stmt USING @keyword_param, @category_param, @min_price_param, @max_price_param;
        WHEN 5 THEN EXECUTE stmt USING @keyword_param, @category_param, @min_price_param, @max_price_param, @status_param;
        WHEN 6 THEN EXECUTE stmt USING @keyword_param, @category_param, @min_price_param, @max_price_param, @status_param, @limit_param;
        WHEN 7 THEN EXECUTE stmt USING @keyword_param, @category_param, @min_price_param, @max_price_param, @status_param, @limit_param, @offset_param;
    END CASE;
    
    DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`bikslrey`@`localhost` PROCEDURE `update_product_stock` (IN `p_product_id` INT, IN `p_quantity` INT, IN `p_operation` VARCHAR(10))   BEGIN
    IF p_operation = 'add' THEN
        UPDATE products 
        SET stock = stock + p_quantity,
            updated_at = NOW()
        WHERE id = p_product_id;
    ELSEIF p_operation = 'subtract' THEN
        UPDATE products 
        SET stock = GREATEST(0, stock - p_quantity),
            updated_at = NOW()
        WHERE id = p_product_id;
    END IF;
END$$

--
-- Functions
--
CREATE DEFINER=`bikslrey`@`localhost` FUNCTION `generate_product_url` (`product_title` VARCHAR(255), `product_id` INT) RETURNS VARCHAR(500) CHARSET latin1 COLLATE latin1_swedish_ci DETERMINISTIC BEGIN
    DECLARE safe_title VARCHAR(255);
    DECLARE folder_name VARCHAR(255);
    DECLARE timestamp_val VARCHAR(20);
    
    SET timestamp_val = UNIX_TIMESTAMP();
    SET safe_title = LOWER(product_title);
    SET safe_title = REPLACE(safe_title, ' ', '-');
    SET safe_title = REGEXP_REPLACE(safe_title, '[^a-z0-9-]', '');
    SET safe_title = REGEXP_REPLACE(safe_title, '-+', '-');
    SET safe_title = TRIM(BOTH '-' FROM safe_title);
    
    -- Batasi panjang
    IF CHAR_LENGTH(safe_title) > 100 THEN
        SET safe_title = SUBSTRING(safe_title, 1, 100);
    END IF;
    
    SET folder_name = CONCAT(safe_title, '-', timestamp_val);
    
    RETURN CONCAT('https://beliyuk.shop/products/', folder_name, '/');
END$$

CREATE DEFINER=`bikslrey`@`localhost` FUNCTION `is_product_available` (`p_product_id` INT) RETURNS TINYINT(1) READS SQL DATA BEGIN
    DECLARE product_stock INT;
    DECLARE product_status VARCHAR(10);
    
    SELECT stock, status INTO product_stock, product_status
    FROM products 
    WHERE id = p_product_id;
    
    IF product_status = 'active' AND product_stock > 0 THEN
        RETURN TRUE;
    ELSE
        RETURN FALSE;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Product categories for better organization';

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `parent_id`, `created_at`) VALUES
(1, 'E-Books', 'ebooks', NULL, '2025-12-15 00:54:22'),
(2, 'Software', 'software', NULL, '2025-12-15 00:54:22'),
(3, 'Courses', 'courses', NULL, '2025-12-15 00:54:22'),
(4, 'Self-Help', 'self-help', 1, '2025-12-15 00:54:22'),
(5, 'Business', 'business', 1, '2025-12-15 00:54:22'),
(6, 'Psychology', 'psychology', 1, '2025-12-15 00:54:22'),
(7, 'Finance', 'finance', 1, '2025-12-15 00:54:22');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `category` varchar(100) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_image_url` varchar(500) DEFAULT NULL,
  `og_type` varchar(50) DEFAULT 'product',
  `product_sku` varchar(100) DEFAULT NULL,
  `product_brand` varchar(100) DEFAULT 'Beliyuk Shop',
  `availability` varchar(50) DEFAULT 'in stock',
  `price_amount` decimal(12,2) DEFAULT NULL,
  `price_currency` varchar(10) DEFAULT 'IDR',
  `sale_price_amount` decimal(12,2) DEFAULT NULL,
  `original_price_amount` decimal(12,2) DEFAULT NULL,
  `product_category` varchar(255) DEFAULT NULL,
  `structured_data_json` text DEFAULT NULL,
  `folder_name` varchar(255) DEFAULT NULL,
  `product_url` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `og_image_width` varchar(10) DEFAULT '1200',
  `og_image_height` varchar(10) DEFAULT '630',
  `og_image_alt` varchar(255) DEFAULT NULL,
  `og_site_name` varchar(100) DEFAULT 'Beliyuk Shop',
  `retailer_item_id` varchar(100) DEFAULT NULL,
  `condition` varchar(50) DEFAULT 'new',
  `item_group_id` varchar(100) DEFAULT NULL,
  `microdata_html` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Main products table for Beliyuk.shop e-commerce system - Created by Dashboard System';

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `title`, `description`, `price`, `stock`, `status`, `category`, `image_url`, `meta_title`, `meta_description`, `meta_image_url`, `og_type`, `product_sku`, `product_brand`, `availability`, `price_amount`, `price_currency`, `sale_price_amount`, `original_price_amount`, `product_category`, `structured_data_json`, `folder_name`, `product_url`, `created_at`, `updated_at`, `og_image_width`, `og_image_height`, `og_image_alt`, `og_site_name`, `retailer_item_id`, `condition`, `item_group_id`, `microdata_html`) VALUES
(1, '1.999+ E-BOOKS PREMIUM Best Seller', 'Koleksi ribuan e-book self improvement, mindset, dan bisnis. Praktis dibaca di semua perangkat.', 499999.00, 100, 'active', 'ebook', 'https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp', '1.999+ E-BOOKS Premium Collection | Beliyuk Shop', 'Akses instan ke 1.999+ e-book premium: bisnis, mindset, self-improvement, bahasa, kesehatan, dan banyak topik lainnya.', 'https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp', 'product', '72e1rdnnrd', 'Beliyuk Shop', 'in stock', 499999.00, 'IDR', 499999.00, 9999999.00, 'Media > Books > E-books', '{\r\n  \"@context\": \"https://schema.org/\",\r\n  \"@type\": \"Product\",\r\n  \"@id\": \"https://beliyuk.shop/products/1999-ebooks-premium-1712345678/#product\",\r\n  \"name\": \"1.999+ E-BOOKS PREMIUM Best Seller\",\r\n  \"alternateName\": \"Paket Lengkap 1.999+ E-Books Premium\",\r\n  \"description\": \"Koleksi ribuan e-book self improvement, mindset, dan bisnis. Praktis dibaca di semua perangkat.\",\r\n  \"sku\": \"72e1rdnnrd\",\r\n  \"mpn\": \"72e1rdnnrd\",\r\n  \"brand\": {\r\n    \"@type\": \"Brand\",\r\n    \"name\": \"Beliyuk Shop\",\r\n    \"url\": \"https://beliyuk.shop/\",\r\n    \"logo\": \"https://beliyuk.shop/logo.png\"\r\n  },\r\n  \"image\": [\r\n    \"https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp\"\r\n  ],\r\n  \"offers\": {\r\n    \"@type\": \"Offer\",\r\n    \"@id\": \"https://beliyuk.shop/products/1999-ebooks-premium-1712345678/#offer\",\r\n    \"url\": \"https://beliyuk.shop/products/1999-ebooks-premium-1712345678/\",\r\n    \"priceCurrency\": \"IDR\",\r\n    \"price\": \"499999\",\r\n    \"priceValidUntil\": \"2030-12-31\",\r\n    \"availability\": \"https://schema.org/InStock\",\r\n    \"itemCondition\": \"https://schema.org/NewCondition\",\r\n    \"seller\": {\r\n      \"@type\": \"Organization\",\r\n      \"name\": \"Beliyuk Shop\",\r\n      \"url\": \"https://beliyuk.shop/\"\r\n    }\r\n  },\r\n  \"aggregateRating\": {\r\n    \"@type\": \"AggregateRating\",\r\n    \"ratingValue\": \"4.9\",\r\n    \"ratingCount\": \"234\",\r\n    \"bestRating\": \"5\",\r\n    \"worstRating\": \"1\"\r\n  },\r\n  \"category\": \"Media > Books > E-books\"\r\n}', '1999-ebooks-premium-1712345678', 'https://beliyuk.shop/products/1999-ebooks-premium-1712345678/', '2025-12-15 00:54:22', '2025-12-15 01:15:08', '1200', '630', '1.999+ E-BOOKS Premium Collection', 'Beliyuk Shop', '72e1rdnnrd', 'new', 'ebooks', '<div itemscope itemtype=\"http://schema.org/Product\" style=\"display:none;\">\r\n  <meta itemprop=\"url\" content=\"https://beliyuk.shop/products/1999-ebooks-premium-1712345678/\">\r\n  <meta itemprop=\"name\" content=\"1.999+ E-BOOKS PREMIUM Best Seller\">\r\n  <meta itemprop=\"alternateName\" content=\"Paket Lengkap 1.999+ E-Books Premium\">\r\n  <meta itemprop=\"description\" content=\"Koleksi ribuan e-book self improvement, mindset, dan bisnis. Praktis dibaca di semua perangkat.\">\r\n  <meta itemprop=\"sku\" content=\"72e1rdnnrd\">\r\n  <meta itemprop=\"mpn\" content=\"72e1rdnnrd\">\r\n  <link itemprop=\"image\" href=\"https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp\">\r\n  <div itemprop=\"brand\" itemscope itemtype=\"http://schema.org/Brand\">\r\n    <meta itemprop=\"name\" content=\"Beliyuk Shop\">\r\n    <link itemprop=\"url\" href=\"https://beliyuk.shop/\">\r\n  </div>\r\n  <div itemprop=\"offers\" itemscope itemtype=\"http://schema.org/Offer\">\r\n    <meta itemprop=\"priceCurrency\" content=\"IDR\">\r\n    <meta itemprop=\"price\" content=\"499999\">\r\n    <meta itemprop=\"availability\" content=\"https://schema.org/InStock\">\r\n    <meta itemprop=\"itemCondition\" content=\"https://schema.org/NewCondition\">\r\n    <meta itemprop=\"url\" content=\"https://beliyuk.shop/products/1999-ebooks-premium-1712345678/\">\r\n    <div itemprop=\"seller\" itemscope itemtype=\"http://schema.org/Organization\">\r\n      <meta itemprop=\"name\" content=\"Beliyuk Shop\">\r\n    </div>\r\n  </div>\r\n  <div itemprop=\"aggregateRating\" itemscope itemtype=\"http://schema.org/AggregateRating\">\r\n    <meta itemprop=\"ratingValue\" content=\"4.9\">\r\n    <meta itemprop=\"ratingCount\" content=\"234\">\r\n  </div>\r\n</div>'),
(2, 'Atomic Habits E-BOOK', 'Cara membangun kebiasaan kecil yang menghasilkan perubahan besar dalam hidup Anda.', 149999.00, 50, 'active', 'ebook', 'https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp', 'Atomic Habits E-Book | Beliyuk Shop', 'Pelajari cara membangun kebiasaan baik dan menghilangkan kebiasaan buruk dengan metode yang terbukti efektif.', 'https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp', 'product', 'atomic-hb-001', 'Beliyuk Shop', 'in stock', 149999.00, 'IDR', 149999.00, 299999.00, 'Media > Books > E-books > Self-Help', '{\"@context\":\"https://schema.org/\",\"@type\":\"Product\",\"name\":\"Atomic Habits E-BOOK\",\"description\":\"Cara membangun kebiasaan kecil yang menghasilkan perubahan besar dalam hidup Anda.\",\"sku\":\"atomic-hb-001\",\"brand\":{\"@type\":\"Brand\",\"name\":\"Beliyuk Shop\"},\"image\":[\"https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp\"],\"offers\":{\"@type\":\"Offer\",\"priceCurrency\":\"IDR\",\"price\":149999,\"availability\":\"https://schema.org/InStock\"}}', 'atomic-habits-ebook-1712345679', 'https://beliyuk.shop/products/atomic-habits-ebook-1712345679/', '2025-12-15 00:54:22', '2025-12-15 00:54:22', '1200', '630', NULL, 'Beliyuk Shop', NULL, 'new', NULL, NULL),
(3, '48 Laws of Power E-BOOK', 'Strategi psikologis dan taktik pengaruh untuk memahami dinamika kekuasaan.', 199999.00, 75, 'active', 'ebook', 'https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp', '48 Laws of Power E-Book | Beliyuk Shop', 'Master the 48 laws of power with this comprehensive guide to understanding and applying power dynamics.', 'https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp', 'product', '48law-pwr-002', 'Beliyuk Shop', 'in stock', 199999.00, 'IDR', 199999.00, 399999.00, 'Media > Books > E-books > Psychology', '{\"@context\":\"https://schema.org/\",\"@type\":\"Product\",\"name\":\"48 Laws of Power E-BOOK\",\"description\":\"Strategi psikologis dan taktik pengaruh untuk memahami dinamika kekuasaan.\",\"sku\":\"48law-pwr-002\",\"brand\":{\"@type\":\"Brand\",\"name\":\"Beliyuk Shop\"},\"image\":[\"https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp\"],\"offers\":{\"@type\":\"Offer\",\"priceCurrency\":\"IDR\",\"price\":199999,\"availability\":\"https://schema.org/InStock\"}}', '48-laws-power-ebook-1712345680', 'https://beliyuk.shop/products/48-laws-power-ebook-1712345680/', '2025-12-15 00:54:22', '2025-12-15 00:54:22', '1200', '630', NULL, 'Beliyuk Shop', NULL, 'new', NULL, NULL),
(4, 'Psychology of Money Finance', 'Wawasan penting terkait perilaku manusia, investasi, dan cara membangun kekayaan.', 249999.00, 60, 'active', 'ebook', 'https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp', 'Psychology of Money Finance E-Book | Beliyuk Shop', 'Understand the psychology behind money management, investing, and wealth building strategies.', 'https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp', 'product', 'psych-money-003', 'Beliyuk Shop', 'in stock', 249999.00, 'IDR', 249999.00, 499999.00, 'Media > Books > E-books > Finance', '{\"@context\":\"https://schema.org/\",\"@type\":\"Product\",\"name\":\"Psychology of Money Finance\",\"description\":\"Wawasan penting terkait perilaku manusia, investasi, dan cara membangun kekayaan.\",\"sku\":\"psych-money-003\",\"brand\":{\"@type\":\"Brand\",\"name\":\"Beliyuk Shop\"},\"image\":[\"https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp\"],\"offers\":{\"@type\":\"Offer\",\"priceCurrency\":\"IDR\",\"price\":249999,\"availability\":\"https://schema.org/InStock\"}}', 'psychology-money-ebook-1712345681', 'https://beliyuk.shop/products/psychology-money-ebook-1712345681/', '2025-12-15 00:54:22', '2025-12-15 00:54:22', '1200', '630', NULL, 'Beliyuk Shop', NULL, 'new', NULL, NULL),
(5, 'The hhhh', 'Strategi praktis membangun mentalitas kuat dan meningkatkan posisi dalam lingkungan sosial.', 299999.00, 40, 'active', 'ebook', 'https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp', 'The Principles of Power New Edition | Beliyuk Shop', 'Practical strategies for building strong mentality and improving your position in social environments.', 'https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp', 'product', 'principles-pwr-004', 'Beliyuk Shop', 'in stock', 299999.00, 'IDR', 299999.00, 599999.00, 'Media > Books > E-books > Self-Help', '{\"@context\":\"https://schema.org/\",\"@type\":\"Product\",\"name\":\"The Principles of Power New\",\"description\":\"Strategi praktis membangun mentalitas kuat dan meningkatkan posisi dalam lingkungan sosial.\",\"sku\":\"principles-pwr-004\",\"brand\":{\"@type\":\"Brand\",\"name\":\"Beliyuk Shop\"},\"image\":[\"https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp\"],\"offers\":{\"@type\":\"Offer\",\"priceCurrency\":\"IDR\",\"price\":299999,\"availability\":\"https://schema.org/InStock\"}}', 'principles-power-ebook-1712345682', 'https://beliyuk.shop/products/principles-power-ebook-1712345682/', '2025-12-15 00:54:22', '2025-12-15 01:33:48', '1200', '630', NULL, 'Beliyuk Shop', NULL, 'new', NULL, NULL);

--
-- Triggers `products`
--
DELIMITER $$
CREATE TRIGGER `before_product_insert_sku` BEFORE INSERT ON `products` FOR EACH ROW BEGIN
    -- Auto-generate SKU jika kosong
    IF NEW.product_sku IS NULL OR NEW.product_sku = '' THEN
        SET NEW.product_sku = CONCAT('PROD-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));
    END IF;
    
    -- Set default values untuk price amounts
    IF NEW.price_amount IS NULL THEN
        SET NEW.price_amount = NEW.price;
    END IF;
    
    IF NEW.sale_price_amount IS NULL THEN
        SET NEW.sale_price_amount = NEW.price;
    END IF;
    
    IF NEW.original_price_amount IS NULL THEN
        SET NEW.original_price_amount = NEW.price;
    END IF;
    
    -- Auto-generate meta title jika kosong
    IF NEW.meta_title IS NULL OR NEW.meta_title = '' THEN
        SET NEW.meta_title = CONCAT(NEW.title, ' | Beliyuk Shop');
    END IF;
    
    -- Auto-generate meta description jika kosong
    IF NEW.meta_description IS NULL OR NEW.meta_description = '' THEN
        SET NEW.meta_description = SUBSTRING(NEW.description, 1, 155);
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_product_update_folder` BEFORE UPDATE ON `products` FOR EACH ROW BEGIN
    DECLARE new_folder_name VARCHAR(255);
    
    -- Hanya update folder jika judul berubah dan folder sudah ada
    IF OLD.title != NEW.title AND OLD.folder_name IS NOT NULL THEN
        -- Generate nama folder baru dari judul
        SET new_folder_name = LOWER(OLD.folder_name);
        SET new_folder_name = REPLACE(new_folder_name, OLD.title, NEW.title);
        SET new_folder_name = REGEXP_REPLACE(new_folder_name, '[^a-z0-9-]', '-');
        SET new_folder_name = REGEXP_REPLACE(new_folder_name, '-+', '-');
        
        -- Update folder name dan URL
        SET NEW.folder_name = new_folder_name;
        SET NEW.product_url = CONCAT('https://beliyuk.shop/products/', new_folder_name, '/');
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `product_stats`
-- (See below for the actual view)
--
CREATE TABLE `product_stats` (
`total_products` bigint(21)
,`active_products` decimal(22,0)
,`inactive_products` decimal(22,0)
,`total_stock` decimal(32,0)
,`avg_price` decimal(16,6)
,`min_price` decimal(12,2)
,`max_price` decimal(12,2)
,`total_categories` bigint(21)
,`discounted_products` decimal(22,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `product_views`
--

CREATE TABLE `product_views` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `views` int(11) DEFAULT 0,
  `last_viewed` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_folder` (`folder_name`),
  ADD KEY `idx_sku` (`product_sku`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_price` (`price`),
  ADD KEY `idx_stock` (`stock`),
  ADD KEY `idx_created_desc` (`created_at` DESC),
  ADD KEY `idx_updated_desc` (`updated_at` DESC),
  ADD KEY `idx_price_range` (`price`,`status`),
  ADD KEY `idx_category_status` (`category`,`status`);
ALTER TABLE `products` ADD FULLTEXT KEY `idx_search` (`title`,`description`,`meta_title`,`meta_description`);

--
-- Indexes for table `product_views`
--
ALTER TABLE `product_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `product_views`
--
ALTER TABLE `product_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Structure for view `product_stats`
--
DROP TABLE IF EXISTS `product_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bikslrey`@`localhost` SQL SECURITY DEFINER VIEW `product_stats`  AS SELECT count(0) AS `total_products`, sum(case when `products`.`status` = 'active' then 1 else 0 end) AS `active_products`, sum(case when `products`.`status` = 'inactive' then 1 else 0 end) AS `inactive_products`, sum(`products`.`stock`) AS `total_stock`, avg(`products`.`price`) AS `avg_price`, min(`products`.`price`) AS `min_price`, max(`products`.`price`) AS `max_price`, count(distinct `products`.`category`) AS `total_categories`, sum(case when `products`.`sale_price_amount` < `products`.`price` then 1 else 0 end) AS `discounted_products` FROM `products` ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_views`
--
ALTER TABLE `product_views`
  ADD CONSTRAINT `fk_product_views` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`bikslrey`@`localhost` EVENT `cleanup_inactive_products` ON SCHEDULE EVERY 1 MONTH STARTS '2025-12-15 00:54:22' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    -- Archive inactive products older than 6 months
    INSERT INTO products_archive 
    SELECT * FROM products 
    WHERE status = 'inactive' 
    AND updated_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
    
    -- Delete archived products
    DELETE FROM products 
    WHERE status = 'inactive' 
    AND updated_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
    
    -- Update statistics
    CALL update_product_statistics();
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
