<?php
// data_api.php
header('Content-Type: application/json');
include 'koneksi.php'; // Pastikan file koneksi.php juga sudah ada

$geojson = array('type' => 'FeatureCollection', 'features' => array());
$query = mysqli_query($conn, "SELECT * FROM objek_petas");

while($row = mysqli_fetch_assoc($query)) {
    $feature = array(
        'type' => 'Feature', 
        'geometry' => json_decode($row['lokasi_geo']),
        'properties' => array(
            'nama' => $row['nama_objek'],
            'kategori' => $row['kategori'],
            'deskripsi' => $row['deskripsi'],
            'warna' => $row['warna']
        )
    );
    array_push($geojson['features'], $feature);
}
echo json_encode($geojson);
?>