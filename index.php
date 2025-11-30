<?php
// --- SETUP & LOGIC ---
require_once 'src/Exceptions/BookNotFoundException.php';
require_once 'src/Exceptions/StockEmptyException.php';
require_once 'src/Entities/Book.php';
require_once 'src/Entities/Category.php';
require_once 'src/Entities/Loan.php';
require_once 'src/Entities/Member.php';
require_once 'src/Entities/Librarian.php';

$petugas = new Librarian("Glowmist");
$pesan = ""; $error = "";

function getUcapan() {
    $jam = date('G');
    if ($jam >= 5 && $jam < 11) return ["Selamat Pagi", "🌤️"];
    if ($jam >= 11 && $jam < 15) return ["Selamat Siang", "☀️"];
    if ($jam >= 15 && $jam < 18) return ["Selamat Sore", "🌥️"];
    return ["Selamat Malam", "🌙"];
}
list($ucapan, $emoji) = getUcapan();
$namaDepan = explode(' ', $petugas->getName())[0];

// --- HANDLE REQUEST ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (isset($_POST['action']) && $_POST['action'] == 'pinjam') {
            $peminjam = new Member($_POST['member_name']);
            $book = Book::find($_POST['book_id']);
            $pesan = Loan::createLoan($peminjam, $book);
        }
        if (isset($_POST['action']) && $_POST['action'] == 'kembali') {
            $pesan = Loan::returnBook($_POST['loan_id']);
        }
        if (isset($_POST['action']) && $_POST['action'] == 'hapus_history') {
            $pesan = Loan::deleteLog($_POST['loan_id']);
        }
    } catch (Exception $e) {
        $error = method_exists($e, 'errorMessage') ? $e->errorMessage() : $e->getMessage();
    }
}

