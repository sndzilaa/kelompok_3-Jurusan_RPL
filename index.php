<?php
// Koneksi ke database
include 'koneksi.php';

// Konversi nama hari bahasa Inggris ke bahasa Indonesia
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

// Status pesan notifikasi (sukses_tambah, sukses_edit, sukses_hapus)
$status = $_GET['status'] ?? '';

// Query data jadwal lab dengan relasi tabel guru dan kelas
$query = "SELECT 
            j.id_jadwal, 
            g.nama_guru, 
            g.mata_pelajaran, 
            k.nama_kelas, 
            j.ruang_lab, 
            j.hari, 
            j.jam_pelajaran 
          FROM jadwal_lab j
          JOIN guru g ON j.id_guru = g.id_guru
          JOIN kelas k ON j.id_kelas = k.id_kelas
          ORDER BY FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), j.jam_pelajaran";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($koneksi));
}

// Olah data untuk statistik
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
    <title>SIM-LAB - Sistem Informasi Jadwal Laboratorium</title>
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Link CSS Eksternal -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="style.css">

    <!-- CSS Bawaan Lengkap -->
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #eef2ff;
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #8b5cf6 100%);
            --success: #10b981;
            --success-light: #ecfdf5;
            --warning: #f59e0b;
            --warning-light: #fffbeb;
            --danger: #ef4444;
            --danger-light: #fef2f2;
            --bg-body: #f8fafc;
            --surface: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.07), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-full: 9999px;
            --transition: all 0.2s ease;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            line-height: 1.6;
            min-height: 100vh;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 260px;
            background: linear-gradient(180deg, rgba(79, 70, 229, 0.09) 0%, rgba(248, 250, 252, 0) 100%);
            pointer-events: none;
            z-index: -1;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px 20px 48px;
        }

        /* Notifikasi Alert */
        .alert-banner {
            padding: 14px 20px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.9rem;
            font-weight: 500;
            animation: fadeIn 0.3s ease;
        }

        .alert-banner.success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-banner.danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-banner.warning {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .close-alert {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: inherit;
            line-height: 1;
            padding: 0 4px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Header */
        .header-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-logo {
            width: 48px;
            height: 48px;
            background: var(--primary-gradient);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.3rem;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        .brand-info h1 {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge-ver {
            font-size: 0.72rem;
            font-weight: 700;
            background: var(--primary-light);
            color: var(--primary);
            padding: 2px 8px;
            border-radius: var(--radius-full);
        }

        .brand-info p {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .today-chip {
            background: var(--surface);
            border: 1px solid var(--border);
            padding: 8px 16px;
            border-radius: var(--radius-full);
            font-size: 0.85rem;
            color: var(--text-muted);
            box-shadow: var(--shadow-sm);
        }

        .today-chip strong {
            color: var(--primary);
        }

        /* Kartu Statistik */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--surface);
            border-radius: var(--radius-md);
            padding: 18px 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stat-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-icon.primary { background: var(--primary-light); color: var(--primary); }
        .stat-icon.success { background: var(--success-light); color: var(--success); }
        .stat-icon.warning { background: var(--warning-light); color: var(--warning); }

        /* Card Utama Tabel */
        .main-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        .card-toolbar {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
        }

        .toolbar-title h2 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .toolbar-title p {
            font-size: 0.825rem;
            color: var(--text-muted);
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-input {
            padding: 9px 14px;
            font-size: 0.875rem;
            font-family: inherit;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--bg-body);
            color: var(--text-main);
            outline: none;
            min-width: 220px;
            transition: var(--transition);
        }

        .search-input:focus {
            border-color: var(--primary);
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        .filter-select {
            padding: 9px 14px;
            font-size: 0.875rem;
            font-family: inherit;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--bg-body);
            color: var(--text-main);
            outline: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-select:focus {
            border-color: var(--primary);
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px 16px;
            font-size: 0.875rem;
            font-weight: 600;
            font-family: inherit;
            border-radius: var(--radius-sm);
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: var(--transition);
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: #ffffff;
            box-shadow: 0 3px 10px rgba(79, 70, 229, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 14px rgba(79, 70, 229, 0.4);
            color: #ffffff;
        }

        .btn-warning {
            background: var(--warning-light);
            color: #b45309;
            border: 1px solid rgba(245, 158, 11, 0.3);
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        .btn-warning:hover {
            background: #fef3c7;
            color: #92400e;
            border-color: #f59e0b;
        }

        .btn-danger {
            background: var(--danger-light);
            color: #b91c1c;
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        .btn-danger:hover {
            background: #fee2e2;
            color: #991b1b;
            border-color: #ef4444;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        /* Table */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .custom-table thead {
            background: #f8fafc;
            border-bottom: 2px solid var(--border);
        }

        .custom-table th {
            padding: 13px 16px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .custom-table tbody tr {
            border-bottom: 1px solid var(--border);
            transition: var(--transition);
        }

        .custom-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .custom-table td {
            padding: 14px 16px;
            font-size: 0.875rem;
            color: var(--text-main);
            vertical-align: middle;
        }

        .custom-table th.text-center,
        .custom-table td.text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            font-size: 0.775rem;
            font-weight: 600;
            border-radius: var(--radius-full);
            background: #f1f5f9;
            color: #475569;
            border: 1px solid var(--border);
            white-space: nowrap;
        }

        .badge-lab {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .badge-class {
            background: #f5f3ff;
            color: #6d28d9;
            border-color: #ddd6fe;
        }

        .badge-day {
            background: #f1f5f9;
            color: #334155;
        }

        .badge-day.active-day {
            background: #ecfdf5;
            color: #065f46;
            border-color: #a7f3d0;
            font-weight: 700;
        }

        .badge-time {
            background: #fffbeb;
            color: #92400e;
            border-color: #fde68a;
        }

        .action-buttons {
            display: inline-flex;
            gap: 6px;
        }

        footer {
            margin-top: 32px;
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-light);
        }

        @media (max-width: 768px) {
            .container { padding: 16px 12px; }
            .header-wrapper, .card-toolbar, .toolbar-actions {
                flex-direction: column;
                align-items: stretch;
            }
            .search-input, .filter-select, .btn-primary {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Notifikasi Status -->
        <?php if ($status === 'sukses_tambah'): ?>
            <div class="alert-banner success">
                <span>✅ <strong>Berhasil!</strong> Jadwal praktikum baru telah berhasil ditambahkan ke database.</span>
                <button class="close-alert" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php elseif ($status === 'sukses_edit'): ?>
            <div class="alert-banner success">
                <span>✅ <strong>Berhasil!</strong> Data jadwal praktikum telah berhasil diperbarui.</span>
                <button class="close-alert" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php elseif ($status === 'sukses_hapus'): ?>
            <div class="alert-banner danger">
                <span>🗑️ <strong>Berhasil!</strong> Jadwal praktikum telah dihapus dari sistem.</span>
                <button class="close-alert" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php elseif ($status === 'not_found'): ?>
            <div class="alert-banner warning">
                <span>⚠️ Data jadwal tidak ditemukan atau telah dihapus sebelumnya.</span>
                <button class="close-alert" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <header class="header-wrapper">
            <div class="brand-section">
                <div class="brand-logo">🧪</div>
                <div class="brand-info">
                    <h1>SIM-LAB <span class="badge-ver">v1.0</span></h1>
                    <p>Sistem Informasi Manajemen Laboratorium RPL & Komputer</p>
                </div>
            </div>
            <div class="header-actions">
                <div class="today-chip">
                    Hari ini: <strong><?= $hari_ini; ?></strong>
                </div>
                <!-- Tombol Tambah di Header Atas -->
                <a href="tambah_jadwal.php" class="btn btn-primary">
                    ➕ Tambah Jadwal Baru
                </a>
            </div>
        </header>

        <!-- Kartu Statistik Ringkas -->
        <section class="stats-grid">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Total Jadwal</div>
                    <div class="stat-value"><?= $total_jadwal; ?></div>
                </div>
                <div class="stat-icon primary">📋</div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-label">Jadwal Hari Ini</div>
                    <div class="stat-value"><?= $total_hari_ini; ?></div>
                </div>
                <div class="stat-icon success">📅</div>
            </div>
            <div class="stat-card">
                <div>
                    <div class="stat-label">Ruang Lab Aktif</div>
                    <div class="stat-value"><?= max($total_lab, 1); ?></div>
                </div>
                <div class="stat-icon warning">🖥️</div>
            </div>
        </section>

        <!-- Kartu Utama Tabel Jadwal -->
        <main class="main-card">
            <div class="card-toolbar">
                <div class="toolbar-title">
                    <h2>Daftar Jadwal Praktikum Lab</h2>
                    <p>Kelola jadwal penggunaan laboratorium sekolah</p>
                </div>
                <div class="toolbar-actions">
                    <!-- Pencarian Cepat -->
                    <input type="text" id="searchInput" class="search-input" placeholder="🔍 Cari guru, mapel, kelas, lab...">
                    
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

                    <!-- Tombol Tambah Jadwal Toolbar -->
                    <a href="tambah_jadwal.php" class="btn btn-primary">
                        ➕ Tambah Jadwal
                    </a>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="table-responsive">
                <table class="custom-table" id="jadwalTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Guru & Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Ruang Lab</th>
                            <th>Hari</th>
                            <th>Jam Pelajaran</th>
                            <th class="text-center" style="width: 160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_jadwal > 0): ?>
                            <?php $no = 1; foreach ($jadwal_list as $row): ?>
                                <tr class="table-row-item">
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['nama_guru']); ?></strong><br>
                                        <small style="color: var(--text-muted);"><?= htmlspecialchars($row['mata_pelajaran']); ?></small>
                                    </td>
                                    <td><span class="badge badge-class"><?= htmlspecialchars($row['nama_kelas']); ?></span></td>
                                    <td><span class="badge badge-lab">🖥️ <?= htmlspecialchars($row['ruang_lab']); ?></span></td>
                                    <td>
                                        <span class="badge badge-day <?= ($row['hari'] === $hari_ini) ? 'active-day' : ''; ?>">
                                            <?= htmlspecialchars($row['hari']); ?>
                                            <?= ($row['hari'] === $hari_ini) ? ' (Hari Ini)' : ''; ?>
                                        </span>
                                    </td>
                                    <td><span class="badge badge-time">⏰ <?= htmlspecialchars($row['jam_pelajaran']); ?></span></td>
                                    <td class="text-center">
                                        <div class="action-buttons">
                                            <!-- Tombol Edit / Update -->
                                            <a href="edit_jadwal.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-warning" title="Edit Jadwal">
                                                ✏️ Edit
                                            </a>
                                            <!-- Tombol Hapus -->
                                            <a href="hapus_jadwal.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-danger" title="Hapus Jadwal" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal <?= htmlspecialchars($row['nama_guru']); ?> di <?= htmlspecialchars($row['ruang_lab']); ?>?');">
                                                🗑️ Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 48px 16px;">
                                    <div style="font-size: 2.5rem; margin-bottom: 8px;">📋</div>
                                    <h3 style="font-size: 1.1rem; color: var(--text-main); margin-bottom: 6px;">Belum Ada Jadwal Praktikum</h3>
                                    <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 16px;">
                                        Database jadwal masih kosong. Silakan tambahkan jadwal pertama Anda sekarang.
                                    </p>
                                    <a href="tambah_jadwal.php" class="btn btn-primary">
                                        ➕ Tambah Jadwal Pertama
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <!-- Footer -->
        <footer>
            <p>&copy; <?= date('Y'); ?> SIM-LAB - Sistem Informasi Manajemen Laboratorium RPL.</p>
        </footer>
    </div>

    <!-- Script Filter dan Pencarian Cepat -->
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

        if (searchInput) searchInput.addEventListener('input', filterTable);
        if (filterHari) filterHari.addEventListener('change', filterTable);
    </script>
</body>
</html>