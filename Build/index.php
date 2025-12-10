<?php
session_start();
// Cek apakah user sudah login
$isLoggedIn = isset($_SESSION['login']);
$namaUser = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Tamu';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebGIS Bekasi - Professional</title>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div id="landing-page">
        
        <nav class="navbar">
            <div class="logo">
                <i class="fas fa-globe-asia"></i> WEBGIS <span>BEKASI</span>
            </div>
            <ul class="nav-links">
                <li><a href="#hero" class="active">Beranda</a></li>
                <li><a href="#infografis">Info Kota</a></li>
                <li><a href="#kontak">Kontak</a></li>
                <li><a href="#" onclick="bukaDashboard()" class="btn-nav-peta">Peta Persebaran</a></li>
            </ul>
            <div class="auth-buttons">
                <a href="#" class="btn-login">Masuk</a>
                <a href="#" class="btn-signup">Daftar</a>
            </div>
        </nav>

        <section id="hero" class="hero">
            <div class="container">
                <div class="hero-content">
                    <span class="tagline">DATA SPASIAL TERINTEGRASI</span>
                    <h1>Peta Persebaran SMA Kota Bekasi</h1>
                    <p>Akses data lokasi sekolah, status akreditasi, dan biodata lengkap secara real-time melalui sistem informasi geografis modern.</p>
                    <button class="btn-signup" onclick="bukaDashboard()">
                        <i class="fas fa-rocket"></i> Jelajahi Peta
                    </button>
                </div>
                <div class="hero-image">
                    <img src="https://www.pngall.com/wp-content/uploads/2016/06/Earth-Free-Download-PNG.png" alt="Digital Earth">
                </div>
            </div>
            <div class="wave-bottom">
                <svg viewBox="0 0 1440 320"><path fill="#ffffff" fill-opacity="1" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,261.3C960,256,1056,224,1152,197.3C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>
            </div>
        </section>

        <section id="infografis" class="info-section">
            <div class="info-container">
                <div class="section-title">
                    <h2>Tentang Kota Bekasi</h2>
                    <p>Kota Patriot dengan perkembangan infrastruktur dan pendidikan yang pesat.</p>
                </div>

                <div class="gallery-grid">
                    <div class="gallery-item" onclick="zoomImage(this)">
                        <img src="summarecon mall bekasi.jpg" alt="Summarecon Mall Bekasi">
                        <div class="overlay"><i class="fas fa-search-plus"></i> <p>Pusat Belanja</p></div>
                    </div>
                    <div class="gallery-item" onclick="zoomImage(this)">
                        <img src="stadion candrabhaga.jpeg" alt="Stadion Patriot">
                        <div class="overlay"><i class="fas fa-search-plus"></i> <p>Stadion Patriot</p></div>
                    </div>
                    <div class="gallery-item" onclick="zoomImage(this)">
                        <img src="grand kamala lagoon.jpg" alt="Grand Kamala Lagoon">
                        <div class="overlay"><i class="fas fa-search-plus"></i> <p>Kawasan Modern</p></div>
                    </div>
                </div>

                <div class="info-stats">
                    <div class="card-stat">
                        <i class="fas fa-school"></i><h3>100+</h3><p>Sekolah Menengah</p>
                    </div>
                    <div class="card-stat">
                        <i class="fas fa-users"></i><h3>2.5Jt</h3><p>Penduduk</p>
                    </div>
                    <div class="card-stat">
                        <i class="fas fa-map-marked"></i><h3>210 km²</h3><p>Luas Wilayah</p>
                    </div>
                </div>
            </div>
        </section>

        <footer id="kontak" class="footer-modern-section">
    <div class="footer-wrapper">
        
        <div class="footer-card">
            <div class="footer-brand">
                <div class="brand-title">
                    <i class="fas fa-globe-asia"></i> WEBGIS <span>BEKASI</span>
                </div>
                <p class="brand-desc">
                    Platform digital untuk memantau persebaran data spasial pendidikan 
                    sekolah menengah atas (SMA) secara interaktif, akurat, dan terintegrasi.
                </p>
            </div>

            <div class="footer-icons">
                <a href="https://wa.me/6281234567890" target="_blank" class="icon-item">
                    <div class="circle-icon wa-bg"><i class="fab fa-whatsapp"></i></div>
                    <span>WhatsApp</span>
                </a>
                
                <a href="https://linkedin.com" target="_blank" class="icon-item">
                    <div class="circle-icon li-bg"><i class="fab fa-linkedin-in"></i></div>
                    <span>LinkedIn</span>
                </a>

                <a href="https://instagram.com" target="_blank" class="icon-item">
                    <div class="circle-icon ig-bg"><i class="fab fa-instagram"></i></div>
                    <span>Instagram</span>
                </a>

                <a href="mailto:info@bekasi.go.id" class="icon-item">
                    <div class="circle-icon mail-bg"><i class="fas fa-envelope"></i></div>
                    <span>Email</span>
                </a>
            </div>
        </div>

        <div class="copyright-pill">
            <p>&copy; 2025 WebGIS SMA Bekasi. Hak Cipta Dilindungi.</p>
            <div class="partner">
                <span>Supported by</span> <i class="fas fa-map"></i> Google Gemini AI
            </div>
        </div>

    </div>
</footer>
    </div>

    <div id="lightbox" class="lightbox" onclick="tutupLightbox()">
        <span class="close-btn">&times;</span>
        <img class="lightbox-content" id="img-zoom">
    </div>

    <div id="dashboard-app" style="display: none;">
        
        <nav class="navbar-dash">
            <div class="logo-dash" onclick="tutupDashboard()">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </div>
            <div class="search-container">
                <input type="text" id="search-input" placeholder="Cari sekolah..." onkeyup="filterList()">
                <i class="fas fa-search"></i>
            </div>
        </nav>

        <div class="main-content">
            
            <div class="sidebar">
                <div class="sidebar-header">
                    <h3>Daftar Sekolah</h3>
                </div>
                <div id="list-container"></div>
            </div>

            <div id="map">
                
                <div id="right-panel" class="right-panel">
                    <div class="panel-header">
                        <h3 id="detail-nama">Nama Sekolah</h3>
                        <button onclick="tutupDetail()" class="close-panel">&times;</button>
                    </div>
                    <div class="panel-body">
                        <div class="detail-item">
                            <span class="label">Alamat</span>
                            <p id="detail-alamat">-</p>
                        </div>
                        <div class="detail-item">
                            <span class="label">Status</span>
                            <p id="detail-status">-</p>
                        </div>
                        <div class="detail-item">
                            <span class="label">Akreditasi</span>
                            <p><span class="badge" id="detail-akreditasi">A</span></p>
                        </div>
                        <div class="detail-item">
                            <span class="label">Kepala Sekolah</span>
                            <p id="detail-kepsek">-</p>
                        </div>
                        <div class="detail-item">
                            <span class="label">Jumlah Siswa</span>
                            <p id="detail-siswa">-</p>
                        </div>
                        <div class="detail-item">
                            <span class="label">Telepon</span>
                            <p id="detail-telp">-</p>
                        </div>
                        <a href="#" class="btn-rute"><i class="fas fa-directions"></i> Lihat Rute</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="script.js"></script>
</body>
</html>

<!-- localhost/webgis_saya/login.php -->
