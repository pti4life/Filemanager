<?php

class Editor extends Controller {

    function __construct() {
        Session::init();
        if(!Session::get("loggedin")) {
            echo "nincs bejelentkezve";
            Session::destroy();
            header("location:..\public\\errorpage");
            exit;
        }

        $this->setModel("editormodel");

    }

    public function index($param=[]) {
        $this->view("editorview",$param);
    }

    public function save() {
        if(!isset($_POST)) {
            header("location:..\public\\errorpage");
            exit;
        }
        $filename=$_POST["filename"].".txt";
        $content = $_POST["text"];
        echo "filename: ".$filename." content: ".$content."<br/>";
        $errormsg=$this->model->save_file($filename,$content);
        switch ($errormsg) {
            case 0:
                header("location:..\\filestorage");
                exit;
                break;
            case 1:
                call_user_func_array(["editor","index"],[["areacontent"=>$content,"message"=>"A fajlnevben nem hasznalhato az alábbi karaktereket:%,?,^,#,&,!,~,ˇ,°,˛,`"]]);
                break;
            case 2:
                call_user_func_array(["editor","index"],[["areacontent"=>$content,"message"=>"Sikertelen mentés!"]]);
                break;

        }
    }

    public function edit($filename) {
        $username=Session::get("user_name");
        $path="../app/files/$username"."/".$filename;
        $fp = fopen($path, "r");
        $data="";
        while(!feof($fp)) {
            $data = $data.fgets($fp, filesize($path));
            //echo "$data <br>";
        }
        fclose($fp);
        call_user_func_array(["editor","index"],[["areacontent"=>$data,]]);
        return;
    }
}