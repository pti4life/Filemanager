<?php

class FileStorage extends Controller {
    function __construct() {
        Session::init();

        //echo "session: ".Session::get("loggedin");
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


    public function uploadFile() {
        $model=$this->getModel("filestoragemodel");
        if (empty($_POST["uploadfile"])) {
            echo "empty";
            return;
        }
        $name=$_FILES["file"]["name"];
        if (empty($name)) {
            echo "Válassz fájlt barom user";
            return;
        }
        $type=$_FILES["file"]["type"];
        $path=$_FILES["file"]["tmp_name"];
        $size=$_FILES["file"]["size"];
        $content=file_get_contents($path);

        echo "NAME: ".$name."<br/>";
        echo "type: ".$type."<br/>";
        echo "path: ".$path."<br/>";
        echo "size: ".$size."<br/>";
        $model->insertFile($name,$size,$content);
    }

    public function listFiles() {
        $model=$this->getModel("filestoragemodel");
        $files=$model->selectFiles();
        print_r($files);
        $this->index($files);
    }




}