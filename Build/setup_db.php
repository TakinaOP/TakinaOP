<?php
// File: setup_db.php
$host = "localhost";
$user = "root";
$pass = ""; // Default XAMPP kosong
$db   = "db_webgis";

// 1. Koneksi ke MySQL
$conn = mysqli_connect($host, $user, $pass);
if (!$conn) { die("Koneksi Server Gagal: " . mysqli_connect_error()); }

// 2. Buat Database jika belum ada
$sql = "CREATE DATABASE IF NOT EXISTS $db";
if (mysqli_query($conn, $sql)) {
    echo "Database OK.<br>";
} else {
    echo "Gagal buat DB: " . mysqli_error($conn) . "<br>";
}

// 3. Pilih Database
mysqli_select_db($conn, $db);

// 4. Buat Tabel Users (Hapus dulu jika ada biar bersih)
mysqli_query($conn, "DROP TABLE IF EXISTS users");
$sqlTable = "CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100)
)";

if (mysqli_query($conn, $sqlTable)) {
    echo "Tabel Users Siap.<br>";
} else {
    echo "Gagal buat Tabel: " . mysqli_error($conn) . "<br>";
}

// 5. Masukkan Akun Admin (Password: admin123)
// Kita generate Hash ASLI agar password_verify nanti SUKSES
$passAsli = "admin123";
$passHash = password_hash($passAsli, PASSWORD_DEFAULT);

$sqlInsert = "INSERT INTO users (username, password, nama_lengkap) 
              VALUES ('admin', '$passHash', 'Administrator')";

if (mysqli_query($conn, $sqlInsert)) {
    echo "<hr><h3>SUKSES! ✅</h3>";
    echo "Akun berhasil dibuat.<br>";
    echo "Username: <b>admin</b><br>";
    echo "Password: <b>admin123</b><br><br>";
    echo "<a href='login.php'>Klik disini untuk LOGIN</a>";
} else {
    echo "Gagal input user: " . mysqli_error($conn);
}
?>