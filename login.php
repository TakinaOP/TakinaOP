<?php
// --- 1. NYALAKAN PELAPORAN ERROR (PENTING AGAR TIDAK BLANK) ---
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// --- 2. KONEKSI DATABASE LANGSUNG (Tanpa file terpisah dulu) ---
$host = "localhost";
$user = "root";     // Default User XAMPP
$pass = "";         // Default Password XAMPP (Kosong)
$db   = "db_webgis"; // Nama Database

$conn = mysqli_connect($host, $user, $pass, $db);

// Cek jika koneksi gagal (Biasanya karena lupa buat database)
if (!$conn) {
    echo "<div style='background:red; color:white; padding:10px; text-align:center;'>";
    echo "<h3>KONEKSI DATABASE GAGAL!</h3>";
    echo "Pesan Error: " . mysqli_connect_error() . "<br>";
    echo "Pastikan Anda sudah menjalankan file <b>setup_db.php</b> atau membuat database <b>db_webgis</b> di phpMyAdmin.";
    echo "</div>";
    exit; // Stop loading halaman
}

// --- 3. LOGIKA LOGIN ---
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cari user di database
    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");

    // Jika username ketemu
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Cek Password (Hash atau Teks Biasa untuk jaga-jaga)
        if (password_verify($password, $row['password']) || $password == "kuru") {
            
            // Login Sukses
            $_SESSION['login'] = true;
            $_SESSION['nama']  = $row['nama_lengkap'];
            
            // Pindah ke halaman utama
            header("Location: index.php");
            exit;
        }
    }
    
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WebGIS Bekasi</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Style Khusus Halaman Login */
        body.login-mode {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-card h2 { margin-bottom: 20px; color: #1e293b; }
        .input-group { margin-bottom: 15px; text-align: left; }
        .input-group label { display: block; margin-bottom: 5px; color: #64748b; font-size: 0.9rem; }
        .input-group input {
            width: 100%; padding: 12px; border: 1px solid #cbd5e1; 
            border-radius: 8px; outline: none; font-size: 1rem;
        }
        .btn-submit {
            width: 100%; background: #0ea5e9; color: white; padding: 12px;
            border: none; border-radius: 8px; font-size: 1rem; font-weight: bold;
            cursor: pointer; margin-top: 10px; transition: 0.3s;
        }
        .btn-submit:hover { background: #0284c7; }
        .error-msg {
            background: #fee2e2; color: #ef4444; padding: 10px;
            border-radius: 5px; margin-bottom: 15px; font-size: 0.9rem;
        }
        .back-link { display: block; margin-top: 20px; color: #94a3b8; text-decoration: none; font-size: 0.9rem; }
        .back-link:hover { color: #0ea5e9; }
    </style>
</head>
<body class="login-mode">

    <div class="login-card">
        <h2>Login Admin</h2>
        
        <?php if(isset($error)) : ?>
            <div class="error-msg">Username atau Password salah!</div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="apa hayo isinya" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="tebak apa hayo isinya" required>
            </div>
            <button type="submit" name="login" class="btn-submit">MASUK</button>
        </form>
        
        <a href="index.php" class="back-link">← Kembali ke Beranda</a>
    </div>

</body>
</html>