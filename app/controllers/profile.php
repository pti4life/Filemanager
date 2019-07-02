<?php

class Profile extends Controller {


    public function __construct() {
        Session::init();
        if(!Session::get("loggedin")) {
            echo "nincs bejelentkezve";
            Session::destroy();
            header("location:..\\errorpage");
            exit;
        }
        $this->setModel("profilemodel");
    }

    public function index($param=[]) {
        $defaultArr=$this->model->select_user();
        $array=array_merge($defaultArr,(array)$param);
        $this->view("profileView",$array);
    }

    public function changeUsername() {
        $newusername=$_POST["newusername"];
        if (!isset($newusername)) {
            call_user_func_array(["profile","index"],[[]]);
            return;
        }

        if (strlen($newusername)==0) {
            call_user_func_array(["profile","index"],[["message"=>"Töltse ki a mezőt!"]]);
            return;
        }

        $msg=$this->model->update_username($newusername);
        switch ($msg) {
            case "SUCCESS":
                call_user_func_array(["profile","index"],[["message"=>"Sikeres felhasználónév változtatás."]]);
                break;
            case "USERNAME_EXISTS":
                call_user_func_array(["profile","index"],[["message"=>"Az új felhasználónév már létezik!"]]);
                break;
            case "INVALID_USERNAME":
                call_user_func_array(["profile","index"],[["message"=>"A felhasználónév túl rövid vagy speciális karaktereket tartalmaz)!"]]);
                break;
        }

    }

    public function changePassword() {
        $oldpass=$_POST["oldpassword"];
        $newpass=$_POST["newpassword"];

        if (!isset($oldpass) or !isset($newpass)) {
            call_user_func_array(["profile","index"],[[]]);
            return;
        }
        if (strlen($oldpass)==0 or strlen($newpass)==0) {
            call_user_func_array(["profile","index"],[["message"=>"Minden mezőt ki kell tölteni."]]);
        }

        $msg=$this->model->update_password($_POST["oldpassword"],$_POST["newpassword"]);
        switch ($msg) {
            case "SUCCESS":
                call_user_func_array(["profile","index"],[["message"=>"Sikeres a jelszó változtatás."]]);
                break;
            case "INVALID_NEW_PASSWORD":
                call_user_func_array(["profile","index"],[["message"=>"Az új jelszó nem megfelelő"]]);
                break;
            case "INVALID_PASSWORD":
                call_user_func_array(["profile","index"],[["message"=>"Nem megfelően írta be a jelszavát."]]);
                break;
        }
    }





}