<?php
// ====================================================
// PRODUCT TEMPLATE - MODERN E-COMMERCE DESIGN
// ====================================================

// Get current folder name
$current_path = __DIR__;
$folder_name = basename(dirname($current_path));

// Connect to database
require_once '../../dashboard/config.php';

// Get product data
$stmt = $pdo->prepare("SELECT * FROM products WHERE folder_name = ? AND status = 'active' LIMIT 1");
$stmt->execute([$folder_name]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Produk Tidak Ditemukan - Beliyuk Shop</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <h1>Produk Tidak Ditemukan</h1>
                        <p>Produk yang Anda cari tidak tersedia atau telah dihapus.</p>
                        <a href="/" class="btn btn-primary">Kembali ke Beranda</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>';
    exit;
}

// Update view count
$stmt = $pdo->prepare("UPDATE products SET views = COALESCE(views, 0) + 1 WHERE id = ?");
$stmt->execute([$product['id']]);

// Set page variables
$page_title = $product['meta_title'] ?: $product['title'] . ' | Beliyuk Shop';
$page_description = $product['meta_description'] ?: substr(strip_tags($product['description']), 0, 155);
$page_image = $product['meta_image_url'] ?: $product['image_url'];
$page_url = 'https://beliyuk.shop/products/' . $folder_name . '/';

// Format price
$formatted_price = 'Rp ' . number_format($product['price'], 0, ',', '.');
$original_price = isset($product['original_price_amount']) && $product['original_price_amount'] > $product['price'] 
    ? 'Rp ' . number_format($product['original_price_amount'], 0, ',', '.') 
    : null;

