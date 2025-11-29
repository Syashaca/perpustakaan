<?php
require_once 'src/Exceptions/BookNotFoundException.php';
require_once 'src/Exceptions/StockEmptyException.php';
require_once 'src/Entities/Book.php';
require_once 'src/Entities/Category.php';
require_once 'src/Entities/Loan.php';
require_once 'src/Entities/Member.php';
require_once 'src/Entities/Librarian.php';

// Setup Objek
$petugas = new Librarian("Pak Budi");
$anggota = new Member("Ani Siswa");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Perpustakaan OOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="text-center mb-4">
        <h1 class="display-5 fw-bold text-primary"><i class="fas fa-book-reader"></i> Perpustakaan OOP</h1>
        <p class="lead">Simulasi Sistem Manajemen Perpustakaan dengan PHP Native</p>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white"><i class="fas fa-user-shield"></i> Petugas</div>
                <div class="card-body">
                    <h5 class="card-title"><?= $petugas->getName() ?></h5>
                    <p class="card-text"><?= $petugas->getRole() ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white"><i class="fas fa-user"></i> Anggota</div>
                <div class="card-body">
                    <h5 class="card-title"><?= $anggota->getName() ?></h5>
                    <p class="card-text"><?= $anggota->getRole() ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white"><i class="fas fa-history"></i> Log Aktivitas (Demo)</div>
        <div class="card-body bg-white">
            <?php
            try {
                // Skenario Demo
                echo "<ul class='list-group list-group-flush'>";
                
                // 1. Tambah Buku
                $bukuBaru = new Book("PHP OOP Master", "John Doe", 1, 2);
                echo "<li class='list-group-item text-success'><i class='fas fa-check-circle'></i> " . $bukuBaru->save() . "</li>";

                // 2. Pinjam Buku
                $targetBuku = Book::find($bukuBaru->id);
                echo "<li class='list-group-item'><i class='fas fa-exchange-alt'></i> " . Loan::createLoan($anggota, $targetBuku) . "</li>";
                echo "<li class='list-group-item'><i class='fas fa-exchange-alt'></i> " . Loan::createLoan($anggota, $targetBuku) . "</li>";

                // 3. Pinjam Saat Stok Habis (Error)
                echo "<li class='list-group-item list-group-item-danger'><i class='fas fa-exclamation-triangle'></i> <b>Percobaan ke-3:</b> ";
                Loan::createLoan($anggota, $targetBuku); // Error trigger
                echo "</li>";

            } catch (Exception $e) {
                if (method_exists($e, 'errorMessage')) {
                    echo $e->errorMessage();
                } else {
                    echo "Error: " . $e->getMessage();
                }
            }
            echo "</ul>";
            ?>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header"><i class="fas fa-list"></i> Daftar Buku di Database</div>
        <div class="card-body">
            <table class="table table-hover table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Judul Buku</th>
                        <th>Penulis</th>
                        <th>Stok</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $books = Book::getAll();
                    foreach($books as $b): 
                    ?>
                    <tr>
                        <td><?= $b['id'] ?></td>
                        <td><?= $b['title'] ?></td>
                        <td><?= $b['author'] ?></td>
                        <td>
                            <span class="badge <?= $b['stock'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                <?= $b['stock'] ?>
                            </span>
                        </td>
                        <td>
                            <?php if($b['stock'] > 0): ?>
                                <button class="btn btn-sm btn-outline-primary">Pinjam</button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-secondary" disabled>Habis</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="text-center mt-4 mb-5 text-muted">
        <small>&copy; 2025 Sistem Perpustakaan - Tugas Besar OOP</small>
    </div>
</div>

</body>
</html>