<?php
include '../koneksi.php';
if (isset($_GET['id_jadwal'])) {
    $id_jadwal = $_GET['id_jadwal'];
    $query = "DELETE FROM jadwal_lab WHERE id_jadwal = '$id_jadwal'";
    $hapus = mysqli_query($koneksi, $query);

    if ($hapus) {
        header("Location: jadwal.php");
        exit();
    } else {
        echo "Gagal menghapus data jadwal: " . mysqli_error($koneksi);
    }
} else {
    header("Location: jadwal.php");
    exit();
}
