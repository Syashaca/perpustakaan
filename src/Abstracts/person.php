<?php
abstract class Person {
    protected $name;
    protected $image; // <--- INI YANG KURANG (Wajib ditambah)

    public function __construct($name) {
        $this->name = $name;
        // Default gambar jika tidak diset
        $this->image = "https://ui-avatars.com/api/?name=" . urlencode($name) . "&background=random";
    }

    abstract public function getRole();

    public function getName() {
        return $this->name;
    }

    // Method untuk mengambil gambar
    public function getImage() {
        return $this->image;
    }
}