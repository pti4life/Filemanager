<?php

class ProfileModel extends Model {

    public function __construct() {
        parent::__construct();
        Session::init();

    }


    public function selectUSer() {

        $user=Session::get("user_name");
        echo "USERNAME: ".$user."<br/>";
        $stmt=$this->db->prepare("SELECT user_name,user_uname,user_email FROM users WHERE user_uname=:username");
        $stmt->execute(["username"=>$user]);
        $select=$stmt->fetch(PDO::FETCH_ASSOC);
        echo "selected USER: ";
        print_r($select);
        echo "<br/>";
        return $select;
    }

    public function updateUsername() {
        $newusername=$_POST["newusername"];
        if($this->checkUser($newusername)) {
            return 1;
        }
        $oldusername=$user=Session::get("user_name");
        if ($this->isValidUsername($newusername)) {
            $stmt=$this->db->prepare("UPDATE users SET user_uname=:newusername  WHERE user_uname=:username");
            $stmt->execute(["username"=>$oldusername,"newusername"=>$newusername]);
            Session::set("user_name",$newusername);
            return 0;
        } else {
            return 2;
        }
    }

    //return 1 if new password doesnt valid
    //return 2 if old password isnt correct
    public function updatePassword() {
        $oldpassword=$_POST["oldpassword"];
        if ($this->isValidPassword($oldpassword)) {
            $username=Session::get("user_name");
            if ($this->authUser($username,$oldpassword)==0) {
                $newpassword=$_POST["newpassword"];
                $newpassword=password_hash($newpassword, PASSWORD_BCRYPT);
                $stmt=$this->db->prepare("UPDATE users SET user_password=:newpassword  WHERE user_uname=:username");
                $stmt->execute(["newpassword"=>$newpassword,"username"=>$username]);
                return 0;

            } else {
                return 2;
            }
        } else {
            return 1;
        }



    }


    //TODO: CODE DUPLICATION!!!!!!!!!
    // solution: User need to be static class and create static methods ex. ChechkUser
    // OR
    // User model abstract class gets Database, and Models ex. ProfileModel extends this class.
    // authUser method doesnt need to know user exsits or not exists in User model;


    public function authUser($username, $password) {
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

    private function isValidUsername($username) {
        if (strlen($username)<5) {
            return false;
        } else {
            return true;
        }
    }

    private function isValidPassword($password) {
        if (strlen($password)<5) {
            return false;
        } else {
            return true;
        }
    }



}