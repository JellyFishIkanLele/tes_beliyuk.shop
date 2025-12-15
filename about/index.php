<?php
// about/index.php
// INI YANG HARUS DITAMBAHKAN DI ATAS FILE
include $_SERVER['DOCUMENT_ROOT'] . '/navbar.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <!-- META PIXEL CODE -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');

    fbq('init', '1270121888157848');
    fbq('track', 'PageView');
    </script>

    <noscript>
    <img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=1270121888157848&ev=PageView&noscript=1"/>
    </noscript>
    <!-- META PIXEL CODE -->

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pelajari lebih lanjut tentang Beliyuk.shop - usaha pribadi yang menyediakan produk dan layanan terbaik dengan kualitas premium.">
    <meta name="keywords" content="tentang kami, about us, beliyuk, beliyuk.shop, usaha pribadi, e-book, produk digital">
    <title>Tentang Kami - Beliyuk.shop</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0d0d0d;
            color: #f8f9fa;
            line-height: 1.6;
        }
        
        /* HAPUS SEMUA STYLE NAVBAR YANG ADA DI SINI */
        /* Karena navbar sudah ada di navbar.php */
        
        /* Back to Home Button */
        .back-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 30px;
            color: #ffd000;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .back-home:hover {
            gap: 12px;
            color: #ffdd44;
        }
        
        /* Hero Section */
        .hero-about {
            background: linear-gradient(135deg, rgba(20, 20, 20, 0.9), rgba(10, 10, 10, 0.9)),
                        url('https://imgdst.tomsamcong.com/public/uploads/original/65/6b/35ef31fbe97ddeb5f4f18a04c1b7.webp');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            padding: 80px 20px;
            text-align: center;
            margin-bottom: 60px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-about::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.6));
            z-index: 1;
        }
        
        .hero-about > * {
            position: relative;
            z-index: 2;
        }
        
        .hero-about h1 {
            font-size: 48px;
            color: #ffd000;
            margin-bottom: 15px;
        }
        
        .hero-about p {
            font-size: 18px;
            color: #ccc;
            max-width: 700px;
            margin: 0 auto;
        }
        
        /* Container */
        .container {
            width: 90%;
            max-width: 1000px;
            margin: 0 auto 60px;
        }
        
        /* About Content */
        .about-content {
            display: grid;
            gap: 50px;
        }
        
        .about-section {
            background: #1c1c1c;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 208, 0, 0.1);
        }
        
        .section-title {
            color: #ffd000;
            font-size: 28px;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: #ffd000;
        }
        
        .about-text {
            color: #ccc;
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        
        .about-text:last-child {
            margin-bottom: 0;
        }
        
        /* Values Section */
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .value-card {
            background: rgba(255, 208, 0, 0.05);
            padding: 25px;
            border-radius: 10px;
            border: 1px solid rgba(255, 208, 0, 0.2);
            transition: all 0.3s ease;
        }
        
        .value-card:hover {
            transform: translateY(-5px);
            border-color: #ffd000;
            box-shadow: 0 10px 25px rgba(255, 208, 0, 0.1);
        }
        
        .value-icon {
            font-size: 32px;
            color: #ffd000;
            margin-bottom: 15px;
        }
        
        .value-title {
            color: #fff;
            font-size: 20px;
            margin-bottom: 10px;
        }
        
        .value-desc {
            color: #aaa;
            font-size: 14px;
        }
        
        /* Mission & Vision */
        .mission-vision {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .mission-card, .vision-card {
            background: rgba(255, 208, 0, 0.05);
            padding: 30px;
            border-radius: 10px;
            border-left: 4px solid #ffd000;
        }
        
        .mission-card h3, .vision-card h3 {
            color: #ffd000;
            font-size: 22px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Team Section */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .team-card {
            background: rgba(255, 255, 255, 0.03);
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid rgba(255, 208, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .team-card:hover {
            transform: translateY(-5px);
            border-color: #ffd000;
        }
        
        .team-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #ffd000, #ffea74);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: #000;
            font-weight: bold;
        }
        
        .team-name {
            color: #fff;
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .team-role {
            color: #ffd000;
            font-size: 16px;
            margin-bottom: 15px;
        }
        
        /* Contact CTA */
        .contact-cta {
            background: linear-gradient(135deg, rgba(255, 208, 0, 0.1), rgba(255, 208, 0, 0.05));
            padding: 50px;
            border-radius: 15px;
            text-align: center;
            border: 1px solid rgba(255, 208, 0, 0.2);
            margin-top: 50px;
        }
        
        .contact-cta h2 {
            color: #ffd000;
            font-size: 32px;
            margin-bottom: 15px;
        }
        
        .contact-cta p {
            color: #ccc;
            margin-bottom: 25px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 35px;
            background: #ffd000;
            color: #000;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        
        .cta-button:hover {
            background: #ffdd44;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 208, 0, 0.3);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-about h1 {
                font-size: 36px;
            }
            
            .about-section {
                padding: 25px;
            }
            
            .contact-cta {
                padding: 30px 20px;
            }
        }
        
        @media (max-width: 480px) {
            .hero-about {
                padding: 60px 20px;
            }
            
            .mission-vision {
                grid-template-columns: 1fr;
            }
        }
        
        /* Reset untuk mencegah tabrakan dengan footer */
        body > *:not(footer):not(.site-footer) {
            background-image: none !important;
        }
    </style>
</head>
<body>
    <!-- NAVBAR SUDAH ADA DARI navbar.php -->
    <!-- JANGAN TAMBAHKAN NAVBAR LAGI DI SINI -->

    <!-- Hero Section -->
    <section class="hero-about">
        <div class="container">
            <a href="/" class="back-home">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Beranda
            </a>
            <h1>Tentang Beliyuk.shop</h1>
            <p>Mengenal lebih dekat visi, misi, dan nilai-nilai yang menjadi dasar usaha pribadi kami</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container">
        <div class="about-content">
            
            <!-- Our Story -->
            <section class="about-section">
                <h2 class="section-title">Cerita Kami</h2>
                <p class="about-text">
                    Beliyuk.shop didirikan dengan satu tujuan sederhana namun kuat: memberikan akses mudah terhadap produk digital berkualitas yang dapat membantu pengembangan diri dan peningkatan pengetahuan.
                </p>
                <p class="about-text">
                    Berawal dari passion terhadap literasi digital dan keinginan untuk berbagi pengetahuan, kami memulai perjalanan ini dengan menyediakan koleksi e-book terpilih yang dianggap memberikan nilai terbaik bagi pembaca.
                </p>
                <p class="about-text">
                    Seiring waktu, Beliyuk.shop berkembang menjadi lebih dari sekadar toko e-book. Kami menjadi mitra belajar bagi banyak individu yang ingin mengembangkan potensi diri mereka melalui literasi berkualitas.
                </p>
            </section>
            
            <!-- Our Values -->
            <section class="about-section">
                <h2 class="section-title">Nilai-Nilai Kami</h2>
                <div class="values-grid">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3 class="value-title">Kualitas Premium</h3>
                        <p class="value-desc">Setiap produk yang kami tawarkan melalui proses kurasi ketat untuk memastikan kualitas terbaik bagi pelanggan.</p>
                    </div>
                    
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h3 class="value-title">Kejujuran & Transparansi</h3>
                        <p class="value-desc">Kami berkomitmen untuk transparan dalam setiap aspek bisnis dan membangun hubungan jangka panjang berdasarkan kepercayaan.</p>
                    </div>
                    
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="value-title">Fokus pada Pelanggan</h3>
                        <p class="value-desc">Kepuasan pelanggan adalah prioritas utama kami. Setiap keputusan dibuat dengan mempertimbangkan nilai yang didapat pelanggan.</p>
                    </div>
                    
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h3 class="value-title">Inovasi Terus Menerus</h3>
                        <p class="value-desc">Kami terus mengembangkan produk dan layanan untuk memberikan pengalaman terbaik yang sesuai dengan kebutuhan zaman.</p>
                    </div>
                </div>
            </section>
            
            <!-- Mission & Vision -->
            <section class="about-section">
                <h2 class="section-title">Misi & Visi</h2>
                <div class="mission-vision">
                    <div class="mission-card">
                        <h3><i class="fas fa-bullseye"></i> Misi Kami</h3>
                        <p class="about-text">Menyediakan akses terhadap produk digital berkualitas yang dapat meningkatkan pengetahuan, keterampilan, dan kualitas hidup pelanggan dengan harga terjangkau.</p>
                        <p class="about-text">Membangun komunitas pembelajar yang saling mendukung dalam perjalanan pengembangan diri.</p>
                    </div>
                    
                    <div class="vision-card">
                        <h3><i class="fas fa-eye"></i> Visi Kami</h3>
                        <p class="about-text">Menjadi platform terpercaya untuk produk digital pengembangan diri yang dikenal karena kualitas, keandalan, dan dampak positifnya bagi masyarakat.</p>
                        <p class="about-text">Memberdayakan satu juta orang Indonesia melalui literasi digital berkualitas dalam 5 tahun ke depan.</p>
                    </div>
                </div>
            </section>
            
            <!-- Our Team -->
            <section class="about-section">
                <h2 class="section-title">Tim Kami</h2>
                <div class="team-grid">
                    <div class="team-card">
                        <div class="team-avatar">
                            BS
                        </div>
                        <h3 class="team-name">Pemilik Beliyuk.shop</h3>
                        <p class="team-role">Founder & Content Curator</p>
                        <p class="about-text">Bertanggung jawab atas seleksi produk, strategi bisnis, dan menjaga kualitas setiap e-book yang kami tawarkan.</p>
                    </div>
                    
                    <div class="team-card">
                        <div class="team-avatar">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 class="team-name">Tim Support</h3>
                        <p class="team-role">Customer Service</p>
                        <p class="about-text">Siap membantu Anda dengan respons cepat dan solusi terbaik untuk setiap pertanyaan atau kendala yang dihadapi.</p>
                    </div>
                </div>
            </section>
            
            <!-- Contact CTA -->
            <section class="contact-cta">
                <h2>Tertarik Bekerja Sama?</h2>
                <p>Kami selalu terbuka untuk kolaborasi, pertanyaan, atau sekedar berkenalan. Jangan ragu untuk menghubungi kami!</p>
                <a href="https://dst.skuylah.click/wa" class="cta-button">
                    <i class="fab fa-whatsapp"></i>
                    Hubungi via WhatsApp
                </a>
            </section>
        </div>
    </main>

  <?php 
include $_SERVER['DOCUMENT_ROOT'] . '/footer.php'; 
?>

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"version":"2024.11.0","token":"689d015908b04b3280da8e39102c1e04","r":1,"server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}' crossorigin="anonymous"></script>
</body>
</html>