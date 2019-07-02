<?php

$GLOBALS["savebutt"]="create";
$GLOBALS["areacontent"]="";
$GLOBALS["filename"]="";
class Editor extends Controller {

    function __construct() {
        Session::init();
        if(!Session::get("loggedin")) {
            Session::destroy();
            header("location: \\filemanager\public\\errorpage");
        }

        $this->setModel("editormodel");
    }

    public function index($param=[]) {
        $this->view("editorview",$param);
    }

    public function create() {
        if(!isset($_POST["savetext"])) {
            header("location:..\public\\filestorage");
        }
        $filename=$_POST["filename"];
        $content = $_POST["text"];
        $errormsg=$this->model->save_file($filename,$content);
        var_dump($errormsg);
        echo($errormsg);
        switch ($errormsg) {
            case "SUCCESS":
                header("location:\\filemanager\public\\filestorage\index");
                break;
            case "NOT_VALID_FNAME":
                $GLOBALS["areacontent"]=$content;
                $GLOBALS["filename"]=$filename;
                call_user_func_array(["editor","index"],[["message"=>"A fájlnévben nem használhatóak speciális karakterek"]]);
                break;
            case "DB_ERR":
                $GLOBALS["areacontent"]=$content;
                $GLOBALS["filename"]=$filename;
                call_user_func_array(["editor","index"],[["message"=>"Sikertelen mentés!"]]);
                break;
            case 'EMPTY_FNAME':
                $GLOBALS["areacontent"]=$content;
                $GLOBALS["filename"]=$filename;
                call_user_func_array(["editor","index"],[["filename"=>$filename,"message"=>"A fájlnevet kötelező megadni!"]]);
                break;

        }
    }

    public function edit($fnameid) {
        $msg=$this->model->edit_file($fnameid);
        if (is_array($msg)) {
            $GLOBALS["savebutt"]="update/".$fnameid;
            $GLOBALS["areacontent"]=$msg["content"];
            $GLOBALS["filename"]=$msg["filename"];
            call_user_func_array(["editor","index"],[[]]);
            return;

        } else {
            switch ($msg) {
                case "DB_ERR":
                    header("location:\\filemanager\public\\errorpage");
                    break;
                case "USER_FILE_NOT_FOUND":
                    header("location:\\filemanager\public\\errorpage");
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
        switch ($msg) {
            case "DB_ERR":
                $GLOBALS["savebutt"]="update/".$filenameid;
                $GLOBALS["areacontent"]=$content;
                $GLOBALS["filename"]=$filename;
                call_user_func_array(["editor","index"],[["message"=>"Sikertelen mentés!"]]);
                break;
            case 0:
                header("location:\\filemanager\public\\filestorage");
                break;
            case "NOT_VALID_FNAME":
                $GLOBALS["savebutt"]="update/".$filenameid;
                $GLOBALS["areacontent"]=$content;
                $GLOBALS["filename"]=$filename;
                call_user_func_array(["editor","index"],[["message"=>"Ne használjon speciális karaktereket kivéve: _"]]);
                break;
            case "EMPTY_FILENAME":
                $GLOBALS["savebutt"]="update/".$filenameid;
                $GLOBALS["areacontent"]=$content;
                $GLOBALS["filename"]=$filename;
                call_user_func_array(["editor","index"],[["message"=>"A fájlnevet kötelező megadni!"]]);
                break;
        }


    }



}