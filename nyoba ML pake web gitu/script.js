// 1. Inisialisasi Peta (Center di Bandung)
var map = L.map('map').setView([-6.9175, 107.6191], 8);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// 2. Fungsi Ambil Data Persebaran dari Flask
fetch('/api/data')
    .then(response => response.json())
    .then(data => {
        data.forEach(point => {
            // Tentukan warna berdasarkan kategori
            var color = 'green';
            if(point.kategori === 'Sedang') color = 'orange';
            if(point.kategori === 'Lebat') color = 'red';

            // Tambahkan lingkaran ke peta
            L.circleMarker([point.lat, point.lon], {
                radius: 6,
                fillColor: color,
                color: "#000",
                weight: 1,
                opacity: 1,
                fillOpacity: 0.8
            }).bindPopup(`
                <b>Kategori: ${point.kategori}</b><br>
                Suhu: ${point.suhu.toFixed(1)}°C<br>
                Lembab: ${point.lembab.toFixed(1)}%
            `).addTo(map);
        });
    });

// 3. Handle Klik pada Peta untuk auto-fill koordinat
map.on('click', function(e) {
    document.getElementById('lat').value = e.latlng.lat.toFixed(4);
    document.getElementById('lon').value = e.latlng.lng.toFixed(4);
});

// 4. Handle Submit Form Prediksi
document.getElementById('predForm').addEventListener('submit', function(e){
    e.preventDefault();
    
    // Ambil data dari form
    var data = {
        lat: document.getElementById('lat').value,
        lon: document.getElementById('lon').value,
        suhu: document.getElementById('suhu').value,
        lembab: document.getElementById('lembab').value
    };

    // Kirim ke Backend Flask
    fetch('/predict', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        // Tampilkan Hasil
        var resDiv = document.getElementById('resultSection');
        var textHasil = document.getElementById('textHasil');
        var listProb = document.getElementById('listProb');
        
        resDiv.classList.remove('d-none');
        textHasil.innerText = "Prediksi: Hujan " + result.hasil;
        
        // Warna hasil
        if(result.hasil === 'Lebat') textHasil.style.color = 'red';
        else if(result.hasil === 'Sedang') textHasil.style.color = 'orange';
        else textHasil.style.color = 'green';

        // Tampilkan Probabilitas
        listProb.innerHTML = '';
        for (var key in result.probabilitas) {
            var li = document.createElement('li');
            li.innerText = `${key}: ${result.probabilitas[key]}%`;
            listProb.appendChild(li);
        }
    });
});