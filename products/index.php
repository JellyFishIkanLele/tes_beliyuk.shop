<?php
// DATA PRODUK
$products = [
    [
        "id" => 1,
        "title" => "1.999+ E-BOOKS PREMIUM",
        "badge" => "Best Seller",
        "desc" => "Koleksi ribuan e-book self improvement, mindset, dan bisnis. Praktis dibaca di semua perangkat.",
        "image" => "https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp",
        "link" => "1.999+ E-BOOKS"
    ],
    [
        "id" => 2,
        "title" => "Atomic Habits E-BOOK",
        "badge" => "Populer",
        "desc" => "Cara membangun kebiasaan kecil yang menghasilkan perubahan besar dalam hidup Anda.",
        "image" => "https://imgdst.tomsamcong.com/public/uploads/original/91/19/3191eb7f4403af2b128685e26ca0.png",
        "link" => "ATOMIC HABITS"
    ],
    [
        "id" => 3,
        "title" => "48 Laws of Power",
        "badge" => "Legend",
        "desc" => "Strategi psikologis dan taktik pengaruh untuk memahami dinamika kekuasaan.",
        "image" => "https://imgdst.tomsamcong.com/public/uploads/original/63/79/f9fc318b6d9f1b24dc06ffca641d.png",
        "link" => "48_Laws_of_Power"
    ],
    [
        "id" => 4,
        "title" => "Psychology of Money",
        "badge" => "Finance",
        "desc" => "Wawasan penting terkait perilaku manusia, investasi, dan cara membangun kekayaan.",
        "image" => "https://imgdst.tomsamcong.com/public/uploads/original/97/af/6ba14f61e165c6bebdddd8de7fdc.jpg",
        "link" => "The_Psychology_of_Money"
    ],
    [
        "id" => 5,
        "title" => "The Principles of Power",
        "badge" => "New",
        "desc" => "Strategi praktis membangun mentalitas kuat dan meningkatkan posisi dalam lingkungan sosial.",
        "image" => "https://imgdst.tomsamcong.com/public/uploads/original/22/8b/5e47f2a7dd61087a8d74ff4833a8.jpeg",
        "link" => "The_Principles_Of_Power"
    ]
];
?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/navbar.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/pixel.php'; ?>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - Beliyuk Shop</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* === GLOBAL STYLE (DARK MODE) === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #0d0d0d; /* Background Gelap */
            color: #f8f9fa; /* Teks Terang */
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
        }
        
        /* === HEADER (KUNING) === */
        header {
            background: linear-gradient(135deg, #ffd000, #ffea74); /* Gradient Kuning */
            padding: 60px 20px;
            text-align: center;
            color: #000; /* Teks Hitam di header kuning */
            position: relative;
            overflow: hidden;
            margin-bottom: 40px;
        }
        
        header h1 {
            font-size: 2.8rem;
            margin-bottom: 10px;
            font-weight: 900;
        }
        
        header .tagline {
            font-size: 1.2rem;
            opacity: 0.9;
            font-weight: 600;
            max-width: 800px;
            margin: 0 auto 25px auto;
        }
        
        /* TOMBOL HEADER */
        .btn-primary {
            display: inline-block;
            background-color: #000;
            color: #ffd000;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #222;
            transform: translateY(-2px);
        }
        
        /* === PRODUK SECTION === */
        .products-section {
            max-width: 1000px;
            margin: 0 auto 60px auto;
            padding: 0 20px;
        }
        
        .section-title {
            font-size: 28px;
            font-weight: bold;
            color: #ffd000; /* Judul Kuning */
            margin-bottom: 30px;
            text-align: center;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }
        
        /* CARD STYLE (GELAP) */
        .product-card {
            background: #1c1c1c; /* Card Abu Gelap */
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(255, 255, 255, 0.05);
            color: #fff;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255, 208, 0, 0.1);
        }

        /* IMAGE WRAPPER */
        .product-img-wrap {
            width: 100%;
            aspect-ratio: 1/1;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 15px;
            background: #222;
        }

        .product-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-img-wrap img {
            transform: scale(1.05);
        }
        
        /* INFO PRODUK */
        .product-title {
            font-size: 20px;
            font-weight: bold;
            color: #ffd000; /* Judul Produk Kuning */
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
        }
        
        .product-badge {
            background-color: #ffd000;
            color: #000;
            font-size: 11px;
            font-weight: 800;
            padding: 4px 8px;
            border-radius: 4px;
            white-space: nowrap;
            text-transform: uppercase;
        }
        
        .product-description {
            font-size: 14px;
            color: #ccc; /* Deskripsi Abu Terang */
            margin-bottom: 20px;
            line-height: 1.5;
            flex-grow: 1;
        }
        
        /* TOMBOL BELI */
        .btn-product {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #ffd000;
            color: #000;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        /* Animasi Kilau pada Tombol */
        .btn-product::before {
            content: '';
            position: absolute;
            width: 150%;
            height: 20px;
            background: linear-gradient(45deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.8) 50%, rgba(255,255,255,0) 100%);
            bottom: -20px;
            left: -100%;
            transform: rotate(45deg);
            transform-origin: left bottom;
            animation: diagonal-sweep 2.5s infinite;
        }

        @keyframes diagonal-sweep {
            0% { left: -100%; bottom: -20px; }
            100% { left: 100%; bottom: calc(100% + 20px); }
        }
        
        .btn-product:hover {
            background-color: #e6bc00;
            transform: scale(1.02);
        }

        /* === FOOTER CUSTOM === */
        #beliyuk-footer.site-footer {
            position: relative;
            padding: 60px 0 30px;
            margin-top: 60px;
            overflow: hidden;
            background: transparent;
            width: 100%;
            font-family: Arial, sans-serif;
            z-index: 10;
        }
        
        #beliyuk-footer .footer-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://imgdst.tomsamcong.com/public/uploads/original/e7/cb/c4d99c2fe0665d5f1a0100552814.webp');
            background-size: cover;
            background-position: center;
            z-index: -2;
        }
        
        #beliyuk-footer .footer-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            z-index: -1;
        }
        
        #beliyuk-footer .footer-container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        #beliyuk-footer .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        
        #beliyuk-footer .footer-section > h3 {
            color: #ffd000 !important;
            font-size: 20px !important;
            margin-bottom: 20px !important;
            position: relative !important;
            padding-bottom: 10px !important;
            font-weight: bold !important;
        }
        
        #beliyuk-footer .footer-section > h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background: #ffd000;
        }
        
        #beliyuk-footer .footer-section > p {
            color: #ccc !important;
            line-height: 1.6 !important;
            margin-bottom: 15px !important;
            font-size: 14px !important;
        }
        
        #beliyuk-footer .footer-links {
            list-style: none !important;
            padding: 0 !important;
        }
        
        #beliyuk-footer .footer-links li {
            margin-bottom: 10px !important;
        }
        
        #beliyuk-footer .footer-links a {
            color: #ccc !important;
            text-decoration: none !important;
            transition: all 0.3s ease !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            font-size: 14px !important;
        }
        
        #beliyuk-footer .footer-links a:hover {
            color: #ffd000 !important;
            transform: translateX(5px);
        }
        
        #beliyuk-footer .contact-info {
            list-style: none !important;
            padding: 0 !important;
        }
        
        #beliyuk-footer .contact-info li {
            display: flex !important;
            align-items: center !important;
            margin-bottom: 15px !important;
            color: #ccc !important;
            font-size: 14px !important;
        }
        
        #beliyuk-footer .contact-icon {
            color: #ffd000;
            margin-right: 10px;
            font-size: 18px;
            width: 24px;
        }
        
        #beliyuk-footer .social-media {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        #beliyuk-footer .social-media a {
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
        
        #beliyuk-footer .social-media a:hover {
            background: #ffd000;
            color: #000;
            transform: translateY(-3px);
        }
        
        #beliyuk-footer .footer-bottom {
            text-align: center !important;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #aaa !important;
            font-size: 14px !important;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            header { padding: 40px 20px; }
            header h1 { font-size: 2.5rem; }
            .section-title { font-size: 24px; }
            .products-grid { grid-template-columns: 1fr; }
            #beliyuk-footer .footer-content { grid-template-columns: 1fr; gap: 30px; }
        }
    </style>
</head>
<body>

    <div class="main-content">
        <header>
            <h1>Beliyuk Shop</h1>
            <p class="tagline">Katalog Lengkap E-Books & Produk Digital Premium</p>
            <a href="../" class="btn-primary"><i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama</a>
        </header>

        <section id="products" class="products-section">
            <h2 class="section-title">Semua Produk</h2>
            
            <div class="products-grid">
                <?php foreach ($products as $p): ?>
                <div class="product-card">
                    
                    <div class="product-img-wrap">
                        <img src="<?= $p['image'] ?>" alt="<?= $p['title'] ?>">
                    </div>

                    <div class="product-title">
                        <?= $p['title'] ?>
                        <?php if(!empty($p['badge'])): ?>
                            <span class="product-badge"><?= $p['badge'] ?></span>
                        <?php endif; ?>
                    </div>

                    <p class="product-description"><?= $p['desc'] ?></p>
                    
                    <a href="<?= $p['link'] ?>" class="btn-product">
                        Beli Sekarang <i class="fas fa-shopping-cart" style="margin-left:8px;"></i>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/footer.php'; 
?>

</body>
</html>