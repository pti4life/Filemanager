<?php
class FilestorageModel extends Model {

    public function __construct() {
        parent::__construct();
    }



    public function insertFile($filename=null,$type,$path,$filesize=null,$senderid=null) {

        echo "INSERTING...<br/>";
        $userid=$this->getId();
        try {
            $stmt = $this->db->prepare('INSERT INTO files(file_name,file_size,file_type,user_id,sender_id)
                                                                    VALUES(:filename,:filesize,:filetype,:userid,:senderid)');
            $stmt->execute(["filename" => $filename,"filesize"=>$filesize,"filetype"=>$type ,"userid"=>$userid,"senderid"=>$senderid]);
            $this->upload($filename,$path);
        } catch (PDOException $ex) {
            return 1;
        }

        return 0;
    }

    private function upload($filename,$path) {
        $username=Session::get("user_name");
        $newpath="../app/files/$username";
        if (!file_exists($newpath)) {
            mkdir($newpath);
        }
        $id=$this->db->lastInsertId();
        $array=explode(".",$filename);
        $filename=$id.".".end($array);
        $newpath=$newpath."/".$filename;
        move_uploaded_file($path,$newpath);
    }

    public function selectFiles($orderby="modif_date ASC",$offset=0) {
        $username=Session::get("user_name");
        echo $username;
        $stmt = $this->db->prepare("select CONCAT(file_id,'.',SUBSTRING_INDEX(file_name, '.', -1)) AS file,file_name,file_size,modif_date, sender_id 
                                                from users inner join files on users.user_id=files.user_id
                                                where user_uname=:username
                                                ORDER BY ".$orderby." LIMIT 10 OFFSET ".$offset);

        $stmt->execute(["username"=>$username]);
        $select = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //print_r($select);
        return $select;
    }

    //return 1 show errorpage
    public function downloadFile($id) {
        $username=Session::get("user_name");
        $stmt = $this->db->prepare("select file_name,file_type 
                                                from users inner join files on users.user_id=files.user_id
                                                where user_uname=:username and file_id=:fileid");

        $stmt->execute(["username"=>$username,"fileid"=>explode(".",$id)[0]]);
        $exsist = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!empty($exsist)) {
            $file="../app/files/".$username."/".$id;
            if (!file_exists($file)) {
                return 2;
            }

            header('Content-Description: File Transfer');
            header('Content-Type: '.$exsist["file_type"]);
            header('Content-Disposition: attachment; filename="'.$exsist["file_name"].'"');
            //header('Expires: 0');
            //header('Cache-Control: must-revalidate');
            header('Content-Transfer-Encoding: binary');
            //header('Pragma: public');
            //header('Content-Length'.filesize($file));
            readfile($file);
            exit;
        } else {
            return 1;
        }

    }

    public function deleteFile($id) {
        $username=Session::get("user_name");
        $stmt = $this->db->prepare("DELETE FROM files 
                                                WHERE user_id=(select user_id from users where user_uname=:username) and file_id=:fileid");

        $stmt->execute(["username"=>$username,"fileid"=>explode(".",$id)[0]]);
        $res=$stmt->rowCount();
        echo "res: ".$res."<br/>";
        if ($res==1) {
            unlink("../app/files/".$username."/".$id);
            if ($this->isDirEmpty("../app/files/".$username."/")) {
                rmdir("../app/files/".$username."/");
                return 0;
            }
        } else {
            //we can display errorpage
            return 1;
        }
    }
    private function isDirEmpty($dir) {
        if (!is_readable($dir)) return NULL;
        return (count(scandir($dir)) == 2);
    }


    private function getId() {
        $user=Session::get("user_name");
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE user_uname=:uname");
        $stmt->execute(["uname" =>$user ]);
        //TODO: JAVÍTANI AZ ÖSSZES HELYEN A DUPLIKÁCIÓT
        $userid = $stmt->fetch(PDO::FETCH_ASSOC);
        return $userid["user_id"];

    }






}