<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "miniprojekdb";


$koneksi = mysqli_connect($hostname, $username, $password, $database);
if (!$koneksi) {
    if (mysqli_connect_errno() === 1049) {
        die("Database '$database' belum tersedia. Import file database.sql melalui phpMyAdmin untuk membuat database dan tabel SIM-LAB.");
    }
    die("Koneksi database gagal: " . mysqli_connect_error());
}

if (!mysqli_set_charset($koneksi, "utf8mb4")) {
    die("Gagal mengatur karakter database: " . mysqli_error($koneksi));
}
