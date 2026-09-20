<?php
include '../koneksi.php';
include '../auth.php';

require_guru('../');

if (isset($_GET['id_guru'])) {
    $id_guru = intval($_GET['id_guru']);
    $query = "DELETE FROM guru WHERE id_guru = $id_guru";
    $hapus = mysqli_query($koneksi, $query);

    if ($hapus) {
        header("Location: index_guru.php?pesan=berhasil_hapus");
        exit();
    } else {
        echo "Gagal menghapus data guru: " . mysqli_error($koneksi);
    }
} else {
    header("Location: index_guru.php");
    exit();
}
