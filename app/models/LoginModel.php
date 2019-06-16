<?php

class Loginmodel extends Model {


    public function __construct() {

        parent::__construct();

    }

    //return value 0: success
    //return value 1: user doesnt exists
    //return value:2: incorrect password
    public function authUser($username, $password) {
        //TODO:PASSWORD HASHING AND SALTING
        if ($this->checkUser($username)) {
            $stmt=$this->db->prepare("SELECT user_password FROM users WHERE user_uname=:username");
            $stmt->execute(["username"=>$username]);
            $select=$stmt->fetch();
            if (password_verify($password,$select[0])) {
                return 0;
            } else {
                return 2;
            }
        } else {
            return 1;
        }

    }

    private function checkUser($username) {
        $stmt=$this->db->prepare("SELECT * FROM users WHERE user_uname=:username");
        $stmt->execute(["username"=>$username]);
        $exsist=$stmt->rowCount();

        if ($exsist==0) {
            return false;
        } else {
            return true;
        }
    }




}