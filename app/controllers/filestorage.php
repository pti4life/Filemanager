<?php
$GLOBALS["files"]=[];
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
        $model=$this->getModel("filestoragemodel");
        $GLOBALS["files"]=$model->selectFiles();
//        if (isset($param[0]) && isset($param[1])) {
//            $param[0]=str_replace("_"," ",$param[0]);
//            $files=$model->selectFiles($param[0],$param[1]);
//        } else if (isset($param[0])) {
//            $files=$model->selectFiles($param[0]);
//        }
        //$msg_and_files=array_merge($files,$param);
        $orders=["nameorder"=>"file_name/DESC","sizeorder"=>"file_size/ASC","modifdateorder"=>"modif_date/DESC"];
        var_dump($param);
        $merged=array_merge($orders,$param);
        echo "msg and files: ";
        print_r($merged);
        echo "<br/>";
        $this->view("filestorageview",$merged);

    }

    public function logOut() {
        Session::destroy();
        header("location:..\login");
        exit;
    }


    public function uploadFile() {
        $model=$this->getModel("filestoragemodel");
        if (empty($_POST["uploadfile"])) {
            call_user_func_array(["errorpage","index"],[["message"=>"Az oldal nem található!"]]);
            return;
        }
        $name=$_FILES["file"]["name"];
        if (empty($name)) {
            call_user_func_array(["filestorage","index"],[["uploadErr"=>"Válassz fájlt!"]]);
            return;
        }

        $type=$_FILES["file"]["type"];
        $path=$_FILES["file"]["tmp_name"];
        $size=$_FILES["file"]["size"];
        $error=$_FILES["file"]["error"];
        //echo "error: ".$error;

        if ($error==0 ) {
            $uploadErr=$model->insertFile($name,$type,$path,$size);
            switch ($uploadErr) {
                case 0:
                    call_user_func_array(["filestorage","index"],[["uploadErr"=>"Sikeres feltöltés!"]]);
                    break;
                case 1:
                    call_user_func_array(["filestorage","index"],[["uploadErr"=>"Hiba a feltöltés során!"]]);
                    break;
            }


        } else {
            call_user_func_array(["filestorage","index"],[["uploadErr"=>"Hiba a feltöltés során!"]]);
            return;
        }

        //echo "NAME: ".$name."<br/>";
        //echo "type: ".$type."<br/>";
        //echo "path: ".$path."<br/>";
        //echo "size: ".$size."<br/>";
    }

    public function downloadFile($param) {
        $model=$this->getModel("filestoragemodel");
        $downErr=$model->downloadFile($param);
        switch ($downErr) {
            case 1:
                call_user_func_array(["filestorage","index"],[["message"=>"Sikertelen letöltés!"]]);
                break;
            case 2:
                call_user_func_array(["filestorage","index"],["message"=>"A fájl nem található!"]);
                break;
        }
    }

    public function deleteFile($param) {
        $model=$this->getModel("filestoragemodel");
        $errmsg=$model->deleteFile($param);
        echo "errmsg: ".$errmsg;
        switch ($errmsg) {
            case 0:
                call_user_func_array(["filestorage","index"],[["message"=>"Sikeres törlés!"]]);
                break;
            case 1:
                call_user_func_array(["filestorage","index"],[["message"=>"Sikertelen törlés!"]]);
                break;
        }
    }

    public function orderby($column,$order) {
        //echo "COL: ".$column."<br/>";
        //echo "ORDER: ".$order."<br/>";
        $orders=["nameorder"=>"file_name/DESC","sizeorder"=>"file_size/ASC","modifdateorder"=>"modif_date/DESC"];
        foreach ($orders as &$item) {
            $exploded=explode("/",$item);
            if (strcmp($column,$exploded[0])==0) {
                echo "true: col:".$column." item: ".explode("/",$item)[0]."<br/>";
                if (strcmp($order,"ASC")==0) {
                    $item=$column."/"."DESC";
                } else {
                    $item=$column."/"."ASC";
                }
                echo "INORDERS: ".$item." ORDERBYIINPUT: ".$column." ".$order;

            }
        }
        $model=$this->getModel("filestoragemodel");
        $GLOBALS["files"]=$model->selectFiles($column." ".$order);
        $this->view("filestorageview",$orders);
    }




}