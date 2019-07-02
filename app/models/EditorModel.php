<?php

class EditorModel {

    private $USERNAME;
    private $db;

    public function __construct() {
        $this->USERNAME=Session::get("user_name");
        $this->db=Database::getDB();
    }

    public function save_file($filename,$content) {
        if (preg_match('/[%?^#&!~ˇ˘°˛˙´˙`˛°˘]/',$filename)) {
            return "NOT_VALID_FNAME";
        }

        if (strlen(trim($filename))==0) {
            return "EMPTY_FNAME";
        }
        $path="../app/files/temp/file";
        file_put_contents($path,$content);
        $size=filesize($path);
        $type="text/plain";
        $userid=$this->getId();
        try {
            $stmt = $this->db->prepare('INSERT INTO files(file_name,file_size,file_type,user_id,sender_id)
                                                                    VALUES(:filename,:filesize,:filetype,:userid,:senderid)');
            $stmt->execute(["filename" => $filename.".txt","filesize"=>$size,"filetype"=>$type ,"userid"=>$userid,"senderid"=>null]);
        } catch (PDOException $ex) {
            return "DB_ERR";
        }
        $id=$this->db->lastInsertId();
        $newpath="../app/files/".$this->USERNAME;
        if (!file_exists($newpath)) {
            mkdir($newpath);
        }
        rename($path,$newpath."/".$id.".txt");
        return "SUCCESS";



    }




    public function edit_file($id) {
        try {
            $stmt = $this->db->prepare("select file_name,file_type 
                                                from users inner join files on users.user_id=files.user_id
                                                where user_uname=:username and file_id=:fileid");
            $stmt->execute(["username"=>$this->USERNAME,"fileid"=>explode(".",$id)[0]]);
            $result=$stmt->fetch(PDO::FETCH_ASSOC);
            $rowc=$stmt->rowCount();
        } catch (PDOException $ex) {
            return "DB_ERR";
        }

        if ($rowc==1) {
            $path="../app/files/".$this->USERNAME."/".$id;
            $data=file_get_contents($path);
            return ["content"=>$data,"filename"=>explode(".",$result["file_name"])[0]];
        } else {
            return "USER_FILE_NOT_FOUND";
        }

    }

    public function update_file($fnameid,$newname,$newcontent) {
        if (preg_match('/[%?^#&!~ˇ˘°˛˙´˙`˛°˘]/',$newname)) {
            return "NOT_VALID_FNAME";
        }
        if (strlen(trim($fnameid))==0) {
            return "EMPTY_FILENAME";
        }
        $path="../app/files/".$this->USERNAME."/".$fnameid;
        //echo "PATH ".$path."<br/>";
        $size=filesize($path);
        $userid=$this->getId();
        try {
            $stmt=$this->db->prepare("UPDATE files SET file_name=:filename, file_size=:filesize, modif_date=CURRENT_TIMESTAMP() where user_id=".$userid." and file_id=".explode(".",$fnameid)[0]);
            $stmt->execute(["filename"=>$newname.".txt","filesize"=>$size]);
            file_put_contents($path,$newcontent);
            return 0;
        } catch (PDOException $ex) {
            //echo $ex;
            return "DB_ERR";
        }

    }

    private function getId() {
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE user_uname=:uname");
        $stmt->execute(["uname" =>$this->USERNAME ]);
        $userid = $stmt->fetch(PDO::FETCH_ASSOC);
        return $userid["user_id"];

    }

}