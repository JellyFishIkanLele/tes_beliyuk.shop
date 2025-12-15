<?php
// ====================================================
// process_product.php - FIXED VERSION (NO ERRORS)
// ====================================================

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Setup logging
$log_file = __DIR__ . '/process_log.txt';

// Ensure log file exists
if (!file_exists($log_file)) {
    file_put_contents($log_file, "=== Process Log Started " . date('Y-m-d H:i:s') . " ===\n");
}

// Log basic request info
$log_entry = "[" . date('Y-m-d H:i:s') . "] " . 
    "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . " | " .
    "Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'unknown') . " | " .
    "URI: " . ($_SERVER['REQUEST_URI'] ?? 'unknown') . "\n";

file_put_contents($log_file, $log_entry, FILE_APPEND);

// CHECK IF IT'S A POST REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Log POST data
    file_put_contents($log_file, 
        "POST Data received:\n" . print_r($_POST, true) . "\n---\n",
        FILE_APPEND
    );
    
    // INCLUDE CONFIG
    $config_file = __DIR__ . '/config.php';
    if (file_exists($config_file)) {
        require_once $config_file;
    } else {
        $error = "Config file not found!";
        file_put_contents($log_file, "ERROR: $error\n", FILE_APPEND);
        die(json_encode(['error' => $error]));
    }
    
    // ============================================
    // ADD PRODUCT
    // ============================================
    if (isset($_POST['add_product'])) {
        file_put_contents($log_file, "Processing ADD_PRODUCT request\n", FILE_APPEND);
        
        try {
            // VALIDASI TITLE: Hanya huruf, angka, dash, underscore - TANPA SPASI
            $raw_title = trim($_POST['title'] ?? '');
            
            if (!preg_match('/^[a-zA-Z0-9\-_]+$/', $raw_title)) {
                throw new Exception('Title hanya boleh huruf, angka, dash (-), underscore (_)! TIDAK BOLEH SPASI!');
            }
            
            if (preg_match('/^[\-_]+$/', $raw_title)) {
                throw new Exception('Title tidak boleh hanya dash/underscore!');
            }
            
            if (strlen($raw_title) < 3) {
                throw new Exception('Judul minimal 3 karakter!');
            }
            
            $title = htmlspecialchars($raw_title);
            $description = htmlspecialchars(trim($_POST['description'] ?? ''));
            $price = (float)($_POST['price'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);
            $status = $_POST['status'] ?? 'active';
            $category = $_POST['category'] ?? 'ebook';
            
            // IMAGE URL
            $image_url = filter_var($_POST['image_url'] ?? '', FILTER_SANITIZE_URL);
            if (empty($image_url)) {
                $image_url = 'https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp';
            }
            
            // VALIDASI
            if (empty($description) || strlen($description) < 10) {
                throw new Exception('Deskripsi minimal 10 karakter');
            }
            
            if ($price <= 0) {
                throw new Exception('Harga harus lebih dari 0');
            }
            
            // CREATE FOLDER NAME - TANPA SPASI & TANPA AUTO-NUMBER
            function createFolderName($name) {
                $name = strtolower($name);
                $name = preg_replace('/[^a-z0-9\-_]/', '', $name);
                $name = preg_replace('/-+/', '-', $name);
                $name = preg_replace('/_+/', '_', $name);
                $name = trim($name, '-_');
                $name = substr($name, 0, 50);
                return $name;
            }
            
            $folder_name = createFolderName($raw_title);
            
            if (empty($folder_name)) {
                throw new Exception('Nama folder tidak valid!');
            }
            
            $product_url = "https://beliyuk.shop/products/" . $folder_name . "/";
            
            // LOG DATA
            file_put_contents($log_file, 
                "Adding product: $title | Folder: $folder_name | Price: $price\n",
                FILE_APPEND
            );
            
            // INSERT TO DATABASE
            $sql = "INSERT INTO products (
                title, description, price, stock, status, category, image_url,
                folder_name, product_url, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $title, $description, $price, $stock, $status, $category, $image_url,
                $folder_name, $product_url
            ]);
            
            $product_id = $pdo->lastInsertId();
            
            // ============================================
            // CREATE PRODUCT FOLDER & INDEX.PHP
            // ============================================
            $base_path = dirname(dirname(__FILE__)) . '/products/';
            
            // Ensure products directory exists
            if (!file_exists($base_path)) {
                if (!mkdir($base_path, 0755, true)) {
                    throw new Exception('Gagal membuat folder products!');
                }
            }
            
            $folder_path = $base_path . $folder_name . '/';
            
            // Create product folder
            if (!file_exists($folder_path)) {
                if (!mkdir($folder_path, 0755, true)) {
                    throw new Exception('Gagal membuat folder produk!');
                }
                
                // ============================================
                // CREATE index.php FILE (SIMPLE & ERROR-FREE)
                // ============================================
                $index_content = '<?php
// PRODUCT PAGE - AUTO GENERATED
error_reporting(E_ALL);
ini_set(\'display_errors\', 1);

// Get folder name
$folder_name = basename(dirname(__FILE__));

// Try to connect to database
$config_path = __DIR__ . \'/../../dashboard/config.php\';
$product = null;

if (file_exists($config_path)) {
    try {
        require_once $config_path;
        $stmt = $pdo->prepare("SELECT * FROM products WHERE folder_name = ? AND status = \'active\' LIMIT 1");
        $stmt->execute([$folder_name]);
        $product = $stmt->fetch();
    } catch (Exception $e) {
        // Database error - continue without product data
    }
}

if (!$product) {
    // Product not found or database error
    $title = "Product: " . $folder_name;
    $description = "Digital product from Beliyuk Shop";
    $price = 0;
    $stock = 0;
    $image_url = "https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp";
    $category = "Digital Product";
} else {
    // Product found
    $title = htmlspecialchars($product[\'title\'] ?? $folder_name);
    $description = htmlspecialchars($product[\'description\'] ?? \'Digital product from Beliyuk Shop\');
    $price = $product[\'price\'] ?? 0;
    $stock = $product[\'stock\'] ?? 0;
    $image_url = htmlspecialchars($product[\'image_url\'] ?? \'https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp\');
    $category = htmlspecialchars($product[\'category\'] ?? \'Digital Product\');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> | Beliyuk Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; font-family: "Segoe UI", sans-serif; }
        .product-card { max-width: 1000px; margin: 30px auto; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .product-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 15px 15px 0 0; }
        .product-image { width: 100%; max-height: 400px; object-fit: contain; border-radius: 10px; }
        .price-tag { font-size: 2rem; color: #667eea; font-weight: bold; }
        .btn-buy { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container mt-3">
        <a href="/" class="text-decoration-none text-muted">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Beranda
        </a>
    </div>
    
    <div class="product-card">
        <div class="product-header text-center">
            <h1><?php echo $title; ?></h1>
            <p class="mb-0"><?php echo $category; ?></p>
        </div>
        
        <div class="p-4">
            <div class="row">
                <div class="col-md-6">
                    <img src="<?php echo $image_url; ?>" class="product-image mb-3" alt="<?php echo $title; ?>">
                    
                    <div class="mb-3">
                        <?php if ($stock > 0): ?>
                            <span class="badge bg-success p-2">
                                <i class="fas fa-check-circle me-1"></i> Stok: <?php echo $stock; ?>
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger p-2">
                                <i class="fas fa-times-circle me-1"></i> Stok Habis
                            </span>
                        <?php endif; ?>
                        <span class="badge bg-info p-2 ms-2"><?php echo $category; ?></span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="price-tag mb-3">
                        Rp <?php echo number_format($price, 0, ",", "."); ?>
                    </div>
                    
                    <div class="mb-4">
                        <h4>Deskripsi Produk</h4>
                        <p class="text-muted"><?php echo nl2br($description); ?></p>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-buy btn-lg" onclick="buyProduct()">
                            <i class="fas fa-shopping-cart me-2"></i> Beli Sekarang
                        </button>
                        <button class="btn btn-outline-primary btn-lg" onclick="chatWhatsApp()">
                            <i class="fab fa-whatsapp me-2"></i> Chat via WhatsApp
                        </button>
                    </div>
                    
                    <div class="mt-4 p-3 bg-light rounded">
                        <h5><i class="fas fa-info-circle me-2"></i> Informasi</h5>
                        <div class="row small">
                            <div class="col-6">
                                <p class="mb-1"><strong>Folder:</strong> <?php echo $folder_name; ?></p>
                                <p class="mb-1"><strong>Tipe:</strong> Digital Product</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1"><strong>Status:</strong> <?php echo ($stock > 0 ? "Tersedia" : "Habis"); ?></p>
                                <p class="mb-1"><strong>Garansi:</strong> 100% Refund</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container text-center mt-4 mb-5">
        <p class="text-muted">
            &copy; <?php echo date("Y"); ?> Beliyuk Shop
        </p>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function chatWhatsApp() {
            const productName = encodeURIComponent("<?php echo $title; ?>");
            const productPrice = encodeURIComponent("Rp <?php echo number_format($price, 0, ",", "."); ?>");
            const message = `Halo, saya tertarik dengan produk:\n\n*${productName}*\nHarga: ${productPrice}\n\nBisa info lebih detail?`;
            window.open(`https://wa.me/?text=${message}`, "_blank");
        }
        
        function buyProduct() {
            alert("Untuk pemesanan, silakan hubungi kami via WhatsApp.");
        }
    </script>
</body>
</html>';
                
                // Save index.php file
                if (!file_put_contents($folder_path . 'index.php', $index_content)) {
                    throw new Exception('Gagal membuat file index.php!');
                }
                
                // Create .htaccess
                $htaccess = "Options -Indexes\nErrorDocument 404 /404.php";
                if (!file_put_contents($folder_path . '.htaccess', $htaccess)) {
                    throw new Exception('Gagal membuat .htaccess!');
                }
                
                file_put_contents($log_file, "Folder created: $folder_path\n", FILE_APPEND);
            }
            
            // SUCCESS
            $success_msg = urlencode("✅ Produk berhasil ditambahkan! Folder: {$folder_name}");
            header("Location: index.php?success={$success_msg}");
            exit;
            
        } catch (Exception $e) {
            // ERROR
            $error_msg = urlencode("❌ Error: " . $e->getMessage());
            file_put_contents($log_file, "ADD ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            header("Location: index.php?error={$error_msg}");
            exit;
        }
    }
    
    // ============================================
    // EDIT PRODUCT
    // ============================================
    elseif (isset($_POST['edit_product'])) {
        try {
            $product_id = (int)$_POST['product_id'];
            
            // VALIDASI TITLE
            $raw_title = trim($_POST['title'] ?? '');
            
            if (!preg_match('/^[a-zA-Z0-9\-_]+$/', $raw_title)) {
                throw new Exception('Title hanya boleh huruf, angka, dash (-), underscore (_)!');
            }
            
            if (preg_match('/^[\-_]+$/', $raw_title)) {
                throw new Exception('Title tidak boleh hanya dash/underscore!');
            }
            
            if (strlen($raw_title) < 3) {
                throw new Exception('Judul minimal 3 karakter!');
            }
            
            $title = htmlspecialchars($raw_title);
            $description = htmlspecialchars(trim($_POST['description'] ?? ''));
            $price = (float)($_POST['price'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);
            $status = $_POST['status'] ?? 'active';
            $category = $_POST['category'] ?? 'ebook';
            $image_url = filter_var($_POST['image_url'] ?? '', FILTER_SANITIZE_URL);
            
            // VALIDASI
            if (empty($description) || $price <= 0) {
                throw new Exception('Data tidak valid!');
            }
            
            // UPDATE DATABASE
            $sql = "UPDATE products SET
                title = ?, description = ?, price = ?, stock = ?,
                status = ?, category = ?, image_url = ?, updated_at = NOW()
                WHERE id = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $price, $stock, $status, $category, $image_url, $product_id]);
            
            // SUCCESS
            $success_msg = urlencode("✅ Produk berhasil diperbarui!");
            header("Location: index.php?success={$success_msg}");
            exit;
            
        } catch (Exception $e) {
            $error_msg = urlencode("❌ Error: " . $e->getMessage());
            header("Location: index.php?error={$error_msg}");
            exit;
        }
    }
}

// ============================================
// DELETE PRODUCT
// ============================================
elseif (isset($_GET['delete'])) {
    try {
        $product_id = (int)$_GET['delete'];
        
        // INCLUDE CONFIG
        require_once 'config.php';
        
        // GET FOLDER NAME
        $stmt = $pdo->prepare("SELECT folder_name FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $folder_name = $stmt->fetchColumn();
        
        // DELETE FOLDER IF EXISTS
        if ($folder_name) {
            $folder_path = dirname(dirname(__FILE__)) . '/products/' . $folder_name . '/';
            
            if (file_exists($folder_path)) {
                // Delete files in folder
                $files = glob($folder_path . '*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        @unlink($file);
                    }
                }
                // Delete folder
                @rmdir($folder_path);
            }
        }
        
        // DELETE FROM DATABASE
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        
        // SUCCESS
        $success_msg = urlencode("✅ Produk berhasil dihapus!");
        header("Location: index.php?success={$success_msg}");
        exit;
        
    } catch (Exception $e) {
        $error_msg = urlencode("❌ Error: " . $e->getMessage());
        header("Location: index.php?error={$error_msg}");
        exit;
    }
}

// ============================================
// DEFAULT REDIRECT
// ============================================
header("Location: index.php");
exit;
?>