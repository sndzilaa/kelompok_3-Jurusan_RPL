<?php
include '../koneksi.php';
if (isset($_GET['id_guru'])) {
    $id_guru = $_GET['id_guru'];
    $query = "DELETE FROM guru WHERE id_guru = '$id_guru'";
    $hapus = mysqli_query($koneksi, $query);

    if ($hapus) {
        header("Location: index_guru.php");
        exit();
    } else {
        echo "Gagal menghapus data guru: " . mysqli_error($koneksi);
    }
} else {
    header("Location: index_guru.php");
    exit();
}
