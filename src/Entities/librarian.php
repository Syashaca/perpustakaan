<?php
require_once __DIR__ . '/../Abstracts/Person.php';

class Librarian extends Person {
    public function __construct($name) {
        parent::__construct($name);
        
        // FOTO KHUSUS PUSTAKAWAN (Admin)
        // Menggunakan gambar stok orang profesional
        $this->image = "https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?auto=format&fit=crop&w=300&q=80";
    }

    public function getRole() {
        return "Pustakawan";
    }
}