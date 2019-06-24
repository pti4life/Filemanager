<?php

class Loginmodel extends Model {


    public function __construct() {

        parent::__construct();

    }


    public function login($username, $password) {
        return UserOperations::authUser($username,$password);
    }




}