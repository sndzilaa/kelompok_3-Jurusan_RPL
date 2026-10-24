# SIM-LAB (Sistem Informasi & Penjadwalan Lab)

Aplikasi CRUD sederhana berbasis PHP & MySQL buat ngatur jadwal pemakaian lab komputer di sekolah biar kaga ada jadwal yang bentrok.

---

## Fitur
- **Cek Bentrok:** Otomatis nolak kalau ada ruangan, hari, sama jam yang sama yang mau dibooking 2x.
- **Data Guru:** Tambah, lihat, edit, hapus data guru.
- **Data Kelas:** Kelola data kelas.
- **Jadwal Lab:** Atur jadwal pemakaian lab.
- **Durasi Lab:** Lihat hitung mundur waktu mulai/selesai penggunaan setiap lab.

Untuk membuat database dan tabel, import file `database.sql` melalui menu **Import** di phpMyAdmin. Skrip tersebut membuat database `miniprojekdb`, membuat tabel yang belum ada, dan menambahkan kolom waktu pada tabel jadwal lama tanpa menghapus data. Pastikan konfigurasi di `koneksi.php` sesuai dengan server MySQL Anda.

Jadwal lama akan tetap tersimpan, tetapi waktu aktualnya perlu diisi melalui menu edit jadwal.

---

## Struktur DB

Pakai database `miniprojekdb` dengan tabel:
- `guru` (id_guru, nama_guru, mata_pelajaran)
- `kelas` (id_kelas, nama_kelas)
- `jadwal_lab` (id_jadwal, id_guru, id_kelas, ruang_lab, hari, jam pelajaran, waktu mulai, waktu selesai)


Link Trello: [https://trello.com/b/rQtQMjyC/project-kelompok-3]
Link ERD: [https://drive.google.com/file/d/1Io-MEyFweqka55LvvPJUswtD_SHXAiQa/view?usp=sharing]