<?php
// navbar.php - Hanya berisi navbar
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    /* NAVBAR STYLES - UNTUK NAVBAR SAJA */
    .beliyuk-navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: rgba(13, 13, 13, 0.95);
        padding: 15px 5%;
        box-shadow: 0 4px 20px rgba(255, 208, 0, 0.15);
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        transition: all 0.3s ease;
        border-bottom: 2px solid #ffd000;
        box-sizing: border-box; /* Tambahkan ini */
    }

    .beliyuk-navbar.scrolled {
        padding: 12px 5%;
        background-color: rgba(13, 13, 13, 0.98);
        box-shadow: 0 6px 25px rgba(255, 208, 0, 0.2);
    }

    .navbar-logo-container {
        display: flex;
        align-items: center;
        gap: 15px;
        min-width: 0; /* Tambahkan ini */
        flex: 1; /* Tambahkan ini */
    }

    .navbar-logo {
        height: 45px;
        width: auto;
        max-height: 45px; /* Tambahkan ini */
        transition: all 0.3s ease;
        filter: drop-shadow(0 2px 4px rgba(255, 208, 0, 0.3));
        border-radius: 5px;
        flex-shrink: 0; /* Tambahkan ini */
    }

    .beliyuk-navbar.scrolled .navbar-logo {
        height: 38px;
        max-height: 38px; /* Tambahkan ini */
    }

    .navbar-logo-text {
        font-size: 24px;
        font-weight: 900;
        color: #ffd000;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        letter-spacing: 0.5px;
        white-space: nowrap; /* Tambahkan ini */
        overflow: hidden; /* Tambahkan ini */
        text-overflow: ellipsis; /* Tambahkan ini */
        flex-shrink: 0; /* Tambahkan ini */
    }

    .navbar-menu {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 5px;
        flex: 2; /* Tambahkan ini */
        justify-content: center; /* Tambahkan ini */
    }

    .navbar-item {
        margin: 0;
    }

    .navbar-link {
        text-decoration: none;
        color: #f8f9fa;
        font-weight: 600;
        font-size: 16px;
        padding: 10px 20px;
        border-radius: 6px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        white-space: nowrap; /* Tambahkan ini */
    }

    .navbar-link:hover {
        color: #000;
        background: #ffd000;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 208, 0, 0.3);
    }

    .navbar-link.active {
        color: #000;
        background: #ffd000;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(255, 208, 0, 0.4);
    }

    .navbar-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 30px;
        height: 3px;
        background-color: #000;
        border-radius: 2px;
    }

    .navbar-cart-container {
        display: flex;
        align-items: center;
        position: relative;
        flex-shrink: 0; /* Tambahkan ini */
        margin-left: auto; /* Tambahkan ini */
        padding-left: 10px; /* Tambahkan ini */
    }

    .navbar-cart-icon {
        font-size: 26px;
        color: #ffd000;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        padding: 8px;
        border-radius: 8px;
        background: rgba(255, 208, 0, 0.1);
        border: 1px solid rgba(255, 208, 0, 0.2);
        flex-shrink: 0; /* Tambahkan ini */
        min-width: 44px; /* Tambahkan ini untuk touch target yang lebih baik */
        min-height: 44px; /* Tambahkan ini untuk touch target yang lebih baik */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .navbar-cart-icon:hover {
        color: #000;
        background: #ffd000;
        transform: scale(1.1) rotate(-5deg);
        box-shadow: 0 4px 15px rgba(255, 208, 0, 0.4);
    }

    .navbar-cart-count {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: #ff0000;
        color: white;
        font-size: 12px;
        font-weight: bold;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #0d0d0d;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.5);
        z-index: 2; /* Pastikan di atas elemen lain */
    }

    .navbar-menu-toggle {
        display: none;
        flex-direction: column;
        cursor: pointer;
        background: rgba(255, 208, 0, 0.1);
        border: 1px solid rgba(255, 208, 0, 0.2);
        border-radius: 6px;
        padding: 10px;
        margin-right: 10px; /* Diubah dari 15px ke 10px */
        flex-shrink: 0; /* Tambahkan ini */
        min-width: 44px; /* Tambahkan ini untuk touch target yang lebih baik */
        min-height: 44px; /* Tambahkan ini untuk touch target yang lebih baik */
        align-items: center;
        justify-content: center;
    }

    .navbar-menu-toggle span {
        height: 3px;
        width: 25px;
        background-color: #ffd000;
        margin-bottom: 4px;
        border-radius: 2px;
        transition: 0.3s ease;
    }

    .navbar-menu-toggle.active {
        background: rgba(255, 208, 0, 0.2);
    }

    /* Perbaikan untuk layar tablet */
    @media (max-width: 992px) {
        .beliyuk-navbar {
            padding: 15px 3%;
        }
        
        .navbar-logo-text {
            font-size: 20px;
        }
        
        .navbar-link {
            font-size: 15px;
            padding: 10px 15px;
        }
        
        .navbar-menu {
            gap: 2px;
        }
    }

    /* Perbaikan untuk layar HP - BREAKPOINT DIUBAH KE 768px */
    @media (max-width: 768px) {
        .beliyuk-navbar {
            padding: 12px 15px; /* Padding diperkecil */
        }
        
        .beliyuk-navbar.scrolled {
            padding: 10px 15px;
        }
        
        .navbar-logo-container {
            gap: 10px; /* Gap diperkecil */
            min-width: 0;
            overflow: hidden;
        }
        
        .navbar-logo {
            height: 35px;
            max-height: 35px;
        }
        
        .beliyuk-navbar.scrolled .navbar-logo {
            height: 30px;
            max-height: 30px;
        }
        
        .navbar-logo-text {
            font-size: 16px; /* Font diperkecil */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 120px; /* Batasi lebar teks */
        }
        
        /* Menu toggle posisi */
        .navbar-menu-toggle {
            display: flex;
            margin-right: 8px; /* Diperkecil lagi */
            order: 1; /* Pindah ke kiri */
        }
        
        /* Logo container di tengah */
        .navbar-logo-container {
            order: 2;
            justify-content: center;
            flex: 1;
            text-align: center;
            padding: 0 5px;
        }
        
        /* Cart di kanan */
        .navbar-cart-container {
            order: 3;
            margin-left: 0;
            padding-left: 0;
        }
        
        /* Menu untuk mobile */
        .navbar-menu {
            position: fixed;
            top: 75px;
            left: -100%;
            width: 100%;
            height: calc(100vh - 75px);
            background-color: #1c1c1c;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            transition: left 0.5s ease;
            padding-top: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.5);
            z-index: 999;
            gap: 0;
            flex: unset;
        }

        .navbar-menu.active {
            left: 0;
        }

        .navbar-item {
            margin: 8px 0;
            width: 90%;
            text-align: center;
        }

        .navbar-link {
            font-size: 16px; /* Font diperkecil */
            padding: 12px 0;
            display: block;
            width: 100%;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            margin-bottom: 5px;
        }
        
        /* Pastikan cart icon tetap terlihat */
        .navbar-cart-icon {
            font-size: 22px;
            padding: 6px;
            min-width: 42px;
            min-height: 42px;
        }
        
        .navbar-cart-count {
            width: 20px;
            height: 20px;
            font-size: 10px;
            top: -3px;
            right: -3px;
        }
    }
    
    /* Perbaikan untuk layar HP kecil (kurang dari 480px) */
    @media (max-width: 480px) {
        .beliyuk-navbar {
            padding: 10px 10px; /* Padding lebih kecil */
        }
        
        .navbar-logo-text {
            font-size: 14px; /* Font lebih kecil */
            max-width: 100px; /* Lebar lebih kecil */
        }
        
        .navbar-logo {
            height: 30px;
            max-height: 30px;
        }
        
        .navbar-cart-icon {
            font-size: 20px;
            padding: 5px;
            min-width: 40px;
            min-height: 40px;
        }
        
        .navbar-menu-toggle {
            padding: 8px;
            margin-right: 5px;
        }
        
        .navbar-menu-toggle span {
            width: 22px;
        }
    }
    
    /* Perbaikan untuk layar sangat kecil (kurang dari 360px) */
    @media (max-width: 360px) {
        .navbar-logo-text {
            display: none; /* Sembunyikan teks logo jika terlalu kecil */
        }
        
        .navbar-logo-container {
            gap: 5px;
        }
    }

    /* Body padding untuk konten */
    body.has-navbar {
        padding-top: 85px;
    }
    
    @media (max-width: 768px) {
        body.has-navbar {
            padding-top: 75px;
        }
    }
    
    @media (max-width: 480px) {
        body.has-navbar {
            padding-top: 65px;
        }
    }
