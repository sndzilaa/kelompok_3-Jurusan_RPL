<?php
include 'koneksi.php';

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM jadwal_lab WHERE id_jadwal = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php?status=sukses_hapus");
        exit;
    } else {
        header("Location: index.php?status=error_hapus");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
