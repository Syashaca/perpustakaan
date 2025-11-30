<?php
require_once __DIR__ . '/../../config/Database.php';

class Loan {
    // 1. Method Meminjam (BALIK KE 7 HARI)
    public static function createLoan(Member $member, Book $book) {
        $conn = Database::getInstance();
        try {
            $book->borrow(); 

            // KEMBALI KE 7 HARI
            $tenggat = date('Y-m-d H:i:s', strtotime('+7 days')); 

            $sql = "INSERT INTO loans (member_name, book_title, book_id, loan_date, due_date, status, fine_amount) 
                    VALUES (:m, :b_title, :b_id, NOW(), :due, 'borrowed', 0)";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':m' => $member->getName(),
                ':b_title' => $book->title,
                ':b_id' => $book->id,
                ':due' => $tenggat
            ]);
            
            return "Berhasil pinjam! Wajib kembali tgl " . date('d M Y', strtotime($tenggat));
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function returnBook($loanId) {
        $conn = Database::getInstance();

        $stmt = $conn->prepare("SELECT * FROM loans WHERE id = :id AND status = 'borrowed'");
        $stmt->execute([':id' => $loanId]);
        $loan = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$loan) throw new Exception("Data tidak valid.");

        $tenggat = new DateTime($loan['due_date']);
        $sekarang = new DateTime();
        
        $denda = 0;
        // Logic Denda (Rp 1000/hari)
        if ($sekarang > $tenggat) {
            $selisih = $sekarang->diff($tenggat);
            $hariTelat = $selisih->days;
            $denda = $hariTelat * 1000; 
        }

        $book = Book::find($loan['book_id']); 
        $book->returnItem(); 

        $update = $conn->prepare("UPDATE loans SET status = 'returned', return_date = NOW(), fine_amount = :denda WHERE id = :id");
        $update->execute([':denda' => $denda, ':id' => $loanId]);

        return "Buku dikembalikan!";
    }

    public static function deleteLog($id) {
        $conn = Database::getInstance();
        $stmt = $conn->prepare("DELETE FROM loans WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return "Log dihapus.";
    }

    public static function getActiveLoans() {
        $conn = Database::getInstance();
        $stmt = $conn->query("SELECT * FROM loans WHERE status = 'borrowed' ORDER BY loan_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getHistory() {
        $conn = Database::getInstance();
        $stmt = $conn->query("SELECT * FROM loans WHERE status = 'returned' ORDER BY return_date DESC LIMIT 10");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}