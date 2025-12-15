
<?php include $_SERVER['DOCUMENT_ROOT'] . '/navbar.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/pixel.php'; ?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Beliyuk.shop — Landing Page Usaha Pribadi</title>

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #0d0d0d; /* DARK MODE */
    color: #f8f9fa;
}

/* HEADER */
header {
    background: linear-gradient(135deg, #ffd000, #ffea74); /* KUNING */
    padding: 80px 20px;
    text-align: center;
    color: #000;
}

header h1 {
    font-size: 42px;
    margin-bottom: 10px;
    font-weight: 900;
}

header p {
    font-size: 18px;
    opacity: 0.9;
}

.btn-contact {
    display: inline-block;
    margin-top: 25px;
    padding: 12px 28px;
    background: #000;
    color: #ffd000;
    border-radius: 8px;
    text-decoration: none;
    font-size: 18px;
    font-weight: bold;
    transition: all 0.3s ease;
}

.btn-contact:hover {
    background: #222;
}

/* CONTAINER */
.container {
    width: 90%;
    max-width: 900px;
    margin: 40px auto;
}

.section-title {
    text-align: center;
    font-size: 28px;
    margin-bottom: 25px;
    color: #ffd000;
    font-weight: bold;
}

/* DARK CARD STYLE */
.box, .product-card {
    background: #1c1c1c;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0px 4px 10px rgba(255, 255, 255, 0.05);
    color: #fff;
}

/* PRODUCT CATALOG */
.catalog-new {
    display: grid;
    grid-template-columns: 1fr;
    gap: 25px;
}

@media (min-width: 700px) {
    .catalog-new {
        grid-template-columns: repeat(2, 1fr);
    }
}

.product-card img {
    width: 100%;
    border-radius: 12px;
    aspect-ratio: 1/1;
    object-fit: cover;
    margin-bottom: 15px;
}

.product-title {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 8px;
    color: #ffd000;
}

.product-desc {
    font-size: 14px;
    color: #ccc;
    margin-bottom: 12px;
    line-height: 1.45;
}

/* BUTTON BELI SEKARANG */
.buy-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    padding: 10px 20px;
    background: #ffd000;
    color: #000;
    border-radius: 8px;
    font-weight: bold;
    font-size: 14px;
    text-decoration: none;
    cursor: pointer;
    isolation: isolate;
}

/* ANIMASI DIAGONAL DARI KIRI BAWAH KE KANAN ATAS */
.buy-btn::before {
    content: '';
    position: absolute;
    width: 150%; /* Lebar garis cahaya */
    height: 20px; /* Ketebalan garis cahaya */
    background: linear-gradient(
        45deg, /* Sudut 45 derajat untuk diagonal */
        rgba(255, 255, 255, 0) 0%,
        rgba(255, 255, 255, 0.8) 50%,
        rgba(255, 255, 255, 0) 100%
    );
    
    /* Mulai dari kiri bawah */
    bottom: -20px;
    left: -100%;
    
    /* Rotasi untuk efek diagonal */
    transform: rotate(45deg);
    transform-origin: left bottom;
    
    animation: diagonal-sweep 1.5s infinite;
    z-index: -1;
}

@keyframes diagonal-sweep {
    0% {
        left: -100%;
        bottom: -20px;
    }
    100% {
        left: 100%;
        bottom: calc(100% + 20px);
    }
}

/* FEATURES */
.features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
}

.box h3 {
    color: #ffd000;
    margin-bottom: 8px;
}

/* FOOTER STYLE BARU DENGAN BACKGROUND IMAGE */
.site-footer {
    position: relative;
    padding: 50px 0 30px;
    margin-top: 60px;
    overflow: hidden;
}

/* Background Image Footer */
.footer-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('https://imgdst.tomsamcong.com/public/uploads/original/e7/cb/c4d99c2fe0665d5f1a0100552814.webp');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    z-index: -2;
}

/* Overlay untuk meningkatkan readability */
.footer-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.75); /* Overlay gelap untuk kontras */
    z-index: -1;
}

/* Container footer */
.footer-container {
    width: 90%;
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

/* Footer sections */
.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 40px;
    margin-bottom: 40px;
}

.footer-section h3 {
    color: #ffd000;
    font-size: 20px;
    margin-bottom: 20px;
    position: relative;
    padding-bottom: 10px;
}

.footer-section h3::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 50px;
    height: 2px;
    background: #ffd000;
}

.footer-section p {
    color: #ccc;
    line-height: 1.6;
    margin-bottom: 15px;
}

/* Footer links */
.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 10px;
}

.footer-links a {
    color: #ccc;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.footer-links a:hover {
    color: #ffd000;
    transform: translateX(5px);
}

.footer-links a i {
    font-size: 12px;
}

/* Contact info */
.contact-info {
    list-style: none;
    padding: 0;
    margin: 0;
}

.contact-info li {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    color: #ccc;
}

.contact-info i {
    color: #ffd000;
    margin-right: 10px;
    font-size: 18px;
    width: 24px;
}

/* Social media */
.social-media {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.social-media a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(255, 208, 0, 0.1);
    border-radius: 50%;
    color: #ffd000;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 208, 0, 0.3);
}

.social-media a:hover {
    background: #ffd000;
    color: #000;
    transform: translateY(-3px);
}

/* Footer bottom */
.footer-bottom {
    text-align: center;
    padding-top: 30px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    color: #aaa;
    font-size: 14px;
}

.footer-bottom p {
    margin: 10px 0;
}

.footer-bottom-links {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 15px;
}

