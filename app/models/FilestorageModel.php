<?php

class FilestorageModel extends Model {

    public function __construct() {
        parent::__construct();
    }



    public function insertFile($filename=null,$filesize=null,$filecontent=null,$senderid=null) {

        $userid=$this->getId();
        $stmt = $this->db->prepare('INSERT INTO files(file,file_name,file_size,user_id,sender_id)
                                                                    VALUES(:filecontent,:filename,:filesize,:userid,:senderid)');
        $stmt->execute(["filecontent" => $filecontent, "filename" => $filename,
                                    "filesize"=>$filesize, "userid"=>$userid,"senderid"=>$senderid]);
    }

    public function selectFiles($offset=0,$orderby="modif_date ASC") {
        $username=Session::get("user_name");
        echo $username;
        $stmt = $this->db->prepare("select file_name,file_size,modif_date, sender_id 
                                                from users inner join files on users.user_id=files.user_id
                                                where user_uname=:username
                                                ORDER BY ".$orderby." LIMIT 5 OFFSET ".$offset);

        $stmt->execute(["username"=>$username]);
        $select = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $select;
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