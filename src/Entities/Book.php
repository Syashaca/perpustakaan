<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Interfaces/IPersistable.php';
require_once __DIR__ . '/../Interfaces/ILoanable.php';
require_once __DIR__ . '/../Exceptions/StockEmptyException.php';
require_once __DIR__ . '/../Exceptions/BookNotFoundException.php';

class Book implements IPersistable, ILoanable {
    private $conn;
    public $id;
    public $title;
    public $author;
    public $category_id;
    public $stock;

    public function __construct($title = "", $author = "", $cat = 1, $stock = 0) {
        $this->conn = Database::getInstance();
        $this->title = $title;
        $this->author = $author;
        $this->category_id = $cat;
        $this->stock = $stock;
    }

    // --- IMPLEMENTASI INTERFACE IPersistable ---
    public function save() {
        $sql = "INSERT INTO books (title, author, category_id, stock) VALUES (:t, :a, :c, :s)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':t' => $this->title, ':a' => $this->author, ':c' => $this->category_id, ':s' => $this->stock
        ]);
        $this->id = $this->conn->lastInsertId();
        return "Buku '{$this->title}' berhasil disimpan (ID: {$this->id})";
    }

    // [UPDATED] Method ini sekarang mengambil nama kategori juga
    public static function getAll() {
        $conn = Database::getInstance();
        $sql = "SELECT b.*, c.name as category_name 
                FROM books b
                LEFT JOIN categories c ON b.category_id = c.id
                ORDER BY b.title ASC";
        $stmt = $conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM books WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Method Static untuk mencari buku
    public static function find($id) {
        $conn = Database::getInstance();
        $stmt = $conn->prepare("SELECT * FROM books WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$data) throw new BookNotFoundException(); 

        $book = new Book($data['title'], $data['author'], $data['category_id'], $data['stock']);
        $book->id = $data['id'];
        return $book;
    }

    // --- IMPLEMENTASI INTERFACE ILoanable ---
    public function borrow() {
        if ($this->stock <= 0) {
            throw new StockEmptyException();
        }
        $this->stock--;
        $stmt = $this->conn->prepare("UPDATE books SET stock = :s WHERE id = :id");
        $stmt->execute([':s' => $this->stock, ':id' => $this->id]);
    }

    public function returnItem() {
        $this->stock++;
        $stmt = $this->conn->prepare("UPDATE books SET stock = :s WHERE id = :id");
        $stmt->execute([':s' => $this->stock, ':id' => $this->id]);
    }
}