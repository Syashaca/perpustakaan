<?php
// --- MANUAL INCLUDE (Urutan Penting) ---
require_once 'src/Exceptions/BookNotFoundException.php';
require_once 'src/Exceptions/StockEmptyException.php';
require_once 'src/Entities/Book.php';
require_once 'src/Entities/Category.php';
require_once 'src/Entities/Loan.php';
require_once 'src/Entities/Member.php';
require_once 'src/Entities/Librarian.php';

echo "<h1>Sistem Perpustakaan OOP (Demo)</h1>";
echo "<hr>";

try {
    // 1. Instansiasi Class (Inheritance)
    $petugas = new Librarian("Pak Budi");
    $anggota = new Member("Ani Siswa");

    echo "Login sebagai: " . $petugas->getRole() . " (" . $petugas->getName() . ")<br>";
    echo "Login sebagai: " . $anggota->getRole() . " (" . $anggota->getName() . ")<br><br>";

    // 2. CREATE Data Buku Baru
    echo "<b>[Action] Menambah Buku Baru...</b><br>";
    $bukuBaru = new Book("PHP Object Oriented", "John Doe", 1, 2); // Stok cuma 2
    echo $bukuBaru->save() . "<br><br>";

    // 3. READ Data
    echo "<b>[Data] Daftar Buku:</b><br>";
    $books = Book::getAll();
    foreach($books as $b) {
        echo "- {$b['title']} (Stok: {$b['stock']})<br>";
    }
    echo "<br>";

    // 4. Proses Peminjaman (Exception Handling)
    // Kita ambil buku ID terakhir yang baru dibuat
    $targetBuku = Book::find($bukuBaru->id);

    echo "<b>[Action] Transaksi Peminjaman:</b><br>";
    
    // Pinjam 1
    echo "1. " . Loan::createLoan($anggota, $targetBuku) . "<br>";
    // Pinjam 2
    echo "2. " . Loan::createLoan($anggota, $targetBuku) . "<br>";
    
    // Pinjam 3 (Harusnya Error karena stok habis)
    echo "3. Mencoba meminjam lagi...<br>";
    echo Loan::createLoan($anggota, $targetBuku); // Ini akan memicu Exception

} catch (StockEmptyException $e) {
    echo $e->errorMessage();
} catch (BookNotFoundException $e) {
    echo $e->errorMessage();
} catch (Exception $e) {
    echo "System Error: " . $e->getMessage();
}
?>