<?php

class Model extends Database {


    protected $db;

    public function __construct() {
        $this->db=Database::getDB();
    }
}