<?php
class FilestorageModel extends Model {

    //TODO: FILE NAME VALIDATION
    // INSERT FILE

    public function __construct() {
        parent::__construct();
        $this->USERNAME=Session::get("user_name");
        $this->USERID=$this->getId();
    }

    private $LIMIT;
    private $USERNAME;
    private $USERID;



    public function insert_file($filename=null,$type,$path,$filesize=null,$senderid=null) {
        //TODO: VALIDATION DUPLICATING WITH EDITORMODEL
        if (preg_match('/[%?^#&!~ˇ˘°˛˙´˙`˛°˘]/',$filename)) {
            return 2;
        }
        if (strlen(trim($filename))==0) {
            return 3;
        }

        try {
            $stmt = $this->db->prepare('INSERT INTO files(file_name,file_size,file_type,user_id,sender_id)
                                                                    VALUES(:filename,:filesize,:filetype,:userid,:senderid)');
            $stmt->execute(["filename" => $filename,"filesize"=>$filesize,"filetype"=>$type ,"userid"=>$this->USERID,"senderid"=>$senderid]);
            $this->upload($filename,$path);
        } catch (PDOException $ex) {
            echo $ex;
            return 1;
        }

        return 0;
    }

    private function upload($filename,$path) {
        //WARNING MODIFIED NEWPATH
        $newpath="../app/files/".$this->USERID;
        if (!file_exists($newpath)) {
            mkdir($newpath);
        }
        //TODO: LASTINSERTEDID DUPLICATION
        $id=$this->db->lastInsertId();
        $array=explode(".",$filename);

        //WARNING, CHANGED TO TERNARY OPERATOR
        count($array)==1?$filename=$id:$filename=$id.".".end($array);

        $newpath=$newpath."/".$filename;
        move_uploaded_file($path,$newpath);
    }


    public function file_list($orderby="file_name ASC", $pagenum=0,$word="") {
        echo "WORD in file_list:".$word."<br/>";
        //NOT NUMERIC VALUE
        $offset=$pagenum*$this->LIMIT;
        $word="%".strtolower($word)."%";
        $stmt = $this->db->prepare("select concat(file_id,substring(file_name,(CHAR_LENGTH(file_name) - LOCATE('.', REVERSE(file_name))+1))) AS file,file_name,file_size,modif_date, sender_id,file_type 
                                                from users inner join files on users.user_id=files.user_id
                                                where user_uname=:username and LOWER(file_name) like :fname
                                                ORDER BY ".$orderby." LIMIT ".$this->LIMIT." OFFSET ".$offset);

        $stmt->execute(["username"=>$this->USERNAME,"fname"=>$word]);
        $select = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //print_r($select);
        return $select;
    }





    //return 1 show errorpage
    public function download_file($fnameid) {
        //echo "id: ".$id;
        $stmt = $this->db->prepare("select file_name,file_type 
                                                from users inner join files on users.user_id=files.user_id
                                                where user_uname=:username and file_id=:fileid");

        $stmt->execute(["username"=>$this->USERNAME,"fileid"=>explode(".",$fnameid)[0]]);
        $exsist = $stmt->fetch(PDO::FETCH_ASSOC);
        $rowc=$stmt->rowCount();
        if ($rowc==1) {
            $file="../app/files/".$this->USERNAME."/".$fnameid;
            //echo "file: ".$file;
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

    public function delete_file($fnameid) {
        $stmt = $this->db->prepare("DELETE FROM files 
                                                WHERE user_id=(select user_id from users where user_uname=:username) and file_id=:fileid");

        $stmt->execute(["username"=>$this->USERNAME,"fileid"=>explode(".",$fnameid)[0]]);
        $res=$stmt->rowCount();
        //echo "res: ".$res."<br/>";
        if ($res==1) {
            unlink("../app/files/".$this->USERNAME."/".$fnameid);
            $dirname="../app/files/".$this->USERNAME."/";
            if ($this->is_dir_empty($dirname)) {
                rmdir($dirname);
                return 0;
            }
        } else {
            //we can display errorpage
            return 1;
        }
    }

    public function calculate_pages($word="") {
        $word="%".strtolower($word)."%";
        $stmt = $this->db->prepare("select *
                                                from users inner join files on users.user_id=files.user_id
                                                where user_uname=:username and LOWER(file_name) like :fname");
        $stmt->execute(["username"=>$this->USERNAME,"fname"=>$word]);
        $count=$stmt->rowCount();
        return $this->pages_number($this->LIMIT,$count);


    }

    private function pages_number($limit,$count) {
        if (($count/$limit)!=0) {
            return ($count/$limit)+1;
        } else {
            return $count/$limit;
        }
    }

    private function is_dir_empty($dir) {
        if (!is_readable($dir)) return NULL;
        return (count(scandir($dir)) == 2);
    }


    private function getId() {
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE user_uname=:uname");
        $stmt->execute(["uname" =>$this->USERNAME ]);
        //TODO: JAVÍTANI AZ ÖSSZES HELYEN A DUPLIKÁCIÓT
        $userid = $stmt->fetch(PDO::FETCH_ASSOC);
        return $userid["user_id"];

    }

    public function setLimit($limit) {
        $this->LIMIT=$limit;
    }






}