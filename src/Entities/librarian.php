<?php
require_once __DIR__ . '/../Abstracts/Person.php';

class Librarian extends Person {
    public function getRole() {
        return "Pustakawan (Admin)";
    }
}