<!DOCTYPE html>
<html lang="id">
<head>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/pixel.php'; ?>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support — Beliyuk.shop</title>
    <style>
        body {
            font-family: system-ui, sans-serif;
            background: #0d0d0d;
            color: #fff;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .header {
            padding: 40px 20px;
            text-align: center;
            border-bottom: 1px solid #facc15;
        }

        .header h1 {
            color: #facc15;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .contact-card {
            background: #1a1a1a;
            border-left: 2px solid #facc15; /* Garis kuning tipis */
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
        }

        .contact-title {
            color: #facc15;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .contact-desc {
            color: #aaa;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .contact-btn {
            display: block;
            background: #facc15;
            color: #000;
            padding: 12px;
            text-align: center;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
        }

        .contact-btn:hover {
            background: #eab308;
        }

        .address {
            background: #1a1a1a;
            padding: 20px;
            margin: 30px 0;
            border-radius: 8px;
        }

        .address h3 {
            color: #facc15;
            margin-bottom: 15px;
        }

        .address p {
            padding: 5px 0;
            border-bottom: 1px solid #222;
            color: #ddd;
        }

        .address p:last-child {
            border-bottom: none;
        }

        footer {
            text-align: center;
            padding: 30px 20px;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #222;
            margin-top: 50px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Support & Bantuan</h1>
    <p>Hubungi kami kapan saja</p>
</div>

<div class="container">
    
    <!-- EMAIL -->
    <div class="contact-card">
        <div class="contact-title">📧 Email Support</div>
        <div class="contact-desc">Kirim email untuk bantuan teknis</div>
        <a href="mailto:yongkykususkerja@gmail.com" class="contact-btn">
            yongkykususkerja@gmail.com
        </a>
    </div>
    
    <!-- WHATSAPP -->
    <div class="contact-card">
        <div class="contact-title">💬 WhatsApp</div>
        <div class="contact-desc">Chat langsung untuk respon cepat</div>
        <a href="https://dst.skuylah.click/wa" target="_blank" class="contact-btn">
            Chat via WhatsApp
        </a>
    </div>
    
    <!-- TELEGRAM -->
    <div class="contact-card">
        <div class="contact-title">📱 Telegram</div>
        <div class="contact-desc">Join channel untuk update produk</div>
        <a href="https://dst.skuylah.click/tele" target="_blank" class="contact-btn">
            Join Telegram Channel
        </a>
    </div>
    
    <!-- ADDRESS -->
    <div class="address">
        <h3>📍 Alamat</h3>
        <p>ST. SYAHRIR LR. ICHSAN NO. 1501</p>
        <p>RT/RW : 013/003</p>
        <p>Kel/Desa : LIMA ILIR</p>
        <p>Kecamatan : ILIR TIMUR DUA</p>
        <p>Palembang, Sumatera Selatan</p>
    </div>

    <!-- JAM OPERASIONAL -->
    <div class="contact-card">
        <div class="contact-title">🕒 Jam Operasional</div>
        <div class="contact-desc">
            <p><strong>Setiap Hari:</strong> 08:00 - 22:00 WIB</p>
            <p><strong>Respon Email:</strong> 1-6 jam</p>
            <p><strong>Respon Chat:</strong> 15-60 menit</p>
        </div>
    </div>

</div>

<footer>
    © 2025 Beliyuk.shop — Support
</footer>

</body>
</html>