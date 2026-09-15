<?php
include '../koneksi.php';
if (isset($_GET['id_kelas'])) {
    $id_kelas = $_GET['id_kelas'];
    $query = "DELETE FROM kelas WHERE id_kelas = '$id_kelas'";
    $hapus = mysqli_query($koneksi, $query);

    if ($hapus) {
        header("Location: ../index.php");
        exit();
    } else {
        echo "Gagal menghapus data kelas: " . mysqli_error($koneksi);
    }
} else {
    header("Location: ../index.php");
    exit();
}
