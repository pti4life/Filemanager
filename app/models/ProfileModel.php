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
        if(UserOperations::checkUser($newusername)) {
            return 1;
        }
        $oldusername=$user=Session::get("user_name");
        if (UserOperations::isValidUsername($newusername)) {
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
        $newpassword=$_POST["newpassword"];
        if (UserOperations::isValidPassword($newpassword)) {
            $username=Session::get("user_name");
            if (UserOperations::authUser($username,$oldpassword)==0) {
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



}