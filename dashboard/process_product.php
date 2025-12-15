<?php
// ====================================================
// process_product.php - cPanel FIXED VERSION
// ====================================================

// TURN OFF ERROR DISPLAY FOR PRODUCTION
// error_reporting(0);

// LOG EVERYTHING FOR DEBUGGING
$log_file = dirname(__FILE__) . '/process_log.txt';
file_put_contents($log_file, 
    "[" . date('Y-m-d H:i:s') . "] " . 
    "IP: " . $_SERVER['REMOTE_ADDR'] . " | " .
    "Method: " . $_SERVER['REQUEST_METHOD'] . " | " .
    "URI: " . $_SERVER['REQUEST_URI'] . "\n",
    FILE_APPEND
);

// CHECK IF IT'S A POST REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // LOG POST DATA
    file_put_contents($log_file, 
        "POST Data: " . print_r($_POST, true) . "\n",
        FILE_APPEND
    );
    
    // INCLUDE CONFIG
    $config_file = dirname(__FILE__) . '/config.php';
    if (file_exists($config_file)) {
        require_once $config_file;
    } else {
        die("Config file not found!");
    }
    
    // ============================================
    // ADD PRODUCT
    // ============================================
    if (isset($_POST['add_product'])) {
        try {
            // SANITIZE INPUT
            $title = htmlspecialchars(trim($_POST['title'] ?? ''));
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
            
            // VALIDATION
            if (empty($title) || strlen($title) < 3) {
                throw new Exception('Judul produk minimal 3 karakter');
            }
            
            if (empty($description) || strlen($description) < 10) {
                throw new Exception('Deskripsi produk minimal 10 karakter');
            }
            
            if ($price <= 0) {
                throw new Exception('Harga harus lebih dari 0');
            }
            
            // CREATE SAFE FOLDER NAME
            function createFolderName($name) {
                $name = strtolower($name);
                $name = preg_replace('/[^a-z0-9\s-]/', '', $name); // Remove special chars
                $name = preg_replace('/\s+/', '-', $name); // Spaces to dashes
                $name = preg_replace('/-+/', '-', $name); // Remove multiple dashes
                $name = trim($name, '-');
                $name = substr($name, 0, 50); // Limit length
                $name = $name . '-' . time(); // Add timestamp
                return $name;
            }
            
            $folder_name = createFolderName($title);
            $product_url = "https://beliyuk.shop/products/" . $folder_name . "/";
            
            // PREPARE SQL
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
            
            // CREATE FOLDER
            $base_path = dirname(dirname(__FILE__)) . '/products/';
            
            // Ensure products directory exists
            if (!file_exists($base_path)) {
                mkdir($base_path, 0755, true);
            }
            
            $folder_path = $base_path . $folder_name . '/';
            
            if (!file_exists($folder_path)) {
                if (mkdir($folder_path, 0755, true)) {
                    // Create index.php in folder
                    $index_content = "<?php
// Product: {$product_id}
header('Location: /dashboard/index.php?view={$product_id}');
exit;
?>";
                    
                    file_put_contents($folder_path . 'index.php', $index_content);
                    
                    // Create .htaccess
                    $htaccess = "Options -Indexes\nErrorDocument 404 /404.php";
                    file_put_contents($folder_path . '.htaccess', $htaccess);
                }
            }
            
            // SUCCESS - REDIRECT WITH MESSAGE
            $success_msg = urlencode("✅ Produk berhasil ditambahkan! Folder: {$folder_name}");
            header("Location: index.php?success={$success_msg}");
            exit;
            
        } catch (Exception $e) {
            // ERROR - REDIRECT WITH MESSAGE
            $error_msg = urlencode("❌ Error: " . $e->getMessage());
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
            
            // SANITIZE INPUT
            $title = htmlspecialchars(trim($_POST['title'] ?? ''));
            $description = htmlspecialchars(trim($_POST['description'] ?? ''));
            $price = (float)($_POST['price'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);
            $status = $_POST['status'] ?? 'active';
            $category = $_POST['category'] ?? 'ebook';
            $image_url = filter_var($_POST['image_url'] ?? '', FILTER_SANITIZE_URL);
            
            // VALIDATION
            if (empty($title) || empty($description) || $price <= 0) {
                throw new Exception('Data tidak valid');
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
// DELETE PRODUCT (GET REQUEST)
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
                // Delete all files
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