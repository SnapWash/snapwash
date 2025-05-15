<?php
session_start(); // Mulai session

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

// Logout handler
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["first_name"]) && isset($_POST["last_name"]) &&
        trim($_POST["first_name"]) != "" && trim($_POST["last_name"]) != "") {

        // === PROSES REGISTRASI ===
        $first_name = trim(mysqli_real_escape_string($conn, $_POST["first_name"]));
        $last_name  = trim(mysqli_real_escape_string($conn, $_POST["last_name"]));
        $no_hp      = trim(mysqli_real_escape_string($conn, $_POST["no_hp"]));
        $password   = $_POST["password"];  // Password asli

        $checkStmt = mysqli_prepare($conn, "SELECT no_hp FROM users WHERE no_hp = ?");
        if (!$checkStmt) {
            die("<p style='color: red;'>Error pemeriksaan user: " . mysqli_error($conn) . "</p>");
        }
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
        if ($stmt === false) {
            die("<p style='color: red;'>Error persiapan query: " . mysqli_error($conn) . "</p>");
        }

        mysqli_stmt_bind_param($stmt, "ssss", $nama, $hashed_password, $no_hp, $role);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: login.html");
            exit();
        } else {
            echo "<p style='color: red;'>Registrasi gagal: " . mysqli_error($conn) . "</p>";
        }

        mysqli_stmt_close($stmt);

    } else {
        // === PROSES LOGIN ===
        $no_hp    = trim(mysqli_real_escape_string($conn, $_POST["no_hp"]));
        $password = $_POST["password"];

        $stmt = mysqli_prepare($conn, "SELECT id, nama, password FROM users WHERE no_hp = ?");
        if (!$stmt) {
            header("Location: login.html?error=" . urlencode("Error pada query!"));
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $no_hp);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id, $nama, $hashed_password);

        if (mysqli_stmt_fetch($stmt)) {
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $nama;

                $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'index.html';
                unset($_SESSION['redirect_after_login']);
                header("Location: " . $redirect);
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
