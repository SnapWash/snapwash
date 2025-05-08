<?php
ob_start();

require_once __DIR__ . '/vendor/autoload.php'; // pastikan path ini benar

use Dotenv\Dotenv;

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Ambil konfigurasi database dari .env
$host     = $_ENV['DB_HOST'];
$dbUser   = $_ENV['DB_USER'];
$dbPass   = $_ENV['DB_PASS'];
$database = $_ENV['DB_NAME'];

// Konfigurasi dan koneksi database
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = mysqli_connect($host, $dbUser, $dbPass, $database);
if (!$conn) {
    die("<p style='color: red;'>Koneksi gagal: " . mysqli_connect_error() . "</p>");
}

// Cek apakah request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["first_name"]) && isset($_POST["last_name"]) &&
        trim($_POST["first_name"]) != "" && trim($_POST["last_name"]) != "") {

        // === REGISTRASI ===
        $first_name = trim(mysqli_real_escape_string($conn, $_POST["first_name"]));
        $last_name  = trim(mysqli_real_escape_string($conn, $_POST["last_name"]));
        $no_hp      = trim(mysqli_real_escape_string($conn, $_POST["no_hp"]));
        $password   = $_POST["password"];

        $checkStmt = mysqli_prepare($conn, "SELECT no_hp FROM users WHERE no_hp = ?");
        mysqli_stmt_bind_param($checkStmt, "s", $no_hp);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);

        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            mysqli_stmt_close($checkStmt);
            header("Location: regist.html?error=" . urlencode("No WA sudah terdaftar!"));
            exit();
        }
        mysqli_stmt_close($checkStmt);

        $nama = $first_name . " " . $last_name;
        $role = "user";
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "INSERT INTO users (nama, password, no_hp, role) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $nama, $hashed_password, $no_hp, $role);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: login.html");
            exit();
        } else {
            echo "<p style='color: red;'>Registrasi gagal: " . mysqli_error($conn) . "</p>";
        }

        mysqli_stmt_close($stmt);

    } else {
        // === LOGIN ===
        $no_hp    = trim(mysqli_real_escape_string($conn, $_POST["no_hp"]));
        $password = $_POST["password"];

        $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE no_hp = ?");
        mysqli_stmt_bind_param($stmt, "s", $no_hp);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $hashed_password);

        if (mysqli_stmt_fetch($stmt)) {
            if (password_verify($password, $hashed_password)) {
                header("Location: loginSuccess.html");
                exit();
            } else {
                header("Location: login.html?error=" . urlencode("Password salah!"));
                exit();
            }
        } else {
            header("Location: login.html?error=" . urlencode("User tidak ditemukan!"));
            exit();
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
ob_end_flush();
?>
