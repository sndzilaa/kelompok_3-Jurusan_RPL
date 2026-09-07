# SIM-LAB (Sistem Informasi & Penjadwalan Lab)

Aplikasi CRUD sederhana berbasis PHP & MySQL buat ngatur jadwal pemakaian lab komputer di sekolah biar kaga ada jadwal yang bentrok.

---

## Fitur
- **Cek Bentrok:** Otomatis nolak kalau ada ruangan, hari, sama jam yang sama yang mau dibooking 2x.
- **Data Guru:** Tambah, lihat, edit, hapus data guru.
- **Data Kelas:** Kelola data kelas.
- **Jadwal Lab:** Atur jadwal pemakaian lab.

---

## Struktur DB

Pake 3 tabel (`db_jadwal_lab`):
- `guru` (id_guru, nama_guru, mata_pelajaran)
- `kelas` (id_kelas, nama_kelas)
- `jadwal_lab` (id_jadwal, id_guru, id_kelas, ruang_lab, hari, jam_pelajaran)


Link Trello: [https://trello.com/b/rQtQMjyC/project-kelompok-3]
Link ERD: [https://drive.google.com/file/d/1Io-MEyFweqka55LvvPJUswtD_SHXAiQa/view?usp=sharing]