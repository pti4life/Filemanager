<?php

class Profile extends Controller {

    private $model;

    public function __construct() {
        Session::init();
        if(!Session::get("loggedin")) {
            echo "nincs bejelentkezve";
            Session::destroy();
            header("location:..\\errorpage");
            exit;
        }
    }

    public function index($param=[]) {
        $this->model=$this->getModel("profilemodel");
        $defaultArr=$this->model->selectUser();
        $array=array_merge($defaultArr,$param);
        $this->view("profileView",$array);
    }

    public function changeUsername() {
        $this->model=$this->getModel("profilemodel");
        $msg=$this->model->updateUsername();
        switch ($msg) {
            case 0:
                call_user_func_array(["profile","index"],[["message"=>"Sikeres felhasználónév változtatás."]]);
                break;
            case 1:
                call_user_func_array(["profile","index"],[["message"=>"Az új felhasználónév már létezik!"]]);
                break;
            case 2:
                call_user_func_array(["profile","index"],[["message"=>"Nem megfelelő az új felhasználónév(túl rövid))!"]]);
                break;
        }

    }

    public function changePassword() {
        $this->model=$this->getModel("profilemodel");
        $msg=$this->model->updatePassword();
        switch ($msg) {
            case 0:
                call_user_func_array(["profile","index"],[["message"=>"Sikeres a jelszó változtatás."]]);
                break;
            case 1:
                call_user_func_array(["profile","index"],[["message"=>"Az új jelszó nem megfelelő(pl. túl rövid)"]]);
                break;
            case 2:
                call_user_func_array(["profile","index"],[["message"=>"Nem megfelően írta be a jelszavát."]]);
                break;
        }
    }





}