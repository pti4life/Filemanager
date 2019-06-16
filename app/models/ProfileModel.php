<?php

class ProfileModel extends Model {

    public function __construct() {
        parent::__construct();

    }


    public function selectUSer() {
        $user=Session::get("user_name");
        $stmt=$this->db->prepare("SELECT user_name,user_uname,user_email FROM users WHERE user_uname=:username");
        $stmt->execute(["username"=>$user]);
        $select=$stmt->fetchObject();
        echo "USER: ";
        print_r($select);
        echo "<br/>";
        return $select;

    }
}