<?php
$GLOBALS["files"]=[];
class FileStorage extends Controller {

    private $pagenum;
    function __construct() {
        Session::init();

        //echo "session: ".Session::get("loggedin");
        if(!Session::get("loggedin")) {
            echo "nincs bejelentkezve";
            Session::destroy();
            header("location:..\\errorpage");
            exit;
        }
        $this->setModel("filestoragemodel");
        $this->pagenum=$this->model->rowCount();
        //TODO:idea: save clicked order in listfiles
        // add default in index method

    }

    public function index($param=[]) {
        $GLOBALS["files"]=$this->model->selectFiles();
        $orders=["nameorder"=>"file_name/DESC","sizeorder"=>"file_size/ASC","modifdateorder"=>"modif_date/DESC"];
        var_dump($param);

        //array_push($param,"pagenum"=>$pagenum);
        $merged=array_merge($param,$orders);
        $merged["pagenum"]=$this->pagenum;
        print_r($merged);
        $this->view("filestorageview",$merged);



    }

    public function logout() {
        Session::destroy();
        header("location:..\login");
        exit;
    }


    public function uploadFile() {
        if (!isset($_POST["uploadfile"])) {
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
        if ($error==0 ) {
            $uploadErr=$this->model->insertFile($name,$type,$path,$size);
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

        echo "NAME: ".$name."<br/>";
        echo "type: ".$type."<br/>";
        echo "path: ".$path."<br/>";
        echo "size: ".$size."<br/>";
    }

    public function downloadFile($param=[]) {
        if (empty($param)) {
            header("location: \\filemanager\public\\errorpage");
            exit();
        }
        $downErr=$this->model->downloadFile($param);
        switch ($downErr) {
            case 1:
                call_user_func_array(["filestorage","index"],[["message"=>"Sikertelen letöltés!"]]);
                break;
            case 2:
                call_user_func_array(["filestorage","index"],[["message"=>"A fájl nem található!"]]);
                break;
        }
    }

    public function deleteFile($param) {
        $errmsg=$this->model->deleteFile($param);
        echo "errmsg: ".$errmsg;
        switch ($errmsg) {
            case 0:
                call_user_func_array(["filestorage","index"],[["message"=>"Sikeres törlés!"]]);
                break;
            case 1:
                call_user_func_array(["errorpage","index"],[["message"=>"Az oldal nem található"]]);
                break;
        }
    }

    public function listfiles($column="", $order="") {
        //echo "COL: ".$column."<br/>";
        //echo "ORDER: ".$order."<br/>";
        $orders=["nameorder"=>"file_name/DESC","sizeorder"=>"file_size/ASC","modifdateorder"=>"modif_date/DESC"];
        //validation
        $counter=0;
        foreach ($orders as $item) {
            if ((strcmp($column,explode("/",$item)[0])==0)) {
                //echo "COL: ".$column." item: ".explode("/",$item)[0]."<br/>";
                $counter++;
            }
        }
        //echo "strcmp asc: ".strcmp($order,"ASC")."<br/>";
        //echo "strcmp desc".strcmp($order,"DESC")."<br/>";
        if ($counter!=1 or (strcmp($order,"ASC")!=0 and strcmp($order,"DESC")!=0 )) {
            echo "asdasd";
            header("location: \\filemanager\public\\errorpage");
            exit();
        }

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
        $GLOBALS["files"]=$this->model->selectFiles($column." ".$order);
        $msg=$orders;
        $msg["pagenum"]=$this->pagenum;
        $this->view("filestorageview",$msg);
    }




}