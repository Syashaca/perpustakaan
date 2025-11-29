<?php
require_once __DIR__ . '/../../config/Database.php';

class Loan {
    public static function createLoan(Member $member, Book $book) {
        $conn = Database::getInstance();
        try {
            // Proses logika buku
            $book->borrow(); 

            // Catat log
            $sql = "INSERT INTO loans (member_name, book_title) VALUES (:m, :b)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':m' => $member->getName(), ':b' => $book->title]);
            
            return "Sukses: " . $member->getName() . " meminjam " . $book->title;
        } catch (Exception $e) {
            throw $e; // Lempar ke main program
        }
    }
}