// Calculate discount if any
$discount = null;
if ($original_price && $product['original_price_amount'] > $product['price']) {
    $discount_percent = round((($product['original_price_amount'] - $product['price']) / $product['original_price_amount']) * 100);
    $discount = $discount_percent . '% OFF';
}
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($product['category'] ?? 'digital product, ebook, software'); ?>">
    <meta name="author" content="Beliyuk Shop">
    <link rel="canonical" href="<?php echo $page_url; ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="product">
    <meta property="og:url" content="<?php echo $page_url; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($product['meta_title'] ?: $product['title']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($page_image); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="Beliyuk Shop">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($product['meta_title'] ?: $product['title']); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($page_image); ?>">
    
    <!-- Product Meta -->
    <meta property="product:brand" content="<?php echo htmlspecialchars($product['product_brand'] ?? 'Beliyuk Shop'); ?>">
    <meta property="product:availability" content="<?php echo $product['availability'] === 'in stock' ? 'in stock' : 'out of stock'; ?>">
    <meta property="product:condition" content="<?php echo $product['condition'] ?? 'new'; ?>">
    <meta property="product:price:amount" content="<?php echo $product['price']; ?>">
    <meta property="product:price:currency" content="<?php echo $product['price_currency'] ?? 'IDR'; ?>">
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "Product",
        "name": "<?php echo addslashes($product['title']); ?>",
        "description": "<?php echo addslashes(strip_tags($product['description'])); ?>",
        "image": "<?php echo htmlspecialchars($page_image); ?>",
        "sku": "<?php echo $product['product_sku'] ?? ''; ?>",
        "brand": {
            "@type": "Brand",
            "name": "<?php echo htmlspecialchars($product['product_brand'] ?? 'Beliyuk Shop'); ?>"
        },
        "offers": {
            "@type": "Offer",
            "url": "<?php echo $page_url; ?>",
            "priceCurrency": "<?php echo $product['price_currency'] ?? 'IDR'; ?>",
            "price": "<?php echo $product['price']; ?>",
            "priceValidUntil": "<?php echo date('Y-m-d', strtotime('+1 year')); ?>",
            "availability": "https://schema.org/<?php echo $product['availability'] === 'in stock' ? 'InStock' : 'OutOfStock'; ?>",
            "itemCondition": "https://schema.org/NewCondition"
        }
    }
    </script>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --border-radius: 12px;
            --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            color: #333;
            line-height: 1.6;
        }
        
        /* Header */
        .main-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .site-logo {
            font-weight: 700;
            font-size: 1.5rem;
            color: white;
            text-decoration: none;
        }
        
        .site-logo:hover {
            color: rgba(255, 255, 255, 0.9);
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            transition: var(--transition);
        }
        
        .nav-link:hover {
            color: white;
        }
        
        /* Product Container */
        .product-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .product-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        
        /* Product Image */
        .product-image-container {
            position: relative;
            padding: 2rem;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            border-bottom: 1px solid #eaeaea;
        }
        
        .product-image-main {
            width: 100%;
            max-height: 500px;
            object-fit: contain;
            border-radius: 8px;
        }
        
        .product-badge {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: var(--danger-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            z-index: 2;
        }
        
        /* Product Info */
        .product-info {
            padding: 2rem;
        }
        
        .product-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }
        
        .product-category {
            color: var(--secondary-color);
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: inline-block;
            background: #f1f3f4;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
        }
        
        .product-rating {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            color: #ffc107;
        }
        
        .rating-stars {
            margin-right: 0.5rem;
        }
        
        .rating-count {
            color: var(--secondary-color);
            font-size: 0.9rem;
            margin-left: 0.5rem;
        }
        
        /* Price Section */
        .price-section {
            margin: 1.5rem 0;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: var(--border-radius);
        }
        
        .current-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--danger-color);
            line-height: 1;
        }
        
        .original-price {
            font-size: 1.2rem;
            color: var(--secondary-color);
            text-decoration: line-through;
            margin-right: 0.5rem;
        }
        
        .discount-badge {
            background: var(--danger-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        /* Stock Info */
        .stock-info {
            display: flex;
            align-items: center;
            margin: 1rem 0;
            padding: 0.75rem 1rem;
            background: #e7f5ff;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--primary-color);
        }
        
        .stock-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .in-stock {
            background: #d1fae5;
            color: var(--success-color);
        }
        
        .low-stock {
            background: #fef3c7;
            color: var(--warning-color);
        }
        
        .out-of-stock {
            background: #fee2e2;
            color: var(--danger-color);
        }
        
        /* Action Buttons */
        .action-buttons {
            display: grid;
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0b5ed7 100%);
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: var(--border-radius);
            color: white;
            transition: var(--transition);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(13, 110, 253, 0.25);
            color: white;
        }
        
        .btn-whatsapp {
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: var(--border-radius);
            color: white;
            transition: var(--transition);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-whatsapp:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.25);
            color: white;
        }
        
        /* Product Details */
        .product-details {
            margin: 2rem 0;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: var(--border-radius);
        }
        
        .details-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--dark-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
        }
        
        .product-description {
            font-size: 1rem;
            line-height: 1.8;
            color: #495057;
        }
        
        .features-list {
            list-style: none;
            padding: 0;
        }
        
        .features-list li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
        }
        
        .features-list li i {
            color: var(--success-color);
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }
        
        /* Product Meta */
        .product-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
            padding: 1.5rem;
            background: white;
            border-radius: var(--border-radius);
            border: 1px solid #eaeaea;
        }
        
        .meta-item {
            display: flex;
            flex-direction: column;
        }
        
        .meta-label {
            font-size: 0.9rem;
            color: var(--secondary-color);
            margin-bottom: 0.25rem;
        }
        
        .meta-value {
            font-size: 1rem;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        /* Footer */
        .main-footer {
            background: var(--dark-color);
            color: white;
            padding: 3rem 0;
            margin-top: 3rem;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 0.5rem;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .footer-links a:hover {
            color: white;
            padding-left: 0.5rem;
        }
        
        .copyright {
            text-align: center;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            margin-top: 2rem;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .product-title {
                font-size: 1.5rem;
            }
            
            .current-price {
                font-size: 2rem;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
            
            .product-meta {
                grid-template-columns: 1fr;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Utility Classes */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--danger-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .badge-custom {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="/" class="site-logo">
                    <i class="fas fa-store me-2"></i>Beliyuk Shop
                </a>
                <nav>
                    <a href="/" class="nav-link">
                        <i class="fas fa-home me-1"></i> Beranda
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Product Container -->
    <main class="product-container fade-in">
        <div class="product-card">
            <!-- Product Image -->
            <div class="product-image-container">
                <?php if ($discount): ?>
                <div class="product-badge pulse">
                    <i class="fas fa-tag me-1"></i> <?php echo $discount; ?>
                </div>
                <?php endif; ?>
                
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                     alt="<?php echo htmlspecialchars($product['title']); ?>" 
                     class="product-image-main">
            </div>

            <!-- Product Info -->
            <div class="product-info">
                <!-- Category & Title -->
                <div class="mb-3">
                    <span class="product-category">
                        <i class="fas fa-tag me-1"></i> 
                        <?php echo htmlspecialchars($product['category'] ?? 'Digital Product'); ?>
                    </span>
                    <h1 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h1>
                </div>

                <!-- Rating -->
                <div class="product-rating mb-4">
                    <div class="rating-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="rating-count">(4.8 dari 234 review)</span>
                </div>

                <!-- Price Section -->
                <div class="price-section">
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <?php if ($original_price): ?>
                        <div class="original-price"><?php echo $original_price; ?></div>
                        <?php endif; ?>
                        
                        <div class="current-price"><?php echo $formatted_price; ?></div>
                        
                        <?php if ($discount): ?>
                        <span class="discount-badge">Hemat <?php echo $discount; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($original_price && $product['original_price_amount'] > $product['price']): ?>
                    <div class="mt-2 text-success">
                        <i class="fas fa-money-bill-wave me-1"></i>
                        Hemat Rp <?php echo number_format($product['original_price_amount'] - $product['price'], 0, ',', '.'); ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Stock Info -->
                <div class="stock-info">
                    <i class="fas fa-box me-2"></i>
                    <div>
                        <strong>Status Stok:</strong>
                        <?php if ($product['stock'] > 10): ?>
                            <span class="stock-badge in-stock ms-2">
                                <i class="fas fa-check-circle me-1"></i> Tersedia (<?php echo $product['stock']; ?> unit)
                            </span>
                        <?php elseif ($product['stock'] > 0): ?>
                            <span class="stock-badge low-stock ms-2">
                                <i class="fas fa-exclamation-triangle me-1"></i> Stok Terbatas (<?php echo $product['stock']; ?> unit)
                            </span>
                        <?php else: ?>
                            <span class="stock-badge out-of-stock ms-2">
                                <i class="fas fa-times-circle me-1"></i> Stok Habis
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="javascript:void(0);" onclick="buyProduct()" class="btn-primary-custom">
                        <i class="fas fa-shopping-cart"></i> Beli Sekarang
                    </a>
                    
                    <a href="javascript:void(0);" onclick="chatWhatsApp()" class="btn-whatsapp">
                        <i class="fab fa-whatsapp"></i> Pesan via WhatsApp
                    </a>
                </div>

                <!-- Quick Features -->
                <div class="row g-3 mt-4">
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 border rounded">
                            <i class="fas fa-shipping-fast fa-2x text-primary mb-2"></i>
                            <div class="small">Pengiriman Instan</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 border rounded">
                            <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                            <div class="small">Garansi 100%</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 border rounded">
                            <i class="fas fa-headset fa-2x text-info mb-2"></i>
                            <div class="small">Support 24/7</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 border rounded">
                            <i class="fas fa-undo fa-2x text-warning mb-2"></i>
                            <div class="small">Garansi Refund</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Details -->
            <div class="product-details">
                <h3 class="details-title">
                    <i class="fas fa-info-circle me-2"></i>Detail Produk
                </h3>
                
                <div class="product-description mb-4">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>
                
                <h4 class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Fitur Utama:</h4>
                <ul class="features-list">
                    <li><i class="fas fa-check"></i> Akses seumur hidup</li>
                    <li><i class="fas fa-check"></i> Update gratis selamanya</li>
                    <li><i class="fas fa-check"></i> Support teknis 24/7</li>
                    <li><i class="fas fa-check"></i> Kompatibel semua device</li>
                    <li><i class="fas fa-check"></i> File original berkualitas tinggi</li>
                    <li><i class="fas fa-check"></i> Download langsung setelah pembayaran</li>
                </ul>
            </div>

            <!-- Product Meta Information -->
            <div class="product-meta">
                <div class="meta-item">
                    <span class="meta-label"><i class="fas fa-barcode me-1"></i> SKU</span>
                    <span class="meta-value"><?php echo $product['product_sku'] ?? 'PROD-' . $product['id']; ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><i class="fas fa-tag me-1"></i> Merek</span>
                    <span class="meta-value"><?php echo htmlspecialchars($product['product_brand'] ?? 'Beliyuk Shop'); ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><i class="fas fa-box me-1"></i> Ketersediaan</span>
                    <span class="meta-value"><?php echo $product['availability'] === 'in stock' ? 'Tersedia' : 'Habis'; ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><i class="fas fa-certificate me-1"></i> Kondisi</span>
                    <span class="meta-value"><?php echo $product['condition'] === 'new' ? 'Baru' : 'Bekas'; ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><i class="fas fa-money-bill me-1"></i> Mata Uang</span>
                    <span class="meta-value"><?php echo $product['price_currency'] ?? 'IDR (Rupiah)'; ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><i class="fas fa-calendar me-1"></i> Diupdate</span>
                    <span class="meta-value"><?php echo date('d/m/Y', strtotime($product['updated_at'])); ?></span>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h4 class="mb-3">
                        <i class="fas fa-store me-2"></i>Beliyuk Shop
                    </h4>
                    <p>Toko digital terpercaya untuk produk digital berkualitas.</p>
                    <div class="mt-3">
                        <a href="#" class="text-white me-3"><i class="fab fa-whatsapp fa-lg"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-telegram fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h4 class="mb-3">Menu Cepat</h4>
                    <ul class="footer-links">
                        <li><a href="/"><i class="fas fa-chevron-right me-1"></i> Beranda</a></li>
                        <li><a href="/products/"><i class="fas fa-chevron-right me-1"></i> Semua Produk</a></li>
                        <li><a href="/refund-policy/"><i class="fas fa-chevron-right me-1"></i> Kebijakan Refund</a></li>
                        <li><a href="/terms-and-conditions/"><i class="fas fa-chevron-right me-1"></i> Syarat & Ketentuan</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h4 class="mb-3">Kontak</h4>
                    <ul class="footer-links">
                        <li><i class="fas fa-envelope me-2"></i> support@beliyuk.shop</li>
                        <li><i class="fab fa-whatsapp me-2"></i> +62 812-3456-7890</li>
                        <li><i class="fas fa-clock me-2"></i> Support 24/7</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i> Indonesia</li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                <p class="mb-0">
                    &copy; <?php echo date('Y'); ?> Beliyuk Shop. All rights reserved. | 
                    Product ID: <?php echo $product['id']; ?> | 
                    Folder: <?php echo $folder_name; ?>
                </p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // WhatsApp Order Function
        function chatWhatsApp() {
            const phoneNumber = "6281234567890"; // Ganti dengan nomor WhatsApp Anda
            const productName = encodeURIComponent("<?php echo addslashes($product['title']); ?>");
            const productPrice = encodeURIComponent("<?php echo $formatted_price; ?>");
            const productUrl = encodeURIComponent("<?php echo $page_url; ?>");
            
            const message = `Halo, saya ingin memesan produk berikut:\n\n` +
                          `📦 *Produk:* ${productName}\n` +
                          `💰 *Harga:* ${productPrice}\n` +
                          `🔗 *Link:* ${productUrl}\n\n` +
                          `Bisa info cara ordernya?`;
            
            const whatsappUrl = `https://wa.me/${phoneNumber}?text=${message}`;
            window.open(whatsappUrl, '_blank');
        }
        
        // Buy Now Function
        function buyProduct() {
            const productName = "<?php echo addslashes($product['title']); ?>";
            const productPrice = "<?php echo $formatted_price; ?>";
            
            // Show order modal
            const modalHTML = `
                <div class="modal fade" id="orderModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title"><i class="fas fa-shopping-cart me-2"></i>Pesan Sekarang</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-success">
                                    <h5>${productName}</h5>
                                    <p class="mb-0">Harga: <strong>${productPrice}</strong></p>
                                </div>
                                <p>Silakan pilih metode order:</p>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success" onclick="chatWhatsApp()">
                                        <i class="fab fa-whatsapp me-2"></i>Order via WhatsApp
                                    </button>
                                    <button class="btn btn-outline-primary" onclick="copyOrderInfo()">
                                        <i class="fas fa-copy me-2"></i>Copy Info Produk
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Create and show modal
            const modalDiv = document.createElement('div');
            modalDiv.innerHTML = modalHTML;
            document.body.appendChild(modalDiv);
            
            const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
            orderModal.show();
            
            // Remove modal from DOM after hiding
            document.getElementById('orderModal').addEventListener('hidden.bs.modal', function () {
                modalDiv.remove();
            });
        }
        
        // Copy Order Info
        function copyOrderInfo() {
            const productInfo = `Produk: <?php echo addslashes($product['title']); ?>\n` +
                              `Harga: <?php echo $formatted_price; ?>\n` +
                              `Link: <?php echo $page_url; ?>\n` +
                              `Deskripsi: <?php echo addslashes(substr(strip_tags($product['description']), 0, 100)); ?>...`;
            
            navigator.clipboard.writeText(productInfo).then(() => {
                alert('Info produk berhasil disalin! Silakan tempel di chat WhatsApp.');
            });
        }
        
        // Add to Cart Animation
        function animateCart() {
            const cartBtn = document.querySelector('.btn-primary-custom');
            cartBtn.innerHTML = '<i class="fas fa-check me-2"></i> Ditambahkan!';
            cartBtn.classList.add('btn-success');
            setTimeout(() => {
                cartBtn.innerHTML = '<i class="fas fa-shopping-cart me-2"></i> Beli Sekarang';
                cartBtn.classList.remove('btn-success');
            }, 2000);
        }
        
        // Page Load Animation
        document.addEventListener('DOMContentLoaded', function() {
            // Add fade-in effect to elements
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });
            
            // Update view count every 30 seconds if user stays on page
            setInterval(() => {
                fetch(`/api/view-product?product_id=<?php echo $product['id']; ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
            }, 30000);
        });
    </script>
</body>
</html>
<?php
// Log the view
$log_file = '../../dashboard/product_views.log';
$log_entry = "[" . date('Y-m-d H:i:s') . "] " . 
    "Product: " . $product['id'] . " | " .
    "Title: " . $product['title'] . " | " .
    "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . " | " .
    "User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown') . "\n";
@file_put_contents($log_file, $log_entry, FILE_APPEND);
?>