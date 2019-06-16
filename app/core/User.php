<?php

class User extends Model {
    //UNUSED

    private $name;
    private $username;
    private $email;
    private $password;

    public function __construct() {

    }



    public function setName($name) {
        if (preg_match($name,"/^[a-zA-Z\s]+$/")) {
            $this->name = $name;
        } else {
            return 1;
        }
    }


    public function setUsername($username) {
        if (strlen($username)<5) {
            return 2;
        }
    }


    public function setEmail($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
        } else {
            return 3;
        }
    }





    public function setPassword($password)
    {
        if(strlen($password)<5) {
            return 4;
        } else {
            //TODO:PASSWORD HASHING
            $this->password=$password;
        }
    }





    public function SignUp() {
        if (isset($this->password) and isset($this->email) and isset($this->username) and isset($this->name) and isset($this->password)) {
            //TODO: INSERT USER
        } else {
            throw new Exception("You have to initialize VALID User!");
        }
    }


}