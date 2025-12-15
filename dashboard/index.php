<?php
// ====================================================
// DASHBOARD ADMIN BELIYUK.SHOP - FULL VERSION 3.0
// ====================================================

require_once 'config.php';

// ====================================================
// PAGINATION & DATA FETCHING
// ====================================================

$limit = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$offset = ($page - 1) * $limit;

// Hitung total data
$stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
$total_rows = $stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Ambil data produk
$stmt = $pdo->prepare("
    SELECT *, 
    DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') as created_formatted,
    DATE_FORMAT(updated_at, '%d/%m/%Y %H:%i') as updated_formatted
    FROM products 
    ORDER BY created_at DESC 
    LIMIT :limit OFFSET :offset
");

$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung statistik
$stats_stmt = $pdo->query("
    SELECT 
        COUNT(*) as total_products,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_products,
        SUM(stock) as total_stock,
        AVG(price) as avg_price
    FROM products
");
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Produk | beliyuk.shop</title>
    
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --dark-color: #1f2937;
            --light-color: #f9fafb;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            min-height: 100vh;
            padding-top: 20px;
        }
        
        .dashboard-container {
            max-width: 1600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        /* Header */
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 35px 40px;
            position: relative;
            overflow: hidden;
        }
        
        .dashboard-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.1;
        }
        
        .dashboard-header h1 {
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }
        
        .dashboard-header .subtitle {
            font-size: 1.2rem;
            opacity: 0.95;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }
        
        .site-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 10px 20px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Quick Stats */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin: 30px 40px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary-color), var(--secondary-color));
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .stat-card .count {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--dark-color);
            line-height: 1;
            margin: 10px 0;
        }
        
        .stat-card .label {
            color: #6b7280;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        /* Alert Messages */
        .alert-container {
            margin: 0 40px 20px;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert i {
            font-size: 1.2rem;
        }
        
        /* Main Content */
        .main-content {
            padding: 0 40px 40px;
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        
        .table-header {
            background: var(--light-color);
            padding: 25px 30px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-header h3 {
            margin: 0;
            color: var(--dark-color);
            font-weight: 700;
            font-size: 1.4rem;
        }
        
        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-sm {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }
        
        .btn-sm:hover {
            transform: translateY(-2px);
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        
        /* Table */
        .table {
            margin: 0;
        }
        
        .table thead th {
            background: var(--dark-color);
            color: white;
            border: none;
            padding: 15px 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        .table tbody tr {
            transition: all 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: #f8fafc;
        }
        
        .table td {
            padding: 15px 20px;
            vertical-align: middle;
            border-color: #e5e7eb;
        }
        
        /* Product Image */
        .product-thumb {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            transition: transform 0.3s ease;
        }
        
        .product-thumb:hover {
            transform: scale(1.1);
        }
        
        /* Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
        }
        
        /* Form Styles */
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
        }
        
        .form-control, .form-select, textarea {
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus, textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        /* Code Editor Style */
        .code-editor {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.5;
            background: #f8fafc;
            border: 2px solid #e5e7eb;
        }
        
        /* Folder Name Input */
        .folder-input-group {
            display: flex;
            gap: 10px;
        }
        
        .folder-input-group input {
            flex: 1;
        }
        
        .folder-input-group .btn {
            white-space: nowrap;
        }
        
        /* Tab Styles */
        .nav-tabs {
            border-bottom: 2px solid #e5e7eb;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: #6b7280;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 8px 8px 0 0;
            margin-right: 5px;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            background-color: white;
            border-bottom: 2px solid var(--primary-color);
        }
        
        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-header {
                padding: 20px;
                text-align: center;
            }
            
            .main-content {
                padding: 0 20px 20px;
            }
            
            .quick-stats {
                grid-template-columns: 1fr;
                margin: 20px;
                gap: 15px;
            }
            
            .table-header {
                flex-direction: column;
                gap: 15px;
                padding: 20px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 5px;
            }
            
            .btn-sm {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <h1><i class="fas fa-store me-2"></i>Dashboard Produk</h1>
            <p class="subtitle">Kelola Open Graph, Meta Catalog, Structured Data & Folder</p>
            <div class="site-badge">
                <i class="fas fa-globe"></i>
                <span>beliyuk.shop/products/</span>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="quick-stats">
            <div class="stat-card">
                <i class="fas fa-box"></i>
                <div class="count"><?php echo $stats['total_products'] ?? 0; ?></div>
                <div class="label">Total Produk</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle"></i>
                <div class="count"><?php echo $stats['active_products'] ?? 0; ?></div>
                <div class="label">Produk Aktif</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-boxes"></i>
                <div class="count"><?php echo number_format($stats['total_stock'] ?? 0); ?></div>
                <div class="label">Total Stok</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-tag"></i>
                <div class="count">Rp <?php echo number_format($stats['avg_price'] ?? 0, 0, ',', '.'); ?></div>
                <div class="label">Rata-rata Harga</div>
            </div>
        </div>

        <!-- Alert Messages -->
        <div class="alert-container">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($_GET['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Product Table -->
            <div class="table-container">
                <div class="table-header">
                    <h3><i class="fas fa-list me-2"></i>Daftar Produk</h3>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus me-1"></i> Tambah Produk Baru
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th width="80">Gambar</th>
                                <th>Produk & Deskripsi</th>
                                <th width="120">Harga</th>
                                <th width="80">Stok</th>
                                <th width="100">Status</th>
                                <th width="150">Folder</th>
                                <th width="200" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada produk</p>
                                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                            <i class="fas fa-plus me-1"></i> Tambah Produk Pertama
                                        </button>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = $offset + 1; ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <?php if (!empty($product['image_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['title']); ?>"
                                                     class="product-thumb">
                                            <?php else: ?>
                                                <div class="product-thumb bg-light d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong class="d-block mb-1"><?php echo htmlspecialchars($product['title']); ?></strong>
                                            <small class="text-muted d-block mb-1">
                                                <?php 
                                                $desc = strip_tags(htmlspecialchars($product['description']));
                                                echo strlen($desc) > 60 ? substr($desc, 0, 60) . '...' : $desc;
                                                ?>
                                            </small>
                                            <?php if (!empty($product['category'])): ?>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($product['category']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-primary">
                                                Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $product['stock'] > 20 ? 'success' : 
                                                     ($product['stock'] > 0 ? 'warning' : 'danger'); 
                                            ?>">
                                                <?php echo $product['stock']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $product['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo $product['status'] == 'active' ? 'Aktif' : 'Nonaktif'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($product['folder_name'])): ?>
                                                <small class="text-muted d-block mb-1">
                                                    <i class="fas fa-folder me-1"></i>
                                                    <?php echo substr($product['folder_name'], 0, 20); ?>...
                                                </small>
                                                <a href="/products/<?php echo $product['folder_name']; ?>/" 
                                                   target="_blank" class="text-decoration-none small">
                                                    <i class="fas fa-external-link-alt me-1"></i>Preview
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Belum ada folder</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="action-buttons">
                                                <button class="btn btn-sm btn-outline-warning btn-edit" 
                                                        data-id="<?php echo $product['id']; ?>"
                                                        data-title="<?php echo htmlspecialchars($product['title']); ?>"
                                                        data-description="<?php echo htmlspecialchars($product['description']); ?>"
                                                        data-price="<?php echo $product['price']; ?>"
                                                        data-stock="<?php echo $product['stock']; ?>"
                                                        data-status="<?php echo $product['status']; ?>"
                                                        data-category="<?php echo htmlspecialchars($product['category'] ?? ''); ?>"
                                                        data-image_url="<?php echo htmlspecialchars($product['image_url'] ?? ''); ?>"
                                                        data-folder_name="<?php echo htmlspecialchars($product['folder_name'] ?? ''); ?>"
                                                        data-meta_title="<?php echo htmlspecialchars($product['meta_title'] ?? ''); ?>"
                                                        data-meta_description="<?php echo htmlspecialchars($product['meta_description'] ?? ''); ?>"
                                                        data-meta_image_url="<?php echo htmlspecialchars($product['meta_image_url'] ?? ''); ?>"
                                                        data-og_type="<?php echo htmlspecialchars($product['og_type'] ?? 'product'); ?>"
                                                        data-og_image_width="<?php echo htmlspecialchars($product['og_image_width'] ?? '1200'); ?>"
                                                        data-og_image_height="<?php echo htmlspecialchars($product['og_image_height'] ?? '630'); ?>"
                                                        data-og_image_alt="<?php echo htmlspecialchars($product['og_image_alt'] ?? ''); ?>"
                                                        data-og_site_name="<?php echo htmlspecialchars($product['og_site_name'] ?? 'Beliyuk Shop'); ?>"
                                                        data-product_sku="<?php echo htmlspecialchars($product['product_sku'] ?? ''); ?>"
                                                        data-product_brand="<?php echo htmlspecialchars($product['product_brand'] ?? 'Beliyuk Shop'); ?>"
                                                        data-retailer_item_id="<?php echo htmlspecialchars($product['retailer_item_id'] ?? ''); ?>"
                                                        data-availability="<?php echo htmlspecialchars($product['availability'] ?? 'in stock'); ?>"
                                                        data-condition="<?php echo htmlspecialchars($product['condition'] ?? 'new'); ?>"
                                                        data-price_amount="<?php echo $product['price_amount'] ?? $product['price']; ?>"
                                                        data-price_currency="<?php echo htmlspecialchars($product['price_currency'] ?? 'IDR'); ?>"
                                                        data-sale_price_amount="<?php echo $product['sale_price_amount'] ?? $product['price']; ?>"
                                                        data-original_price_amount="<?php echo $product['original_price_amount'] ?? $product['price']; ?>"
                                                        data-item_group_id="<?php echo htmlspecialchars($product['item_group_id'] ?? ''); ?>"
                                                        data-product_category="<?php echo htmlspecialchars($product['product_category'] ?? ''); ?>"
                                                        data-structured_data_json="<?php echo htmlspecialchars($product['structured_data_json'] ?? ''); ?>"
                                                        data-microdata_html="<?php echo htmlspecialchars($product['microdata_html'] ?? ''); ?>">
                                                    <i class="fas fa-edit"></i> Edit Lengkap
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger btn-delete" 
                                                        data-id="<?php echo $product['id']; ?>"
                                                        data-title="<?php echo htmlspecialchars($product['title']); ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted d-block mt-1">
                                                <?php echo $product['created_formatted']; ?>
                                            </small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <p class="text-center text-muted mt-2">
                    Menampilkan <?php echo count($products); ?> dari <?php echo $total_rows; ?> produk
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================
    MODAL EDIT PRODUK LENGKAP
    ============================================ -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="process_product.php" method="POST" id="editProductForm">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Produk Lengkap</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="product_id" id="edit_product_id">
                        <input type="hidden" name="edit_type" value="full">
                        
                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs mb-4" id="editProductTabs">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#editBasicInfo" type="button">
                                    <i class="fas fa-info-circle me-1"></i>Info Dasar
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#editFolderInfo" type="button">
                                    <i class="fas fa-folder me-1"></i>Folder & URL
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#editOpenGraph" type="button">
                                    <i class="fas fa-share-alt me-1"></i>Open Graph
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#editMetaCatalog" type="button">
                                    <i class="fas fa-shopping-cart me-1"></i>Meta Catalog
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#editStructuredData" type="button">
                                    <i class="fas fa-code me-1"></i>Structured Data
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <!-- Tab 1: Basic Info -->
                            <div class="tab-pane fade show active" id="editBasicInfo">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Judul Produk <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="title" id="edit_title" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" class="form-control" name="price" id="edit_price" min="0" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Stok</label>
                                            <input type="number" class="form-control" name="stock" id="edit_stock" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">URL Gambar Produk</label>
                                            <input type="url" class="form-control" name="image_url" id="edit_image_url">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Kategori</label>
                                            <select class="form-select" name="category" id="edit_category">
                                                <option value="">Pilih Kategori</option>
                                                <option value="ebook">E-Book</option>
                                                <option value="software">Software</option>
                                                <option value="course">Course</option>
                                                <option value="template">Template</option>
                                                <option value="digital-product">Digital Product</option>
                                                <option value="lainnya">Lainnya</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="status" id="edit_status">
                                                <option value="active">Aktif</option>
                                                <option value="inactive">Nonaktif</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi Produk <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="description" id="edit_description" rows="5" required></textarea>
                                </div>
                            </div>

                            <!-- Tab 2: Folder & URL -->
                            <div class="tab-pane fade" id="editFolderInfo">
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Informasi Folder:</strong> Ubah nama folder hanya jika diperlukan. 
                                    URL produk akan otomatis menyesuaikan.
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Folder Saat Ini</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="current_folder_display" readonly>
                                                <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard('current_folder_display')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nama Folder Baru</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="folder_name" id="edit_folder_name" 
                                                       placeholder="Contoh: nama-produk-123456">
                                                <button type="button" class="btn btn-outline-primary" onclick="generateFolderName()">
                                                    <i class="fas fa-magic"></i> Generate
                                                </button>
                                            </div>
                                            <small class="text-muted">
                                                Format: huruf kecil, tanda hubung, tidak ada spasi. 
                                                Contoh: <code>nama-produk-123456</code>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">URL Produk Saat Ini</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="current_url_display" readonly>
                                                <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard('current_url_display')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">URL Produk Baru</label>
                                            <div class="input-group">
                                                <span class="input-group-text">https://beliyuk.shop/products/</span>
                                                <input type="text" class="form-control" id="new_url_display" readonly>
                                                <span class="input-group-text">/</span>
                                            </div>
                                            <small class="text-muted">URL akan otomatis diupdate berdasarkan nama folder</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Peringatan:</strong> Mengubah nama folder akan mengubah URL produk. 
                                    Pastikan untuk update semua link yang terhubung.
                                </div>
                            </div>

                            <!-- Tab 3: Open Graph -->
                            <div class="tab-pane fade" id="editOpenGraph">
                                <h5 class="mb-3">Open Graph Basic</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">OG Title</label>
                                            <input type="text" class="form-control" name="meta_title" id="edit_meta_title">
                                            <small class="text-muted">Judul untuk social media</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">OG Description</label>
                                            <textarea class="form-control" name="meta_description" id="edit_meta_description" rows="3"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">OG Image URL</label>
                                            <input type="url" class="form-control" name="meta_image_url" id="edit_meta_image_url">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">OG Type</label>
                                            <select class="form-select" name="og_type" id="edit_og_type">
                                                <option value="product">Product</option>
                                                <option value="website">Website</option>
                                                <option value="article">Article</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">OG Image Width</label>
                                            <input type="number" class="form-control" name="og_image_width" id="edit_og_image_width" value="1200">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">OG Image Height</label>
                                            <input type="number" class="form-control" name="og_image_height" id="edit_og_image_height" value="630">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">OG Image Alt</label>
                                            <input type="text" class="form-control" name="og_image_alt" id="edit_og_image_alt">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">OG Site Name</label>
                                            <input type="text" class="form-control" name="og_site_name" id="edit_og_site_name" value="Beliyuk Shop">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 4: Meta Catalog -->
                            <div class="tab-pane fade" id="editMetaCatalog">
                                <h5 class="mb-3">Product OG (META CATALOG)</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">SKU Produk</label>
                                            <input type="text" class="form-control" name="product_sku" id="edit_product_sku">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Brand/Merek</label>
                                            <input type="text" class="form-control" name="product_brand" id="edit_product_brand" value="Beliyuk Shop">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Retailer Item ID</label>
                                            <input type="text" class="form-control" name="retailer_item_id" id="edit_retailer_item_id">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Availability</label>
                                            <select class="form-select" name="availability" id="edit_availability">
                                                <option value="in stock">In Stock</option>
                                                <option value="out of stock">Out of Stock</option>
                                                <option value="preorder">Preorder</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Condition</label>
                                            <select class="form-select" name="condition" id="edit_condition">
                                                <option value="new">New</option>
                                                <option value="used">Used</option>
                                                <option value="refurbished">Refurbished</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Item Group ID</label>
                                            <input type="text" class="form-control" name="item_group_id" id="edit_item_group_id">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Price Currency</label>
                                            <select class="form-select" name="price_currency" id="edit_price_currency">
                                                <option value="IDR">IDR (Rupiah)</option>
                                                <option value="USD">USD</option>
                                                <option value="EUR">EUR</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Sale Price Amount</label>
                                            <input type="number" class="form-control" name="sale_price_amount" id="edit_sale_price_amount">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Original Price Amount</label>
                                            <input type="number" class="form-control" name="original_price_amount" id="edit_original_price_amount">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Product Category</label>
                                    <input type="text" class="form-control" name="product_category" id="edit_product_category" 
                                           value="Media > Books > E-books" placeholder="Format: Parent > Child">
                                </div>
                            </div>

                            <!-- Tab 5: Structured Data -->
                            <div class="tab-pane fade" id="editStructuredData">
                                <h5 class="mb-3">Enhanced Structured Data untuk Product Catalog & SEO</h5>
                                
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>JSON-LD Structured Data:</strong> Data ini digunakan oleh Google untuk rich results. 
                                    Edit dengan hati-hati.
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Structured Data JSON</label>
                                    <textarea class="form-control code-editor" name="structured_data_json" id="edit_structured_data_json" rows="15" 
                                              placeholder='{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "Nama Produk",
  ...
}'></textarea>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="formatJSON('edit_structured_data_json')">
                                            <i class="fas fa-code me-1"></i> Format JSON
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="generateStructuredData()">
                                            <i class="fas fa-magic me-1"></i> Generate Otomatis
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="validateJSON('edit_structured_data_json')">
                                            <i class="fas fa-check me-1"></i> Validasi JSON
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Microdata HTML (Alternatif)</label>
                                    <textarea class="form-control code-editor" name="microdata_html" id="edit_microdata_html" rows="8" 
                                              placeholder='<div itemscope itemtype="http://schema.org/Product">
  <meta itemprop="name" content="Nama Produk">
  ...
</div>'></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary" name="edit_product">
                            <i class="fas fa-save me-1"></i> Update Semua Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Delete -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Hapus Produk</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Hapus produk <strong id="delete_product_title"></strong>?</p>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Folder produk juga akan dihapus permanen!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="#" class="btn btn-danger" id="confirm_delete">Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ============================================
        // EDIT PRODUCT FUNCTIONALITY
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.btn-edit');
            
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Set basic product info
                    document.getElementById('edit_product_id').value = this.dataset.id;
                    document.getElementById('edit_title').value = this.dataset.title;
                    document.getElementById('edit_description').value = this.dataset.description;
                    document.getElementById('edit_price').value = this.dataset.price;
                    document.getElementById('edit_stock').value = this.dataset.stock;
                    document.getElementById('edit_status').value = this.dataset.status;
                    document.getElementById('edit_category').value = this.dataset.category;
                    document.getElementById('edit_image_url').value = this.dataset.image_url || 
                        'https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp';
                    
                    // Set folder info
                    const folderName = this.dataset.folder_name || '';
                    document.getElementById('current_folder_display').value = folderName;
                    document.getElementById('edit_folder_name').value = folderName;
                    
                    const productUrl = folderName ? `https://beliyuk.shop/products/${folderName}/` : '';
                    document.getElementById('current_url_display').value = productUrl;
                    document.getElementById('new_url_display').value = folderName;
                    
                    // Set Open Graph data
                    document.getElementById('edit_meta_title').value = this.dataset.meta_title || '';
                    document.getElementById('edit_meta_description').value = this.dataset.meta_description || '';
                    document.getElementById('edit_meta_image_url').value = this.dataset.meta_image_url || 
                        'https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp';
                    document.getElementById('edit_og_type').value = this.dataset.og_type || 'product';
                    document.getElementById('edit_og_image_width').value = this.dataset.og_image_width || '1200';
                    document.getElementById('edit_og_image_height').value = this.dataset.og_image_height || '630';
                    document.getElementById('edit_og_image_alt').value = this.dataset.og_image_alt || '';
                    document.getElementById('edit_og_site_name').value = this.dataset.og_site_name || 'Beliyuk Shop';
                    
                    // Set Meta Catalog data
                    document.getElementById('edit_product_sku').value = this.dataset.product_sku || '';
                    document.getElementById('edit_product_brand').value = this.dataset.product_brand || 'Beliyuk Shop';
                    document.getElementById('edit_retailer_item_id').value = this.dataset.retailer_item_id || '';
                    document.getElementById('edit_availability').value = this.dataset.availability || 'in stock';
                    document.getElementById('edit_condition').value = this.dataset.condition || 'new';
                    document.getElementById('edit_price_currency').value = this.dataset.price_currency || 'IDR';
                    document.getElementById('edit_sale_price_amount').value = this.dataset.sale_price_amount || this.dataset.price;
                    document.getElementById('edit_original_price_amount').value = this.dataset.original_price_amount || this.dataset.price;
                    document.getElementById('edit_item_group_id').value = this.dataset.item_group_id || '';
                    document.getElementById('edit_product_category').value = this.dataset.product_category || 'Media > Books > E-books';
                    
                    // Set Structured Data
                    document.getElementById('edit_structured_data_json').value = this.dataset.structured_data_json || 
                        generateDefaultStructuredData(this.dataset);
                    document.getElementById('edit_microdata_html').value = this.dataset.microdata_html || 
                        generateDefaultMicrodata(this.dataset);
                    
                    // Show modal
                    const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
                    editModal.show();
                });
            });
            
            // ============================================
            // DELETE PRODUCT
            // ============================================
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('delete_product_title').textContent = this.dataset.title;
                    document.getElementById('confirm_delete').href = `process_product.php?delete=${this.dataset.id}`;
                    
                    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                    deleteModal.show();
                });
            });
            
            // ============================================
            // FOLDER NAME CHANGE LISTENER
            // ============================================
            const folderNameInput = document.getElementById('edit_folder_name');
            if (folderNameInput) {
                folderNameInput.addEventListener('input', function() {
                    const newUrlDisplay = document.getElementById('new_url_display');
                    newUrlDisplay.value = this.value;
                });
            }
            
            // ============================================
            // AUTO-HIDE ALERTS
            // ============================================
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    bootstrap.Alert.getOrCreateInstance(alert).close();
                });
            }, 5000);
        });
        
        // ============================================
        // HELPER FUNCTIONS
        // ============================================
        
        // Generate folder name from product title
        function generateFolderName() {
            const title = document.getElementById('edit_title').value;
            if (!title) {
                alert('Masukkan judul produk terlebih dahulu');
                return;
            }
            
            // Clean the title for folder name
            let folderName = title.toLowerCase()
                .replace(/[^a-z0-9\s]/g, '') // Remove special chars
                .replace(/\s+/g, '-')        // Replace spaces with dashes
                .replace(/-+/g, '-')         // Remove multiple dashes
                .trim()                      // Trim spaces
                .substring(0, 50);           // Limit length
            
            // Add timestamp for uniqueness
            const timestamp = Date.now().toString().slice(-6);
            folderName = `${folderName}-${timestamp}`;
            
            document.getElementById('edit_folder_name').value = folderName;
            document.getElementById('new_url_display').value = folderName;
        }
        
        // Copy to clipboard
        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            element.select();
            element.setSelectionRange(0, 99999); // For mobile devices
            navigator.clipboard.writeText(element.value)
                .then(() => {
                    const btn = event.target.closest('button');
                    const originalHTML = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                    setTimeout(() => {
                        btn.innerHTML = originalHTML;
                    }, 2000);
                })
                .catch(err => {
                    console.error('Copy failed:', err);
                });
        }
        
        // Format JSON
        function formatJSON(textareaId) {
            const textarea = document.getElementById(textareaId);
            try {
                const obj = JSON.parse(textarea.value);
                textarea.value = JSON.stringify(obj, null, 2);
                showAlert('JSON formatted successfully!', 'success');
            } catch (e) {
                showAlert('Invalid JSON: ' + e.message, 'danger');
            }
        }
        
        // Validate JSON
        function validateJSON(textareaId) {
            const textarea = document.getElementById(textareaId);
            try {
                JSON.parse(textarea.value);
                showAlert('JSON is valid!', 'success');
            } catch (e) {
                showAlert('Invalid JSON: ' + e.message, 'danger');
            }
        }
        
        // Generate default structured data
        function generateDefaultStructuredData(productData) {
            return JSON.stringify({
                "@context": "https://schema.org/",
                "@type": "Product",
                "name": productData.title,
                "description": productData.description,
                "sku": productData.product_sku || `SKU-${Date.now()}`,
                "brand": {
                    "@type": "Brand",
                    "name": productData.product_brand || "Beliyuk Shop"
                },
                "image": [
                    productData.meta_image_url || "https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp"
                ],
                "offers": {
                    "@type": "Offer",
                    "priceCurrency": productData.price_currency || "IDR",
                    "price": productData.price_amount || productData.price,
                    "availability": "https://schema.org/InStock",
                    "itemCondition": "https://schema.org/NewCondition"
                },
                "aggregateRating": {
                    "@type": "AggregateRating",
                    "ratingValue": "4.9",
                    "ratingCount": "234"
                }
            }, null, 2);
        }
        
        // Generate default microdata
        function generateDefaultMicrodata(productData) {
            return `<div itemscope itemtype="http://schema.org/Product" style="display:none;">
  <meta itemprop="url" content="https://beliyuk.shop/products/${productData.folder_name || ''}/">
  <meta itemprop="name" content="${productData.title}">
  <meta itemprop="description" content="${productData.description}">
  <meta itemprop="sku" content="${productData.product_sku || ''}">
  <meta itemprop="mpn" content="${productData.product_sku || ''}">
  <link itemprop="image" href="${productData.meta_image_url || 'https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp'}">
  <div itemprop="brand" itemscope itemtype="http://schema.org/Brand">
    <meta itemprop="name" content="${productData.product_brand || 'Beliyuk Shop'}">
  </div>
  <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
    <meta itemprop="priceCurrency" content="${productData.price_currency || 'IDR'}">
    <meta itemprop="price" content="${productData.price_amount || productData.price}">
    <meta itemprop="availability" content="https://schema.org/InStock">
    <meta itemprop="itemCondition" content="https://schema.org/NewCondition">
  </div>
</div>`;
        }
        
        // Generate structured data automatically
        function generateStructuredData() {
            const productData = {
                title: document.getElementById('edit_title').value,
                description: document.getElementById('edit_description').value,
                price: document.getElementById('edit_price').value,
                product_sku: document.getElementById('edit_product_sku').value,
                product_brand: document.getElementById('edit_product_brand').value,
                price_currency: document.getElementById('edit_price_currency').value,
                price_amount: document.getElementById('edit_sale_price_amount').value || document.getElementById('edit_price').value,
                meta_image_url: document.getElementById('edit_meta_image_url').value,
                folder_name: document.getElementById('edit_folder_name').value
            };
            
            document.getElementById('edit_structured_data_json').value = generateDefaultStructuredData(productData);
            document.getElementById('edit_microdata_html').value = generateDefaultMicrodata(productData);
            
            showAlert('Structured data generated successfully!', 'success');
        }
        
        // Show alert
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.querySelector('.modal-body').insertBefore(alertDiv, document.querySelector('.modal-body').firstChild);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }
        
        // Form validation
        document.getElementById('editProductForm')?.addEventListener('submit', function(e) {
            const title = this.querySelector('#edit_title');
            const price = this.querySelector('#edit_price');
            
            if (!title.value || !price.value) {
                e.preventDefault();
                alert('Judul dan harga harus diisi!');
                return false;
            }
            
            // Show loading
            const submitBtn = this.querySelector('[name="edit_product"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="loading"></span> Menyimpan...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });
    </script>
</body>
</html>