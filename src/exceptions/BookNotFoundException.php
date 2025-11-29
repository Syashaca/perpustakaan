<?php
class BookNotFoundException extends Exception {
    public function errorMessage() {
        return "<span style='color:red'>Error: Buku tidak ditemukan di database!</span>";
    }
}