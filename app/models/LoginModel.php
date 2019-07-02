<?php

class Loginmodel {

    private $db;


    public function __construct() {
        $this->db=Database::getDB();

    }


    public function login($username, $password) {
        return UserOperations::authUser($username,$password);
    }




}