<?php

class ProfileModel {

    private $db;

    public function __construct() {
        Session::init();
        $this->db=Database::getDB();

    }


    public function select_user() {

        $user=Session::get("user_name");
        $stmt=$this->db->prepare("SELECT user_name,user_uname,user_email FROM users WHERE user_uname=:username");
        $stmt->execute(["username"=>$user]);
        $select=$stmt->fetch(PDO::FETCH_ASSOC);
        return $select;
    }

    public function update_username($newusername) {
        if(UserOperations::checkUser($newusername)) {
            return "USERNAME_EXISTS";
        }
        $oldusername=$user=Session::get("user_name");
        if (UserOperations::isValidUsername($newusername)) {
            $stmt=$this->db->prepare("UPDATE users SET user_uname=:newusername  WHERE user_uname=:username");
            $stmt->execute(["username"=>$oldusername,"newusername"=>$newusername]);
            Session::set("user_name",$newusername);
            return "SUCCESS";
        } else {
            return "INVALID_USERNAME";
        }
    }


    public function update_password($oldpassword,$newpassword) {
        if (UserOperations::isValidPassword($newpassword)) {
            $username=Session::get("user_name");
            if (UserOperations::authUser($username,$oldpassword)==0) {
                $newpassword=password_hash($newpassword, PASSWORD_BCRYPT);
                $stmt=$this->db->prepare("UPDATE users SET user_password=:newpassword  WHERE user_uname=:username");
                $stmt->execute(["newpassword"=>$newpassword,"username"=>$username]);
                return "SUCCESS";

            } else {
                return "INVALID_PASSWORD";
            }
        } else {
            return 1;
        }



    }



}