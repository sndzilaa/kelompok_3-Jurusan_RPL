<?php
include 'koneksi.php';

$daftar_hari = [
    "Sunday" => "Minggu",
    "Monday" => "Senin",
    "Tuesday" => "Selasa",
    "Wednesday" => "Rabu",
    "Thursday" => "Kamis",
    "Friday" => "Jumat",
    "Saturday" => "Sabtu"
];
$hari_ini = $daftar_hari[date("l")];
$tanggal_lengkap = date("d F Y");

$query = "SELECT 
            j.id_jadwal, 
            g.nama_guru, 
            g.mata_pelajaran, 
            k.nama_kelas, 
            j.ruang_lab, 
            j.hari,    
            j.jam_pelajaran 
          FROM jadwal_lab j
          JOIN guru g ON g.id_guru = j.id_guru
          JOIN kelas k ON k.id_kelas = j.id_kelas
          ORDER BY FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'), j.jam_pelajaran";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($koneksi));
}

// Ambil semua data jadwal
$jadwal_list = [];
$total_hari_ini = 0;
$daftar_lab_unik = [];

while ($row = mysqli_fetch_assoc($result)) {
    $jadwal_list[] = $row;
    if ($row['hari'] === $hari_ini) {
        $total_hari_ini++;
    }
    if (!empty($row['ruang_lab'])) {
        $daftar_lab_unik[$row['ruang_lab']] = true;
    }
}
$total_jadwal = count($jadwal_list);
$total_lab = count($daftar_lab_unik);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIM-LAB - Sistem Informasi Manajemen Laboratorium</title>
    <!-- Link file CSS eksternal -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <!-- Header & Navigasi -->
        <header class="header-wrapper">
            <div class="brand-section">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        <circle cx="10" cy="8" r="2"></circle>
                        <path d="m14 13-3-3-3 3"></path>
                    </svg>
                </div>
                <div class="brand-info">
                    <h1>SIM-LAB <span class="badge-ver">v1.0</span></h1>
                    <p>Sistem Informasi Manajemen Laboratorium Komputer & RPL</p>
                </div>
            </div>

            <div class="header-meta">
                <div class="date-badge">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>Hari ini: <strong><?= $hari_ini; ?></strong></span>
                </div>
            </div>
        </header>

        <!-- Kartu Statistik Ringkas -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Jadwal</div>
                    <div class="stat-value"><?= $total_jadwal; ?></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Jadwal Hari Ini (<?= $hari_ini; ?>)</div>
                    <div class="stat-value"><?= $total_hari_ini; ?></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Ruang Lab Aktif</div>
                    <div class="stat-value"><?= max($total_lab, 1); ?></div>
                </div>
            </div>
        </section>

        <!-- Kartu Utama Tabel Jadwal -->
        <main class="main-card">
            <!-- Toolbar: Judul, Pencarian, Filter, dan Tombol Tambah -->
            <div class="card-toolbar">
                <div class="toolbar-title">
                    <h2>Daftar Jadwal Praktikum Lab</h2>
                    <p>Jadwal penggunaan laboratorium sekolah terpadu</p>
                </div>
                <div class="toolbar-actions">
                    <!-- Fitur Pencarian Cepat -->
                    <div class="search-input-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" id="searchInput" class="search-input" placeholder="Cari guru, mapel, kelas, lab...">
                    </div>

                    <!-- Filter Hari -->
                    <select id="filterHari" class="filter-select">
                        <option value="">Semua Hari</option>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                    </select>

                    <!-- Tombol Tambah Jadwal -->
                    <a href="tambah_jadwal.php" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Tambah Jadwal
                    </a>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="table-responsive">
                <table class="custom-table table table-bordered" id="jadwalTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th>Guru & Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Ruang Lab</th>
                            <th>Hari</th>
                            <th>Jam Pelajaran</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_jadwal > 0): ?>
                            <?php $no = 1; foreach ($jadwal_list as $row): ?>
                                <tr class="table-row-item">
                                    <td class="text-center cell-number"><?= $no++; ?></td>
                                    <td>
                                        <div class="teacher-name"><?= htmlspecialchars($row['nama_guru']); ?></div>
                                        <span class="subject-name"><?= htmlspecialchars($row['mata_pelajaran']); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-class">
                                            <?= htmlspecialchars($row['nama_kelas']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-lab">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <rect x="2" y="3" width="20" height="14" rx="2"></rect>
                                                <line x1="8" y1="21" x2="16" y2="21"></line>
                                                <line x1="12" y1="17" x2="12" y2="21"></line>
                                            </svg>
                                            <?= htmlspecialchars($row['ruang_lab']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-day <?= ($row['hari'] === $hari_ini) ? 'active-day' : ''; ?>">
                                            <?= htmlspecialchars($row['hari']); ?>
                                            <?= ($row['hari'] === $hari_ini) ? '• Hari Ini' : ''; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-time">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                            <?= htmlspecialchars($row['jam_pelajaran']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-buttons">
                                            <a href="edit_jadwal.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-warning btn-sm" title="Edit Jadwal">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                                Edit
                                            </a>
                                            <a href="hapus_jadwal.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-danger btn-sm" title="Hapus Jadwal" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                                </svg>
                                                Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="emptyRow">
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                        </div>
                                        <h3>Belum Ada Jadwal</h3>
                                        <p>Tidak ada jadwal laboratorium yang ditemukan dalam database saat ini.</p>
                                        <a href="tambah_jadwal.php" class="btn btn-primary btn-sm">
                                            Tambah Jadwal Pertama
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <!-- Footer -->
        <footer>
            <p>&copy; <?= date('Y'); ?> SIM-LAB. Sistem Informasi Manajemen Laboratorium RPL.</p>
        </footer>
    </div>

    <!-- Script Interaktif: Pencarian & Filter Cepat -->
    <script>
        const searchInput = document.getElementById('searchInput');
        const filterHari = document.getElementById('filterHari');
        const tableRows = document.querySelectorAll('#jadwalTable tbody tr.table-row-item');

        function filterTable() {
            const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
            const selectedDay = (filterHari ? filterHari.value : '').toLowerCase().trim();

            tableRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const matchesSearch = !query || text.includes(query);
                const matchesDay = !selectedDay || text.includes(selectedDay);

                if (matchesSearch && matchesDay) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterTable);
        }
        if (filterHari) {
            filterHari.addEventListener('change', filterTable);
        }
    </script>
</body>
</html>