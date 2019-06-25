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
        $defaultArr=$this->model->selectUser();
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

        $msg=$this->model->updateUsername($newusername);
        switch ($msg) {
            case 0:
                call_user_func_array(["profile","index"],[["message"=>"Sikeres felhasználónév változtatás."]]);
                break;
            case 1:
                call_user_func_array(["profile","index"],[["message"=>"Az új felhasználónév már létezik!"]]);
                break;
            case 2:
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

        $msg=$this->model->updatePassword($_POST["oldpassword"],$_POST["newpassword"]);
        switch ($msg) {
            case 0:
                call_user_func_array(["profile","index"],[["message"=>"Sikeres a jelszó változtatás."]]);
                break;
            case 1:
                call_user_func_array(["profile","index"],[["message"=>"Az új jelszó nem megfelelő"]]);
                break;
            case 2:
                call_user_func_array(["profile","index"],[["message"=>"Nem megfelően írta be a jelszavát."]]);
                break;
        }
    }





}