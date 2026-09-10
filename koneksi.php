<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "miniprojekdb";


$koneksi = mysqli_connect($hostname, $username, $password, $database);
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}



