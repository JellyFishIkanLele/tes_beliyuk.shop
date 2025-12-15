<?php
// footer.php untuk Beliyuk.shop

// Cek apakah file sedang diakses langsung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

// ===== LOGIKA DETEKSI HALAMAN YANG LEBIH AKURAT =====
$current_path = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

// Hapus query string jika ada
if (($pos = strpos($current_path, '?')) !== false) {
    $current_path = substr($current_path, 0, $pos);
}

// Normalisasi path
$current_path = rtrim($current_path, '/');
if ($current_path === '') {
    $current_path = '/';
}

// Deteksi apakah di halaman beranda
$is_homepage = false;

// Cek berbagai kemungkinan path beranda
$home_checks = [
    $current_path === '/',
    $current_path === '/index.php',
    $current_path === '',
    basename($script_name) === 'index.php' && ($current_path === '/' || $current_path === ''),
    (isset($_SERVER['REDIRECT_URL']) && $_SERVER['REDIRECT_URL'] === '/'),
    strpos($current_path, '/?') === 0
];

foreach ($home_checks as $check) {
    if ($check) {
        $is_homepage = true;
        break;
    }
}

// DEBUG: (hapus di production jika sudah berfungsi)
// echo "<!-- DEBUG: current_path: $current_path, is_homepage: " . ($is_homepage ? 'true' : 'false') . " -->";
?>

<!-- Footer untuk Beliyuk.shop -->
<footer class="site-footer" id="beliyuk-footer">
    <!-- Background Image -->
    <div class="footer-bg"></div>
    
    <!-- Overlay untuk kontras -->
    <div class="footer-overlay"></div>
    
    <!-- Footer Content -->
    <div class="footer-container">
        <div class="footer-content">
            <!-- About Section -->
            <div class="footer-section">
                <h3>Beliyuk.shop</h3>
                <p>Landing Page Usaha Pribadi yang menyediakan produk dan layanan terbaik dengan kualitas premium dan harga terjangkau.</p>
                <p>Komitmen kami adalah memberikan pengalaman berbelanja yang memuaskan untuk setiap pelanggan.</p>
            </div>
            
            <!-- Quick Links -->
            <div class="footer-section">
                <h3>Tautan Cepat</h3>
                <ul class="footer-links">
                    <?php
                    $links = [
                        '/' => 'Beranda',
                        '/about/' => 'Tentang Kami',
                        '/products/' => 'List Products',
                        '/terms-and-conditions/' => 'Terms & Conditions',
                        '/refund-policy/' => 'Refund Policy',
                        '/support&contact/' => 'Support/Contact',
                    ];
                    
                    foreach ($links as $url => $text) {
                        // Normalisasi URL untuk perbandingan
                        $normalized_url = rtrim($url, '/');
                        if ($normalized_url === '') {
                            $normalized_url = '/';
                        }
                        
                        $is_active = false;
                        
                        // LOGIKA DETECT AKTIF YANG LEBIH AKURAT
                        if ($normalized_url === '/') {
                            // Untuk beranda
                            $is_active = $is_homepage;
                        } else {
                            // Untuk halaman lain
                            $normalized_current = rtrim($current_path, '/');
                            $normalized_current = $normalized_current === '' ? '/' : $normalized_current;
                            
                            // Cek beberapa kondisi
                            if ($normalized_current === $normalized_url) {
                                $is_active = true;
                            } elseif (strpos($current_path . '/', $normalized_url . '/') === 0) {
                                $is_active = true;
                            } elseif (basename($_SERVER['PHP_SELF']) === basename($url) && $normalized_url !== '/') {
                                $is_active = true;
                            }
                        }
                        
                        $active_class = $is_active ? 'class="active"' : '';
                        echo '<li><a href="' . $url . '" ' . $active_class . '><i class="fas fa-chevron-right footer-icon"></i> ' . $text . '</a></li>';
                    }
                    ?>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div class="footer-section">
                <h3>Hubungi Kami</h3>
                <ul class="contact-info">
                    <li>
                        <i class="fas fa-map-marker-alt contact-icon"></i>
                        <span>Indonesia</span>
                    </li>
                    <li>
                        <i class="fas fa-envelope contact-icon"></i>
                        <span>yongkykususkerja@gmail.com</span>
                    </li>
                    <li>
                        <i class="fas fa-phone contact-icon"></i>
                        <span>+62 895-1315-1027</span>
                    </li>
                    <li>
                        <i class="fas fa-clock contact-icon"></i>
                        <span>Senin - Minggu: 24 jam Online</span>
                    </li>
                </ul>
                
                <!-- Social Media -->
                <div class="social-media">
                    <a href="https://dst.skuylah.click/wa" title="WhatsApp" target="_blank">
                        <i class="fab fa-whatsapp social-icon"></i>
                    </a>
                    <a href="https://dst.skuylah.click/tele" title="Telegram" target="_blank">
                        <i class="fab fa-telegram social-icon"></i>
                    </a>
                    <a href="mailto:yongkykususkerja@gmail.com" title="Email">
                        <i class="fas fa-envelope social-icon"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <p class="copyright">© <?php echo date('Y'); ?> Beliyuk.shop — Usaha Pribadi. All rights reserved.</p>
            <p class="footer-note">
                Website designed with ❤️ for better customer experience
            </p>
        </div>
    </div>
</footer>

