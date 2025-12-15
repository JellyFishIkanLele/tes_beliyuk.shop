<?php
session_start();

// ==========================
// SIMPAN EMAIL KE SESSION DARI FORM
// ==========================
if (isset($_POST['customerEmail']) && !empty($_POST['customerEmail'])) {
    $_SESSION['customer_email'] = $_POST['customerEmail'];
    $_SESSION['customer_email'] = filter_var($_SESSION['customer_email'], FILTER_SANITIZE_EMAIL);
}

if (isset($_POST['qty']) && !empty($_POST['qty'])) {
    $_SESSION['quantity'] = intval($_POST['qty']);
}

// ==========================
// VALIDASI DATA
// ==========================
if (!isset($_SESSION['customer_email']) || empty($_SESSION['customer_email'])) {
    die("Email tidak ditemukan. Silakan kembali ke halaman produk.");
}

if (!isset($_SESSION['quantity']) || $_SESSION['quantity'] < 1) {
    $_SESSION['quantity'] = 1;
}

// ==========================
// CONFIG
// ==========================
$merchantCode   = "DS26517"; 
$apiKey         = "c994a717d6e4fe0d2f051352ad21463c";

$pricePerItem = 1;
$qty = $_SESSION['quantity'];
$paymentAmount = $pricePerItem * $qty;

// ==========================
// PAYMENT INFO
// ==========================
$paymentMethod  = "SP"; 
$orderId        = time(); 
$productDetails = "Paket Super Lengkap 1.999+ E-Books Premium (Qty: $qty)";
$customerName   = "Customer";
$customerEmail  = $_SESSION['customer_email']; // Gunakan email dari form
$customerPhone  = "628123456789";

// ==========================
// SIMPAN DATA ORDER KE SESSION
// ==========================
$_SESSION['order_id'] = $orderId;
$_SESSION['payment_amount'] = $paymentAmount;
$_SESSION['product_details'] = $productDetails;

// ==========================
// URL CALLBACK / REDIRECT
// ==========================
$callbackUrl = "https://beliyuk.shop/products/1.999+%20E-BOOKS/callback.php";
$returnUrl   = "https://beliyuk.shop/products/1.999+%20E-BOOKS/finish.php?order_id=" . $orderId;

// ==========================
// SIGNATURE
// ==========================
$signature = md5($merchantCode . $orderId . $paymentAmount . $apiKey);

// ==========================
// REQUEST BODY
// ==========================
$data = array(
    'merchantCode'      => $merchantCode,
    'paymentAmount'     => $paymentAmount,
    'paymentMethod'     => $paymentMethod,
    'merchantOrderId'   => $orderId,
    'productDetails'    => $productDetails,
    'email'             => $customerEmail,
    'customerVaName'    => $customerName,
    'phoneNumber'       => $customerPhone,
    'callbackUrl'       => $callbackUrl,
    'returnUrl'         => $returnUrl,
    'signature'         => $signature,
    'expiryPeriod'      => 60
);

$dataJson = json_encode($data);

// ==========================
// CURL REQUEST
// ==========================
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $dataJson);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($dataJson)
));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);

curl_close($ch);

// ==========================
// PARSE RESPONSE
// ==========================
$result = json_decode($response, true);

// Redirect to Duitku payment page
if ($httpCode == 200 && isset($result['paymentUrl'])) {
    // Simpan reference ke session
    $_SESSION['duitku_reference'] = $result['reference'] ?? '';
    
    header("Location: " . $result['paymentUrl']);
    exit;
}

// ==========================
// ERROR OUTPUT
// ==========================
echo "<h2 style='color:red'>❌ Gagal membuat pembayaran</h2>";
echo "<pre>";
echo "HTTP CODE: $httpCode\n";
echo "CURL ERROR: $curlErr\n\n";
print_r($result);
echo "</pre>";
?>