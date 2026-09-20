<?php
include '../koneksi.php';
include '../auth.php';

require_guru('../');

if (isset($_GET['id_kelas'])) {
    $id_kelas = intval($_GET['id_kelas']);
    $query = "DELETE FROM kelas WHERE id_kelas = $id_kelas";
    $hapus = mysqli_query($koneksi, $query);

    if ($hapus) {
        header("Location: index_kelas.php?pesan=berhasil_hapus");
        exit();
    } else {
        echo "Gagal menghapus data kelas: " . mysqli_error($koneksi);
    }
} else {
    header("Location: index_kelas.php");
    exit();
}
