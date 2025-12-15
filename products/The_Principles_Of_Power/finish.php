<?php
session_start();

// ==========================
// CONFIG
// ==========================
$merchantCode = "DS26707"; 
$apiKey       = "3fb2f3932d6883c583412ae386b677e6";

// LINK DOWNLOAD UTAMA
$downloadLink = "https://drive.google.com/file/d/1HRqlwaRfyS20c341Gqbdz_4T5OaRnY57/view";

// ==========================
// NAMA PRODUK
// ==========================
$productName = "The Principles Of Power (Dion Yulianto) (indo)";

// ==========================
// GET merchantOrderId
// ==========================
if (!isset($_GET['merchantOrderId'])) {
    if (isset($_SESSION['order_id'])) {
        $merchantOrderId = $_SESSION['order_id'];
    } else {
        die("Invalid Request: merchantOrderId missing.");
    }
} else {
    $merchantOrderId = $_GET['merchantOrderId'];
}

// ==========================
// SIGNATURE CEK STATUS
// ==========================
$signature = md5($merchantCode . $merchantOrderId . $apiKey);

// ==========================
// REQUEST BODY
// ==========================
$data = array(
    "merchantCode"     => $merchantCode,
    "merchantOrderId"  => $merchantOrderId,
    "signature"        => $signature
);

$jsonData = json_encode($data);

// ==========================
// CURL REQUEST KE DUITKU
// ==========================
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://sandbox.duitku.com/webapi/api/merchant/transactionStatus");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "Content-Length: " . strlen($jsonData)
));

$response = curl_exec($ch);
curl_close($ch);

// ==========================
// PARSE RESPONSE
// ==========================
$result = json_decode($response, true);
$status = $result["statusCode"] ?? "??";

// ==========================
// AMOUNT DINAMIS DARI DUITKU
// ==========================
$amountAPI = isset($result["amount"]) ? (int)$result["amount"] : 0;
$amountFormatted = number_format($amountAPI, 0, ',', '.');

// ==========================
// HITUNG QUANTITY DARI AMOUNT
// ==========================
$pricePerItem = 49999; // Harga per paket
$quantity = 1; // Default

if ($amountAPI > 0 && $pricePerItem > 0) {
    $quantity = floor($amountAPI / $pricePerItem);
    if ($quantity < 1) $quantity = 1;
}

// Cek jika ada quantity di session
if (isset($_SESSION['quantity']) && $_SESSION['quantity'] > 0) {
    $quantity = $_SESSION['quantity'];
}

