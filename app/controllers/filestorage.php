<?php

class FileStorage extends Controller {
    function __construct() {
        Session::init();

        //echo "session: ".!Session::get("loggedin");
        if(!Session::get("loggedin")) {
            echo "nincs bejelentkezve";
            Session::destroy();
            header("location:..\public\\errorpage");
            exit;
        }

    }

    public function index($param=[]) {
        //echo "lefut loggedinkonstruktor";

        $this->view("filestorageview",$param);

    }

    public function logOut() {
        Session::destroy();
        header("location:..\login");
        exit;
    }


}