</style>

<!-- Overlay untuk cart modal -->
<div class="cart-overlay" id="cartOverlay"></div>

<!-- Sticky Navigation Bar -->
<nav class="beliyuk-navbar" id="beliyukNavbar">
    <!-- Menu Toggle untuk Mobile -->
    <div class="navbar-menu-toggle" id="navbarMenuToggle">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <!-- Logo dan Nama Brand -->
    <div class="navbar-logo-container">
        <img src="https://imgdst.tomsamcong.com/public/uploads/original/e7/cb/c4d99c2fe0665d5f1a0100552814.webp" 
             alt="beliyuk.shop Logo" 
             class="navbar-logo">
        <div class="navbar-logo-text">beliyuk.shop</div>
    </div>

    <!-- Menu Navigasi -->
    <ul class="navbar-menu" id="navbarMenu">
        <li class="navbar-item">
            <a href="/index.php" class="navbar-link <?php echo ($current_page == 'index.php' || $current_page == '') ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Beranda
            </a>
        </li>
        <li class="navbar-item">
            <a href="/products" class="navbar-link <?php echo $current_page == 'produk.php' ? 'active' : ''; ?>">
                <i class="fas fa-box"></i> Daftar Produk
            </a>
        </li>
        <li class="navbar-item">
            <a href="/kategori.php" class="navbar-link <?php echo $current_page == 'kategori.php' ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i> Kategori
            </a>
        </li>
      
        <li class="navbar-item">
            <a href="/support&contact" class="navbar-link <?php echo $current_page == 'kontak.php' ? 'active' : ''; ?>">
                <i class="fas fa-phone-alt"></i> Kontak
            </a>
        </li>
    </ul>

    <!-- Cart Icon -->
    <div class="navbar-cart-container">
        <div class="navbar-cart-icon" id="navbarCartIcon">
            🛒
            <div class="navbar-cart-count" id="navbarCartCount">3</div>
        </div>
    </div>
