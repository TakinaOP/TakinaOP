<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebGIS Dashboard Kota Bekasi</title>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="navbar-top">
        <div class="navbar-brand">
            <i class="fas fa-globe-asia"></i> WebGIS Kota Bekasi
        </div>
        <div class="navbar-menu">
            <a href="#"><i class="fas fa-home"></i> Beranda</a>
            <a href="#" class="btn-signin"><i class="fas fa-user"></i> Sign In</a>
        </div>
    </nav>

    <div id="map-container">
        
        <div id="map"></div>

        <div id="search-wrapper">
            <div class="search-box">
                <input type="text" id="search-input" placeholder="Cari Masjid atau Rumah Sakit..." autocomplete="off">
                <button id="search-btn"><i class="fas fa-search"></i></button>
            </div>
            <ul id="search-results"></ul>
        </div>
        <button id="toggle-btn" onclick="toggleSidebar()" title="Buka/Tutup Legenda">
            <i class="fas fa-layer-group"></i>
        </button>

        <div id="sidebar">
            <div id="sidebar-header">
                <span><i class="fas fa-list"></i> Layer Peta</span>
                <i class="fas fa-times" style="cursor:pointer; color:#999;" onclick="toggleSidebar()"></i>
            </div>

            <div id="sidebar-content">
                <span class="sidebar-subtitle">Wilayah & Akses</span>
                <label class="layer-item">
                    <input type="checkbox" id="chk-kecamatan" checked onchange="toggleLayer('kecamatan')">
                    <span class="color-box" style="background:orange; opacity:0.6;"></span> Kecamatan
                </label>
                <label class="layer-item">
                    <input type="checkbox" id="chk-jalan" checked onchange="toggleLayer('jalan')">
                    <span class="color-box" style="background:#333;"></span> Jalan Raya
                </label>

                <hr class="divider">

                <span class="sidebar-subtitle">Fasilitas Umum</span>
                <label class="layer-item">
                    <input type="checkbox" id="chk-masjid" checked onchange="toggleLayer('masjid')">
                    <img src="https://cdn-icons-png.flaticon.com/512/2319/2319886.png" class="sidebar-img-icon" alt="Masjid">
                    Masjid
                </label>
                <label class="layer-item">
                    <input type="checkbox" id="chk-rs" checked onchange="toggleLayer('rumah sakit')">
                    <img src="https://cdn-icons-png.flaticon.com/512/4006/4006511.png" class="sidebar-img-icon" alt="RS">
                    Rumah Sakit
                </label>
            </div>
        </div>
    </div>

    <footer class="navbar-bottom">
        &copy; 2025 WebGIS Kelurahan Jatirangga - Kota Bekasi
    </footer>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="script.js"></script>

</body>
</html>