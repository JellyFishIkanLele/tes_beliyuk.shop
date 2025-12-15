<?php
session_start();

// ==========================
// INCLUDE FUNGSI KIRIM EMAIL
// ==========================
function sendProductEmail($toEmail, $orderId, $productName, $amount) {
    $subject = "✅ Paket E-Book 1.999+ Premium Anda Telah Siap - Order #" . $orderId;
    
    $formattedAmount = number_format($amount, 0, ',', '.');
    $downloadLink = "https://drive.google.com/drive/folders/1AFqTm-afDBkqN1S4JAtPFfFvXxQ5HuDR?usp=drive_link";

    
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Paket E-Book Premium Anda</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; background: #f4f4f4; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #facc15, #eab308); padding: 30px; text-align: center; color: #000; }
            .content { padding: 30px; }
            .download-section { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #facc15; }
            .btn { display: inline-block; padding: 12px 30px; background: #22c55e; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; }
            .footer { background: #1a1a1a; color: #aaa; padding: 20px; text-align: center; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🎉 TERIMA KASIH TELAH MEMBELI!</h1>
                <p>Paket Super Lengkap 1.999+ E-Books Premium</p>
            </div>
            
            <div class="content">
                <h2>Halo Pelanggan!</h2>
                <p>Pembayaran Anda telah <strong>berhasil diverifikasi</strong>. Berikut adalah detail pesanan Anda:</p>
                
                <div class="download-section">
                    <h3>📦 DETAIL PESANAN</h3>
                    <p><strong>Order ID:</strong> ' . $orderId . '</p>
                    <p><strong>Produk:</strong> ' . $productName . '</p>
                    <p><strong>Total Pembayaran:</strong> Rp ' . $formattedAmount . '</p>
                    <p><strong>Tanggal:</strong> ' . date("d/m/Y H:i:s") . '</p>
                </div>
                
                <h3>📚 LINK DOWNLOAD PAKET LENGKAP:</h3>
                <p style="text-align: center; margin: 30px 0;">
                    <a href="https://drive.google.com/drive/folders/1ABC123XYZ?usp=sharing" class="btn">
                        📥 KLIK UNTUK UNDUH SEKARANG
                    </a>
                </p>
                
                <p><strong>🔐 Password file (jika diminta):</strong> beliyuk2024</p>
                
                <h3>💡 PETUNJUK DOWNLOAD:</h3>
                <ol>
                    <li>Klik tombol "KLIK UNTUK UNDUH SEKARANG" di atas</li>
                    <li>File dalam format ZIP/RAR berukuran besar (sekitar 2GB)</li>
                    <li>Ekstrak menggunakan WinRAR, 7-Zip, atau aplikasi sejenis</li>
                    <li>Baca e-book dengan aplikasi PDF reader seperti Adobe Acrobat</li>
                    <li>Link berlaku seumur hidup, bisa diunduh ulang kapan saja</li>
                </ol>
                
                <h3>🔄 LINK ALTERNATIF (jika link utama error):</h3>
                <ul>
                    <li><a href="https://mega.nz/folder/abc123">Mega.nz Backup</a></li>
                    <li><a href="https://drive.google.com/drive/folders/1XYZ456ABC">Google Drive Backup</a></li>
                </ul>
                
                <div style="background: #fff8e1; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #facc15;">
                    <h4>⚠️ PERHATIAN:</h4>
                    <p>1. Jangan bagikan link ini kepada siapapun</p>
                    <p>2. Simpan email ini untuk akses di masa depan</p>
                    <p>3. Jika ada masalah download, hubungi support kami</p>
                </div>
                
                <hr>
                <h3>📞 BANTUAN & SUPPORT</h3>
                <p>Jika mengalami masalah, hubungi kami:</p>
                <ul>
                    <li><strong>Email:</strong> support@beliyuk.shop</li>
                    <li><strong>WhatsApp:</strong> 0812-3456-7890</li>
                    <li><strong>Telegram:</strong> @beliyuksupport</li>
                    <li><strong>Website:</strong> https://beliyuk.shop</li>
                </ul>
                
                <p>Salam sukses,<br>
                <strong>Tim Beliyuk Shop</strong></p>
            </div>
            
            <div class="footer">
                <p>© ' . date("Y") . ' Beliyuk Shop. All rights reserved.</p>
                <p>Email ini dikirim otomatis, harap tidak membalas.</p>
            </div>
        </div>
    </body>
    </html>
    ';
    
    // Headers untuk email HTML
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Beliyuk Shop <noreply@beliyuk.shop>" . "\r\n";
    $headers .= "Reply-To: support@beliyuk.shop" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "X-Priority: 1" . "\r\n";
    
    // Kirim email
    $mailSent = mail($toEmail, $subject, $message, $headers);
    
    // Log pengiriman email
    if ($mailSent) {
        file_put_contents("email-log.txt", date("Y-m-d H:i:s") . " - EMAIL SENT to " . $toEmail . " - Order: " . $orderId . PHP_EOL, FILE_APPEND);
    } else {
        file_put_contents("email-error.txt", date("Y-m-d H:i:s") . " - EMAIL FAILED to " . $toEmail . " - Order: " . $orderId . PHP_EOL, FILE_APPEND);
    }
    
    return $mailSent;
}

// ==========================
// PROSES CALLBACK DUITKU
// ==========================
$json = file_get_contents("php://input");

// Log callback ke file
file_put_contents("callback-log.txt", date("Y-m-d H:i:s") . " - " . $json . PHP_EOL, FILE_APPEND);

$data = json_decode($json, true);

// Validasi signature callback
$merchantCode = "DS26707";
$apiKey       = "3fb2f3932d6883c583412ae386b677e6";

if (isset($data['merchantOrderId']) && isset($data['amount'])) {
    $checkSignature = md5($merchantCode . $data['merchantOrderId'] . $data['amount'] . $apiKey);
    
    if (isset($data['signature']) && $data['signature'] === $checkSignature) {
        // Transaksi selesai
        if ($data['resultCode'] == "00") {
            // Pembayaran sukses
            
            // Ambil email dari session atau database
            $customerEmail = isset($_SESSION['customer_email']) ? $_SESSION['customer_email'] : '';
            
            // Jika tidak ada di session, coba ambil dari callback data
            if (empty($customerEmail) && isset($data['email'])) {
                $customerEmail = $data['email'];
            }
            
            // Jika email ditemukan, kirim produk
            if (!empty($customerEmail)) {
                $orderId = $data['merchantOrderId'];
                $amount = $data['amount'];
                $productName = isset($_SESSION['product_details']) ? $_SESSION['product_details'] : "Paket 1.999+ E-Books Premium";
                
                // Kirim email produk
                sendProductEmail($customerEmail, $orderId, $productName, $amount);
                
                // Hapus session setelah pengiriman
                unset($_SESSION['customer_email']);
                unset($_SESSION['quantity']);
                unset($_SESSION['order_id']);
                unset($_SESSION['product_details']);
            }
            
            file_put_contents("paid-log.txt", date("Y-m-d H:i:s") . " - PAID: " . json_encode($data) . PHP_EOL, FILE_APPEND);
        } else {
            // Pembayaran gagal
            file_put_contents("failed-log.txt", date("Y-m-d H:i:s") . " - FAILED: " . json_encode($data) . PHP_EOL, FILE_APPEND);
        }
    }
}

// Kirim response 200 agar Duitku anggap berhasil
http_response_code(200);
echo "OK";
?>