</nav>

<script>
    // Fungsi untuk navbar
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.getElementById('beliyukNavbar');
        const menuToggle = document.getElementById('navbarMenuToggle');
        const navMenu = document.getElementById('navbarMenu');
        const body = document.body;
        
        // Tambah class ke body
        body.classList.add('has-navbar');
        
        // Toggle menu mobile
        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                navMenu.classList.toggle('active');
                menuToggle.classList.toggle('active');
            });
        }
        
        // Tutup menu saat klik link
        const navLinks = document.querySelectorAll('.navbar-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                if (menuToggle) menuToggle.classList.remove('active');
            });
        });
        
        // Sticky navbar saat scroll
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Inisialisasi scroll state
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        }
        
        // Update cart count dari localStorage jika ada
        const updateCartCount = () => {
            const cartCountElement = document.getElementById('navbarCartCount');
            if (cartCountElement) {
                try {
                    // Coba ambil dari localStorage
                    const cartItems = JSON.parse(localStorage.getItem('beliyuk_cart')) || [];
                    const totalItems = cartItems.reduce((total, item) => total + (item.quantity || 1), 0);
                    
                    // Update tampilan
                    if (totalItems > 0) {
                        cartCountElement.textContent = totalItems;
                        cartCountElement.style.display = 'flex';
                    } else {
                        cartCountElement.style.display = 'none';
                    }
                } catch (e) {
                    console.error('Error updating cart count:', e);
                }
            }
        };
        
        // Panggil fungsi update cart count
        updateCartCount();
        
        // Event listener untuk cart icon
        const cartIcon = document.getElementById('navbarCartIcon');
        if (cartIcon) {
            cartIcon.addEventListener('click', function() {
                // Redirect ke halaman cart atau tampilkan modal
                window.location.href = '/cart.php';
            });
        }
    });
</script>