// Ambil data
$books = Book::getAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* CSS BACKGROUND UTAMA */
        body {
            background: url('https://dynamic-media-cdn.tripadvisor.com/media/photo-o/1a/2d/67/44/photo2jpg.jpg?w=1100&h=-1&s=1') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Container Utama (TRANSPARAN / TIDAK ADA KOTAK PUTIH LAGI) */
        .container-main { 
            max-width: 1200px; 
            margin: 30px auto; 
            /* Background dihapus/transparan */
            background-color: transparent; 
            /* Shadow dan border radius dihapus */
            box-shadow: none;
            border-radius: 0;
            /* Padding disesuaikan */
            padding: 0 15px;
            min-height: 85vh;
        }
        
        /* HEADER GLASS */
        .header-dashboard {
            background: rgba(194, 234, 255, 0.85); /* Tetap pakai background agar tulisan terbaca */
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(163, 223, 255, 0.5);
            padding: 1.5rem 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 30px rgba(0, 112, 249, 0.1);
            margin-bottom: 2rem;
        }

        .profil-pill {
            background-color: #b3d6f9ff;
            border: 1px solid #9fcaffff;
        }
        .text-purple { color: #0d6efd; }

        /* STYLE TABEL & KARTU */
        /* Kartu tetap putih agar kontennya jelas */
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); background: rgba(255,255,255,0.95); }
        .card-header { background: white; border-bottom: 1px solid #f0f0f0; padding: 1.2rem; font-weight: 700; color: #333; border-radius: 12px 12px 0 0 !important; }

        .table-custom thead th {
            text-transform: uppercase; font-size: 0.75rem; font-weight: 700; color: #6c757d; letter-spacing: 0.5px;
            border-bottom: 1px solid #eee; padding: 1rem 1.5rem;
        }
        .table-custom tbody td { vertical-align: middle; padding: 1rem 1.5rem; border-bottom: 1px solid #f9f9f9; }
        
        .book-title { font-weight: 700; color: #2d3748; font-size: 1rem; display: block; margin-bottom: 2px; }
        .book-author { color: #718096; font-size: 0.85rem; display: block; }
        
        /* Badge Kategori/Genre */
        .badge-kategori {
            background-color: #e0e7ff; color: #4338ca; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 0.8rem; display: inline-block;
        }
        
        /* Indikator Stok */
        .stock-wrapper { display: flex; align-items: center; font-weight: 600; color: #4a5568; }
        .dot-stock { height: 10px; width: 10px; background-color: #48bb78; border-radius: 50%; margin-right: 8px; }
        .dot-empty { background-color: #f56565; }
    </style>
</head>
<body class="bg-light">

<div class="container-main">

    <div class="header-dashboard d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold text-dark mb-1"><?= $ucapan ?>, <?= $namaDepan ?>! <?= $emoji ?></h2>
            <p class="text-muted mb-0">Semangat menjaga perpustakaan hari ini.</p>
        </div>
        <div class="profil-pill rounded-pill p-2 pe-4 d-flex align-items-center shadow-sm">
            <img src="<?= $petugas->getImage() ?>" class="rounded-circle me-3 border border-2 border-white" width="45" height="45" style="object-fit: cover;">
            <div>
                <h6 class="fw-bold text-dark mb-0"><?= $petugas->getName() ?></h6>
                <small class="text-purple fw-bold" style="font-size: 0.7rem;">PUSTAKAWAN UTAMA</small>
            </div>
        </div>
    </div>

    <?php if ($pesan) { ?><div class="alert alert-success alert-dismissible fade show shadow-sm border-0"><i class="fas fa-check-circle me-2"></i> <?= $pesan ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php } ?>
    <?php if ($error) { ?><div class="alert alert-danger alert-dismissible fade show shadow-sm border-0"><i class="fas fa-exclamation-triangle me-2"></i> <?= $error ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php } ?>

    <div class="row mb-4">
        <div class="col-md-5">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold small"><i class="fas fa-paper-plane me-2"></i> Form Peminjaman Cepat</div>
                <div class="card-body p-4">
                    <form method="POST" id="formPinjam">
                        <input type="hidden" name="action" value="pinjam">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Nama Peminjam</label>
                            <select name="member_name" class="form-select py-2 bg-light border-0 fw-bold" required>
                                <option value="Ahmad Dhani">Ahmad Dhani</option>
                                <option value="Siti Nurhaliza">Siti Nurhaliza</option>
                                <option value="Budi Santoso">Budi Santoso</option>
                                <option value="Faizah Salsabila">Faizah Salsabila</option>
                                <option value="Nur Kaedah">Nur Kaedah</option>
                                <option value="Indah Lestari">Indah Lestari</option>
                                <option value="Muhammad Fadli">Muhammad Fadli</option>
                                <option value="Maulana Azis">Maulana Azis</option>
                                <option value="Dinda Maeva">Dinda Maeva</option>
                                <option value="Muhammad Abar">Muhammad Abar</option>
                                <option value="Zayn Malik">Zayn Malik</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Buku</label>
                            <select name="book_id" id="selectBuku" class="form-select py-2 bg-light border-0 fw-bold" required>
                                <option value="" disabled selected>-- Pilih Buku --</option>
                                <?php foreach($books as $b) { $disabled = ($b['stock'] <= 0) ? 'disabled' : ''; ?>
                                <option value="<?= $b['id'] ?>" <?= $disabled ?>><?= $b['title'] ?> (Stok: <?= $b['stock'] ?>)</option>
                                <?php } ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm">PROSES PINJAMAN</button>
                    </form>
                </div>
            </div>
        </div>

<div class="col-md-7">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold small py-2">
                    <i class="fas fa-clock me-2"></i> Peminjaman Aktif
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-primary text-uppercase">
                                <tr>
                                    <th class="ps-3 py-2">Peminjam</th>
                                    <th class="py-2">Buku</th>
                                    <th class="py-2">Tanggal Peminjaman</th>
                                    <th class="text-center py-2" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $loans = Loan::getActiveLoans();
                                if (empty($loans)) { 
                                    echo "<tr><td colspan='4' class='text-center p-3 text-muted small'>Tidak ada data aktif.</td></tr>"; 
                                } else { 
                                    foreach($loans as $l) { 
                                        $isLate = (strtotime(date('Y-m-d')) > strtotime($l['due_date']));
                                        $tglPinjam = date('d M', strtotime($l['loan_date']));  // Format: 29 Nov
                                        $tglKembali = date('d M Y', strtotime($l['due_date'])); // Format: 06 Dec
                                ?>
                                    <tr>
                                        <td class="ps-3 fw-bold text-dark"><?= $l['member_name'] ?></td>
                                        
                                        <td style="max-width: 150px;" class="text-truncate" title="<?= $l['book_title'] ?>">
                                            <?= $l['book_title'] ?>
                                        </td>
                                        
                                        <td class="text-muted">
                                            <?= $tglPinjam ?> - <?= $tglKembali ?>
                                            <?php if($isLate) { ?> <i class="fas fa-exclamation-circle text-danger ms-1" title="Telat"></i> <?php } ?>
                                        </td>
                                        
                                        <td class="text-center">
                                            <form method="POST">
                                                <input type="hidden" name="action" value="kembali">
                                                <input type="hidden" name="loan_id" value="<?= $l['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;">
                                                    Kembali
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php }} ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    <hr><div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom-0 pt-4 px-4 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-3">
                            <i class="fas fa-layer-group fs-5"></i>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark">Koleksi Buku</h5>
                    </div>
                </div>

                <div class="card-body p-0 table-responsive">
                    <table class="table table-custom mb-0">
                        <thead class="bg-primary"><tr><th class="ps-4">INFORMASI BUKU</th><th>KETERSEDIAAN</th><th class="text-end pe-4">GENRE</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach($books as $b): $stokHabis = $b['stock'] <= 0; ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <span class="book-title"><?= $b['title'] ?></span>
                                            <span class="book-author"><?= $b['author'] ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="stock-wrapper">
                                        <span class="dot-stock <?= $stokHabis ? 'dot-empty' : '' ?>"></span>
                                        <?= $b['stock'] ?> pcs
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="badge-kategori">
                                        <?= $b['category_name'] ?? 'Umum' ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold small"><i class="fas fa-history me-2"></i> Riwayat Pengembalian</div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-primary"><tr><th class="ps-4">Nama</th><th>Buku</th><th>Denda</th><th class="text-end pe-4">Hapus</th></tr></thead>
                        <tbody>
                        <?php $history = Loan::getHistory(); foreach($history as $h) { $bgDenda = ($h['fine_amount'] > 0) ? "text-danger fw-bold" : "text-success"; ?>
                            <tr>
                                <td class="ps-4"><?= $h['member_name'] ?></td><td><?= $h['book_title'] ?></td>
                                <td class="<?= $bgDenda ?>">Rp <?= number_format($h['fine_amount'], 0, ',', '.') ?></td>
                                <td class="text-end pe-4"><form method="POST" onsubmit="return confirm('Hapus?');"><input type="hidden" name="action" value="hapus_history"><input type="hidden" name="loan_id" value="<?= $h['id'] ?>"><button class="btn btn-sm text-danger"><i class="fas fa-trash"></i></button></form></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-5 mb-4 text-white small" style="text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">&copy; 2025 Sistem Perpustakaan Modern</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>