// ==========================
// KIRIM EMAIL JIKA PEMBAYARAN BERHASIL
// ==========================
if ($status == "00") {
    // Fungsi kirim email dengan quantity dan nama produk
    function sendBackupEmail($toEmail, $orderId, $amount, $quantity, $downloadLink, $productName) {
        $formattedAmount = number_format($amount, 0, ',', '.');
        
        $subject = "📦 " . $productName . " ($quantity paket) - Order #" . $orderId;
        
        $message = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Akses ' . $productName . '</title>
            <style>
                body { font-family: Arial, sans-serif; line-height:1.6; margin:0; padding:20px; background:#f5f5f5; }
                .container { max-width:600px; margin:0 auto; background:white; border-radius:10px; padding:25px; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
                .header { text-align:center; padding-bottom:20px; border-bottom:2px solid #facc15; }
                .btn-download { display:inline-block; background:#22c55e; color:white; padding:14px 28px; text-decoration:none; border-radius:8px; font-weight:bold; font-size:16px; margin:15px 0; }
                .details { background:#f8f9fa; padding:15px; border-radius:8px; margin:15px 0; }
                .footer { margin-top:20px; padding-top:15px; border-top:1px solid #ddd; color:#666; font-size:13px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2 style="color:#22c55e; margin:0 0 10px 0;">✅ ' . $productName . ' SIAP!</h2>
                    <p style="color:#666; margin:0;">Order #' . $orderId . '</p>
                </div>
                
                <div style="margin:20px 0;">
                    <p><strong>Nama Produk:</strong> ' . $productName . '</p>
                    <p><strong>Jumlah Paket:</strong> ' . $quantity . ' paket</p>
                    <p><strong>Total:</strong> Rp ' . $formattedAmount . '</p>
                    <p><strong>Tanggal:</strong> ' . date("d/m/Y H:i:s") . '</p>
                </div>
                
                <div style="text-align:center; margin:25px 0;">
                    <a href="' . $downloadLink . '" class="btn-download" target="_blank">
                        📥 AKSES SEKARANG (' . $quantity . ' Paket)
                    </a>
                    <p style="color:#666; font-size:14px; margin-top:10px;">
                        Terimakasih Telah Berbelanja💕
                    </p>
                </div>
                
                <div class="details">
                    <h3 style="margin-top:0;">📋 Petunjuk:</h3>
                    <ol>
                        <li>Klik tombol "AKSES SEKARANG" di atas</li>
                        <li>Silahkan di Baca Ebook anda</li>
                    </ol>
                </div>
                
                <div class="footer">
                    <p>Terima kasih telah berbelanja di <strong>Beliyuk.Shop</strong></p>
                    <p>Butuh bantuan? Email: support@beliyuk.shop</p>
                    <p>© ' . date('Y') . ' Beliyuk Shop. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Beliyuk Shop <noreply@beliyuk.shop>" . "\r\n";
        $headers .= "Reply-To: support@beliyuk.shop" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        return mail($toEmail, $subject, $message, $headers);
    }
    
    // Kirim email backup jika ada email di session
    if (isset($_SESSION['customer_email']) && !empty($_SESSION['customer_email'])) {
        sendBackupEmail(
            $_SESSION['customer_email'], 
            $merchantOrderId, 
            $amountAPI, 
            $quantity,
            $downloadLink,
            $productName
        );
        
        // Hapus session setelah pengiriman
        unset($_SESSION['customer_email']);
        unset($_SESSION['quantity']);
        unset($_SESSION['order_id']);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Status Pembayaran - Beliyuk Shop</title>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/pixel.php'; ?>

<!-- ============================================
     MIKRODATA UNTUK KATALOG PRODUK
     ============================================ -->
<?php if ($status == "00") { ?>
<!-- Open Graph untuk Pembelian Berhasil -->
<meta property="og:title" content="The Principles Of Power (Dion Yulianto) (indo)">
<meta property="og:description" content="The Principles of Power (Dion Yulianto) membahas prinsip-prinsip kekuatan dan pengaruh, serta strategi praktis untuk membangun mentalitas kuat dan meningkatkan posisi Anda dalam kehidupan sosial maupun profesional.">
<meta property="og:image" content="https://imgdst.tomsamcong.com/public/uploads/original/22/8b/5e47f2a7dd61087a8d74ff4833a8.jpeg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="The Principles Of Power (Dion Yulianto) (indo)">
<meta property="og:url" content="https://beliyuk.shop/products/The_Principles_Of_Power/">
<meta property="og:type" content="product">
<meta property="og:site_name" content="Beliyuk Shop">

<!-- Product OG Meta Tags -->
<meta property="product:brand" content="Beliyuk Shop">
<meta property="product:retailer_item_id" content="akkqud45dz">
<meta property="product:availability" content="in stock">
<meta property="product:condition" content="new">
<meta property="product:price:amount" content="<?= $amountAPI ?>">
<meta property="product:price:currency" content="IDR">
<meta property="product:original_price:amount" content="399999">
<meta property="product:item_group_id" content="ebooks">
<meta property="product:category" content="Media > Books > E-books">

<!-- Structured Data JSON-LD untuk Pembelian Berhasil -->
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "@id": "https://beliyuk.shop/products/The_Principles_Of_Power/#product",
  "name": "Paket The Principles Of Power (Dion Yulianto) (indo)",
  "alternateName": "Paket Lengkap 1.999+ E-books Premium",
  "description": "The Principles of Power (Dion Yulianto) membahas prinsip-prinsip kekuatan dan pengaruh, serta strategi praktis untuk membangun mentalitas kuat dan meningkatkan posisi Anda dalam kehidupan sosial maupun profesional.",
  "sku": "akkqud45dz",
  "mpn": "akkqud45dz",
  "gtin": "akkqud45dz",
  "brand": {
    "@type": "Brand",
    "name": "Beliyuk Shop",
    "url": "https://beliyuk.shop/",
    "logo": "https://beliyuk.shop/logo.png"
  },
  "image": [
    "https://imgdst.tomsamcong.com/public/uploads/original/22/8b/5e47f2a7dd61087a8d74ff4833a8.jpeg"
  ],
  "offers": {
    "@type": "Offer",
    "@id": "https://beliyuk.shop/products/The_Principles_Of_Power/#offer",
    "url": "https://beliyuk.shop/products/The_Principles_Of_Power/finish.php?merchantOrderId=<?= $merchantOrderId ?>",
    "priceCurrency": "IDR",
    "price": "<?= $amountAPI ?>",
    "priceValidUntil": "2030-12-31",
    "availability": "https://schema.org/InStock",
    "itemCondition": "https://schema.org/NewCondition",
    "seller": {
      "@type": "Organization",
      "name": "Beliyuk Shop",
      "url": "https://beliyuk.shop/"
    },
    "priceSpecification": {
      "@type": "UnitPriceSpecification",
      "price": "<?= $amountAPI ?>",
      "priceCurrency": "IDR",
      "billingIncrement": 1,
      "eligibleQuantity": {
        "@type": "QuantitativeValue",
        "value": "<?= $quantity ?>"
      }
    }
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.9",
    "ratingCount": "234",
    "bestRating": "5",
    "worstRating": "1"
  },
  "additionalProperty": [
    {
      "@type": "PropertyValue",
      "name": "Format",
      "value": "PDF Digital"
    },
    {
      "@type": "PropertyValue",
      "name": "Bahasa",
      "value": "Indonesia"
    },
    {
      "@type": "PropertyValue",
      "name": "Total Buku",
      "value": "1999+"
    },
    {
      "@type": "PropertyValue",
      "name": "Jumlah Paket",
      "value": "<?= $quantity ?>"
    },
    {
      "@type": "PropertyValue",
      "name": "Total Halaman",
      "value": "500.000+"
    },
    {
      "@type": "PropertyValue",
      "name": "Ukuran File",
      "value": "15 GB"
    }
  ],
  "category": "Media > Books > E-books",
  "releaseDate": "2024-02-01",
  "inLanguage": "Indonesian",
  "countryOfAssembly": "Indonesia",
  "countryOfLastProcessing": "Indonesia",
  "audience": {
    "@type": "PeopleAudience",
    "suggestedMinAge": "15",
    "suggestedMaxAge": "99",
    "audienceType": "Students, Professionals, Entrepreneurs, Book Lovers"
  }
}
</script>

<!-- Order/Invoice Structured Data -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Invoice",
  "provider": {
    "@type": "Organization",
    "name": "Beliyuk Shop",
    "url": "https://beliyuk.shop/"
  },
  "accountId": "<?= $merchantOrderId ?>",
  "customer": {
    "@type": "Person",
    "name": "Pelanggan Beliyuk Shop"
  },
  "paymentStatus": "PaymentComplete",
  "totalPaymentDue": {
    "@type": "PriceSpecification",
    "price": "<?= $amountAPI ?>",
    "priceCurrency": "IDR"
  },
  "confirmationNumber": "<?= $result["reference"] ?? $merchantOrderId ?>",
  "paymentMethod": "OnlinePayment",
  "orderStatus": "OrderDelivered",
  "orderNumber": "<?= $merchantOrderId ?>",
  "acceptedOffer": {
    "@type": "Offer",
    "itemOffered": {
      "@type": "Product",
      "name": "Paket The Principles Of Power (Dion Yulianto) (indo)",
      "sku": "akkqud45dz"
    },
    "price": "<?= $amountAPI ?>",
    "priceCurrency": "IDR",
    "eligibleQuantity": {
      "@type": "QuantitativeValue",
      "value": "<?= $quantity ?>"
    }
  }
}
</script>

<!-- Product Catalog Microdata (alternatif untuk Facebook) -->
<div itemscope itemtype="http://schema.org/Product" style="display:none;">
  <meta itemprop="url" content="https://beliyuk.shop/products/The_Principles_Of_Power/finish.php?merchantOrderId=<?= $merchantOrderId ?>">
  <meta itemprop="name" content="Paket The Principles Of Power (Dion Yulianto) (indo)">
  <meta itemprop="alternateName" content="Paket Lengkap 1.999+ E-Books Premium">
  <meta itemprop="description" content="Akses instan ke 1.999+ e-book premium: bisnis, mindset, self-improvement, bahasa, kesehatan, dan banyak topik lainnya.">
  <meta itemprop="sku" content="akkqud45dz">
  <meta itemprop="mpn" content="akkqud45dz">
  <meta itemprop="gtin" content="akkqud45dz">
  <link itemprop="image" href="https://imgdst.tomsamcong.com/public/uploads/original/22/8b/5e47f2a7dd61087a8d74ff4833a8.jpeg">
  <div itemprop="brand" itemscope itemtype="http://schema.org/Brand">
    <meta itemprop="name" content="Beliyuk Shop">
    <link itemprop="url" href="https://beliyuk.shop/">
  </div>
  <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
    <meta itemprop="priceCurrency" content="IDR">
    <meta itemprop="price" content="<?= $amountAPI ?>">
    <meta itemprop="availability" content="https://schema.org/InStock">
    <meta itemprop="itemCondition" content="https://schema.org/NewCondition">
    <meta itemprop="url" content="https://beliyuk.shop/products/The_Principles_Of_Power/finish.php?merchantOrderId=<?= $merchantOrderId ?>">
    <div itemprop="seller" itemscope itemtype="http://schema.org/Organization">
      <meta itemprop="name" content="Beliyuk Shop">
    </div>
  </div>
  <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
    <meta itemprop="ratingValue" content="4.9">
    <meta itemprop="ratingCount" content="234">
  </div>
</div>
<?php } ?>

<style>
    /* INVISIBLE LOCK SCREEN - Hanya tampil saat pembayaran berhasil */
    <?php if ($status == "00") { ?>
    .invisible-lock {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: transparent;
        z-index: 9999;
    }
    <?php } ?>
    
    /* Disable semua interaksi saat locked */
    body.locked {
        overflow: hidden;
        pointer-events: none;
        user-select: none;
    }
    
    body.locked .container {
        pointer-events: none;
    }
    
    /* Style untuk debugging (opsional) */
    .debug-info {
        position: fixed;
        top: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.8);
        color: #22c55e;
        padding: 10px;
        border-radius: 5px;
        font-size: 12px;
        z-index: 10000;
        display: none; /* Sembunyikan di production */
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #0d0d0d 0%, #1a1a1a 100%);
        margin: 0;
        padding: 20px;
        color: #f5f5f5;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .container {
        max-width: 500px;
        background: #1a1a1a;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        text-align: center;
        position: relative;
        border: 2px solid rgba(250, 204, 21, 0.3);
        transition: all 0.3s ease;
    }

    .icon {
        font-size: 80px;
        margin-bottom: 20px;
        animation: bounce 1s ease infinite alternate;
    }
    @keyframes bounce {
        from { transform: translateY(0); }
        to { transform: translateY(-10px); }
    }
    
    .success { color: #22c55e; }
    .pending { color: #facc15; }
    .failed  { color: #ef4444; }

    h2 {
        margin: 10px 0;
        font-weight: 800;
        color: #facc15;
        font-size: 28px;
    }

    .details {
        text-align: left;
        margin: 30px 0;
        background: rgba(255, 255, 255, 0.05);
        padding: 20px;
        border-radius: 12px;
        font-size: 15px;
        line-height: 1.7;
        border-left: 4px solid #facc15;
    }

    .details b {
        color: #facc15;
    }

    .btn-access {
        display: inline-block;
        width: 100%;
        padding: 18px;
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: white;
        text-decoration: none;
        border-radius: 12px;
        font-size: 18px;
        font-weight: 800;
        margin: 20px 0;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(34, 197, 94, 0.3);
        text-align: center;
    }
    
    .btn-access:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(34, 197, 94, 0.4);
        background: linear-gradient(135deg, #16a34a, #15803d);
    }
    
    .btn-access:active {
        transform: translateY(-1px);
    }

    .btn-back {
        display: inline-block;
        width: 100%;
        padding: 16px;
        background: rgba(250, 204, 21, 0.1);
        color: #facc15;
        text-decoration: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        border: 2px solid #facc15;
        transition: all 0.3s ease;
        text-align: center;
        margin-top: 10px;
    }
    
    .btn-back:hover {
        background: rgba(250, 204, 21, 0.2);
    }

    .quantity-badge {
        display: inline-block;
        background: rgba(250, 204, 21, 0.2);
        color: #facc15;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 700;
        margin: 15px 0;
        border: 2px solid rgba(250, 204, 21, 0.3);
    }
    
    .product-name {
        display: inline-block;
        background: rgba(59, 130, 246, 0.2);
        color: #93c5fd;
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 600;
        margin: 10px 0 15px 0;
        border: 2px solid rgba(59, 130, 246, 0.3);
    }
    
    .email-notice {
        background: rgba(34, 197, 94, 0.15);
        padding: 20px;
        border-radius: 12px;
        margin: 25px 0;
        border-left: 4px solid #22c55e;
        border-right: 1px solid rgba(34, 197, 94, 0.3);
    }
    
    .email-notice strong {
        color: #86efac;
    }
    
    p {
        color: #ddd;
        margin: 10px 0;
    }
    
    .download-instruction {
        background: rgba(59, 130, 246, 0.1);
        padding: 15px;
        border-radius: 10px;
        margin: 20px 0;
        font-size: 14px;
        color: #93c5fd;
        border-left: 4px solid #3b82f6;
    }
    
    .button-section {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>

</head>
<body <?php if ($status == "00") echo 'class="locked"'; ?>>

<?php if ($status == "00") { ?>
<!-- INVISIBLE LOCK SCREEN (Transparent, tidak terlihat) -->
<div class="invisible-lock" id="invisibleLock"></div>

<!-- Debug info (opsional, bisa diaktifkan untuk testing) -->
<div class="debug-info" id="debugInfo">
    🔒 Locked for tracking: <span id="timer">5</span>s
</div>
<?php } ?>

<div class="container">

<?php if ($status == "00") { ?>

    <div class="icon success">✅</div>
    <h2>Pembayaran Berhasil!</h2>
    
    <div class="product-name">
        📚 <?= htmlspecialchars($productName) ?>
    </div>
    
    <div class="quantity-badge">
        📦 <?= $quantity ?> Paket E-Book
    </div>
    
    <p>Terima kasih! Pembayaran <b style="color:#facc15;">Rp <?= $amountFormatted ?></b> telah diterima.</p>

<?php } elseif ($status == "01") { ?>

    <div class="icon pending">⏳</div>
    <h2>Pembayaran Pending</h2>
    <p>Pembayaran Anda belum selesai. Silakan lanjutkan pembayaran.</p>

<?php } else { ?>

    <script>
        setTimeout(function() { history.back(); }, 2000);
    </script>

    <div class="icon failed">❌</div>
    <h2>Pembayaran Gagal</h2>
    <p>Silakan coba lagi atau hubungi support.</p>

<?php } ?>

    <div class="details">
        <b>📋 Detail Transaksi:</b><br>
        Order ID: <?= htmlspecialchars($result["merchantOrderId"] ?? "-") ?><br>
        Nama Produk: <?= htmlspecialchars($productName) ?><br>
        Jumlah Paket: <?= $quantity ?> paket<br>
        Reference: <?= htmlspecialchars($result["reference"] ?? "-") ?><br>
        Amount: Rp <?= $amountFormatted ?><br>
        Status Code: <?= htmlspecialchars($status) ?><br>
        Message: <?= htmlspecialchars($result["statusMessage"] ?? "Completed") ?><br>
    </div>
    
    <!-- SECTION TOMBOL DI BAWAH DETAIL TRANSAKSI -->
    <div class="button-section">
        <?php if ($status == "00") { ?>
            <a class="btn-back" href="https://beliyuk.shop/" id="homeButton">
                🏠 Kembali ke Beranda
            </a>
        <?php } elseif ($status == "01") { ?>
            <!-- HANYA UNTUK PEMBAYARAN PENDING -->
            <a class="btn-back" href="javascript:history.back();">
                ⬅ Kembali ke Pembayaran
            </a>
        <?php } else { ?>
            <!-- HANYA UNTUK PEMBAYARAN GAGAL -->
            <a class="btn-back" href="javascript:history.back();">
                🔄 Coba Lagi
            </a>
        <?php } ?>
    </div>

</div>

<?php if ($status == "00") { ?>
<script>
// ============================================
// INVISIBLE LOCK SCREEN TIMER (5 DETIK)
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    var debugInfo = document.getElementById('debugInfo');
    var timerElement = document.getElementById('timer');
    var invisibleLock = document.getElementById('invisibleLock');
    var body = document.body;
    var lockDuration = 5; // 5 detik
    var currentTime = lockDuration;
    
    // Update timer untuk debugging
    var timerInterval = setInterval(function() {
        currentTime--;
        if (timerElement) {
            timerElement.textContent = currentTime;
        }
        
        if (currentTime <= 0) {
            clearInterval(timerInterval);
        }
    }, 1000);
    
    // Setelah 5 detik, hapus lock
    setTimeout(function() {
        if (invisibleLock) {
            invisibleLock.style.display = 'none';
        }
        body.classList.remove('locked');
        
        // Log untuk debugging
        console.log('✅ Lock screen dihapus setelah 5 detik untuk tracking');
        console.log('🎯 Meta Pixel memiliki waktu cukup untuk tracking conversion');
        
        // Sembunyikan debug info jika ada
        if (debugInfo) {
            debugInfo.style.display = 'none';
        }
    }, lockDuration * 1000);
    
    // ============================================
    // PREVENT ALL USER INTERACTIONS DURING 5 SECONDS
    // ============================================
    
    // Prevent semua klik saat locked
    document.addEventListener('click', function(e) {
        if (body.classList.contains('locked')) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            console.log('⏳ Mohon tunggu 5 detik untuk proses tracking...');
            return false;
        }
    }, true);
    
    // Prevent keyboard interactions
    document.addEventListener('keydown', function(e) {
        if (body.classList.contains('locked')) {
            // Izinkan hanya refresh page (F5) jika perlu
            if (!e.key.includes('F5')) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
        }
    }, true);
    
    // Prevent right click
    document.addEventListener('contextmenu', function(e) {
        if (body.classList.contains('locked')) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }
    }, true);
    
    // Prevent back button dengan lebih agresif
    history.pushState(null, null, location.href);
    window.addEventListener('popstate', function(e) {
        if (body.classList.contains('locked')) {
            history.pushState(null, null, location.href);
            e.preventDefault();
            return false;
        }
    });
    
    // Disable semua link saat locked
    var allLinks = document.querySelectorAll('a');
    allLinks.forEach(function(link) {
        var originalHref = link.href;
        link.addEventListener('click', function(e) {
            if (body.classList.contains('locked')) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                console.log('⏳ Link dinonaktifkan sementara untuk tracking...');
                return false;
            }
        }, true);
    });
    
    // Tambahkan event listener untuk semua elemen yang bisa diinteraksi
    document.addEventListener('mousedown', function(e) {
        if (body.classList.contains('locked')) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }
    }, true);
    
    document.addEventListener('touchstart', function(e) {
        if (body.classList.contains('locked')) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }
    }, true);
    
    // ============================================
    // META PIXEL PURCHASE EVENT
    // ============================================
    // KONFIGURASI PRODUK UNTUK PIXEL
    const PRODUCT_CATALOG_DATA = {
        id: 'akkqud45dz',
        sku: 'akkqud45dz',
        title: 'Paket The Principles Of Power (Dion Yulianto) (indo)',
        description: 'Akses instan ke 1.999+ e-book premium: bisnis, mindset, self-improvement, bahasa, kesehatan, dan banyak topik lainnya.',
        price: <?= $amountAPI ?>,
        currency: 'IDR',
        category: 'E-books',
        brand: 'Beliyuk Shop',
        availability: 'in stock',
        condition: 'new',
        image_url: 'https://imgdst.tomsamcong.com/public/uploads/original/22/8b/5e47f2a7dd61087a8d74ff4833a8.jpeg',
        url: 'https://beliyuk.shop/products/The_Principles_Of_Power/finish.php?merchantOrderId=<?= $merchantOrderId ?>',
        google_product_category: 'Media > Books > E-books'
    };

    // Track Purchase Event untuk Facebook Pixel
    console.log('🎯 Mempersiapkan Facebook Pixel Purchase Event...');
    console.log('⏰ Lock screen aktif selama 5 detik untuk tracking optimal');
    
    // Kirim event Pixel dengan delay untuk memastikan pixel loaded
    setTimeout(function() {
        if (typeof fbq !== 'undefined') {
            // Kirim Purchase Event
            fbq('track', 'Purchase', {
                value: <?= $amountAPI ?>,
                currency: 'IDR',
                content_ids: ["akkqud45dz"],
                content_type: 'product',
                content_name: "<?= $productName ?>",
                content_category: 'E-books',
                contents: [{
                    id: 'akkqud45dz',
                    sku: 'akkqud45dz',
                    title: '<?= $productName ?>',
                    quantity: <?= $quantity ?>,
                    item_price: <?= $amountAPI ?>,
                    brand: 'Beliyuk Shop',
                    category: 'E-books',
                    delivery_category: 'home_delivery'
                }],
                num_items: <?= $quantity ?>,
                order_id: '<?= $merchantOrderId ?>',
                product_catalog_id: 'akkqud45dz'
            });
            
            console.log('✅ Facebook Pixel Purchase Event terkirim!');
            console.log('📊 Data Purchase:');
            console.log('- Order ID:', '<?= $merchantOrderId ?>');
            console.log('- Amount:', <?= $amountAPI ?>);
            console.log('- Quantity:', <?= $quantity ?>);
            console.log('- Product:', '<?= $productName ?>');
            
            
            
        } else {
            console.warn('⚠️ Facebook Pixel tidak terdeteksi, mencoba lagi...');
            // Coba lagi setelah 1 detik
            setTimeout(function() {
                if (typeof fbq !== 'undefined') {
                    fbq('track', 'Purchase', {
                        value: <?= $amountAPI ?>,
                        currency: 'IDR',
                        content_ids: ["akkqud45dz"],
                        content_type: 'product',
                        content_name: "<?= $productName ?>",
                        content_category: 'E-books',
                        contents: [{
                            id: 'akkqud45dz',
                            sku: 'akkqud45dz',
                            title: '<?= $productName ?>',
                            quantity: <?= $quantity ?>,
                            item_price: <?= $amountAPI ?>,
                            brand: 'Beliyuk Shop',
                            category: 'E-books',
                            delivery_category: 'home_delivery'
                        }],
                        num_items: <?= $quantity ?>,
                        order_id: '<?= $merchantOrderId ?>',
                        product_catalog_id: 'akkqud45dz'
                    });
                    console.log('✅ Facebook Pixel Purchase Event terkirim (retry)!');
                } else {
                    console.error('❌ Facebook Pixel gagal di-load setelah 2 percobaan');
                }
            }, 1000);
        }
        
        // Google Analytics/GTM jika ada
        if (typeof gtag !== 'undefined') {
            gtag('event', 'purchase', {
                transaction_id: '<?= $merchantOrderId ?>',
                value: <?= $amountAPI ?>,
                currency: 'IDR',
                items: [{
                    id: 'akkqud45dz',
                    name: '<?= $productName ?>',
                    quantity: <?= $quantity ?>,
                    price: <?= $amountAPI ?>,
                    category: 'E-books'
                }]
            });
            console.log('✅ Google Analytics Purchase Event terkirim!');
        }
        
        // Track juga untuk analytics lainnya jika diperlukan
        if (typeof dataLayer !== 'undefined') {
            dataLayer.push({
                'event': 'purchase',
                'ecommerce': {
                    'purchase': {
                        'actionField': {
                            'id': '<?= $merchantOrderId ?>',
                            'revenue': <?= $amountAPI ?>,
                            'currency': 'IDR'
                        },
                        'products': [{
                            'id': 'akkqud45dz',
                            'name': '<?= $productName ?>',
                            'price': <?= $amountAPI ?>,
                            'quantity': <?= $quantity ?>,
                            'category': 'E-books'
                        }]
                    }
                }
            });
            console.log('✅ Google Tag Manager Purchase Event terkirim!');
        }
        
    }, 800); // Delay 800ms untuk memastikan pixel sudah fully loaded
    
    // Log setelah 5 detik
    setTimeout(function() {
        console.log('🎉 Tracking selesai! User sekarang bisa berinteraksi dengan halaman');
        console.log('📈 Total waktu tracking: 4 detik');
    }, 4000);
});
</script>
<?php } ?>

</body>
</html>