<?php
require_once __DIR__ . '/../Abstracts/Person.php';

class Member extends Person {
    public function getRole() {
        return "Anggota Perpustakaan";
    }
    // Tidak ada setting $this->image disini, 
    // jadi otomatis pakai Inisial (Logic dari Parent Class)
}