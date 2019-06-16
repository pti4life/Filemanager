<?php

class Profile extends Controller {

    public function __construct() {
        Session::init();
        if(!Session::get("loggedin")) {
            echo "nincs bejelentkezve";
            Session::destroy();
            header("location:..\public\\errorpage");
            exit;
        }
    }

    public function index() {
        $model=$this->getModel("profilemodel");
        $array=(array)$model->selectUser();
        $this->view("profileView",$array);
    }

}