<?php
abstract class Person {
    protected $name;
    
    public function __construct($name) {
        $this->name = $name;
    }

    abstract public function getRole(); // Abstract method

    public function getName() {
        return $this->name;
    }
}