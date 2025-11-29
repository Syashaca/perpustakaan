<?php
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        // Konfigurasi default Laragon
        $host = 'localhost';
        $db   = 'db_perpustakaan';
        $user = 'root';
        $pass = ''; // Password default Laragon biasanya kosong

        try {
            $this->conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Koneksi Error: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance->conn;
    }
}