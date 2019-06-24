<?php

$GLOBALS["savebutt"]="create";
$GLOBALS["areacontent"]="";
$GLOBALS["filename"]="";
class Editor extends Controller {

    function __construct() {
        Session::init();
        if(!Session::get("loggedin")) {
            echo "nincs bejelentkezve";
            Session::destroy();
            header("location: \\filemanager\public\\errorpage");
            exit;
        }

        $this->setModel("editormodel");

    }

    public function index($param=[]) {
        $this->view("editorview",$param);
    }

    public function create() {
        if(!isset($_POST["savetext"])) {
            header("location:..\public\\filestorage");
            exit;
        }
        $filename=$_POST["filename"];
        $content = $_POST["text"];
        //echo "filename: ".$filename." content: ".$content."<br/>";
        $errormsg=$this->model->save_file($filename,$content);
        switch ($errormsg) {
            case 0:
                header("location:\\filemanager\public\\filestorage\index");
                exit;
                break;
            case 1:
                $GLOBALS["areacontent"]=$content;
                $GLOBALS["filename"]=$filename;
                call_user_func_array(["editor","index"],[["message"=>"A fájlnévben nem használhatóak speciális karakterek"]]);
                break;
            case 2:
                $GLOBALS["areacontent"]=$content;
                $GLOBALS["filename"]=$filename;
                call_user_func_array(["editor","index"],[["message"=>"Sikertelen mentés!"]]);
                break;
            case 3:
                $GLOBALS["areacontent"]=$content;
                $GLOBALS["filename"]=$filename;
                call_user_func_array(["editor","index"],[["filename"=>$filename,"message"=>"A fájlnevet kötelező megadni!"]]);
                break;

        }
    }

    public function edit($fnameid) {
        $msg=$this->model->edit_file($fnameid);
        //echo "FILEID: ".$fnameid;
        if (is_array($msg)) {
            $GLOBALS["savebutt"]="update/".$fnameid;
            $GLOBALS["areacontent"]=$msg["content"];
            $GLOBALS["filename"]=$msg["filename"];
            call_user_func_array(["editor","index"],[[]]);
            return;

        } else {
            switch ($msg) {
                case 1:
                    header("location:\\filemanager\public\\errorpage");
                    exit;
                    break;
                case 2:
                    header("location:\\filemanager\public\\errorpage");
                    exit;
                    break;

            }
        }
    }

    public function update($filenameid) {
        if(!isset($_POST["savetext"])) {
            header("location:..\public\\errorpage");
            exit;
        }
        $filename=$_POST["filename"];
        $content = $_POST["text"];
        $msg=$this->model->update_file($filenameid,$filename,$content);
        //echo "message: ".$msg."<br/>";
        switch ($msg) {
            case 1:
                $GLOBALS["savebutt"]="update/".$filenameid;
                $GLOBALS["areacontent"]=$content;
                $GLOBALS["filename"]=$filename;
                call_user_func_array(["editor","index"],[["message"=>"Sikertelen mentés!"]]);
                break;
            case 0:
                header("location:\\filemanager\public\\filestorage");
                exit;
                break;
            case 2:
                $GLOBALS["savebutt"]="update/".$filenameid;
                $GLOBALS["areacontent"]=$content;
                $GLOBALS["filename"]=$filename;
                call_user_func_array(["editor","index"],[["message"=>"Ne használjon speciális karaktereket kivéve: _"]]);
                break;
            case 3:
                $GLOBALS["savebutt"]="update/".$filenameid;
                $GLOBALS["areacontent"]=$content;
                $GLOBALS["filename"]=$filename;
                call_user_func_array(["editor","index"],[["message"=>"A fájlnevet kötelező megadni!"]]);
                break;
        }


    }



}