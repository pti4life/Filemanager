<?php
$GLOBALS["files"]=[];
$GLOBALS["checkbox"]=true;
$GLOBALS["sorting"]="ASC/0";
$GLOBALS["pagenumber"]=0;

class FileStorage extends Controller {

    private $LIMIT=6;

    function __construct() {
        Session::init();

        if(!Session::get("loggedin")) {
            Session::destroy();
            header("location: \\filemanager\public\\errorpage");
        }
        $this->setModel("filestoragemodel");
        $this->model->setLimit($this->LIMIT);
    }

    public function index($param=[]) {
        if(!is_array($param)) {
            header("location: \\filemanager\public\\filestorage");
        }
        $GLOBALS["files"]=$this->model->file_list();
        $GLOBALS["pagenumber"]=$this->model->calculate_pages();
        $param=array_merge(["clickedorder"=>"file_name/ASC/","word"=>""],$param);
        $this->view("filestorageview",$param);


    }

    public function logout() {
        Session::destroy();
        header("location:..\login");
    }


    public function uploadFile() {
        if (!isset($_POST["uploadfile"])) {
            header("location: \\filemanager\public\\errorpage");
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
    }

    public function downloadFile($fnameid) {
        if (strlen($fnameid)==0) {
            header("location: \\filemanager\public\\errorpage");
        }
        $msg=$this->model->select_file($fnameid);
        switch ($msg) {
            case "USER_FILE_NOT_FOUND":
                call_user_func_array(["filestorage","index"],[["message"=>"Sikertelen letöltés!"]]);
                break;
            case "FILE_DOESNT_EXISTS":
                call_user_func_array(["filestorage","index"],[["message"=>"A fájl nem található!"]]);
                break;
            default:
                header('Content-Disposition: attachment; filename="'.$msg["fname"].'"');
                header('Content-Description: File Transfer');
                header('Content-Type:'.$msg["ftype"]);
                readfile($msg["file"]);
        }
    }

    public function deleteFile($param) {
        $errmsg=$this->model->delete_file($param);
        switch ($errmsg) {
            case "SUCCESS":
                call_user_func_array(["filestorage","index"],[["message"=>"Sikeres törlés!"]]);
                break;
            case "DB_ERR":
                call_user_func_array(["filestorage","index"],[["message"=>"Sikertelen törlés!"]]);
                break;
        }
    }

    public function search($column=null, $order=null,$pagenum=null,$word="") {

        if(isset($_POST["search"])) {
            $word=str_replace(" ","!",$_POST["word"]);
        }

        if ((strcmp($order,"ASC")!=0 and strcmp($order,"DESC")!=0) or !is_numeric($pagenum) ) {
            header("location: \\filemanager\public\\filestorage");
        }

        switch ($column) {
            case "file_name":
                break;
            case "file_size":
                break;
            case "modif_date":
                break;
            default:
                header("location: \\filemanager\public\\filestorage");
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

    public function send() {
        $submit=$_POST["send"];
        $sendto=$_POST["username"];
        if (!isset($submit) ) {
            call_user_func_array(["filestorage","index"],[[]]);
            return;
        }
        if (!isset($_POST["filename"]))  {
            call_user_func_array(["filestorage","index"],[["message"=>"Küldésre szükséges legalább 1 fájlt kijelölni!"]]);
            return;
        }
        if (!isset($sendto)) {
            call_user_func_array(["filestorage","index"],[["message"=>"Adja meg, hogy kinek szeretne küldeni"]]);
            return;
        }

        $filearray=$_POST["filename"];

        $msg=$this->model->send_files($filearray,$sendto);
        if(is_array($msg)) {
            print_r($msg);
            $errmsg=$this->send_email($msg["username"],$msg["sendtouseremail"],$msg["sendtousername"],$msg["filenames"]);
            switch($errmsg) {
                case 0:
                    call_user_func_array(["filestorage","index"],[["message"=>"Sikeres küldés!"]]);
                    break;
                case 1:
                    call_user_func_array(["filestorage","index"],[["message"=>"Az email értesítést nem sikerült elküldeni!"]]);
                    break;
            }
        }
        switch ($msg) {
            case "FILE_NOT_FOUND":
                call_user_func_array(["filestorage","index"],[["message"=>"A küldés sikertelen, a fájl(ok) nem találhatóak"]]);
                break;
            case "USER_NOT_FOUND":
                call_user_func_array(["filestorage","index"],[["message"=>"A kiválasztott felhasználó nem található"]]);
                break;
            case "EMPTY_ARRAY":
                call_user_func_array(["filestorage","index"],[[]]);
                break;
        }
    }

    private function send_email($sendername,$sendtoemail,$sendtoname,$filenames) {
        echo($sendername." mail: ".$sendtoemail." toname: ".$sendtoname." filenames:");
        print_r($filenames);
        $EMAIL="companynorep@gmail.com";
        $PASSWORD="p4ssvv0rd";
        $COMPANYNAME='Company';

        $mail=Mail::getMail();
        try {
            $mail->isSMTP();
            $mail->Host='smtp.gmail.com';
            $mail->SMTPAuth=true;
            $mail->Username=$EMAIL;
            $mail->Password= $PASSWORD;
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->CharSet = 'UTF-8';
            $mail->setFrom($EMAIL, $COMPANYNAME);
            $arr=explode(" ",$sendtoname);
            $mail->addAddress($sendtoemail, end($arr));     // Add a recipient

            $mail->Subject = 'Új fájlja van';
            $mail->Body    = 'Kedves, '.$sendtoname."!\n Új fájl(jai) vannak:".$filenames."\nKüldte: ".$sendername;

            $mail->send();
            return 0;
        } catch (Exception $e) {
            echo $e;
            return 1;
        }
    }




}