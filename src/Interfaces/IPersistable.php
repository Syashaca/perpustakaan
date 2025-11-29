<?php
interface IPersistable {
    public function save();
    public function delete($id);
    public static function getAll();
}