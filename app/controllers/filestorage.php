<?php
$GLOBALS["files"]=[];
$GLOBALS["checkbox"]=true;
$GLOBALS["sorting"]="ASC/0";
$GLOBALS["pagenumber"]=0;

class FileStorage extends Controller {
    //TODO:WE CAN CALL file_list WITHOUT PARAMETERS ORDER:FILE_NAME, PAGENUM:0,WORD:""
    //TODO: WE CAN CALL CALCULATE_PAGES WITH NONE PARAMETERS, THEN WE CALCULATE FOR ALL FILES

    private $LIMIT=3;

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
        $this->model->setLimit($this->LIMIT);
    }

    public function index($param=[]) {
        $GLOBALS["files"]=$this->model->file_list();
        $GLOBALS["pagenumber"]=$this->model->calculate_pages();
        $param=array_merge(["clickedorder"=>"file_name/ASC/","word"=>""],$param);
        $this->view("filestorageview",$param);


    }

    public function logout() {
        Session::destroy();
        header("location:..\login");
        exit;
    }


    public function uploadFile() {
        if (!isset($_POST["uploadfile"])) {
            header("location: \\filemanager\public\\errorpage");
            exit();
        }
        $name=$_FILES["file"]["name"];
        if (empty($name)) {
            call_user_func_array(["filestorage","index"],[["message"=>"Válassz fájlt!"]]);
            return;
        }

        $type=$_FILES["file"]["type"];
        $path=$_FILES["file"]["tmp_name"];
        $size=$_FILES["file"]["size"];
        $error=$_FILES["file"]["error"];
        if ($error==0 ) {
            $uploadErr=$this->model->insert_file($name,$type,$path,$size);
            switch ($uploadErr) {
                case 0:
                    call_user_func_array(["filestorage","index"],[["message"=>"Sikeres feltöltés!"]]);
                    break;
                case 1:
                    call_user_func_array(["filestorage","index"],[["message"=>"Hiba a feltöltés során!"]]);
                    break;
                case 2:
                    call_user_func_array(["filestorage","index"],[["message"=>"Nem használhatja az alábbi karaktereket:%,?,^,#,&,!,~,ˇ,,`"]]);
                    break;
                case 3:
                    call_user_func_array(["filestorage","index"],[["message"=>"A fájl nevét meg kell adni!"]]);
                    break;
            }


        } else {
            call_user_func_array(["filestorage","index"],[["message"=>"Hiba a feltöltés során!"]]);
            return;
        }
        //TODO:NEM LEHET FÁJLNÉVBE HASZNÁLNI +,!,?,%,/
        //echo "NAME: ".$name."<br/>";
        //echo "type: ".$type."<br/>";
        //echo "path: ".$path."<br/>";
        //echo "size: ".$size."<br/>";
    }

    //Warning PARAM WAS ARRAY AND CHANGED TO NOT ARRAY
    public function downloadFile($fnameid) {
        //WARNING empty function changed to strlen
        if (strlen($fnameid)==0) {
            header("location: \\filemanager\public\\errorpage");
            exit();
        }
        $downErr=$this->model->download_file($fnameid);
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
        $errmsg=$this->model->delete_file($param);
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

    public function search($column=null, $order=null,$pagenum=null,$word="") {

        if(isset($_POST["search"])) {
            $word=str_replace(" ","!",$_POST["word"]);
        }

        if (strcmp($order,"ASC")!=0 and strcmp($order,"DESC")!=0 ) {
            //echo "asdasd";
            header("location: \\filemanager\public\\errorpage");
            exit();
        }

        switch ($column) {
            case "file_name":
                break;
            case "file_size":
                break;
            case "modif_date":
                break;
            default:
                header("location: \\filemanager\public\\errorpage");
                exit();
        }

        strcmp($order,"ASC")==0?$GLOBALS["sorting"]="DESC/0":$GLOBALS["sorting"]="ASC/0";

        $GLOBALS["files"]=$this->model->file_list($column." ".$order,$pagenum,str_replace("!"," ",$word));
        $word=str_replace("!"," ",$word);
        $GLOBALS["pagenumber"]=$this->model->calculate_pages($word);
        $msg=["clickedorder"=>$column."/".$order."/"];
        strlen($word)!=0?$word="/".$word:"";
        $msg["word"]=$word;
        $this->view("filestorageview",$msg);

    }




}