<style>
    /* === FOOTER STYLES - SUPER SPESIFIK === */
    /* Gunakan ID dan class yang sangat spesifik */
    #beliyuk-footer.site-footer {
        position: relative;
        padding: 50px 0 30px;
        margin-top: 60px;
        overflow: hidden;
        background: transparent;
        width: 100%;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        z-index: 10;
        box-sizing: border-box;
    }
    
    /* HANYA target elemen di dalam footer dengan sangat spesifik */
    #beliyuk-footer .footer-bg {
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
    
    #beliyuk-footer .footer-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.75);
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
    
    /* HANYA header di footer yang dapat styling ini */
    #beliyuk-footer .footer-section > h3 {
        color: #ffd000 !important;
        font-size: 20px !important;
        margin-bottom: 20px !important;
        position: relative !important;
        padding-bottom: 10px !important;
        font-weight: bold !important;
        text-align: left !important;
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
    
    /* HANYA paragraf di dalam footer section */
    #beliyuk-footer .footer-section > p {
        color: #ccc !important;
        line-height: 1.6 !important;
        margin-bottom: 15px !important;
        font-size: 14px !important;
        font-weight: normal !important;
        text-align: left !important;
    }
    
    #beliyuk-footer .footer-links {
        list-style: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    #beliyuk-footer .footer-links li {
        margin-bottom: 10px !important;
    }
    
    /* HANYA link di dalam footer-links */
    #beliyuk-footer .footer-links a {
        color: #cccccc !important;
        text-decoration: none !important;
        transition: all 0.3s ease !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        font-size: 14px !important;
        font-weight: normal !important;
        text-align: left !important;
    }
    
    #beliyuk-footer .footer-links a:hover {
        color: #ffd000 !important;
        transform: translateX(5px);
    }
    
    #beliyuk-footer .footer-links a.active {
        color: #ffd000 !important;
        font-weight: bold !important;
    }
    
    #beliyuk-footer .footer-icon {
        font-size: 12px;
        color: inherit;
        font-family: "Font Awesome 6 Free";
    }
    
    #beliyuk-footer .contact-info {
        list-style: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    #beliyuk-footer .contact-info li {
        display: flex !important;
        align-items: center !important;
        margin-bottom: 15px !important;
        color: #ccc !important;
        font-size: 14px !important;
        font-weight: normal !important;
        text-align: left !important;
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
    
    #beliyuk-footer .social-icon {
        font-size: 18px;
    }
    
    /* Footer bottom - tambahkan !important untuk properti penting */
    #beliyuk-footer .footer-bottom {
        text-align: center !important;
        padding-top: 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        color: #aaa !important;
        font-size: 14px !important;
        font-weight: normal !important;
    }
    
    #beliyuk-footer .copyright {
        margin: 10px 0;
        color: #aaa !important;
        font-size: 14px !important;
        font-weight: normal !important;
    }
    
    #beliyuk-footer .footer-bottom-links {
        display: flex !important;
        justify-content: center !important;
        flex-wrap: wrap !important;
        gap: 20px !important;
        margin-top: 15px !important;
    }
    
    #beliyuk-footer .footer-bottom-links a {
        color: #ffd000 !important;
        text-decoration: none !important;
        font-size: 14px !important;
        font-weight: normal !important;
    }
    
    #beliyuk-footer .footer-bottom-links a:hover {
        text-decoration: underline !important;
    }
    
    #beliyuk-footer .footer-note {
        margin-top: 20px;
        font-size: 12px !important;
        color: #888 !important;
        font-weight: normal !important;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        #beliyuk-footer .footer-content {
            grid-template-columns: 1fr;
            gap: 30px;
        }
        
        #beliyuk-footer .footer-bottom-links {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>

<!-- JavaScript untuk memastikan link aktif -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Target footer spesifik
    const footer = document.getElementById('beliyuk-footer');
    if (!footer) return;
    
    // Cek dan tambahkan Font Awesome jika belum ada
    if (!document.querySelector('link[href*="font-awesome"]')) {
        const faLink = document.createElement('link');
        faLink.rel = 'stylesheet';
        faLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
        document.head.appendChild(faLink);
    }
    
    // Fungsi untuk update link colors
    function updateFooterLinks() {
        const links = footer.querySelectorAll('.footer-links a');
        const currentPath = window.location.pathname;
        
        links.forEach(link => {
            const linkHref = link.getAttribute('href');
            const normalizedCurrent = currentPath.replace(/\/$/, '') || '/';
            const normalizedLink = linkHref.replace(/\/$/, '') || '/';
            
            // Cek jika link aktif
            const isActive = normalizedCurrent === normalizedLink || 
                           (normalizedLink === '/' && normalizedCurrent === '/');
            
            if (isActive) {
                link.classList.add('active');
                link.style.color = '#ffd000';
                link.style.fontWeight = 'bold';
                
                // Update icon color
                const icon = link.querySelector('.footer-icon');
                if (icon) icon.style.color = '#ffd000';
            } else {
                link.classList.remove('active');
                link.style.color = '#cccccc';
                link.style.fontWeight = 'normal';
                
                // Update icon color
                const icon = link.querySelector('.footer-icon');
                if (icon) icon.style.color = '#cccccc';
            }
        });
    }
    
    // Jalankan saat load
    updateFooterLinks();
    
    // Jalankan juga saat ada perubahan URL (untuk SPA)
    window.addEventListener('popstate', updateFooterLinks);
});
</script>

<!-- Font Awesome CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">