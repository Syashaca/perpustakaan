<?php
class StockEmptyException extends Exception {
    public function errorMessage() {
        return "<span style='color:orange'>Warning: Stok buku habis, tidak bisa dipinjam!</span>";
    }
}