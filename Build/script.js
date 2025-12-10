// --- 1. DATA SEKOLAH LENGKAP ---
const dataSekolah = {
  "type": "FeatureCollection",
  "features": [
    // NEGERI
    { "type": "Feature", "properties": { "nama": "SMAN 1 Kota Bekasi", "status": "Negeri", "alamat": "Jl. KH. Agus Salim No.181", "akreditasi": "A", "kepsek": "Dra. Ekowati", "siswa": "1200", "telp": "(021) 8802538" }, "geometry": { "type": "Point", "coordinates": [107.0198, -6.2464] } },
    { "type": "Feature", "properties": { "nama": "SMAN 2 Kota Bekasi", "status": "Negeri", "alamat": "Jl. Tangkuban Perahu No.1", "akreditasi": "A", "kepsek": "Dr. Ardin", "siswa": "1150", "telp": "(021) 8841367" }, "geometry": { "type": "Point", "coordinates": [106.9774, -6.2346] } },
    { "type": "Feature", "properties": { "nama": "SMAN 3 Kota Bekasi", "status": "Negeri", "alamat": "Jl. Pulo Ribung, Pekayon", "akreditasi": "A", "kepsek": "Reni Yosefa", "siswa": "1100", "telp": "(021) 8202517" }, "geometry": { "type": "Point", "coordinates": [106.9745, -6.2648] } },
    { "type": "Feature", "properties": { "nama": "SMAN 4 Kota Bekasi", "status": "Negeri", "alamat": "Jl. Cemara Permai", "akreditasi": "A", "kepsek": "Dra. Hj. Sumartini", "siswa": "1050", "telp": "(021) 8841367" }, "geometry": { "type": "Point", "coordinates": [106.9921, -6.2165] } },
    { "type": "Feature", "properties": { "nama": "SMAN 5 Kota Bekasi", "status": "Negeri", "alamat": "Jl. Gamprit", "akreditasi": "A", "kepsek": "Waluyo M.Pd", "siswa": "1180", "telp": "(021) 8460678" }, "geometry": { "type": "Point", "coordinates": [106.9135, -6.2731] } },
    // SWASTA
    { "type": "Feature", "properties": { "nama": "SMA Islam Al-Azhar 4", "status": "Swasta", "alamat": "Jl. Kemang Pratama Raya", "akreditasi": "A", "kepsek": "H. Ngadimin", "siswa": "800", "telp": "(021) 8202027" }, "geometry": { "type": "Point", "coordinates": [106.9733, -6.2688] } },
    { "type": "Feature", "properties": { "nama": "SMA Marsudirini", "status": "Swasta", "alamat": "Jl. Raya Narogong", "akreditasi": "A", "kepsek": "Sr. M. Clementine", "siswa": "850", "telp": "(021) 8202279" }, "geometry": { "type": "Point", "coordinates": [106.9876, -6.2655] } },
    { "type": "Feature", "properties": { "nama": "SMA Penabur Harapan Indah", "status": "Swasta", "alamat": "Jl. Harapan Indah", "akreditasi": "A", "kepsek": "Siwi Tri W", "siswa": "700", "telp": "(021) 88866509" }, "geometry": { "type": "Point", "coordinates": [106.9750, -6.1770] } }
  ]
};

// --- 2. INISIALISASI PETA ---
let map;

function initMap() {
    if (map) return;
    map = L.map('map').setView([-6.2383, 106.9756], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);

    const markers = L.geoJSON(dataSekolah, {
        pointToLayer: function (feature, latlng) {
            let warna = feature.properties.status === "Negeri" ? "#22c55e" : "#3b82f6";
            return L.circleMarker(latlng, { radius: 8, fillColor: warna, color: "#fff", weight: 2, fillOpacity: 0.9 });
        },
        onEachFeature: function (feature, layer) {
            layer.on('click', function() {
                tampilkanDetail(feature.properties);
            });
        }
    }).addTo(map);

    renderList(markers);
}

// --- 3. PANEL BIODATA KANAN ---
function tampilkanDetail(props) {
    document.getElementById('detail-nama').innerText = props.nama;
    document.getElementById('detail-alamat').innerText = props.alamat;
    document.getElementById('detail-status').innerText = props.status;
    document.getElementById('detail-akreditasi').innerText = props.akreditasi;
    document.getElementById('detail-kepsek').innerText = props.kepsek;
    document.getElementById('detail-siswa').innerText = props.siswa + " Siswa";
    document.getElementById('detail-telp').innerText = props.telp;
    
    // Munculkan Panel
    document.getElementById('right-panel').classList.add('active');
}

function tutupDetail() {
    document.getElementById('right-panel').classList.remove('active');
}

// --- 4. RENDER LIST KIRI ---
function renderList(markersLayer) {
    const container = document.getElementById('list-container');
    container.innerHTML = ''; 
    dataSekolah.features.forEach(item => {
        const div = document.createElement('div');
        div.className = 'school-item';
        div.setAttribute('data-name', item.properties.nama.toLowerCase());
        
        let badgeClass = item.properties.status === "Negeri" ? "negeri" : "swasta";
        div.innerHTML = `<h4>${item.properties.nama}</h4><p>${item.properties.alamat}</p><span class="badge ${badgeClass}">${item.properties.status} - Akreditasi ${item.properties.akreditasi}</span>`;
        
        div.onclick = () => {
            markersLayer.eachLayer(layer => {
                if(layer.feature.properties.nama === item.properties.nama) {
                    map.flyTo(layer.getLatLng(), 16);
                    tampilkanDetail(item.properties); // Buka panel kanan juga
                }
            });
        };
        container.appendChild(div);
    });
}

function filterList() {
    const key = document.getElementById('search-input').value.toLowerCase();
    const items = document.querySelectorAll('.school-item');
    items.forEach(el => {
        const name = el.getAttribute('data-name');
        el.style.display = name.includes(key) ? 'block' : 'none';
    });
}

// --- LOGIKA HALAMAN ---
function bukaDashboard() {
    document.getElementById('landing-page').classList.add('hidden');
    document.getElementById('dashboard-app').style.display = 'flex';
    initMap();
    setTimeout(() => { if(map) map.invalidateSize(); }, 200);
}

function tutupDashboard() {
    document.getElementById('dashboard-app').style.display = 'none';
    document.getElementById('landing-page').classList.remove('hidden');
}

// --- ZOOM FOTO ---
function zoomImage(element) {
    document.getElementById('img-zoom').src = element.querySelector('img').src;
    document.getElementById('lightbox').style.display = "flex";
}
function tutupLightbox() {
    document.getElementById('lightbox').style.display = "none";
}