<?php
require_once 'config.php';

$product_id = $_GET['id'] ?? 0;

// AMBIL DATA PRODUK
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    die("❌ Produk tidak ditemukan");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: <?php echo htmlspecialchars($product['title']); ?> | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #f8f9fa; }
        .preview-card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .meta-box { background: #f8f9fa; border-radius: 10px; padding: 20px; margin: 15px 0; }
        .code-box { background: #2d2d2d; color: #fff; padding: 15px; border-radius: 8px; overflow-x: auto; font-family: monospace; }
        .product-image { max-width: 100%; border-radius: 10px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-eye"></i> Preview Produk</h1>
                <p class="text-muted">ID: <?php echo $product['id']; ?> | Dibuat: <?php echo $product['created_at']; ?></p>
            </div>
            <div class="col text-end">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                </a>
                <a href="/products/<?php echo $product['folder_name']; ?>/" target="_blank" class="btn btn-primary">
                    <i class="fas fa-external-link-alt"></i> Buka Halaman Produk
                </a>
            </div>
        </div>
        
        <div class="preview-card">
            <!-- BASIC INFO -->
            <div class="row">
                <div class="col-md-4">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($product['title']); ?>" 
                         class="product-image">
                </div>
                <div class="col-md-8">
                    <h2><?php echo htmlspecialchars($product['title']); ?></h2>
                    <p class="text-muted"><?php echo htmlspecialchars($product['description']); ?></p>
                    <div class="row mt-4">
                        <div class="col">
                            <strong>Harga:</strong> Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                        </div>
                        <div class="col">
                            <strong>Stok:</strong> <span class="badge bg-success"><?php echo $product['stock']; ?></span>
                        </div>
                        <div class="col">
                            <strong>Status:</strong> 
                            <span class="badge bg-<?php echo $product['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                <?php echo $product['status']; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- FOLDER INFO -->
            <div class="meta-box mt-4">
                <h4><i class="fas fa-folder"></i> Info Folder</h4>
                <p><strong>Nama Folder:</strong> <?php echo $product['folder_name']; ?></p>
                <p><strong>URL Produk:</strong> 
                    <a href="<?php echo $product['product_url']; ?>" target="_blank">
                        <?php echo $product['product_url']; ?>
                    </a>
                </p>
            </div>
            
            <!-- SEO META -->
            <div class="meta-box">
                <h4><i class="fas fa-chart-line"></i> SEO Meta Tags</h4>
                <div class="code-box">
                    &lt;title&gt;<?php echo htmlspecialchars($product['meta_title']); ?>&lt;/title&gt;<br>
                    &lt;meta name="description" content="<?php echo htmlspecialchars($product['meta_description']); ?>"&gt;<br>
                    &lt;meta property="og:title" content="<?php echo htmlspecialchars($product['meta_title']); ?>"&gt;<br>
                    &lt;meta property="og:description" content="<?php echo htmlspecialchars($product['meta_description']); ?>"&gt;<br>
                    &lt;meta property="og:image" content="<?php echo htmlspecialchars($product['meta_image_url']); ?>"&gt;<br>
                    &lt;meta property="og:url" content="<?php echo $product['product_url']; ?>"&gt;<br>
                    &lt;meta property="og:type" content="<?php echo $product['og_type']; ?>"&gt;
                </div>
            </div>
            
            <!-- STRUCTURED DATA -->
            <div class="meta-box">
                <h4><i class="fas fa-code"></i> Structured Data (JSON-LD)</h4>
                <div class="code-box">
                    <?php echo htmlspecialchars($product['structured_data_json']); ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>