// script.js

// Variabel Global untuk menyimpan data GeoJSON agar bisa dicari
var allFeatures = [];

// --- A. Inisialisasi Peta ---
var map = L.map('map', { zoomControl: false }).setView([-6.2383, 106.9756], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
    attribution: '© OpenStreetMap contributors' 
}).addTo(map);

// Zoom Control di Kiri Bawah
L.control.zoom({ position: 'bottomleft' }).addTo(map);

// --- B. Wadah Layer ---
var layers = {
    'kecamatan': L.layerGroup().addTo(map),
    'jalan': L.layerGroup().addTo(map),
    'masjid': L.layerGroup().addTo(map),
    'rumah sakit': L.layerGroup().addTo(map),
    'lainnya': L.layerGroup().addTo(map)
};

// --- C. Style & Icon ---
function getStyle(feature) {
    switch(feature.properties.kategori) {
        case 'kecamatan': return { color: "orange", weight: 2, fillOpacity: 0.2 };
        case 'jalan': return { color: "#333", weight: 3 };
        default: return { color: "grey" };
    }
}

function getIcon(kategori) {
    if(kategori === 'masjid') return 'https://cdn-icons-png.flaticon.com/512/2319/2319886.png';
    if(kategori === 'rumah sakit') return 'https://cdn-icons-png.flaticon.com/512/4006/4006511.png';
    return 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png';
}

// --- D. Ambil Data & Simpan untuk Pencarian ---
$.getJSON("data_api.php", function(data) {
    
    // Simpan semua feature ke variabel global untuk fitur search
    allFeatures = data.features;

    L.geoJSON(data, {
        style: getStyle,
        pointToLayer: function(feature, latlng) {
            return L.marker(latlng, {icon: L.icon({
                iconUrl: getIcon(feature.properties.kategori),
                iconSize: [32, 32], iconAnchor: [16, 32], popupAnchor: [0, -28]
            })});
        },
        onEachFeature: function(feature, layer) {
            // Tambahkan referensi layer ke feature agar bisa di-zoom nanti
            feature.layer = layer;

            var iconUrl = getIcon(feature.properties.kategori);
            var content = `<div style="display:flex; align-items:center;">
                            <img src="${iconUrl}" style="width:30px; height:30px; margin-right:10px;">
                            <div>
                                <b>${feature.properties.nama}</b><br>
                                <i style="color:#666; font-size:12px;">${feature.properties.kategori}</i>
                            </div>
                           </div>`;
            layer.bindPopup(content);
            
            var katDB = feature.properties.kategori;
            if (layers[katDB]) layers[katDB].addLayer(layer);
            else layers['lainnya'].addLayer(layer);
        }
    });
});

// --- E. FIX: Logika Checklist ---
function toggleLayer(kategori) {
    var idCheckbox;
    if (kategori === 'rumah sakit') idCheckbox = 'chk-rs';
    else idCheckbox = 'chk-' + kategori;
    
    var checkbox = document.getElementById(idCheckbox);
    if (checkbox) {
        if (checkbox.checked) map.addLayer(layers[kategori]);
        else map.removeLayer(layers[kategori]);
    }
}

// --- F. Toggle Sidebar ---
function toggleSidebar() {
    var sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('hidden');
}

// --- G. LOGIKA PENCARIAN (SEARCH) ---
var searchInput = document.getElementById('search-input');
var searchResults = document.getElementById('search-results');

searchInput.addEventListener('keyup', function(e) {
    var keyword = e.target.value.toLowerCase();
    searchResults.innerHTML = ''; // Bersihkan hasil sebelumnya

    if (keyword.length < 2) {
        searchResults.style.display = 'none';
        return;
    }

    // Filter Data: Hanya ambil 'masjid' atau 'rumahsakit' yang namanya cocok
    var filtered = allFeatures.filter(function(f) {
        var isTargetCategory = (f.properties.kategori === 'masjid' || f.properties.kategori === 'rumah sakit');
        var isNameMatch = f.properties.nama.toLowerCase().includes(keyword);
        return isTargetCategory && isNameMatch;
    });

    if (filtered.length > 0) {
        searchResults.style.display = 'block';
        filtered.forEach(function(f) {
            var li = document.createElement('li');
            li.className = 'result-item';
            
            // Icon
            var iconImg = getIcon(f.properties.kategori);
            
            li.innerHTML = `<img src="${iconImg}"> 
                            <div>
                                <strong>${f.properties.nama}</strong><br>
                                <small>${f.properties.kategori}</small>
                            </div>`;
            
            // Klik Hasil Pencarian
            li.onclick = function() {
                // Zoom ke lokasi
                var latlng;
                if(f.geometry.type === 'Point') {
                    latlng = [f.geometry.coordinates[1], f.geometry.coordinates[0]];
                    map.setView(latlng, 17); // Zoom deket
                    
                    // Buka Popup (jika layer ada)
                    if(f.layer) {
                        // Pastikan layer aktif (checkbox dicentang) agar popup muncul
                        var kat = f.properties.kategori;
                        if(kat === 'rumah sakit') document.getElementById('chk-rs').checked = true;
                        else document.getElementById('chk-' + kat).checked = true;
                        toggleLayer(kat); // Paksa tampilkan layer

                        f.layer.openPopup();
                    }
                }
                searchResults.style.display = 'none';
                searchInput.value = f.properties.nama;
            };

            searchResults.appendChild(li);
        });
    } else {
        searchResults.style.display = 'none';
    }
});