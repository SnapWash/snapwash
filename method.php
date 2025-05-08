<?php
include ""; // Pastikan koneksi database di-include dengan benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = mysqli_real_escape_string($conn, $_POST["first_name"] ?? '');
    $last_name = mysqli_real_escape_string($conn, $_POST["last_name"] ?? '');
    $no_hp = mysqli_real_escape_string($conn, $_POST["no_hp"] ?? '');
    $password = $_POST["password"] ?? '';
    $role = strtolower(trim(mysqli_real_escape_string($conn, $_POST["role"] ?? '')));

    if (empty($first_name) || empty($last_name) || empty($no_hp) || empty($password) || empty($role)) {
        die("<p style='color: red;'>Semua kolom harus diisi!</p>");
    }

    // Validasi role
    if ($role !== "admin" && $role !== "user") {
        die("<p style='color: red;'>Role harus berupa 'admin' atau 'user'!</p>");
    }

    $nama = $first_name . " " . $last_name;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (nama, password, no_hp, role) VALUES ('$nama', '$hashed_password', '$no_hp', '$role')";

    if (mysqli_query($conn, $sql)) {
        echo "<p style='color: green;'>Registrasi berhasil!</p>";
    } else {
        echo "<p style='color: red;'>Error saat menyimpan data: " . mysqli_error($conn) . "</p>";
    }
}
?>
