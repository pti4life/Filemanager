<?php

class Loginmodel extends Model {


    public function __construct() {

        parent::__construct();

    }

    //return value 0: success
    //return value 1: user doesnt exists
    //return value:2: incorrect password
    public function login($username, $password) {
        return UserOperations::authUser($username,$password);
    }




}