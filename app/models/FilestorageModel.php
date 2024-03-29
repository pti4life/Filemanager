<?php
class FilestorageModel {

    private $db;

    public function __construct() {
        $this->USERNAME=Session::get("user_name");
        $this->db=Database::getDB();
    }

    private $LIMIT;
    private $USERNAME;


    public function insert_file($filename=null,$type,$path,$filesize=null,$senderid=null) {
        if (preg_match('/[%?^#&!~ˇ˘°˛˙´˙`˛°˘]/',$filename)) {
            return 2;
        }
        if (strlen(trim($filename))==0) {
            return 3;
        }
        $uid=$this->getId();

        try {
            $stmt = $this->db->prepare('INSERT INTO files(file_name,file_size,file_type,user_id,sender_id)
                                                                    VALUES(:filename,:filesize,:filetype,:userid,:senderid)');
            $stmt->execute(["filename" => $filename,"filesize"=>$filesize,"filetype"=>$type ,"userid"=>$uid,"senderid"=>$senderid]);
            $this->upload($filename,$path);
        } catch (PDOException $ex) {
            //echo $ex;
            return 1;
        }

        return 0;
    }

    private function upload($filename,$path) {
        $newpath="../app/files/".$this->USERNAME;
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
        $offset=$pagenum*$this->LIMIT;
        $word="%".strtolower($word)."%";
        $stmt = $this->db->prepare("select concat(file_id,substring(file_name,(CHAR_LENGTH(file_name) - LOCATE('.', REVERSE(file_name))+1))) AS file,ouf.file_name,ouf.file_size,ouf.modif_date, ius.user_uname as sender,ouf.file_type 
                                                from users ous inner join files ouf on ous.user_id=ouf.user_id
                                                LEFT JOIN users ius on ouf.sender_id=ius.user_id
                                                where ous.user_uname=:username and LOWER(ouf.file_name) like :fname
                                                ORDER BY ".$orderby." LIMIT ".$this->LIMIT." OFFSET ".$offset);

        $stmt->execute(["username"=>$this->USERNAME,"fname"=>$word]);
        $select = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //print_r($select);
        return $select;
    }





    public function select_file($fnameid) {
        //echo "id: ".$id;
        $stmt = $this->db->prepare("select file_name,file_type 
                                                from users inner join files on users.user_id=files.user_id
                                                where user_uname=:username and file_id=:fileid");

        $stmt->execute(["username"=>$this->USERNAME,"fileid"=>explode(".",$fnameid)[0]]);
        $exsist = $stmt->fetch(PDO::FETCH_ASSOC);
        $rowc=$stmt->rowCount();
        if ($rowc==1) {
            $file="../app/files/".$this->USERNAME."/".$fnameid;
            if (!file_exists($file)) {
                return "FILE_DOESNT_EXISTS";
            }
            return ["file"=>$file,"ftype"=>$exsist["file_type"],"fname"=>$exsist["file_name"]];
        } else {
            return "USER_FILE_NOT_FOUND";
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
                return "SUCCESS";
            }
        } else {
            //we can display errorpage
            return "DB_ERR";
        }
    }


    public function send_files($filenameids,$tousername) {
        if (empty($filenameids)) {
            return "EMPTY_ARRAY";
        }
        foreach ($filenameids as $filenameid) {
            $path="../app/files/".$this->USERNAME."/".$filenameid;
            //echo "PATH: ".$path;
            if (!file_exists($path)) {
                //echo "nem létezik";
                return "FILE_NOT_FOUND";
            }
        }

        $stmt = $this->db->prepare("select user_name,user_uname,user_email from users where user_uname=:touname");

        $stmt->execute(["touname"=>$tousername]);
        $sendtouser = $stmt->fetch(PDO::FETCH_ASSOC);
        $rowc=$stmt->rowCount();
        if ($rowc!=1) {
            return "USER_NOT_FOUND";
        }

        $stmt = $this->db->prepare('INSERT INTO files (file_name,file_size,file_type,user_id,sender_id)
                                                SELECT ofi.file_name, ofi.file_size,ofi.file_type,ius.user_id ,ous.user_id
                                                FROM files ofi inner join users ous on ofi.user_id=ous.user_id
                                                LEFT JOIN users ius on ius.user_uname=:touname
                                                WHERE ous.user_id=(select user_id from users where user_uname=:senderuname) and file_id=:fileid');



        $newpath="../app/files/".$sendtouser["user_uname"];
        if (!file_exists($newpath)) {
            mkdir($newpath);
        }
        $filenames="";
        $fnamestatement=$this->db->prepare('SELECT file_name FROM files WHERE file_id=:fileid ');
        foreach ($filenameids as $fnameid) {
            $path="../app/files/".$this->USERNAME."/".$fnameid;
            $fnamearr=explode(".",$fnameid);
            $stmt->execute(["senderuname"=>$this->USERNAME ,"fileid"=>$fnamearr[0],"touname"=>$sendtouser["user_uname"] ]);
            $newid=$this->db->lastInsertId();
            $fnamestatement->execute(["fileid"=>$newid]);
            $result=$fnamestatement->fetch(PDO::FETCH_ASSOC);
            $filenames=$filenames.",".$result["file_name"];
            count($fnamearr)==1?$fnameid=$newid:$fnameid=$newid.".".end($fnamearr);
            copy($path,$newpath."/".$fnameid);
        }
        return ["username"=>$this->USERNAME,"sendtouseremail"=>$sendtouser["user_email"],"sendtousername"=>$sendtouser["user_name"],"filenames"=>$filenames];
        
        if ($errmsg!=0) {
            return 4;
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
        $userid = $stmt->fetch(PDO::FETCH_ASSOC);
        return $userid["user_id"];

    }

    public function setLimit($limit) {
        $this->LIMIT=$limit;
    }






}