.footer-bottom-links a {
    color: #ffd000;
    text-decoration: none;
    transition: all 0.3s ease;
}

.footer-bottom-links a:hover {
    text-decoration: underline;
}

/* Responsive footer */
@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .footer-bottom-links {
        flex-direction: column;
        gap: 10px;
    }
}

/* Copyright animation */
.copyright {
    animation: fadeIn 2s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Hover effect untuk seluruh footer */
.site-footer:hover .footer-overlay {
    background: rgba(0, 0, 0, 0.7);
    transition: background 0.5s ease;
}
</style>

<!-- Font Awesome untuk ikon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>

<!-- HEADER -->
<header>
    <h1>Selamat Datang di Beliyuk.shop</h1>
    <p>Landing Page Usaha Pribadi — Tempat Anda Mendapatkan Produk & Layanan Terbaik</p>
    <a href="https://dst.skuylah.click/wa" class="btn-contact">Hubungi Saya</a>
</header>

<!-- ABOUT -->
<div class="container">
    <h2 class="section-title">Tentang Usaha Saya</h2>
    <p style="text-align:center; max-width:700px; margin:auto; color:#ccc;">
        Beliyuk.shop adalah landing page sederhana untuk menampilkan produk dan layanan usaha pribadi saya.
        Dibuat agar pelanggan dapat melihat penawaran terbaik dengan jelas dan mudah.
    </p>
</div>

<!-- PRODUCT CATALOG -->
<div class="container">
    <h2 class="section-title">Katalog Produk</h2>
    <div class="catalog-new">
        <!-- PRODUK 1 -->
        <div class="product-card">
            <img src="https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp">
            <div class="product-title">1.999+ E-BOOKS</div>
            <div class="product-desc">
                Koleksi 1.999+ e-book tentang self improvement, mindset, bisnis, dan pengembangan diri. Praktis dan bisa dibaca di semua perangkat.
            </div>
            <a href="./products/1.999%2b%20E-BOOKS" class="buy-btn">Beli Sekarang</a>
        </div>
        
        <!-- PRODUK 2 -->
        <div class="product-card">
            <img src="https://imgdst.tomsamcong.com/public/uploads/original/91/19/3191eb7f4403af2b128685e26ca0.png">
            <div class="product-title">Atomic Habits E-BOOKS</div>
            <div class="product-desc">
                Buku Atomic Habits karya James Clear, Pelajari cara membangun kebiasaan kecil yang menghasilkan perubahan besar. Praktis, mudah diterapkan, dan membantu meningkatkan produktivitas serta disiplin setiap hari.
            </div>
            <a href="./products/ATOMIC HABITS" class="buy-btn">Beli Sekarang</a>
        </div>
        
        <!-- PRODUK 3 -->
        <div class="product-card">
    <img src="https://imgdst.tomsamcong.com/public/uploads/original/63/79/f9fc318b6d9f1b24dc06ffca641d.png">
    <div class="product-title">48 Laws of Power E-BOOKS</div>
    <div class="product-desc">
        E-book legendaris karya Robert Greene yang membahas 48 hukum kekuasaan. Pelajari strategi psikologis, taktik pengaruh, dan cara memahami dinamika kekuasaan dalam kehidupan, bisnis, maupun hubungan sosial.
    </div>
    <a href="./products/48_Laws_of_Power" class="buy-btn">Beli Sekarang</a>
</div>

        
        <!-- PRODUK 4 -->
       <div class="product-card">
    <img src="https://imgdst.tomsamcong.com/public/uploads/original/97/af/6ba14f61e165c6bebdddd8de7fdc.jpg">
    <div class="product-title">The Psychology of Money E-BOOKS</div>
    <div class="product-desc">
        Buku populer karya Morgan Housel tentang bagaimana pola pikir memengaruhi keputusan finansial. Temukan 19 wawasan penting terkait perilaku manusia, investasi, kekayaan, dan cara membangun hubungan sehat dengan uang.
    </div>
    <a href="./products/The_Psychology_of_Money" class="buy-btn">Beli Sekarang</a>
</div>

        
        <!-- PRODUK 5 -->
       <div class="product-card">
    <img src="https://imgdst.tomsamcong.com/public/uploads/original/22/8b/5e47f2a7dd61087a8d74ff4833a8.jpeg">
    <div class="product-title">The Principles of Power E-BOOKS</div>
    <div class="product-desc">
       The Principles of Power (Dion Yulianto) adalah buku yang membahas prinsip-prinsip kekuatan dan pengaruh dalam kehidupan modern. Isinya memberikan strategi praktis untuk membangun mentalitas kuat, meningkatkan posisi dalam lingkungan sosial maupun profesional, serta memahami cara menghadapi dan menggunakan kekuasaan secara cerdas.
    </div>
    <a href="./products/The_Principles_Of_Power" class="buy-btn">Beli Sekarang</a>
</div>

    </div>
</div>

<!-- FEATURES -->
<div class="container">
    <h2 class="section-title">Kenapa Memilih Saya?</h2>
    <div class="features">
        <div class="box">
            <h3>✔ Kualitas Terbaik</h3>
            <p>Produk dan layanan dengan kualitas terbaik untuk kepuasan pelanggan.</p>
        </div>
        <div class="box">
            <h3>✔ Harga Terjangkau</h3>
            <p>Harga bersahabat tanpa mengorbankan kualitas.</p>
        </div>
        <div class="box">
            <h3>✔ Respon Cepat</h3>
            <p>Fast Response via WhatsApp — ramah, cepat, dan profesional.</p>
        </div>
    </div>
</div>

<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/footer.php'; 
?>
</body>
</html>