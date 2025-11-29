<?php
require_once __DIR__ . '/../Abstracts/Person.php';

class Member extends Person {
    public function getRole() {
        return "Anggota Perpustakaan";
    }
}