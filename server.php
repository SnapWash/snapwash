<?php
ob_start(); // Mulai output buffering

// KONFIGURASI DAN KONEKSI DATABASE
$host     = "127.0.0.1";  
$dbUser   = "root";
$dbPass   = "";
$database = "dbloundry";
$port     = "3307";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = mysqli_connect($host, $dbUser, $dbPass, $database, $port );
if (!$conn) {
    die("<p style='color: red;'>Koneksi gagal: " . mysqli_connect_error() . "</p>");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* 
     * Jika data POST memiliki field 'first_name' dan 'last_name',
     * maka proses ini dianggap sebagai registrasi.
     */
    if (isset($_POST["first_name"]) && isset($_POST["last_name"]) &&
        trim($_POST["first_name"]) != "" && trim($_POST["last_name"]) != "") {

        // === PROSES REGISTRASI ===
        $first_name = trim(mysqli_real_escape_string($conn, $_POST["first_name"]));
        $last_name  = trim(mysqli_real_escape_string($conn, $_POST["last_name"]));
        $no_hp      = trim(mysqli_real_escape_string($conn, $_POST["no_hp"]));
        $password   = $_POST["password"];  // Password asli

        // Cek apakah no_hp sudah terdaftar
        $checkStmt = mysqli_prepare($conn, "SELECT no_hp FROM users WHERE no_hp = ?");
        if (!$checkStmt) {
            die("<p style='color: red;'>Error pemeriksaan user: " . mysqli_error($conn) . "</p>");
        }
        mysqli_stmt_bind_param($checkStmt, "s", $no_hp);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);
        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            // Jika no_hp sudah ada, redirect kembali ke regist.html dengan pesan error
            mysqli_stmt_close($checkStmt);
            header("Location: regist.html?error=" . urlencode("No WA sudah terdaftar!"));
            exit();
        }
        mysqli_stmt_close($checkStmt);

        // Gabungkan first_name dan last_name menjadi kolom 'nama'
        $nama = $first_name . " " . $last_name;

        // Set nilai default role sebagai 'user'
        $role = "user";

        // Hash password untuk keamanan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Siapkan query INSERT menggunakan prepared statement
        $stmt = mysqli_prepare($conn, "INSERT INTO users (nama, password, no_hp, role) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            die("<p style='color: red;'>Error persiapan query: " . mysqli_error($conn) . "</p>");
        }

        mysqli_stmt_bind_param($stmt, "ssss", $nama, $hashed_password, $no_hp, $role);

        if (mysqli_stmt_execute($stmt)) {
            // Registrasi berhasil, redirect ke login.html
            header("Location: login.html");
            exit();
        } else {
            // Jika terjadi error saat registrasi, tampilkan error (opsional)
            echo "<p style='color: red;'>Registrasi gagal: " . mysqli_error($conn) . "</p>";
        }

        mysqli_stmt_close($stmt);

    } else {
        // === PROSES LOGIN ===
        // Form login diasumsikan hanya mengirim field: no_hp dan password
        $no_hp    = trim(mysqli_real_escape_string($conn, $_POST["no_hp"]));
        $password = $_POST["password"];

        // Siapkan query untuk mencari user berdasarkan no_hp
        $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE no_hp = ?");
        if (!$stmt) {
            header("Location: login.html?error=" . urlencode("Error pada query!"));
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $no_hp);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $hashed_password);

        // Jika user ditemukan
        if (mysqli_stmt_fetch($stmt)) {
            // Verifikasi password menggunakan password_verify
            if (password_verify($password, $hashed_password)) {
                // Jika password valid, redirect ke index.html (beranda)
                header("Location: loginSuccess.html");
                exit();
            } else {
                // Jika password salah, redirect kembali ke login.html dengan parameter error
                header("Location: login.html?error=" . urlencode("Password salah!"));
                exit();
            }
        } else {
            // Jika user tidak ditemukan, redirect kembali ke login.html dengan parameter error
            header("Location: login.html?error=" . urlencode("User tidak ditemukan!"));
            exit();
        }

        mysqli_stmt_close($stmt);
    } // end if (proses registrasi vs login)
}

mysqli_close($conn);
ob_end_flush(); // Kirim output buffering
?>
